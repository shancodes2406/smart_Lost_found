<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: signin.php');
    exit;
}
$user_name     = htmlspecialchars($_SESSION['full_name']);
$user_role     = htmlspecialchars($_SESSION['role']);
$user_initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $_SESSION['full_name']), 0, 2))));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CampusFind — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root {
  --navy:    #2F4156;
  --steel:   #567C8D;
  --mist:    #C8D9E6;
  --cream:   #F5EFEB;
  --white:   #FFFFFF;
  --navy2:   #3d5470;
  --err:     #c0392b;
  --success: #1D9E75;
  --warn:    #EF9F27;
  --bg:      #eee8e3;
  --admin-accent: #7B4F9E;
  --admin-accent2: #9B6FBE;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'DM Sans', sans-serif; background: var(--bg); }

/* ── LAYOUT ── */
.app { display: flex; min-height: 100vh; }

/* ── SIDEBAR ── */
.sidebar {
  width: 240px; flex-shrink: 0;
  background: var(--navy);
  display: flex; flex-direction: column;
  padding: 0; position: fixed;
  top: 0; left: 0; height: 100vh;
  z-index: 100; overflow: hidden;
}
.sidebar::before {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(200,217,230,0.1) 1px, transparent 1px);
  background-size: 22px 22px; z-index: 0; pointer-events: none;
}
.sidebar-orb {
  position: absolute; top: -80px; right: -80px;
  width: 240px; height: 240px; border-radius: 50%;
  background: var(--admin-accent); opacity: 0.18; z-index: 0; pointer-events: none;
}
.sidebar-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

