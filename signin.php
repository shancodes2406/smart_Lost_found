<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CampusFind — Sign In</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet"/>
<style>
:root {
  --navy:  #2F4156;
  --steel: #567C8D;
  --mist:  #C8D9E6;
  --cream: #F5EFEB;
  --white: #FFFFFF;
  --navy2: #3d5470;
  --err:   #c0392b;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { height: 100%; }
body {
  font-family: 'DM Sans', sans-serif;
  display: flex;
  min-height: 100vh;
  height: 100%;
  background: var(--navy);
}
 
/* ── LEFT ── */
.left {
  width: 44%;
  flex-shrink: 0;
  background: var(--navy);
  position: relative;
  display: flex;
  flex-direction: column;
  padding: 2.8rem 2.8rem 2.2rem;
  overflow: hidden;
  min-height: 100vh;
}
.left::before {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(200,217,230,0.14) 1px, transparent 1px);
  background-size: 26px 26px;
  z-index: 0;
}
.orb-tr {
  position: absolute; top: -110px; right: -110px;
  width: 370px; height: 370px; border-radius: 50%;
  background: var(--steel); opacity: 0.22; z-index: 0;
}
.orb-bl {
  position: absolute; bottom: -90px; left: -90px;
  width: 320px; height: 320px; border-radius: 50%;
  border: 44px solid rgba(200,217,230,0.09); z-index: 0;
}
.hatch {
  position: absolute; bottom: 130px; right: -20px;
  width: 160px; height: 200px;
  background: repeating-linear-gradient(
    -45deg,
    rgba(200,217,230,0.07) 0, rgba(200,217,230,0.07) 1px,
    transparent 1px, transparent 9px
  );
  z-index: 0;
}
.ring-mid {
  position: absolute; top: 42%; left: -60px;
  width: 180px; height: 180px; border-radius: 50%;
  border: 28px solid rgba(86,124,141,0.15); z-index: 0;
}
.left-inner {
  position: relative; z-index: 1;
  display: flex; flex-direction: column;
  height: 100%; flex: 1;
}
 
/* brand */
.brand { display: flex; align-items: center; gap: 11px; margin-bottom: 2.4rem; }
.brand-icon {
  width: 40px; height: 40px; border-radius: 11px;
  background: var(--steel);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.brand-icon svg { width: 20px; height: 20px; }
.brand-name { font-family: 'DM Serif Display', serif; font-size: 22px; color: var(--white); line-height: 1; letter-spacing: -0.3px; }
.brand-name em { font-style: italic; color: var(--mist); }
.brand-sub { font-size: 10px; font-weight: 300; color: rgba(200,217,230,0.45); letter-spacing: 0.1em; text-transform: uppercase; margin-top: 3px; }
 
/* headline */
.headline {
  font-family: 'DM Serif Display', serif;
  font-size: 50px; line-height: 1.08;
  color: var(--white); letter-spacing: -1.8px;
  margin-bottom: 1rem;
}
.headline em { font-style: italic; color: var(--mist); }
 
/* tagline pill */
.tagline {
  display: inline-flex; align-items: center; gap: 7px;
  background: rgba(200,217,230,0.13);
  border: 1px solid rgba(200,217,230,0.22);
  border-radius: 999px;
  padding: 6px 14px;
  font-size: 12px; font-weight: 400; color: var(--mist);
  letter-spacing: 0.02em;
  margin-bottom: 1.5rem;
  width: fit-content;
}
.tagline-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--mist); opacity: 0.6; flex-shrink: 0;
}
 
.desc {
  font-size: 15px; font-weight: 300;
  color: rgba(200,217,230,0.75);
  line-height: 1.8; max-width: 305px;
  margin-bottom: 1.8rem;
}
 
/* stats row */
.stats { display: flex; gap: 0; margin-bottom: 2rem; }
.stat {
  flex: 1;
  padding: 14px 16px;
  background: rgba(255,255,255,0.045);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 0;
}
.stat:first-child { border-radius: 12px 0 0 12px; }
.stat:last-child  { border-radius: 0 12px 12px 0; border-left: none; }
.stat-num { font-family: 'DM Serif Display', serif; font-size: 24px; color: var(--white); line-height: 1; }
.stat-lbl { font-size: 11px; font-weight: 300; color: rgba(200,217,230,0.5); margin-top: 3px; }
 
