<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
$user_name   = htmlspecialchars($_SESSION['full_name']);
$user_role   = htmlspecialchars($_SESSION['role']);
$user_dept   = htmlspecialchars($_SESSION['department'] ?? '');
$user_initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $_SESSION['full_name']), 0, 2))));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CampusFind — Dashboard</title>
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
  background: var(--steel); opacity: 0.18; z-index: 0; pointer-events: none;
}
.sidebar-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

.sb-brand {
  display: flex; align-items: center; gap: 10px;
  padding: 1.6rem 1.4rem 1.2rem;
  border-bottom: 1px solid rgba(200,217,230,0.1);
}
.sb-brand-icon {
  width: 36px; height: 36px; border-radius: 9px;
  background: var(--steel);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.sb-brand-icon svg { width: 17px; height: 17px; }
.sb-brand-name { font-family: 'DM Serif Display', serif; font-size: 19px; color: #fff; line-height: 1; letter-spacing: -0.2px; }
.sb-brand-name em { font-style: italic; color: var(--mist); }

.sb-user {
  margin: 1rem 1rem 0.5rem;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px; padding: 10px 12px;
  display: flex; align-items: center; gap: 10px;
}
.sb-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--steel);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 500; color: #fff; flex-shrink: 0;
}
.sb-uname { font-size: 13px; font-weight: 500; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sb-urole { font-size: 10px; color: rgba(200,217,230,0.5); text-transform: capitalize; margin-top: 1px; letter-spacing: 0.04em; }

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
.nav-item.active { background: rgba(255,255,255,0.12); color: #fff; font-weight: 500; }
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
.topbar-title { font-family: 'DM Serif Display', serif; font-size: 22px; color: #2F4156; letter-spacing: -0.3px; }
.topbar-title em { font-style: italic; color: var(--steel); }
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

.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 1.8rem; }
.stat-card {
  background: #fff; border: 1px solid #e4ddd7; border-radius: 12px;
  padding: 1rem 1.2rem;
}
.stat-card-label { font-size: 11px; color: var(--steel); font-weight: 400; text-transform: uppercase; letter-spacing: 0.06em; }
.stat-card-num { font-family: 'DM Serif Display', serif; font-size: 28px; color: #2F4156; margin-top: 4px; line-height: 1; }
.stat-card-sub { font-size: 11px; color: #aaa; margin-top: 4px; }
.stat-card.accent { background: var(--navy); border-color: var(--navy); }
.stat-card.accent .stat-card-label { color: rgba(200,217,230,0.6); }
.stat-card.accent .stat-card-num { color: #fff; }
.stat-card.accent .stat-card-sub { color: rgba(200,217,230,0.4); }

.quick-actions { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 1.8rem; }
.qa-card {
  background: #fff; border: 1px solid #e4ddd7; border-radius: 12px;
  padding: 1.2rem; display: flex; flex-direction: column; gap: 10px;
  cursor: pointer; transition: box-shadow .15s, border-color .15s;
}
.qa-card:hover { border-color: var(--steel); box-shadow: 0 2px 12px rgba(47,65,86,0.08); }
.qa-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.qa-icon svg { width: 18px; height: 18px; }
.qa-icon.lost   { background: #FAECE7; }
.qa-icon.lost svg { color: #993C1D; }
.qa-icon.found  { background: #E1F5EE; }
.qa-icon.found svg { color: #0F6E56; }
.qa-icon.match  { background: #E6F1FB; }
.qa-icon.match svg { color: #185FA5; }
.qa-icon.analytics { background: #F0EAFF; }
.qa-icon.analytics svg { color: #6B3FA0; }
.qa-title { font-size: 14px; font-weight: 500; color: #2F4156; }
.qa-sub   { font-size: 12px; color: var(--steel); font-weight: 300; }

.sec-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.sec-head h3 { font-family: 'DM Serif Display', serif; font-size: 19px; color: #2F4156; letter-spacing: -0.3px; }
.sec-head h3 em { font-style: italic; color: var(--steel); }
.sec-btn {
  font-size: 12px; color: var(--steel); background: none; border: 1px solid #ddd6cf;
  padding: 5px 12px; border-radius: 7px; cursor: pointer; font-family: 'DM Sans', sans-serif;
  transition: background .15s;
}
.sec-btn:hover { background: var(--cream); }

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

.tbl-btn {
  font-size: 11px; padding: 4px 10px; border-radius: 6px;
  border: 1px solid #ddd6cf; background: #fff; color: #2F4156;
  cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background .12s;
}
.tbl-btn:hover { background: var(--cream); }
.tbl-btn.primary { background: var(--navy); color: #fff; border-color: var(--navy); }
.tbl-btn.primary:hover { background: var(--navy2); }

.empty-state {
  text-align: center; padding: 3rem 1rem;
  color: var(--steel); font-size: 13px; font-weight: 300;
}
.empty-state svg { width: 36px; height: 36px; color: var(--mist); margin-bottom: 10px; display: block; margin-inline: auto; }

/* NOTIFICATIONS */
.notif-list { display: flex; flex-direction: column; gap: 10px; }
.notif-item {
  background: #fff; border: 1px solid #e4ddd7; border-radius: 10px;
  padding: 12px 14px; display: flex; align-items: flex-start; gap: 12px;
}
.notif-item.unread { border-left: 3px solid var(--steel); }
.notif-dot-icon {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
  background: #E6F1FB; display: flex; align-items: center; justify-content: center;
}
.notif-dot-icon svg { width: 14px; height: 14px; color: #185FA5; }
.notif-msg { font-size: 13px; color: #2F4156; line-height: 1.5; }
.notif-time { font-size: 11px; color: var(--steel); margin-top: 3px; font-weight: 300; }

/* ── ANALYTICS ── */
.analytics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.analytics-grid.wide { grid-template-columns: 1fr; }
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
  background: linear-gradient(135deg, var(--navy), var(--navy2));
  border-radius: 14px; padding: 1.4rem; color: #fff;
  display: flex; flex-direction: column; gap: 10px;
}
.insight-box h4 { font-family: 'DM Serif Display', serif; font-size: 16px; color: #fff; margin-bottom: 2px; }
.insight-item {
  display: flex; align-items: flex-start; gap: 10px;
  background: rgba(255,255,255,0.08); border-radius: 8px;
  padding: 10px 12px; font-size: 13px; line-height: 1.5;
}
.insight-item svg { width: 14px; height: 14px; color: var(--mist); flex-shrink: 0; margin-top: 2px; }

.analytics-kpi-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 16px; }
.kpi-card { background:#fff; border:1px solid #e4ddd7; border-radius:12px; padding:1rem 1.2rem; text-align:center; }
.kpi-num { font-family:'DM Serif Display',serif; font-size:26px; color:#2F4156; line-height:1; }
.kpi-lbl { font-size:11px; color:var(--steel); margin-top:4px; font-weight:400; }
.kpi-card.green .kpi-num { color: var(--success); }
.kpi-card.warn  .kpi-num { color: var(--warn); }

/* MODAL */
.overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(47,65,86,0.45); z-index: 200;
  align-items: center; justify-content: center;
}
.overlay.show { display: flex; }
.modal {
  background: var(--cream); border-radius: 16px;
  width: 100%; max-width: 480px; margin: 1rem;
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
.mfield input::placeholder, .mfield textarea::placeholder { color: #b5c3ca; }
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
  border: none; background: var(--navy); color: #fff;
  font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500; cursor: pointer;
  display: flex; align-items: center; gap: 7px; transition: background .15s;
}
.modal-submit:hover { background: var(--navy2); }
.modal-submit svg { width: 14px; height: 14px; }
.merr { font-size: 12px; color: var(--err); margin-top: -8px; margin-bottom: 8px; min-height: 16px; }

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

/* LOADING SPINNER */
.spinner {
  width: 20px; height: 20px; border: 2px solid rgba(200,217,230,0.3);
  border-top-color: var(--steel); border-radius: 50%;
  animation: spin .7s linear infinite; display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media(max-width:900px){
  .sidebar { width: 60px; }
  .sb-brand-name, .sb-user, .sb-section-label, .nav-item span, .logout-btn span { display: none; }
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

<!-- MODAL: Report Lost -->
<div class="overlay" id="modal-lost">
  <div class="modal">
    <div class="modal-head">
      <h2>Report lost item</h2>
      <button class="modal-close" onclick="closeModal('modal-lost')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="mfield"><label>Item name *</label><input type="text" id="l-name" placeholder="e.g. Blue water bottle"/></div>
      <div class="mgrid2">
        <div class="mfield"><label>Category</label>
          <select id="l-cat"><option value="">Select…</option><option>Electronics</option><option>Clothing</option><option>Stationery</option><option>Bag / Wallet</option><option>Keys</option><option>ID / Card</option><option>Other</option></select>
        </div>
        <div class="mfield"><label>Color</label><input type="text" id="l-color" placeholder="e.g. Blue"/></div>
      </div>
      <div class="mgrid2">
        <div class="mfield"><label>Brand</label><input type="text" id="l-brand" placeholder="e.g. Nike"/></div>
        <div class="mfield"><label>Date lost</label><input type="date" id="l-date"/></div>
      </div>
      <div class="mfield"><label>Location lost</label><input type="text" id="l-loc" placeholder="e.g. Library, 2nd floor"/></div>
      <div class="mfield"><label>Description</label><textarea id="l-desc" rows="3" placeholder="Any identifying features, serial numbers, etc."></textarea></div>
      <div class="merr" id="l-err"></div>
    </div>
    <div class="modal-foot">
      <button class="modal-cancel" onclick="closeModal('modal-lost')">Cancel</button>
      <button class="modal-submit" onclick="submitLost()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Submit report
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Report Found -->
<div class="overlay" id="modal-found">
  <div class="modal">
    <div class="modal-head">
      <h2>Report found item</h2>
      <button class="modal-close" onclick="closeModal('modal-found')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="mfield"><label>Item name *</label><input type="text" id="f-name" placeholder="e.g. Black umbrella"/></div>
      <div class="mgrid2">
        <div class="mfield"><label>Category</label>
          <select id="f-cat"><option value="">Select…</option><option>Electronics</option><option>Clothing</option><option>Stationery</option><option>Bag / Wallet</option><option>Keys</option><option>ID / Card</option><option>Other</option></select>
        </div>
        <div class="mfield"><label>Color</label><input type="text" id="f-color" placeholder="e.g. Black"/></div>
      </div>
      <div class="mgrid2">
        <div class="mfield"><label>Brand</label><input type="text" id="f-brand" placeholder="e.g. Samsonite"/></div>
        <div class="mfield"><label>Date found</label><input type="date" id="f-date"/></div>
      </div>
      <div class="mfield"><label>Location found</label><input type="text" id="f-loc" placeholder="e.g. Cafeteria, near entrance"/></div>
      <div class="mfield"><label>Description</label><textarea id="f-desc" rows="3" placeholder="Describe the item so the owner can identify it…"></textarea></div>
      <div class="merr" id="f-err"></div>
    </div>
    <div class="modal-foot">
      <button class="modal-cancel" onclick="closeModal('modal-found')">Cancel</button>
      <button class="modal-submit" onclick="submitFound()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Submit report
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Claim -->
<div class="overlay" id="modal-claim">
  <div class="modal">
    <div class="modal-head">
      <h2 id="claim-modal-title">Claim this item</h2>
      <button class="modal-close" onclick="closeModal('modal-claim')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="claim-found-id"/>
      <div class="mfield">
        <label>Claim reason *</label>
        <textarea id="claim-reason" rows="4" placeholder="Describe why this item belongs to you — include unique identifiers, purchase details, etc."></textarea>
      </div>
      <div class="merr" id="claim-err"></div>
      <p style="font-size:12px;color:var(--steel);font-weight:300;line-height:1.6;">Your claim will be reviewed by a campus admin. You will receive a notification once approved or rejected.</p>
    </div>
    <div class="modal-foot">
      <button class="modal-cancel" onclick="closeModal('modal-claim')">Cancel</button>
      <button class="modal-submit" onclick="submitClaim()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Submit claim
      </button>
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
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        </div>
        <div class="sb-brand-name">Campus<em>Find</em></div>
      </div>
      <div class="sb-user">
        <div class="sb-avatar"><?= $user_initials ?></div>
        <div>
          <div class="sb-uname"><?= $user_name ?></div>
          <div class="sb-urole"><?= $user_role ?></div>
        </div>
      </div>
      <nav class="sb-nav">
        <div class="sb-section-label">Overview</div>
        <button class="nav-item active" onclick="showPanel('dashboard',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          <span>Dashboard</span>
        </button>
        <div class="sb-section-label">Items</div>
        <button class="nav-item" onclick="showPanel('lost',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <span>Lost items</span>
        </button>
        <button class="nav-item" onclick="showPanel('found',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span>Found items</span>
        </button>
        <button class="nav-item" onclick="showPanel('matches',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
          <span>Matched items</span>
        </button>
        <div class="sb-section-label">Activity</div>
        <button class="nav-item" onclick="showPanel('claims',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span>My claims</span>
        </button>
        <button class="nav-item" onclick="showPanel('notifications',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span>Notifications</span>
          <span class="nav-badge" id="notif-badge" style="display:none">0</span>
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
      <div class="topbar-title" id="topbar-title">Dashboard</div>
      <div class="topbar-right">
        <button class="notif-btn" onclick="showPanel('notifications', document.querySelectorAll('.nav-item')[5])">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span class="notif-dot" id="notif-dot"></span>
        </button>
      </div>
    </div>

    <div class="content">

      <!-- DASHBOARD PANEL -->
      <div class="panel active" id="panel-dashboard">
        <div class="greeting">
          <h2>Good day, <em><?= htmlspecialchars(explode(' ', $user_name)[0]) ?></em></h2>
          <p>Here's what's happening with your items today.</p>
        </div>
        <div class="stats-row">
          <div class="stat-card accent">
            <div class="stat-card-label">My lost reports</div>
            <div class="stat-card-num" id="s-lost">—</div>
            <div class="stat-card-sub">Active open reports</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-label">Found by me</div>
            <div class="stat-card-num" id="s-found">—</div>
            <div class="stat-card-sub">Reported to campus</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-label">Matches</div>
            <div class="stat-card-num" id="s-matches">—</div>
            <div class="stat-card-sub">AI similarity &gt;60%</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-label">Notifications</div>
            <div class="stat-card-num" id="s-notifs">—</div>
            <div class="stat-card-sub">Unread messages</div>
          </div>
        </div>
        <div class="sec-head"><h3>Quick <em>actions</em></h3></div>
        <div class="quick-actions">
          <div class="qa-card" onclick="openModal('modal-lost')">
            <div class="qa-icon lost"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div>
            <div><div class="qa-title">Report lost item</div><div class="qa-sub">Submit a new lost item report</div></div>
          </div>
          <div class="qa-card" onclick="openModal('modal-found')">
            <div class="qa-icon found"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
            <div><div class="qa-title">Report found item</div><div class="qa-sub">Help reunite someone with their item</div></div>
          </div>
          <div class="qa-card" onclick="showPanel('analytics', document.querySelectorAll('.nav-item')[6])">
            <div class="qa-icon analytics"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/></svg></div>
            <div><div class="qa-title">View analytics</div><div class="qa-sub">Hotspots, trends & insights</div></div>
          </div>
        </div>
        <div class="sec-head" style="margin-top:1.6rem;">
          <h3>Recent <em>lost reports</em></h3>
          <button class="sec-btn" onclick="showPanel('lost', document.querySelectorAll('.nav-item')[1])">View all</button>
        </div>
        <div class="tbl-wrap" id="dash-lost-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- LOST ITEMS PANEL -->
      <div class="panel" id="panel-lost">
        <div class="sec-head">
          <h3>My <em>lost items</em></h3>
          <button class="sec-btn" onclick="openModal('modal-lost')">+ Report lost item</button>
        </div>
        <div class="tbl-wrap" id="lost-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- FOUND ITEMS PANEL -->
      <div class="panel" id="panel-found">
        <div class="sec-head">
          <h3>Found <em>items</em></h3>
          <button class="sec-btn" onclick="openModal('modal-found')">+ Report found item</button>
        </div>
        <div class="tbl-wrap" id="found-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- MATCHES PANEL -->
      <div class="panel" id="panel-matches">
        <div class="sec-head"><h3>Matched <em>items</em></h3></div>
        <div class="tbl-wrap" id="matches-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- CLAIMS PANEL -->
      <div class="panel" id="panel-claims">
        <div class="sec-head"><h3>My <em>claims</em></h3></div>
        <div class="tbl-wrap" id="claims-tbl"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- NOTIFICATIONS PANEL -->
      <div class="panel" id="panel-notifications">
        <div class="sec-head">
          <h3>Notifications</h3>
          <button class="sec-btn" onclick="markAllRead()">Mark all as read</button>
        </div>
        <div class="notif-list" id="notif-list"><div class="empty-state"><span class="spinner"></span></div></div>
      </div>

      <!-- ANALYTICS PANEL -->
      <div class="panel" id="panel-analytics">
        <div class="greeting">
          <h2>Campus <em>Analytics</em></h2>
          <p>Insights derived from all reported lost &amp; found activity on campus.</p>
        </div>

        <!-- KPI row -->
        <div class="analytics-kpi-row" id="analytics-kpis">
          <div class="kpi-card"><div class="kpi-num" id="kpi-lost">—</div><div class="kpi-lbl">Total lost reports</div></div>
          <div class="kpi-card"><div class="kpi-num" id="kpi-found">—</div><div class="kpi-lbl">Total found reports</div></div>
          <div class="kpi-card green"><div class="kpi-num" id="kpi-claimed">—</div><div class="kpi-lbl">Items recovered</div></div>
          <div class="kpi-card warn"><div class="kpi-num" id="kpi-rate">—%</div><div class="kpi-lbl">Recovery rate</div></div>
        </div>

        <div class="analytics-grid">
          <!-- Category chart -->
          <div class="chart-card">
            <h4>Most lost <em>categories</em></h4>
            <p>Which item types are reported lost most often</p>
            <div class="chart-canvas-wrap"><canvas id="chart-categories"></canvas></div>
          </div>
          <!-- Monthly trend -->
          <div class="chart-card">
            <h4>Monthly <em>trend</em></h4>
            <p>Lost item reports over the past 6 months</p>
            <div class="chart-canvas-wrap"><canvas id="chart-trend"></canvas></div>
          </div>
          <!-- Hotspot locations -->
          <div class="chart-card">
            <h4>Hotspot <em>locations</em></h4>
            <p>Where items are lost most frequently on campus</p>
            <div class="chart-canvas-wrap"><canvas id="chart-locations"></canvas></div>
          </div>
          <!-- Day of week -->
          <div class="chart-card">
            <h4>Peak <em>days</em></h4>
            <p>Which days of the week see the most losses</p>
            <div class="chart-canvas-wrap"><canvas id="chart-days"></canvas></div>
          </div>
        </div>

        <!-- Insights box -->
        <div style="margin-top:16px;" class="analytics-grid">
          <div class="insight-box" id="insight-box">
            <h4>🔍 Key Insights</h4>
            <div id="insight-items">Loading insights…</div>
          </div>
          <div class="chart-card">
            <h4>Top found <em>locations</em></h4>
            <p>Where items are most often turned in</p>
            <div class="chart-canvas-wrap"><canvas id="chart-found-locs"></canvas></div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script>
const API = 'api.php';
const panelTitles = {
  dashboard: 'Dashboard', lost: 'Lost items', found: 'Found items',
  matches: 'Matched items', claims: 'My claims',
  notifications: 'Notifications', analytics: 'Analytics'
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
  if (name === 'dashboard')     { loadStats(); loadLost('dash-lost-tbl', 3); }
  if (name === 'lost')          loadLost('lost-tbl');
  if (name === 'found')         loadFound();
  if (name === 'matches')       loadMatches();
  if (name === 'claims')        loadClaims();
  if (name === 'notifications') loadNotifications();
  if (name === 'analytics')     loadAnalytics();
}

// ── STATS ──
function loadStats() {
  api('get_stats').then(d => {
    setText('s-lost',    d.lost);
    setText('s-found',   d.found);
    setText('s-matches', d.matches);
    setText('s-notifs',  d.notifs);
    const badge = document.getElementById('notif-badge');
    const dot   = document.getElementById('notif-dot');
    badge.textContent = d.notifs;
    badge.style.display = d.notifs > 0 ? 'inline' : 'none';
    dot.style.display   = d.notifs > 0 ? 'block' : 'none';
  });
}

// ── LOST ──
function loadLost(containerId, limit) {
  api('get_lost').then(rows => {
    const el = document.getElementById(containerId);
    if (limit) rows = rows.slice(0, limit);
    if (!rows.length) { el.innerHTML = emptyState('No lost items reported yet.'); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Item</th><th>Category</th><th>Location</th><th>Date lost</th><th>Status</th>
    </tr></thead><tbody>` +
    rows.map(r => `<tr>
      <td><strong>${esc(r.item_name)}</strong></td>
      <td class="td-muted">${esc(r.category||'—')}</td>
      <td class="td-muted">${esc(r.location_lost||'—')}</td>
      <td class="td-muted">${r.date_lost||'—'}</td>
      <td>${badge(r.status)}</td>
    </tr>`).join('') + `</tbody></table>`;
  });
}

// ── FOUND ──
function loadFound() {
  api('get_found').then(rows => {
    const el = document.getElementById('found-tbl');
    if (!rows.length) { el.innerHTML = emptyState('No found items reported yet.'); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Item</th><th>Category</th><th>Location found</th><th>Date found</th><th>Reported by</th><th>Status</th><th></th>
    </tr></thead><tbody>` +
    rows.map(r => `<tr>
      <td><strong>${esc(r.item_name)}</strong></td>
      <td class="td-muted">${esc(r.category||'—')}</td>
      <td class="td-muted">${esc(r.location_found||'—')}</td>
      <td class="td-muted">${r.date_found||'—'}</td>
      <td class="td-muted">${esc(r.reporter_name||'—')}</td>
      <td>${badge(r.status)}</td>
      <td>${r.status === 'Available' ? `<button class="tbl-btn primary" onclick="openClaim(${r.found_id},'${esc(r.item_name)}')">Claim</button>` : ''}</td>
    </tr>`).join('') + `</tbody></table>`;
  });
}

// ── MATCHES ──
function loadMatches() {
  api('get_matches').then(rows => {
    const el = document.getElementById('matches-tbl');
    if (!rows.length) { el.innerHTML = emptyState('No matches yet. AI matching runs automatically when items are reported.'); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Your lost item</th><th>Matched with</th><th>Location found</th><th>Similarity</th><th>Matched on</th><th></th>
    </tr></thead><tbody>` +
    rows.map(r => {
      const pct = Math.round(r.similarity_score * 100);
      const col = pct >= 80 ? '#1D9E75' : pct >= 60 ? '#854F0B' : '#A32D2D';
      return `<tr>
        <td><strong>${esc(r.lost_name)}</strong></td>
        <td class="td-muted">${esc(r.found_name)}</td>
        <td class="td-muted">${esc(r.location_found||'—')}</td>
        <td><span style="font-weight:500;color:${col}">${pct}%</span></td>
        <td class="td-muted">${r.matched_at ? r.matched_at.split(' ')[0] : '—'}</td>
        <td>${r.found_status === 'Matched' || r.found_status === 'Available'
          ? `<button class="tbl-btn primary" onclick="openClaim(${r.found_id},'${esc(r.found_name)}')">Claim</button>` : ''}</td>
      </tr>`;
    }).join('') + `</tbody></table>`;
  });
}

// ── CLAIMS ──
function loadClaims() {
  api('get_claims').then(rows => {
    const el = document.getElementById('claims-tbl');
    if (!rows.length) { el.innerHTML = emptyState("You haven't submitted any claims yet."); return; }
    el.innerHTML = `<table><thead><tr>
      <th>Item claimed</th><th>Claim reason</th><th>Status</th><th>Admin remark</th><th>Date</th>
    </tr></thead><tbody>` +
    rows.map(r => `<tr>
      <td><strong>${esc(r.found_name)}</strong></td>
      <td class="td-muted" style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(r.claim_reason||'')}</td>
      <td>${badge(r.claim_status)}</td>
      <td class="td-muted">${esc(r.admin_remark||'—')}</td>
      <td class="td-muted">${r.claim_date ? r.claim_date.split('T')[0] : '—'}</td>
    </tr>`).join('') + `</tbody></table>`;
  });
}

// ── NOTIFICATIONS ──
function loadNotifications() {
  api('get_notifications').then(rows => {
    const el = document.getElementById('notif-list');
    if (!rows.length) { el.innerHTML = emptyState('No notifications yet.'); return; }
    el.innerHTML = rows.map(n => `
      <div class="notif-item ${n.status === 'Unread' ? 'unread' : ''}" id="notif-${n.notification_id}">
        <div class="notif-dot-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div style="flex:1">
          <div class="notif-msg">${esc(n.message)}</div>
          <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
            <span class="notif-time">${timeAgo(n.created_at)}</span>
            ${badge(n.status)}
            ${n.status === 'Unread' ? `<button class="tbl-btn" style="font-size:10px;padding:2px 8px;" onclick="markRead(${n.notification_id})">Mark read</button>` : ''}
          </div>
        </div>
      </div>`).join('');
  });
}

function markRead(id) {
  post('mark_read', { notification_id: id }).then(() => {
    loadNotifications(); loadStats();
  });
}
function markAllRead() {
  post('mark_all_read', {}).then(() => {
    loadNotifications(); loadStats(); showToast('All notifications marked as read.');
  });
}

// ── ANALYTICS ──
let chartInstances = {};

function loadAnalytics() {
  api('get_analytics').then(data => {
    // KPIs
    setText('kpi-lost',    data.totals.total_lost);
    setText('kpi-found',   data.totals.total_found);
    setText('kpi-claimed', data.totals.total_claimed);
    setText('kpi-rate',    data.totals.recovery_rate + '%');

    const colors = ['#2F4156','#567C8D','#8CA7B8','#B5CBDA','#C8D9E6','#D9E8F0'];

    // Categories bar chart
    makeChart('chart-categories', 'bar', {
      labels: data.top_categories.map(r => r.category || 'Unknown'),
      datasets: [{ label: 'Lost reports', data: data.top_categories.map(r => r.count), backgroundColor: colors, borderRadius: 6, borderSkipped: false }]
    }, { indexAxis: 'y', plugins: { legend: { display: false } } });

    // Monthly trend line chart
    makeChart('chart-trend', 'line', {
      labels: data.monthly_trend.map(r => r.month),
      datasets: [{ label: 'Lost reports', data: data.monthly_trend.map(r => r.lost_count), borderColor: '#2F4156', backgroundColor: 'rgba(47,65,86,0.08)', tension: 0.4, fill: true, pointBackgroundColor: '#2F4156' }]
    }, { plugins: { legend: { display: false } } });

    // Hotspot locations
    makeChart('chart-locations', 'doughnut', {
      labels: data.hotspot_locations.map(r => r.location || 'Unknown'),
      datasets: [{ data: data.hotspot_locations.map(r => r.count), backgroundColor: colors, hoverOffset: 8 }]
    }, { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } } });

    // Peak days
    const dayOrder = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const dayData = {};
    data.peak_days.forEach(r => dayData[r.day_name] = parseInt(r.count));
    makeChart('chart-days', 'bar', {
      labels: dayOrder,
      datasets: [{ label: 'Lost reports', data: dayOrder.map(d => dayData[d] || 0), backgroundColor: dayOrder.map((d,i) => i === 0 || i === 6 ? '#C8D9E6' : '#2F4156'), borderRadius: 5 }]
    }, { plugins: { legend: { display: false } } });

    // Found locations
    makeChart('chart-found-locs', 'bar', {
      labels: data.found_locations.map(r => r.location || 'Unknown'),
      datasets: [{ label: 'Found reports', data: data.found_locations.map(r => r.count), backgroundColor: '#567C8D', borderRadius: 6, borderSkipped: false }]
    }, { indexAxis: 'y', plugins: { legend: { display: false } } });

    // Insights
    buildInsights(data);
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

function buildInsights(data) {
  const container = document.getElementById('insight-items');
  const insights = [];

  if (data.top_categories.length > 0) {
    const top = data.top_categories[0];
    insights.push(`<strong>${top.category}</strong> is the most commonly lost category with <strong>${top.count}</strong> report${top.count != 1 ? 's' : ''}.`);
  }

  if (data.hotspot_locations.length > 0) {
    const top = data.hotspot_locations[0];
    const pct = data.totals.total_lost > 0 ? Math.round((top.count / data.totals.total_lost) * 100) : 0;
    insights.push(`<strong>${pct}%</strong> of lost items were reported near <strong>${top.location}</strong> — the #1 hotspot on campus.`);
  }

  if (data.peak_days.length > 0) {
    const sorted = [...data.peak_days].sort((a,b) => b.count - a.count);
    insights.push(`<strong>${sorted[0].day_name}</strong> sees the most losses — consider extra awareness campaigns on this day.`);
  }

  if (data.totals.recovery_rate > 0) {
    insights.push(`Overall recovery rate is <strong>${data.totals.recovery_rate}%</strong> — ${data.totals.recovery_rate >= 50 ? 'great job, campus!' : 'there is room to improve reporting & claiming.'}`);
  }

  if (data.top_items.length > 0) {
    insights.push(`Most commonly lost item: <strong>"${data.top_items[0].item_name}"</strong> reported ${data.top_items[0].count} time${data.top_items[0].count != 1 ? 's' : ''}.`);
  }

  if (!insights.length) {
    container.innerHTML = '<div class="insight-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>Not enough data yet. Start reporting items to generate insights!</div>';
    return;
  }

  container.innerHTML = insights.map(text => `
    <div class="insight-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
      <span>${text}</span>
    </div>`).join('');
}

// ── SUBMIT LOST ──
function submitLost() {
  const name = document.getElementById('l-name').value.trim();
  if (!name) { document.getElementById('l-err').textContent = 'Item name is required.'; return; }
  document.getElementById('l-err').textContent = '';
  post('submit_lost', {
    item_name: name,
    category: document.getElementById('l-cat').value,
    color: document.getElementById('l-color').value,
    brand: document.getElementById('l-brand').value,
    date_lost: document.getElementById('l-date').value,
    location_lost: document.getElementById('l-loc').value,
    description: document.getElementById('l-desc').value,
  }).then(r => {
    if (r.success) {
      closeModal('modal-lost');
      ['l-name','l-color','l-brand','l-date','l-loc','l-desc'].forEach(id => document.getElementById(id).value = '');
      document.getElementById('l-cat').selectedIndex = 0;
      loadStats();
      loadLost('dash-lost-tbl', 3);
      loadLost('lost-tbl');
      showToast('Lost item reported successfully!');
    } else {
      document.getElementById('l-err').textContent = r.message || 'Error submitting report.';
    }
  });
}

// ── SUBMIT FOUND ──
function submitFound() {
  const name = document.getElementById('f-name').value.trim();
  if (!name) { document.getElementById('f-err').textContent = 'Item name is required.'; return; }
  document.getElementById('f-err').textContent = '';
  post('submit_found', {
    item_name: name,
    category: document.getElementById('f-cat').value,
    color: document.getElementById('f-color').value,
    brand: document.getElementById('f-brand').value,
    date_found: document.getElementById('f-date').value,
    location_found: document.getElementById('f-loc').value,
    description: document.getElementById('f-desc').value,
  }).then(r => {
    if (r.success) {
      closeModal('modal-found');
      ['f-name','f-color','f-brand','f-date','f-loc','f-desc'].forEach(id => document.getElementById(id).value = '');
      document.getElementById('f-cat').selectedIndex = 0;
      loadStats();
      showToast('Found item reported! Thank you.');
    } else {
      document.getElementById('f-err').textContent = r.message || 'Error submitting report.';
    }
  });
}

// ── CLAIM ──
let claimTargetId = null;
function openClaim(foundId, name) {
  claimTargetId = foundId;
  document.getElementById('claim-modal-title').textContent = 'Claim: ' + name;
  document.getElementById('claim-found-id').value = foundId;
  document.getElementById('claim-reason').value = '';
  document.getElementById('claim-err').textContent = '';
  openModal('modal-claim');
}
function submitClaim() {
  const reason = document.getElementById('claim-reason').value.trim();
  if (!reason) { document.getElementById('claim-err').textContent = 'Please provide a claim reason.'; return; }
  post('submit_claim', { found_id: claimTargetId, claim_reason: reason }).then(r => {
    if (r.success) {
      closeModal('modal-claim');
      loadClaims();
      loadStats();
      showToast('Claim submitted! Awaiting admin review.');
    } else {
      document.getElementById('claim-err').textContent = r.message || 'Error submitting claim.';
    }
  });
}

// ── MODALS ──
function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.overlay').forEach(o => o.addEventListener('click', function(e){ if(e.target===this) this.classList.remove('show'); }));

// ── HELPERS ──
function api(action) {
  return fetch(`${API}?action=${action}`).then(r => r.json());
}
function post(action, data) {
  const fd = new FormData();
  fd.append('action', action);
  Object.entries(data).forEach(([k,v]) => fd.append(k, v));
  return fetch(API, { method: 'POST', body: fd }).then(r => r.json());
}
function setText(id, val) { const el = document.getElementById(id); if(el) el.textContent = val; }
function esc(str) { return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function badge(status) {
  const map = { Open:'badge-open', Matched:'badge-matched', Claimed:'badge-claimed', Closed:'badge-closed',
    Available:'badge-available', Returned:'badge-returned',
    Pending:'badge-pending', Approved:'badge-approved', Rejected:'badge-rejected',
    Unread:'badge-unread', Read:'badge-read' };
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
  return Math.floor(diff/86400) + ' day' + (Math.floor(diff/86400) > 1 ? 's' : '') + ' ago';
}
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── INIT ──
loadPanel('dashboard');
</script>
</body>
</html>