.sb-brand {
  display: flex; align-items: center; gap: 10px;
  padding: 1.6rem 1.4rem 1.2rem;
  border-bottom: 1px solid rgba(200,217,230,0.1);
}
.sb-brand-icon {
  width: 36px; height: 36px; border-radius: 9px;
  background: var(--admin-accent);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.sb-brand-icon svg { width: 17px; height: 17px; }
.sb-brand-name { font-family: 'DM Serif Display', serif; font-size: 19px; color: #fff; line-height: 1; letter-spacing: -0.2px; }
.sb-brand-name em { font-style: italic; color: var(--mist); }
.admin-pill {
  display: inline-flex; align-items: center; gap: 4px;
  background: rgba(123,79,158,0.3); border: 1px solid rgba(123,79,158,0.5);
  border-radius: 999px; padding: 2px 8px;
  font-size: 9px; font-weight: 500; color: #d4b8f0;
  letter-spacing: 0.1em; text-transform: uppercase;
  margin-top: 4px;
}

.sb-user {
  margin: 1rem 1rem 0.5rem;
  background: rgba(123,79,158,0.15);
  border: 1px solid rgba(123,79,158,0.3);
  border-radius: 10px; padding: 10px 12px;
  display: flex; align-items: center; gap: 10px;
}
.sb-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--admin-accent);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 500; color: #fff; flex-shrink: 0;
}
.sb-uname { font-size: 13px; font-weight: 500; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sb-urole { font-size: 10px; color: #d4b8f0; text-transform: capitalize; margin-top: 1px; letter-spacing: 0.04em; }

.sb-nav { flex: 1; padding: 0.8rem 0.75rem; display: flex; flex-direction: column; gap: 2px; overflow-y: auto; }
.sb-section-label {
  font-size: 9px; font-weight: 500; color: rgba(200,217,230,0.35);
  letter-spacing: 0.1em; text-transform: uppercase;
  padding: 8px 8px 4px; margin-top: 6px;
}
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 10px; border-radius: 8px;
  cursor: pointer; transition: background .15s;
  border: none; background: transparent; width: 100%; text-align: left;
  color: rgba(200,217,230,0.6); font-family: 'DM Sans', sans-serif; font-size: 13px;
}
.nav-item svg { width: 15px; height: 15px; flex-shrink: 0; }
.nav-item:hover { background: rgba(255,255,255,0.07); color: #fff; }
.nav-item.active { background: rgba(123,79,158,0.25); color: #fff; font-weight: 500; border: 1px solid rgba(123,79,158,0.3); }
.nav-badge {
  margin-left: auto; background: #E24B4A;
  color: #fff; font-size: 10px; font-weight: 500;
  padding: 1px 6px; border-radius: 999px; min-width: 18px; text-align: center;
}

.sb-footer {
  padding: 0.75rem;
  border-top: 1px solid rgba(200,217,230,0.1);
}
.logout-btn {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 10px; border-radius: 8px;
  cursor: pointer; transition: background .15s;
  border: none; background: transparent; width: 100%;
  color: rgba(200,217,230,0.5); font-family: 'DM Sans', sans-serif; font-size: 13px;
}
.logout-btn svg { width: 15px; height: 15px; }
.logout-btn:hover { background: rgba(226,75,74,0.15); color: #f09595; }

/* ── MAIN ── */
.main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: #fff; border-bottom: 1px solid #e4ddd7;
  padding: 0 2rem; height: 58px;
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 50;
}
.topbar-left { display: flex; align-items: center; gap: 12px; }
.topbar-title { font-family: 'DM Serif Display', serif; font-size: 22px; color: #2F4156; letter-spacing: -0.3px; }
.topbar-title em { font-style: italic; color: var(--steel); }
.admin-badge {
  display: inline-flex; align-items: center; gap: 5px;
  background: rgba(123,79,158,0.1); border: 1px solid rgba(123,79,158,0.25);
  border-radius: 999px; padding: 3px 10px;
  font-size: 10px; font-weight: 500; color: var(--admin-accent);
  letter-spacing: 0.06em; text-transform: uppercase;
}
.topbar-right { display: flex; align-items: center; gap: 10px; }
.notif-btn {
  width: 36px; height: 36px; border-radius: 9px;
  border: 1px solid #ddd6cf; background: #fff;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; position: relative; transition: background .15s;
}
.notif-btn:hover { background: var(--cream); }
.notif-btn svg { width: 16px; height: 16px; color: var(--steel); }
.notif-dot {
  position: absolute; top: 6px; right: 6px;
  width: 7px; height: 7px; border-radius: 50%;
  background: #E24B4A; border: 1.5px solid #fff; display: none;
}

/* ── CONTENT ── */
.content { padding: 1.8rem 2rem 3rem; flex: 1; }
.panel { display: none; }
.panel.active { display: block; }

.greeting { margin-bottom: 1.6rem; }
.greeting h2 { font-family: 'DM Serif Display', serif; font-size: 26px; color: #2F4156; letter-spacing: -0.4px; }
.greeting h2 em { font-style: italic; color: var(--steel); }
.greeting p { font-size: 13px; color: var(--steel); margin-top: 4px; font-weight: 300; }

/* STATS */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 1.8rem; }
.stat-card {
  background: #fff; border: 1px solid #e4ddd7; border-radius: 12px;
  padding: 1rem 1.2rem;
}
.stat-card-label { font-size: 11px; color: var(--steel); font-weight: 400; text-transform: uppercase; letter-spacing: 0.06em; }
.stat-card-num { font-family: 'DM Serif Display', serif; font-size: 28px; color: #2F4156; margin-top: 4px; line-height: 1; }
.stat-card-sub { font-size: 11px; color: #aaa; margin-top: 4px; }
.stat-card.accent { background: var(--admin-accent); border-color: var(--admin-accent); }
.stat-card.accent .stat-card-label { color: rgba(255,255,255,0.6); }
.stat-card.accent .stat-card-num { color: #fff; }
.stat-card.accent .stat-card-sub { color: rgba(255,255,255,0.45); }
.stat-card.navy { background: var(--navy); border-color: var(--navy); }
.stat-card.navy .stat-card-label { color: rgba(200,217,230,0.6); }
.stat-card.navy .stat-card-num { color: #fff; }
.stat-card.navy .stat-card-sub { color: rgba(200,217,230,0.4); }

/* QUICK ACTIONS */
.quick-actions { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 1.8rem; }
.qa-card {
  background: #fff; border: 1px solid #e4ddd7; border-radius: 12px;
  padding: 1.2rem; display: flex; flex-direction: column; gap: 10px;
  cursor: pointer; transition: box-shadow .15s, border-color .15s;
}
.qa-card:hover { border-color: var(--steel); box-shadow: 0 2px 12px rgba(47,65,86,0.08); }
.qa-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.qa-icon svg { width: 18px; height: 18px; }
.qa-icon.claims { background: #F0EAFF; } .qa-icon.claims svg { color: #6B3FA0; }
.qa-icon.users  { background: #E6F1FB; } .qa-icon.users svg  { color: #185FA5; }
.qa-icon.items  { background: #E1F5EE; } .qa-icon.items svg  { color: #0F6E56; }
.qa-icon.logs   { background: #FAECE7; } .qa-icon.logs svg   { color: #993C1D; }
.qa-title { font-size: 14px; font-weight: 500; color: #2F4156; }
.qa-sub   { font-size: 12px; color: var(--steel); font-weight: 300; }

/* SEC HEAD */
.sec-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.sec-head h3 { font-family: 'DM Serif Display', serif; font-size: 19px; color: #2F4156; letter-spacing: -0.3px; }
.sec-head h3 em { font-style: italic; color: var(--steel); }
.sec-btn {
  font-size: 12px; color: var(--steel); background: none; border: 1px solid #ddd6cf;
  padding: 5px 12px; border-radius: 7px; cursor: pointer; font-family: 'DM Sans', sans-serif;
  transition: background .15s;
}
.sec-btn:hover { background: var(--cream); }

/* FILTER BAR */
.filter-bar {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 1rem; flex-wrap: wrap;
}
.filter-select {
  height: 34px; background: #fff; border: 1px solid #ddd6cf; border-radius: 8px;
  font-family: 'DM Sans', sans-serif; font-size: 12px; color: #2F4156;
  padding: 0 28px 0 10px; outline: none; cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23567C8D' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 8px center;
}
.filter-select:focus { border-color: var(--steel); }
.search-input {
  height: 34px; background: #fff; border: 1px solid #ddd6cf; border-radius: 8px;
  font-family: 'DM Sans', sans-serif; font-size: 12px; color: #2F4156;
  padding: 0 10px; outline: none; width: 200px;
}
.search-input:focus { border-color: var(--steel); }
.filter-label { font-size: 12px; color: var(--steel); margin-right: 2px; }

/* TABLE */
.tbl-wrap { background: #fff; border: 1px solid #e4ddd7; border-radius: 12px; overflow: hidden; }
table { width: 100%; border-collapse: collapse; font-size: 13px; }
thead th {
  background: #f9f5f2; text-align: left;
  padding: 10px 14px; font-weight: 500; color: var(--steel);
  font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;
  border-bottom: 1px solid #e4ddd7;
}
tbody tr { border-bottom: 1px solid #f0ebe7; transition: background .1s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: #fdf9f6; }
tbody td { padding: 11px 14px; color: #2F4156; vertical-align: middle; }
.td-muted { color: var(--steel); font-weight: 300; }

.badge {
  display: inline-block; font-size: 10px; font-weight: 500;
  padding: 2px 8px; border-radius: 999px; letter-spacing: 0.03em;
}
.badge-open      { background: #E1F5EE; color: #0F6E56; }
.badge-matched   { background: #E6F1FB; color: #185FA5; }
.badge-claimed   { background: #EEEDFE; color: #534AB7; }
.badge-closed    { background: #F1EFE8; color: #5F5E5A; }
.badge-pending   { background: #FAEEDA; color: #854F0B; }
.badge-approved  { background: #E1F5EE; color: #0F6E56; }
.badge-rejected  { background: #FCEBEB; color: #A32D2D; }
.badge-available { background: #E6F1FB; color: #185FA5; }
.badge-returned  { background: #EEEDFE; color: #534AB7; }
.badge-unread    { background: #FAECE7; color: #993C1D; }
.badge-read      { background: #F1EFE8; color: #5F5E5A; }
.badge-student   { background: #E6F1FB; color: #185FA5; }
.badge-staff     { background: #E1F5EE; color: #0F6E56; }
.badge-admin     { background: #F0EAFF; color: #6B3FA0; }

.tbl-btn {
  font-size: 11px; padding: 4px 10px; border-radius: 6px;
  border: 1px solid #ddd6cf; background: #fff; color: #2F4156;
  cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background .12s;
}
.tbl-btn:hover { background: var(--cream); }
.tbl-btn.approve { background: #E1F5EE; color: #0F6E56; border-color: #B2E4D2; }
.tbl-btn.approve:hover { background: #C2F0E0; }
.tbl-btn.reject { background: #FCEBEB; color: #A32D2D; border-color: #F5C6C6; }
.tbl-btn.reject:hover { background: #FADADB; }
.tbl-btn.primary { background: var(--navy); color: #fff; border-color: var(--navy); }
.tbl-btn.primary:hover { background: var(--navy2); }
.tbl-btn.danger { background: #FCEBEB; color: #A32D2D; border-color: #F5C6C6; }
.tbl-btn.danger:hover { background: #FADADB; }
.btn-group { display: flex; gap: 5px; }

.empty-state {
  text-align: center; padding: 3rem 1rem;
  color: var(--steel); font-size: 13px; font-weight: 300;
}
.empty-state svg { width: 36px; height: 36px; color: var(--mist); margin-bottom: 10px; display: block; margin-inline: auto; }

/* ANALYTICS */
.analytics-kpi-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 16px; }
.kpi-card { background:#fff; border:1px solid #e4ddd7; border-radius:12px; padding:1rem 1.2rem; text-align:center; }
.kpi-num { font-family:'DM Serif Display',serif; font-size:26px; color:#2F4156; line-height:1; }
.kpi-lbl { font-size:11px; color:var(--steel); margin-top:4px; font-weight:400; }
.kpi-card.green .kpi-num { color: var(--success); }
.kpi-card.warn  .kpi-num { color: var(--warn); }
.kpi-card.purple .kpi-num { color: var(--admin-accent); }

.analytics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.chart-card {
  background: #fff; border: 1px solid #e4ddd7; border-radius: 14px;
  padding: 1.4rem; position: relative;
}
.chart-card h4 { font-family: 'DM Serif Display', serif; font-size: 16px; color: #2F4156; margin-bottom: 4px; letter-spacing: -0.2px; }
.chart-card h4 em { font-style: italic; color: var(--steel); }
.chart-card p { font-size: 12px; color: var(--steel); font-weight: 300; margin-bottom: 1rem; }
.chart-canvas-wrap { position: relative; height: 200px; }
canvas { max-height: 200px; }

.insight-box {
  background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent2));
  border-radius: 14px; padding: 1.4rem; color: #fff;
  display: flex; flex-direction: column; gap: 10px;
}
.insight-box h4 { font-family: 'DM Serif Display', serif; font-size: 16px; color: #fff; margin-bottom: 2px; }
.insight-item {
  display: flex; align-items: flex-start; gap: 10px;
  background: rgba(255,255,255,0.12); border-radius: 8px;
  padding: 10px 12px; font-size: 13px; line-height: 1.5;
}
.insight-item svg { width: 14px; height: 14px; color: #e8d8ff; flex-shrink: 0; margin-top: 2px; }

/* NOTIFICATIONS */
.notif-list { display: flex; flex-direction: column; gap: 10px; }
.notif-item {
  background: #fff; border: 1px solid #e4ddd7; border-radius: 10px;
  padding: 12px 14px; display: flex; align-items: flex-start; gap: 12px;
}
.notif-item.unread { border-left: 3px solid var(--admin-accent); }
.notif-dot-icon {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
  background: #F0EAFF; display: flex; align-items: center; justify-content: center;
}
.notif-dot-icon svg { width: 14px; height: 14px; color: var(--admin-accent); }
.notif-msg { font-size: 13px; color: #2F4156; line-height: 1.5; }
.notif-time { font-size: 11px; color: var(--steel); margin-top: 3px; font-weight: 300; }

/* MODAL */
.overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(47,65,86,0.45); z-index: 200;
  align-items: center; justify-content: center;
}
.overlay.show { display: flex; }
.modal {
  background: var(--cream); border-radius: 16px;
  width: 100%; max-width: 500px; margin: 1rem;
  max-height: 90vh; overflow-y: auto;
  box-shadow: 0 20px 60px rgba(47,65,86,0.25);
}
.modal-head {
  padding: 1.4rem 1.6rem 1rem;
  border-bottom: 1px solid #e4ddd7;
  display: flex; align-items: center; justify-content: space-between;
}
.modal-head h2 { font-family: 'DM Serif Display', serif; font-size: 22px; color: #2F4156; letter-spacing: -0.3px; }
.modal-close {
  width: 30px; height: 30px; border-radius: 7px;
  border: 1px solid #ddd6cf; background: #fff;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: var(--steel); transition: background .15s;
}
.modal-close:hover { background: #f0ebe7; }
.modal-close svg { width: 14px; height: 14px; }
.modal-body { padding: 1.4rem 1.6rem; }
.mfield { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.mfield label { font-size: 11px; font-weight: 500; color: #2F4156; letter-spacing: 0.06em; text-transform: uppercase; }
.mfield input, .mfield select, .mfield textarea {
  width: 100%; background: #fff; border: 1.5px solid var(--mist); border-radius: 9px;
  font-family: 'DM Sans', sans-serif; font-size: 14px; color: #2F4156;
  padding: 9px 12px; outline: none;
  transition: border-color .2s, box-shadow .2s;
  appearance: none; -webkit-appearance: none; resize: vertical;
}
.mfield input:focus, .mfield select:focus, .mfield textarea:focus {
  border-color: var(--steel); box-shadow: 0 0 0 3px rgba(86,124,141,0.13);
}
.mfield select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23567C8D' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px;
}
.mgrid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.modal-foot {
  padding: 1rem 1.6rem 1.4rem;
  border-top: 1px solid #e4ddd7;
  display: flex; gap: 10px; justify-content: flex-end;
}
.modal-cancel {
  padding: 9px 18px; border-radius: 9px;
  border: 1px solid #ddd6cf; background: #fff; color: var(--steel);
  font-family: 'DM Sans', sans-serif; font-size: 14px; cursor: pointer;
}
.modal-cancel:hover { background: #f0ebe7; }
.modal-submit {
  padding: 9px 22px; border-radius: 9px;
  border: none; background: var(--admin-accent); color: #fff;
  font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500; cursor: pointer;
  display: flex; align-items: center; gap: 7px; transition: background .15s;
}
.modal-submit:hover { background: var(--admin-accent2); }
.modal-submit svg { width: 14px; height: 14px; }
.modal-submit.danger { background: var(--err); }
.modal-submit.danger:hover { background: #a93226; }
.merr { font-size: 12px; color: var(--err); margin-top: -8px; margin-bottom: 8px; min-height: 16px; }

/* DETAIL CARD */
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
.detail-item { background: #fff; border: 1px solid #e4ddd7; border-radius: 8px; padding: 10px 12px; }
.detail-item label { font-size: 10px; font-weight: 500; color: var(--steel); letter-spacing: 0.06em; text-transform: uppercase; display: block; margin-bottom: 4px; }
.detail-item span { font-size: 13px; color: #2F4156; font-weight: 400; }
.detail-item.full { grid-column: 1/-1; }

/* LOGS */
.log-item {
  display: flex; gap: 12px; padding: 10px 0;
  border-bottom: 1px solid #f0ebe7;
}
.log-item:last-child { border-bottom: none; }
.log-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--admin-accent); flex-shrink: 0; margin-top: 5px;
}
.log-action { font-size: 13px; color: #2F4156; line-height: 1.5; }
.log-time { font-size: 11px; color: var(--steel); font-weight: 300; margin-top: 2px; }

/* TOAST */
.toast {
  position: fixed; bottom: 1.4rem; right: 1.4rem;
  background: var(--navy); color: #fff;
  padding: 12px 18px; border-radius: 12px;
  font-size: 13px; display: flex; align-items: center; gap: 10px;
  transform: translateY(80px); opacity: 0;
  transition: all .35s cubic-bezier(.4,0,.2,1); z-index: 999;
  box-shadow: 0 8px 32px rgba(47,65,86,0.22);
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast svg { color: #5DCAA5; width: 16px; height: 16px; flex-shrink: 0; }
.toast.error svg { color: #E24B4A; }

/* SPINNER */
.spinner {
  width: 20px; height: 20px; border: 2px solid rgba(200,217,230,0.3);
  border-top-color: var(--steel); border-radius: 50%;
  animation: spin .7s linear infinite; display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* PAGINATION */
.pagination {
  display: flex; align-items: center; justify-content: flex-end; gap: 6px;
  padding: 10px 14px; border-top: 1px solid #f0ebe7;
  font-size: 12px; color: var(--steel);
}
.page-btn {
  width: 28px; height: 28px; border-radius: 6px;
  border: 1px solid #ddd6cf; background: #fff; color: #2F4156;
  font-family: 'DM Sans', sans-serif; font-size: 12px;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: background .12s;
}
.page-btn:hover { background: var(--cream); }
.page-btn.active { background: var(--navy); color: #fff; border-color: var(--navy); }

@media(max-width:900px){
  .sidebar { width: 60px; }
  .sb-brand-name, .sb-user, .sb-section-label, .nav-item span, .logout-btn span, .admin-pill { display: none; }
  .main { margin-left: 60px; }
  .stats-row, .analytics-kpi-row { grid-template-columns: repeat(2,1fr); }
  .quick-actions, .analytics-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toast-msg">Done!</span>
</div>

<!-- MODAL: View Claim -->
<div class="overlay" id="modal-claim-detail">
  <div class="modal">
    <div class="modal-head">
      <h2 id="claim-detail-title">Claim Details</h2>
      <button class="modal-close" onclick="closeModal('modal-claim-detail')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" id="claim-detail-body"></div>
    <div class="modal-foot" id="claim-detail-foot"></div>
  </div>
</div>

<!-- MODAL: Admin Remark -->
<div class="overlay" id="modal-remark">
  <div class="modal">
    <div class="modal-head">
      <h2 id="remark-title">Add Remark</h2>
      <button class="modal-close" onclick="closeModal('modal-remark')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="remark-claim-id"/>
      <input type="hidden" id="remark-action"/>
      <div class="mfield">
        <label>Admin Remark (optional)</label>
        <textarea id="remark-text" rows="3" placeholder="Add a note to the claimant…"></textarea>
      </div>
      <div class="merr" id="remark-err"></div>
    </div>
    <div class="modal-foot">
      <button class="modal-cancel" onclick="closeModal('modal-remark')">Cancel</button>
      <button class="modal-submit" id="remark-submit-btn" onclick="submitRemark()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        Confirm
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Edit Item Status -->
<div class="overlay" id="modal-edit-status">
  <div class="modal">
    <div class="modal-head">
      <h2>Update Item Status</h2>
      <button class="modal-close" onclick="closeModal('modal-edit-status')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="edit-item-id"/>
      <input type="hidden" id="edit-item-type"/>
      <div class="mfield">
        <label>Item</label>
        <input type="text" id="edit-item-name" readonly style="background:#f9f5f2;"/>
      </div>
      <div class="mfield">
        <label>New Status</label>
        <select id="edit-item-status"></select>
      </div>
      <div class="merr" id="edit-err"></div>
    </div>
    <div class="modal-foot">
      <button class="modal-cancel" onclick="closeModal('modal-edit-status')">Cancel</button>
      <button class="modal-submit" onclick="submitStatusUpdate()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Update Status
      </button>
    </div>
  </div>
</div>

<!-- MODAL: View User -->
<div class="overlay" id="modal-user-detail">
  <div class="modal">
    <div class="modal-head">
      <h2>User Details</h2>
      <button class="modal-close" onclick="closeModal('modal-user-detail')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" id="user-detail-body"></div>
    <div class="modal-foot">
      <button class="modal-cancel" onclick="closeModal('modal-user-detail')">Close</button>
    </div>
  </div>
</div>

<!-- APP -->
<div class="app">
  <aside class="sidebar">
    <div class="sidebar-orb"></div>
    <div class="sidebar-inner">
      <div class="sb-brand">
        <div class="sb-brand-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div>
          <div class="sb-brand-name">Campus<em>Find</em></div>
          <div class="admin-pill">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="8" height="8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Admin Panel
          </div>
        </div>
      </div>
      <div class="sb-user">
        <div class="sb-avatar"><?= $user_initials ?></div>
        <div>
          <div class="sb-uname"><?= $user_name ?></div>
          <div class="sb-urole">Administrator</div>
        </div>
      </div>
      <nav class="sb-nav">
        <div class="sb-section-label">Overview</div>
        <button class="nav-item active" onclick="showPanel('dashboard',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          <span>Dashboard</span>
        </button>
        <div class="sb-section-label">Claims</div>
        <button class="nav-item" onclick="showPanel('claims',this)" id="nav-claims">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          <span>Claim Requests</span>
          <span class="nav-badge" id="claims-badge" style="display:none">0</span>
        </button>
        <div class="sb-section-label">Items</div>
        <button class="nav-item" onclick="showPanel('lost',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <span>Lost Items</span>
        </button>
        <button class="nav-item" onclick="showPanel('found',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span>Found Items</span>
        </button>
        <button class="nav-item" onclick="showPanel('matches',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
          <span>Match Results</span>
        </button>
        <div class="sb-section-label">Management</div>
        <button class="nav-item" onclick="showPanel('users',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <span>User Management</span>
        </button>
        <button class="nav-item" onclick="showPanel('notifications',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span>Send Notifications</span>
        </button>
        <button class="nav-item" onclick="showPanel('logs',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span>Admin Logs</span>
        </button>
        <button class="nav-item" onclick="showPanel('analytics',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/></svg>
          <span>Analytics</span>
        </button>
      </nav>
      <div class="sb-footer">
        <a href="logout.php" style="text-decoration:none;">
          <button class="logout-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Log out</span>
          </button>
        </a>
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <div class="topbar-title" id="topbar-title">Dashboard</div>
        <span class="admin-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="10" height="10"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Admin
        </span>
      </div>
      <div class="topbar-right">
        <button class="notif-btn" onclick="showPanel('notifications', document.querySelectorAll('.nav-item')[7])">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span class="notif-dot" id="notif-dot"></span>
        </button>
      </div>
    </div>

    <div class="content">

      <!-- ══ DASHBOARD ══ -->
      <div class="panel active" id="panel-dashboard">
        <div class="greeting">
          <h2>Admin <em>Control Centre</em></h2>
          <p>Manage claims, verify items, and oversee campus lost &amp; found activity.</p>
        </div>
        <div class="stats-row">
          <div class="stat-card accent">
            <div class="stat-card-label">Pending claims</div>
            <div class="stat-card-num" id="s-pending">—</div>
            <div class="stat-card-sub">Awaiting review</div>
          </div>
          <div class="stat-card navy">
            <div class="stat-card-label">Total users</div>
            <div class="stat-card-num" id="s-users">—</div>
            <div class="stat-card-sub">Registered accounts</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-label">Open lost items</div>
            <div class="stat-card-num" id="s-lost">—</div>
            <div class="stat-card-sub">Active reports</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-label">Available found</div>
            <div class="stat-card-num" id="s-found">—</div>
            <div class="stat-card-sub">Unclaimed items</div>
          </div>
        </div>

        <div class="sec-head"><h3>Quick <em>actions</em></h3></div>
        <div class="quick-actions">
          <div class="qa-card" onclick="showPanel('claims', document.querySelectorAll('.nav-item')[1])">
            <div class="qa-icon claims"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            <div><div class="qa-title">Review Claims</div><div class="qa-sub">Approve or reject pending requests</div></div>
          </div>
          <div class="qa-card" onclick="showPanel('users', document.querySelectorAll('.nav-item')[6])">
            <div class="qa-icon users"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
            <div><div class="qa-title">Manage Users</div><div class="qa-sub">View accounts & roles</div></div>
          </div>
          <div class="qa-card" onclick="showPanel('lost', document.querySelectorAll('.nav-item')[2])">
            <div class="qa-icon items"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div>
            <div><div class="qa-title">All Lost Items</div><div class="qa-sub">View & update any report</div></div>
          </div>
          <div class="qa-card" onclick="showPanel('logs', document.querySelectorAll('.nav-item')[8])">
            <div class="qa-icon logs"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            <div><div class="qa-title">Admin Logs</div><div class="qa-sub">Audit trail of all admin actions</div></div>
          </div>
        </div>

        <div class="sec-head" style="margin-top:1.6rem;">
          <h3>Pending <em>claims</em></h3>
          <button class="sec-btn" onclick="showPanel('claims', document.querySelectorAll('.nav-item')[1])">View all</button>
        </div>
        <div class="tbl-wrap" id="dash-claims-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ══ CLAIMS ══ -->
      <div class="panel" id="panel-claims">
        <div class="sec-head">
          <h3>Claim <em>requests</em></h3>
        </div>
        <div class="filter-bar">
          <span class="filter-label">Filter:</span>
          <select class="filter-select" id="claims-filter-status" onchange="loadClaims()">
            <option value="">All statuses</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
          </select>
        </div>
        <div class="tbl-wrap" id="claims-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ══ LOST ITEMS ══ -->
      <div class="panel" id="panel-lost">
        <div class="sec-head">
          <h3>All <em>lost items</em></h3>
        </div>
        <div class="filter-bar">
          <span class="filter-label">Filter:</span>
          <select class="filter-select" id="lost-filter-status" onchange="loadAllLost()">
            <option value="">All statuses</option>
            <option value="Open">Open</option>
            <option value="Matched">Matched</option>
            <option value="Claimed">Claimed</option>
            <option value="Closed">Closed</option>
          </select>
          <select class="filter-select" id="lost-filter-cat" onchange="loadAllLost()">
            <option value="">All categories</option>
            <option>Electronics</option><option>Clothing</option><option>Stationery</option>
            <option>Bag / Wallet</option><option>Keys</option><option>ID / Card</option><option>Other</option>
          </select>
        </div>
        <div class="tbl-wrap" id="all-lost-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ══ FOUND ITEMS ══ -->
      <div class="panel" id="panel-found">
        <div class="sec-head">
          <h3>All <em>found items</em></h3>
        </div>
        <div class="filter-bar">
          <span class="filter-label">Filter:</span>
          <select class="filter-select" id="found-filter-status" onchange="loadAllFound()">
            <option value="">All statuses</option>
            <option value="Available">Available</option>
            <option value="Matched">Matched</option>
            <option value="Returned">Returned</option>
          </select>
        </div>
        <div class="tbl-wrap" id="all-found-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ══ MATCHES ══ -->
      <div class="panel" id="panel-matches">
        <div class="sec-head"><h3>Match <em>results</em></h3></div>
        <div class="tbl-wrap" id="matches-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ══ USERS ══ -->
      <div class="panel" id="panel-users">
        <div class="sec-head">
          <h3>User <em>management</em></h3>
        </div>
        <div class="filter-bar">
          <span class="filter-label">Filter:</span>
          <select class="filter-select" id="user-filter-role" onchange="loadUsers()">
            <option value="">All roles</option>
            <option value="student">Student</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
          </select>
          <select class="filter-select" id="user-filter-dept" onchange="loadUsers()">
            <option value="">All departments</option>
            <option>Computer Science</option><option>Engineering</option>
            <option>Business Administration</option><option>Arts &amp; Humanities</option>
            <option>Science &amp; Mathematics</option><option>Medicine &amp; Health Sciences</option>
            <option>Law</option><option>Architecture</option><option>Administration / Staff</option><option>Other</option>
          </select>
        </div>
        <div class="tbl-wrap" id="users-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ══ NOTIFICATIONS ══ -->
      <div class="panel" id="panel-notifications">
        <div class="sec-head">
          <h3>Send <em>notification</em></h3>
        </div>
        <div style="background:#fff;border:1px solid #e4ddd7;border-radius:12px;padding:1.4rem;max-width:560px;margin-bottom:1.6rem;">
          <div class="mfield">
            <label>Recipient</label>
            <select id="notif-user-select">
              <option value="">Loading users…</option>
            </select>
          </div>
          <div class="mfield">
            <label>Message</label>
            <textarea id="notif-message" rows="3" style="background:#fff;border:1.5px solid var(--mist);border-radius:9px;font-family:'DM Sans',sans-serif;font-size:14px;color:#2F4156;padding:9px 12px;outline:none;width:100%;resize:vertical;" placeholder="Type your message to the user…"></textarea>
          </div>
          <div class="merr" id="notif-err"></div>
          <button class="modal-submit" onclick="sendAdminNotif()" style="margin-top:4px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            Send Notification
          </button>
        </div>

        <div class="sec-head" style="margin-top:0.4rem;">
          <h3>Recent <em>notifications</em></h3>
        </div>
        <div class="notif-list" id="admin-notif-list"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ══ LOGS ══ -->
      <div class="panel" id="panel-logs">
        <div class="sec-head"><h3>Admin <em>logs</em></h3></div>
        <div class="tbl-wrap" id="logs-tbl">
          <div style="padding:1.2rem 1.4rem;" id="logs-inner"><div class="empty-state"><span class="spinner"></span></div></div>
        </div>
      </div>

      <!-- ══ ANALYTICS ══ -->
      <div class="panel" id="panel-analytics">
        <div class="greeting">
          <h2>Campus <em>Analytics</em></h2>
          <p>Full platform insights — all users, all reports, all activity.</p>
        </div>
        <div class="analytics-kpi-row">
          <div class="kpi-card"><div class="kpi-num" id="kpi-lost">—</div><div class="kpi-lbl">Total lost reports</div></div>
          <div class="kpi-card"><div class="kpi-num" id="kpi-found">—</div><div class="kpi-lbl">Total found reports</div></div>
          <div class="kpi-card green"><div class="kpi-num" id="kpi-claimed">—</div><div class="kpi-lbl">Items recovered</div></div>
          <div class="kpi-card warn"><div class="kpi-num" id="kpi-rate">—%</div><div class="kpi-lbl">Recovery rate</div></div>
        </div>
        <div class="analytics-grid">
          <div class="chart-card">
            <h4>Most lost <em>categories</em></h4>
            <p>Which item types are reported lost most often</p>
            <div class="chart-canvas-wrap"><canvas id="chart-categories"></canvas></div>
          </div>
          <div class="chart-card">
            <h4>Monthly <em>trend</em></h4>
            <p>Lost vs found reports over the past 6 months</p>
            <div class="chart-canvas-wrap"><canvas id="chart-trend"></canvas></div>
          </div>
          <div class="chart-card">
            <h4>Hotspot <em>locations</em></h4>
            <p>Where items are lost most frequently</p>
            <div class="chart-canvas-wrap"><canvas id="chart-locations"></canvas></div>
          </div>
          <div class="chart-card">
            <h4>Claims <em>breakdown</em></h4>
            <p>Approved vs rejected vs pending</p>
            <div class="chart-canvas-wrap"><canvas id="chart-claims-status"></canvas></div>
          </div>
        </div>
        <div style="margin-top:16px;" class="analytics-grid">
          <div class="insight-box">
            <h4>🔍 Admin Insights</h4>
            <div id="admin-insight-items">Loading insights…</div>
          </div>
          <div class="chart-card">
            <h4>User <em>registrations</em></h4>
            <p>New accounts registered by role</p>
            <div class="chart-canvas-wrap"><canvas id="chart-users-role"></canvas></div>
          </div>
        </div>
      </div>

    </div><!-- /content -->
  </main>
</div><!-- /app -->

<script>
const API = 'admin_api.php';
const panelTitles = {
  dashboard:'Dashboard', claims:'Claim Requests', lost:'Lost Items',
  found:'Found Items', matches:'Match Results', users:'User Management',
  notifications:'Send Notifications', logs:'Admin Logs', analytics:'Analytics'
};

// ── PANEL NAV ──
function showPanel(name, btn) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
  document.getElementById('panel-' + name).classList.add('active');
  if (btn) btn.classList.add('active');
  document.getElementById('topbar-title').textContent = panelTitles[name] || name;
  loadPanel(name);
}
function loadPanel(name) {
  if (name === 'dashboard')     { loadAdminStats(); loadDashClaims(); }
  if (name === 'claims')        loadClaims();
  if (name === 'lost')          loadAllLost();
  if (name === 'found')         loadAllFound();
  if (name === 'matches')       loadAllMatches();
  if (name === 'users')         loadUsers();
  if (name === 'notifications') { loadNotifUsers(); loadAdminNotifs(); }
  if (name === 'logs')          loadLogs();
  if (name === 'analytics')     loadAnalytics();
}

// ── ADMIN STATS ──
function loadAdminStats() {
  api('admin_stats').then(d => {
    setText('s-pending', d.pending_claims);
    setText('s-users',   d.total_users);
    setText('s-lost',    d.open_lost);
    setText('s-found',   d.available_found);
    const badge = document.getElementById('claims-badge');
    const dot   = document.getElementById('notif-dot');
    badge.textContent = d.pending_claims;
    badge.style.display = d.pending_claims > 0 ? 'inline' : 'none';
    dot.style.display   = d.pending_claims > 0 ? 'block' : 'none';
  });
}

// ── DASHBOARD CLAIMS TABLE (pending only) ──
function loadDashClaims() {
  api('admin_claims&status=Pending').then(rows => {
    const el = document.getElementById('dash-claims-tbl');
    const limited = rows.slice(0, 5);
    if (!limited.length) { el.innerHTML = emptyState('No pending claims — all caught up!'); return; }
    el.innerHTML = claimsTable(limited, true);
  });
}

// ── CLAIMS ──
function loadClaims() {
  const status = document.getElementById('claims-filter-status').value;
  api('admin_claims' + (status ? '&status=' + encodeURIComponent(status) : '')).then(rows => {
    const el = document.getElementById('claims-tbl');
    if (!rows.length) { el.innerHTML = emptyState('No claims found.'); return; }
    el.innerHTML = claimsTable(rows, false);
  });
}

function claimsTable(rows, compact) {
  return `<table><thead><tr>
    <th>Claimant</th><th>Item claimed</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th>
  </tr></thead><tbody>` +
  rows.map(r => `<tr>
    <td><strong>${esc(r.claimant_name)}</strong><br><span class="td-muted" style="font-size:11px;">${esc(r.claimant_email)}</span></td>
    <td class="td-muted">${esc(r.found_name)}</td>
    <td class="td-muted" style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(r.claim_reason||'—')}</td>
    <td>${badge(r.claim_status)}</td>
    <td class="td-muted">${r.claim_date ? r.claim_date.split('T')[0] : '—'}</td>
    <td>
      <div class="btn-group">
        <button class="tbl-btn" onclick='openClaimDetail(${JSON.stringify(r)})'>View</button>
        ${r.claim_status === 'Pending' ? `
        <button class="tbl-btn approve" onclick="openRemark(${r.claim_id},'approve')">Approve</button>
        <button class="tbl-btn reject"  onclick="openRemark(${r.claim_id},'reject')">Reject</button>
        ` : ''}
      </div>
    </td>
  </tr>`).join('') + `</tbody></table>`;
}

function openClaimDetail(r) {
  document.getElementById('claim-detail-title').textContent = 'Claim: ' + r.found_name;
  document.getElementById('claim-detail-body').innerHTML = `
    <div class="detail-grid">
      <div class="detail-item"><label>Claimant</label><span>${esc(r.claimant_name)}</span></div>
      <div class="detail-item"><label>Email</label><span>${esc(r.claimant_email)}</span></div>
      <div class="detail-item"><label>Item Claimed</label><span>${esc(r.found_name)}</span></div>
      <div class="detail-item"><label>Item Category</label><span>${esc(r.found_category||'—')}</span></div>
      <div class="detail-item"><label>Location Found</label><span>${esc(r.location_found||'—')}</span></div>
      <div class="detail-item"><label>Date Found</label><span>${r.date_found||'—'}</span></div>
      <div class="detail-item full"><label>Claim Reason</label><span>${esc(r.claim_reason||'—')}</span></div>
      <div class="detail-item"><label>Status</label><span>${r.claim_status}</span></div>
      <div class="detail-item"><label>Submitted</label><span>${r.claim_date ? r.claim_date.split('T')[0] : '—'}</span></div>
      ${r.admin_remark ? `<div class="detail-item full"><label>Admin Remark</label><span>${esc(r.admin_remark)}</span></div>` : ''}
    </div>`;
  const foot = document.getElementById('claim-detail-foot');
  foot.innerHTML = `<button class="modal-cancel" onclick="closeModal('modal-claim-detail')">Close</button>`;
  if (r.claim_status === 'Pending') {
    foot.innerHTML += `
      <button class="modal-submit" style="background:var(--err);" onclick="closeModal('modal-claim-detail');openRemark(${r.claim_id},'reject')">Reject</button>
      <button class="modal-submit" onclick="closeModal('modal-claim-detail');openRemark(${r.claim_id},'approve')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><path d="M20 6L9 17l-5-5"/></svg>
        Approve
      </button>`;
  }
  openModal('modal-claim-detail');
}

function openRemark(claimId, action) {
  document.getElementById('remark-claim-id').value = claimId;
  document.getElementById('remark-action').value = action;
  document.getElementById('remark-text').value = '';
  document.getElementById('remark-err').textContent = '';
  document.getElementById('remark-title').textContent = action === 'approve' ? 'Approve Claim' : 'Reject Claim';
  const btn = document.getElementById('remark-submit-btn');
  btn.textContent = '';
  if (action === 'approve') {
    btn.className = 'modal-submit';
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><path d="M20 6L9 17l-5-5"/></svg> Approve';
  } else {
    btn.className = 'modal-submit danger';
    btn.innerHTML = 'Reject';
  }
  openModal('modal-remark');
}

function submitRemark() {
  const claimId = document.getElementById('remark-claim-id').value;
  const action  = document.getElementById('remark-action').value;
  const remark  = document.getElementById('remark-text').value.trim();
  post('update_claim', { claim_id: claimId, action, admin_remark: remark }).then(r => {
    if (r.success) {
      closeModal('modal-remark');
      showToast(action === 'approve' ? 'Claim approved successfully!' : 'Claim rejected.');
      loadAdminStats();
      loadDashClaims();
      if (document.getElementById('panel-claims').classList.contains('active')) loadClaims();
    } else {
      document.getElementById('remark-err').textContent = r.message || 'Error updating claim.';
    }
  });
}

// ── ALL LOST ──
function loadAllLost() {
  const status = document.getElementById('lost-filter-status').value;
  const cat    = document.getElementById('lost-filter-cat').value;
  api('admin_lost' + (status ? '&status='+encodeURIComponent(status) : '') + (cat ? '&category='+encodeURIComponent(cat) : '')).then(rows => {
    const el = document.getElementById('all-lost-tbl');
    if (!rows.length) { el.innerHTML = emptyState('No lost items found.'); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Item</th><th>Reporter</th><th>Category</th><th>Location</th><th>Date Lost</th><th>Status</th><th>Actions</th>
    </tr></thead><tbody>` +
    rows.map(r => `<tr>
      <td><strong>${esc(r.item_name)}</strong></td>
      <td class="td-muted">${esc(r.reporter_name||'—')}</td>
      <td class="td-muted">${esc(r.category||'—')}</td>
      <td class="td-muted">${esc(r.location_lost||'—')}</td>
      <td class="td-muted">${r.date_lost||'—'}</td>
      <td>${badge(r.status)}</td>
      <td><button class="tbl-btn" onclick="openEditStatus(${r.lost_id},'lost','${esc(r.item_name)}','${r.status}')">Edit Status</button></td>
    </tr>`).join('') + `</tbody></table>`;
  });
}

// ── ALL FOUND ──
function loadAllFound() {
  const status = document.getElementById('found-filter-status').value;
  api('admin_found' + (status ? '&status='+encodeURIComponent(status) : '')).then(rows => {
    const el = document.getElementById('all-found-tbl');
    if (!rows.length) { el.innerHTML = emptyState('No found items found.'); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Item</th><th>Reported By</th><th>Category</th><th>Location Found</th><th>Date Found</th><th>Status</th><th>Actions</th>
    </tr></thead><tbody>` +
    rows.map(r => `<tr>
      <td><strong>${esc(r.item_name)}</strong></td>
      <td class="td-muted">${esc(r.reporter_name||'—')}</td>
      <td class="td-muted">${esc(r.category||'—')}</td>
      <td class="td-muted">${esc(r.location_found||'—')}</td>
      <td class="td-muted">${r.date_found||'—'}</td>
      <td>${badge(r.status)}</td>
      <td><button class="tbl-btn" onclick="openEditStatus(${r.found_id},'found','${esc(r.item_name)}','${r.status}')">Edit Status</button></td>
    </tr>`).join('') + `</tbody></table>`;
  });
}

function openEditStatus(id, type, name, currentStatus) {
  document.getElementById('edit-item-id').value = id;
  document.getElementById('edit-item-type').value = type;
  document.getElementById('edit-item-name').value = name;
  document.getElementById('edit-err').textContent = '';
  const sel = document.getElementById('edit-item-status');
  sel.innerHTML = type === 'lost'
    ? `<option>Open</option><option>Matched</option><option>Claimed</option><option>Closed</option>`
    : `<option>Available</option><option>Matched</option><option>Returned</option>`;
  sel.value = currentStatus;
  openModal('modal-edit-status');
}
function submitStatusUpdate() {
  const id     = document.getElementById('edit-item-id').value;
  const type   = document.getElementById('edit-item-type').value;
  const status = document.getElementById('edit-item-status').value;
  post('update_item_status', { item_id: id, item_type: type, status }).then(r => {
    if (r.success) {
      closeModal('modal-edit-status');
      showToast('Item status updated!');
      if (type === 'lost') loadAllLost(); else loadAllFound();
    } else {
      document.getElementById('edit-err').textContent = r.message || 'Error updating.';
    }
  });
}

// ── MATCHES ──
function loadAllMatches() {
  api('admin_matches').then(rows => {
    const el = document.getElementById('matches-tbl');
    if (!rows.length) { el.innerHTML = emptyState('No matches yet.'); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Lost item</th><th>Reporter</th><th>Matched with</th><th>Location found</th><th>Similarity</th><th>Matched on</th>
    </tr></thead><tbody>` +
    rows.map(r => {
      const pct = Math.round(r.similarity_score * 100);
      const col = pct >= 80 ? '#1D9E75' : pct >= 60 ? '#854F0B' : '#A32D2D';
      return `<tr>
        <td><strong>${esc(r.lost_name)}</strong></td>
        <td class="td-muted">${esc(r.reporter_name||'—')}</td>
        <td class="td-muted">${esc(r.found_name)}</td>
        <td class="td-muted">${esc(r.location_found||'—')}</td>
        <td><span style="font-weight:500;color:${col}">${pct}%</span></td>
        <td class="td-muted">${r.matched_at ? r.matched_at.split(' ')[0] : '—'}</td>
      </tr>`;
    }).join('') + `</tbody></table>`;
  });
}

// ── USERS ──
function loadUsers() {
  const role = document.getElementById('user-filter-role').value;
  const dept = document.getElementById('user-filter-dept').value;
  api('admin_users' + (role ? '&role='+encodeURIComponent(role) : '') + (dept ? '&department='+encodeURIComponent(dept) : '')).then(rows => {
    const el = document.getElementById('users-tbl');
    if (!rows.length) { el.innerHTML = emptyState('No users found.'); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Joined</th><th>Activity</th><th></th>
    </tr></thead><tbody>` +
    rows.map(r => `<tr>
      <td>
        <div style="display:flex;align-items:center;gap:8px;">
          <div style="width:28px;height:28px;border-radius:50%;background:var(--steel);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;color:#fff;flex-shrink:0;">
            ${initials(r.full_name)}
          </div>
          <strong>${esc(r.full_name)}</strong>
        </div>
      </td>
      <td class="td-muted">${esc(r.email)}</td>
      <td>${badge(r.role)}</td>
      <td class="td-muted">${esc(r.department||'—')}</td>
      <td class="td-muted">${r.created_at ? r.created_at.split('T')[0] : '—'}</td>
      <td class="td-muted" style="font-size:11px;">
        ${r.lost_count||0} lost · ${r.found_count||0} found · ${r.claim_count||0} claims
      </td>
      <td><button class="tbl-btn" onclick='openUserDetail(${JSON.stringify(r)})'>View</button></td>
    </tr>`).join('') + `</tbody></table>`;
  });
}

function openUserDetail(r) {
  document.getElementById('user-detail-body').innerHTML = `
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:1.2rem;">
      <div style="width:52px;height:52px;border-radius:50%;background:var(--admin-accent);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:500;color:#fff;">${initials(r.full_name)}</div>
      <div>
        <div style="font-size:17px;font-weight:500;color:#2F4156;">${esc(r.full_name)}</div>
        <div style="font-size:12px;color:var(--steel);">${esc(r.email)}</div>
      </div>
    </div>
    <div class="detail-grid">
      <div class="detail-item"><label>Role</label><span>${r.role}</span></div>
      <div class="detail-item"><label>Department</label><span>${esc(r.department||'—')}</span></div>
      <div class="detail-item"><label>Phone</label><span>${esc(r.phone||'—')}</span></div>
      <div class="detail-item"><label>Joined</label><span>${r.created_at ? r.created_at.split('T')[0] : '—'}</span></div>
      <div class="detail-item"><label>Lost Reports</label><span>${r.lost_count||0}</span></div>
      <div class="detail-item"><label>Found Reports</label><span>${r.found_count||0}</span></div>
      <div class="detail-item"><label>Claims Submitted</label><span>${r.claim_count||0}</span></div>
      <div class="detail-item"><label>User ID</label><span>#${r.user_id}</span></div>
    </div>`;
  openModal('modal-user-detail');
}

// ── NOTIFICATIONS ──
function loadNotifUsers() {
  api('admin_users').then(rows => {
    const sel = document.getElementById('notif-user-select');
    sel.innerHTML = '<option value="">— Select user —</option>' +
      rows.map(r => `<option value="${r.user_id}">${esc(r.full_name)} (${r.role})</option>`).join('');
  });
}
function sendAdminNotif() {
  const userId  = document.getElementById('notif-user-select').value;
  const message = document.getElementById('notif-message').value.trim();
  document.getElementById('notif-err').textContent = '';
  if (!userId) { document.getElementById('notif-err').textContent = 'Please select a recipient.'; return; }
  if (!message) { document.getElementById('notif-err').textContent = 'Please enter a message.'; return; }
  post('send_notification', { user_id: userId, message }).then(r => {
    if (r.success) {
      document.getElementById('notif-message').value = '';
      document.getElementById('notif-user-select').selectedIndex = 0;
      showToast('Notification sent!');
      loadAdminNotifs();
    } else {
      document.getElementById('notif-err').textContent = r.message || 'Error sending.';
    }
  });
}
function loadAdminNotifs() {
  api('admin_notifications').then(rows => {
    const el = document.getElementById('admin-notif-list');
    if (!rows.length) { el.innerHTML = emptyState('No notifications sent yet.'); return; }
    el.innerHTML = rows.slice(0,20).map(n => `
      <div class="notif-item">
        <div class="notif-dot-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </div>
        <div style="flex:1">
          <div class="notif-msg"><strong>${esc(n.recipient_name)}</strong> — ${esc(n.message)}</div>
          <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
            <span class="notif-time">${timeAgo(n.created_at)}</span>
            ${badge(n.status)}
          </div>
        </div>
      </div>`).join('');
  });
}

// ── LOGS ──
function loadLogs() {
  api('admin_logs').then(rows => {
    const el = document.getElementById('logs-inner');
    if (!rows.length) { el.innerHTML = emptyState('No admin actions logged yet.'); return; }
    el.innerHTML = rows.map(r => `
      <div class="log-item">
        <div class="log-dot"></div>
        <div>
          <div class="log-action"><strong>${esc(r.admin_name)}</strong> — ${esc(r.action)}</div>
          <div class="log-time">${timeAgo(r.created_at)}</div>
        </div>
      </div>`).join('');
  });
}

// ── ANALYTICS ──
let chartInstances = {};
function loadAnalytics() {
  api('get_analytics').then(data => {
    setText('kpi-lost',    data.totals.total_lost);
    setText('kpi-found',   data.totals.total_found);
    setText('kpi-claimed', data.totals.total_claimed);
    setText('kpi-rate',    data.totals.recovery_rate + '%');

    const colors = ['#2F4156','#567C8D','#7B4F9E','#8CA7B8','#B5CBDA','#C8D9E6'];

    makeChart('chart-categories', 'bar', {
      labels: data.top_categories.map(r => r.category || 'Unknown'),
      datasets: [{ label: 'Lost', data: data.top_categories.map(r => r.count), backgroundColor: colors, borderRadius: 6, borderSkipped: false }]
    }, { indexAxis: 'y', plugins: { legend: { display: false } } });

    makeChart('chart-trend', 'line', {
      labels: data.monthly_trend.map(r => r.month),
      datasets: [
        { label: 'Lost', data: data.monthly_trend.map(r => r.lost_count), borderColor: '#2F4156', backgroundColor: 'rgba(47,65,86,0.07)', tension: 0.4, fill: true, pointBackgroundColor: '#2F4156' },
        { label: 'Found', data: data.monthly_trend.map(r => r.found_count || 0), borderColor: '#7B4F9E', backgroundColor: 'rgba(123,79,158,0.06)', tension: 0.4, fill: true, pointBackgroundColor: '#7B4F9E' }
      ]
    }, { plugins: { legend: { labels: { font: { family: 'DM Sans', size: 11 } } } } });

    makeChart('chart-locations', 'doughnut', {
      labels: data.hotspot_locations.map(r => r.location || 'Unknown'),
      datasets: [{ data: data.hotspot_locations.map(r => r.count), backgroundColor: colors, hoverOffset: 8 }]
    }, { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } } });

    // Claims breakdown
    api('admin_stats').then(s => {
      makeChart('chart-claims-status', 'doughnut', {
        labels: ['Pending','Approved','Rejected'],
        datasets: [{ data: [s.pending_claims||0, s.approved_claims||0, s.rejected_claims||0], backgroundColor: ['#EF9F27','#1D9E75','#E24B4A'], hoverOffset: 8 }]
      }, { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } } });

      // Users by role
      makeChart('chart-users-role', 'bar', {
        labels: ['Students','Staff','Admins'],
        datasets: [{ label: 'Users', data: [s.student_count||0, s.staff_count||0, s.admin_count||0], backgroundColor: ['#567C8D','#2F4156','#7B4F9E'], borderRadius: 6 }]
      }, { plugins: { legend: { display: false } } });
    });

    buildAdminInsights(data);
  });
}

function makeChart(id, type, chartData, extraOptions = {}) {
  if (chartInstances[id]) chartInstances[id].destroy();
  const ctx = document.getElementById(id).getContext('2d');
  chartInstances[id] = new Chart(ctx, {
    type,
    data: chartData,
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { labels: { font: { family: 'DM Sans', size: 11 } } } },
      scales: type === 'doughnut' ? {} : {
        x: { grid: { color: '#f0ebe7' }, ticks: { font: { family: 'DM Sans', size: 11 } } },
        y: { grid: { color: '#f0ebe7' }, ticks: { font: { family: 'DM Sans', size: 11 } } }
      },
      ...extraOptions
    }
  });
}

function buildAdminInsights(data) {
  const container = document.getElementById('admin-insight-items');
  const insights = [];
  if (data.top_categories.length > 0) insights.push(`Most lost category: <strong>${data.top_categories[0].category}</strong> with <strong>${data.top_categories[0].count}</strong> reports.`);
  if (data.hotspot_locations.length > 0) insights.push(`Top loss hotspot: <strong>${data.hotspot_locations[0].location}</strong> — consider posting reminders there.`);
  if (data.totals.recovery_rate > 0) insights.push(`Campus recovery rate is <strong>${data.totals.recovery_rate}%</strong>. ${data.totals.recovery_rate >= 50 ? '🎉 Above average!' : 'Room for improvement — increase awareness.'}`);
  if (data.peak_days && data.peak_days.length > 0) {
    const sorted = [...data.peak_days].sort((a,b) => b.count-a.count);
    insights.push(`Peak loss day: <strong>${sorted[0].day_name}</strong>. Consider extra vigilance messaging.`);
  }
  if (!insights.length) {
    container.innerHTML = '<div class="insight-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>Not enough data yet.</div>';
    return;
  }
  container.innerHTML = insights.map(text => `
    <div class="insight-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M9 12l2 2 4-4"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
      <span>${text}</span>
    </div>`).join('');
}

// ── MODALS ──
function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.overlay').forEach(o => o.addEventListener('click', function(e){ if(e.target===this) this.classList.remove('show'); }));

// ── HELPERS ──
function api(action) { return fetch(`${API}?action=${action}`).then(r => r.json()); }
function post(action, data) {
  const fd = new FormData();
  fd.append('action', action);
  Object.entries(data).forEach(([k,v]) => fd.append(k,v));
  return fetch(API, { method:'POST', body:fd }).then(r => r.json());
}
function setText(id, val) { const el = document.getElementById(id); if(el) el.textContent = val ?? '—'; }
function esc(str) { return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function initials(name) {
  return String(name||'?').split(' ').slice(0,2).map(w=>w[0]||'').join('').toUpperCase();
}
function badge(status) {
  const map = {
    Open:'badge-open', Matched:'badge-matched', Claimed:'badge-claimed', Closed:'badge-closed',
    Available:'badge-available', Returned:'badge-returned',
    Pending:'badge-pending', Approved:'badge-approved', Rejected:'badge-rejected',
    Unread:'badge-unread', Read:'badge-read',
    student:'badge-student', staff:'badge-staff', admin:'badge-admin'
  };
  return `<span class="badge ${map[status]||''}">${status}</span>`;
}
function emptyState(msg) {
  return `<div class="empty-state"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>${msg}</div>`;
}
function timeAgo(ts) {
  if (!ts) return '';
  const diff = Math.floor((Date.now() - new Date(ts)) / 1000);
  if (diff < 60) return 'Just now';
  if (diff < 3600) return Math.floor(diff/60) + ' min ago';
  if (diff < 86400) return Math.floor(diff/3600) + ' hr ago';
  return Math.floor(diff/86400) + ' day' + (Math.floor(diff/86400)>1?'s':'') + ' ago';
}
function showToast(msg, isError) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.className = 'toast' + (isError ? ' error' : '');
  t.querySelector('svg').innerHTML = isError
    ? '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'
    : '<path d="M20 6L9 17l-5-5"/>';
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── INIT ──
loadPanel('dashboard');
</script>
</body>
</html>