/* features */
.features { display: flex; flex-direction: column; gap: 10px; }
.feat {
  display: flex; align-items: center; gap: 13px;
  background: rgba(255,255,255,0.045);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
  padding: 12px 15px;
}
.feat-icon {
  width: 34px; height: 34px; border-radius: 9px;
  background: rgba(86,124,141,0.3);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.feat-icon svg { width: 16px; height: 16px; }
.feat-title { font-size: 13px; font-weight: 500; color: var(--white); }
.feat-sub   { font-size: 11px; font-weight: 300; color: rgba(200,217,230,0.55); margin-top: 1px; }
 
.left-footer {
  margin-top: auto; padding-top: 2rem;
  font-size: 11px; color: rgba(200,217,230,0.28); letter-spacing: 0.03em;
}
 
/* ── RIGHT ── */
.right {
  flex: 1;
  background: #F5EFEB;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem 3.5rem 3rem 2.5rem;
  min-height: 100vh;
  overflow-y: auto;
}
 
.form-card { width: 100%; max-width: 415px; }
 
.fh { margin-bottom: 2rem; }
.fh h1 {
  font-family: 'DM Serif Display', serif;
  font-size: 36px;
  color: #2F4156 !important;
  letter-spacing: -0.5px;
  line-height: 1.15;
  margin-bottom: 6px;
}
.fh p {
  font-size: 12px;
  color: #567C8D !important;
  margin-top: 4px;
  font-weight: 300;
}
.fh p a {
  color: #2F4156 !important;
  font-weight: 500;
  text-decoration: none;
  border-bottom: 1px solid #C8D9E6;
  transition: border-color .2s;
}
.fh p a:hover { border-color: #567C8D; }
 
.grid { display: grid; grid-template-columns: 1fr; gap: 14px; }
.field { display: flex; flex-direction: column; gap: 5px; }
 
label {
  font-size: 11px; font-weight: 500;
  color: #2F4156 !important;
  letter-spacing: 0.06em; text-transform: uppercase;
}
 
.iw { position: relative; }
.iw svg.fi {
  position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
  width: 15px; height: 15px; color: var(--steel); pointer-events: none;
}
 
input {
  width: 100%; height: 42px;
  background: #FFFFFF !important;
  border: 1.5px solid var(--mist); border-radius: 10px;
  font-family: 'DM Sans', sans-serif; font-size: 14px;
  color: #2F4156 !important;
  padding: 0 12px 0 35px; outline: none;
  transition: border-color .2s, box-shadow .2s;
}
input:focus {
  border-color: var(--steel);
  box-shadow: 0 0 0 3px rgba(86,124,141,0.13);
}
input::placeholder { color: #b5c3ca; }
 
.pw-tog {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; color: var(--steel);
  display: flex; padding: 2px; transition: color .2s;
}
.pw-tog:hover { color: var(--navy); }
.pw-tog svg { width: 15px; height: 15px; }
 
/* role tabs */
.role-tabs { display: flex; gap: 6px; background: #FFFFFF; border: 1.5px solid var(--mist); border-radius: 10px; padding: 4px; }
.rtab {
  flex: 1; height: 34px; border: none; border-radius: 7px;
  background: transparent; font-family: 'DM Sans', sans-serif;
  font-size: 13px; font-weight: 400; color: var(--steel);
  cursor: pointer; transition: all .18s;
  display: flex; align-items: center; justify-content: center; gap: 5px;
}
.rtab svg { width: 13px; height: 13px; }
.rtab.active { background: var(--navy); color: #FFFFFF; font-weight: 500; }
.rtab:hover:not(.active) { background: var(--mist); color: var(--navy); }
 
/* forgot password link */
.forgot-row {
  display: flex; justify-content: flex-end;
  margin-top: -6px;
}
.forgot-row a {
  font-size: 12px; color: #567C8D !important;
  text-decoration: none; border-bottom: 1px solid transparent;
  transition: border-color .2s; font-weight: 400;
}
.forgot-row a:hover { border-color: #567C8D; }
 
/* divider */
.divider { display: flex; align-items: center; gap: 10px; margin: 4px 0; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--mist); }
.divider span { font-size: 10px; color: #b8c5cc; text-transform: uppercase; letter-spacing: 0.06em; }
 
/* remember me */
.remember-row { display: flex; align-items: center; gap: 10px; }
.remember-row input[type=checkbox] {
  width: 17px; height: 17px; flex-shrink: 0;
  border-radius: 5px; border: 1.5px solid var(--mist);
  appearance: none; -webkit-appearance: none;
  background: #FFFFFF; cursor: pointer;
  padding: 0; position: relative;
  transition: all .15s;
}
.remember-row input[type=checkbox]:checked { background: var(--navy); border-color: var(--navy); }
.remember-row input[type=checkbox]:checked::after {
  content: ''; position: absolute;
  width: 9px; height: 5px;
  border-left: 2px solid white; border-bottom: 2px solid white;
  transform: rotate(-45deg) translate(1px,-1px);
}
.remember-label {
  font-size: 12px; color: #567C8D !important;
  font-weight: 300; cursor: pointer;
  text-transform: none; letter-spacing: normal;
}
 
/* submit */
.submit-btn {
  width: 100%; height: 46px;
  background: var(--navy); color: #FFFFFF;
  border: none; border-radius: 12px;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px; font-weight: 500; cursor: pointer;
  transition: background .2s, transform .1s;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  margin-top: 4px;
}
.submit-btn:hover { background: var(--navy2); }
.submit-btn:active { transform: scale(0.99); }
.submit-btn .arr { width: 17px; height: 17px; transition: transform .2s; }
.submit-btn:hover .arr { transform: translateX(3px); }
 
.err { font-size: 11px; color: var(--err); min-height: 14px; margin-top: 2px; }
 
/* welcome back badge */
.welcome-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(47,65,86,0.07);
  border: 1px solid rgba(47,65,86,0.12);
  border-radius: 999px;
  padding: 5px 12px 5px 7px;
  margin-bottom: 1.4rem;
  width: fit-content;
}
.badge-dot {
  width: 22px; height: 22px; border-radius: 50%;
  background: var(--navy);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.badge-dot svg { width: 11px; height: 11px; }
.badge-text { font-size: 12px; color: #2F4156 !important; font-weight: 400; }
 
/* toast */
.toast {
  position: fixed; top: 1.4rem; right: 1.4rem;
  background: var(--navy); color: #FFFFFF;
  padding: 13px 18px; border-radius: 12px;
  font-size: 14px; display: flex; align-items: center; gap: 10px;
  transform: translateY(-80px); opacity: 0;
  transition: all .4s cubic-bezier(.4,0,.2,1); z-index: 999;
  box-shadow: 0 8px 32px rgba(47,65,86,0.25);
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast svg { color: #5DCAA5; width: 17px; height: 17px; flex-shrink: 0; }
 
/* error toast */
.toast.error svg { color: #E24B4A; }
 
@media(max-width:820px){
  body { flex-direction: column; background: #F5EFEB; }
  .left { width: 100%; min-height: auto; padding: 2rem 1.5rem 1.5rem; }
  .headline { font-size: 36px; }
  .desc { display: none; }
  .stats { display: none; }
  .features { flex-direction: row; flex-wrap: wrap; gap: 8px; }
  .feat-sub { display: none; }
  .left-footer { display: none; }
  .right { padding: 2rem 1.5rem; min-height: auto; }
}
</style>
</head>
<body>
 
<div class="toast" id="toast">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toast-msg">Welcome back! Redirecting…</span>
</div>
 
<!-- LEFT -->
<div class="left">
  <div class="orb-tr"></div>
  <div class="orb-bl"></div>
  <div class="hatch"></div>
  <div class="ring-mid"></div>
 
  <div class="left-inner">
 
    <div class="brand">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </div>
      <div>
        <div class="brand-name">Campus<em>Find</em></div>
        <div class="brand-sub">Smart Lost &amp; Found System</div>
      </div>
    </div>
 
    <div class="headline">Welcome<br><em>back.</em></div>
 
    <div class="tagline">
      <span class="tagline-dot"></span>
      Your belongings are waiting for you
    </div>
 
    <p class="desc">Sign in to report a lost item, claim a found one, or check the status of your matches and notifications.</p>
 
    <div class="stats">
      <div class="stat">
        <div class="stat-num">1,240+</div>
        <div class="stat-lbl">Items reunited</div>
      </div>
      <div class="stat">
        <div class="stat-num">94%</div>
        <div class="stat-lbl">Match accuracy</div>
      </div>
      <div class="stat">
        <div class="stat-num">&lt;48h</div>
        <div class="stat-lbl">Avg. recovery time</div>
      </div>
    </div>
 
    <div class="features">
      <div class="feat">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#C8D9E6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>
          </svg>
        </div>
        <div>
          <div class="feat-title">View your open reports</div>
          <div class="feat-sub">Track lost &amp; found submissions</div>
        </div>
      </div>
      <div class="feat">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#C8D9E6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/>
          </svg>
        </div>
        <div>
          <div class="feat-title">AI match results</div>
          <div class="feat-sub">Similarity scores updated in real-time</div>
        </div>
      </div>
      <div class="feat">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#C8D9E6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
        </div>
        <div>
          <div class="feat-title">Unread notifications</div>
          <div class="feat-sub">Claims, matches &amp; admin updates</div>
        </div>
      </div>
    </div>
 
    <div class="left-footer">© 2025 CampusFind. All rights reserved.</div>
  </div>
</div>
 
<!-- RIGHT -->
<div class="right">
  <div class="form-card">
 
    <div class="welcome-badge">
      <div class="badge-dot">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <span class="badge-text">Campus community sign-in</span>
    </div>
 
    <div class="fh">
      <h1>Sign in</h1>
      <p>New here? <a href="signup.php">Create an account</a>
    </div>
 
    <form id="loginForm" novalidate>
      <div class="grid">
 
        <!-- role selector — maps to users.role ENUM -->
        <div class="field">
          <label>Sign in as</label>
          <div class="role-tabs" role="group">
            <button type="button" class="rtab active" data-role="student" onclick="selectRole(this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
              Student
            </button>
            <button type="button" class="rtab" data-role="staff" onclick="selectRole(this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
              Staff
            </button>
            <button type="button" class="rtab" data-role="admin" onclick="selectRole(this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Admin
            </button>
          </div>
          <input type="hidden" id="role" name="role" value="student"/>
        </div>
 
        <div class="divider"><span>credentials</span></div>
 
        <!-- maps to users.email -->
        <div class="field">
          <label for="email">Campus email</label>
          <div class="iw">
            <input type="email" id="email" name="email" placeholder="you@campus.edu" autocomplete="email"/>
            <svg class="fi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div class="err" id="e-email"></div>
        </div>
 
        <!-- maps to users.password -->
        <div class="field">
          <label for="password">Password</label>
          <div class="iw">
            <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password"/>
            <svg class="fi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <button type="button" class="pw-tog" onclick="togglePw('password',this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="err" id="e-password"></div>
        </div>
 
        <!-- bottom row: remember me + forgot password -->
        <div style="display:flex; align-items:center; justify-content:space-between;">
          <div class="remember-row">
            <input type="checkbox" id="remember" name="remember"/>
            <label for="remember" class="remember-label" style="color:#567C8D !important;">Keep me signed in</label>
          </div>
          <div class="forgot-row">
            <a href="#">Forgot password?</a>
          </div>
        </div>
 
        <button type="submit" class="submit-btn">
          Sign in
          <svg class="arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
 
      </div>
    </form>
  </div>
</div>
 
<script>
let role = 'student';
 
function selectRole(btn) {
  document.querySelectorAll('.rtab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  role = btn.dataset.role;
  document.getElementById('role').value = role;
}
 
function togglePw(id, btn) {
  const inp = document.getElementById(id);
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  btn.querySelector('svg').innerHTML = show
    ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
    : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}
 
function setErr(id, msg) { document.getElementById('e-'+id).textContent = msg; }
function clearErrs() { document.querySelectorAll('.err').forEach(e => e.textContent = ''); }
 
function showToast(msg, isError) {
  const toast = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  toast.classList.toggle('error', !!isError);
  toast.querySelector('svg').innerHTML = isError
    ? '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'
    : '<path d="M20 6L9 17l-5-5"/>';
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);
}
 
function validate() {
  clearErrs(); let ok = true;
  const em = document.getElementById('email').value.trim();
  const pw = document.getElementById('password').value;
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { setErr('email', 'Enter a valid email address.'); ok = false; }
  if (pw.length < 1) { setErr('password', 'Please enter your password.'); ok = false; }
  return ok;
}
 
document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
 
  if (!validate()) return;
 
  const formData = new FormData();
  formData.append("email",    document.getElementById("email").value.trim());
  formData.append("password", document.getElementById("password").value);
  formData.append("role",     role);
 
  fetch("login.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    console.log(data);
 
    if (data.trim() === "success") {
      showToast("Welcome back! Redirecting…", false);
 
      setTimeout(() => {
        // Route based on selected role
        if (role === 'admin') {
          window.location.href = "admin_dashboard.php";
        } else {
          window.location.href = "dashboard.php";
        }
      }, 1000);
 
    } else {
      showToast(data, true);
    }
  })
  .catch(error => {
    console.log(error);
    showToast("Login failed. Try again.", true);
  });
});
</script>
</body>
</html>