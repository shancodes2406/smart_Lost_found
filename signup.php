<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CampusFind — Sign Up</title>
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

.headline {
  font-family: 'DM Serif Display', serif;
  font-size: 50px; line-height: 1.08;
  color: var(--white); letter-spacing: -1.8px;
  margin-bottom: 1rem;
}
.headline em { font-style: italic; color: var(--mist); }

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
  align-items: flex-start;
  justify-content: center;
  padding: 3rem 3.5rem 3rem 2.5rem;
  min-height: 100vh;
  overflow-y: auto;
}

.form-card { width: 100%; max-width: 415px; padding-top: 1rem; }

/* FIX: hardcoded colors so dark-mode CSS vars don't override */
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

.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.field { display: flex; flex-direction: column; gap: 5px; }
.field.full { grid-column: 1/-1; }

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

input, select {
  width: 100%; height: 42px;
  background: #FFFFFF !important;
  border: 1.5px solid var(--mist); border-radius: 10px;
  font-family: 'DM Sans', sans-serif; font-size: 14px;
  color: #2F4156 !important;
  padding: 0 12px 0 35px; outline: none;
  transition: border-color .2s, box-shadow .2s;
  appearance: none; -webkit-appearance: none;
}
input:focus, select:focus {
  border-color: var(--steel);
  box-shadow: 0 0 0 3px rgba(86,124,141,0.13);
}
input::placeholder { color: #b5c3ca; }
select {
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23567C8D' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
  background-repeat: no-repeat !important;
  background-position: right 12px center !important;
  padding-left: 12px;
}

.pw-tog {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; color: var(--steel);
  display: flex; padding: 2px; transition: color .2s;
}
.pw-tog:hover { color: var(--navy); }
.pw-tog svg { width: 15px; height: 15px; }

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

.str-bars { display: flex; gap: 4px; margin-top: 5px; }
.sb { flex: 1; height: 3px; border-radius: 999px; background: var(--mist); transition: background .3s; }
.str-lbl { font-size: 11px; color: var(--steel); margin-top: 2px; min-height: 15px; }

.divider { grid-column: 1/-1; display: flex; align-items: center; gap: 10px; margin: 2px 0; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--mist); }
.divider span { font-size: 10px; color: #b8c5cc; text-transform: uppercase; letter-spacing: 0.06em; }

.terms-row { display: flex; align-items: flex-start; gap: 10px; grid-column: 1/-1; margin-top: 2px; }
.terms-row input[type=checkbox] {
  width: 17px; height: 17px; flex-shrink: 0;
  border-radius: 5px; border: 1.5px solid var(--mist);
  appearance: none; -webkit-appearance: none;
  background: #FFFFFF; cursor: pointer;
  padding: 0; margin-top: 2px; position: relative;
  transition: all .15s;
}
.terms-row input[type=checkbox]:checked { background: var(--navy); border-color: var(--navy); }
.terms-row input[type=checkbox]:checked::after {
  content: ''; position: absolute;
  width: 9px; height: 5px;
  border-left: 2px solid white; border-bottom: 2px solid white;
  transform: rotate(-45deg) translate(1px,-1px);
}
.terms-text { font-size: 12px; color: #567C8D !important; line-height: 1.6; }
.terms-text a { color: #2F4156 !important; font-weight: 500; text-decoration: none; border-bottom: 1px solid var(--mist); }

.submit-btn {
  width: 100%; height: 46px;
  background: var(--navy); color: #FFFFFF;
  border: none; border-radius: 12px;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px; font-weight: 500; cursor: pointer;
  grid-column: 1/-1;
  transition: background .2s, transform .1s;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.submit-btn:hover { background: var(--navy2); }
.submit-btn:active { transform: scale(0.99); }
.submit-btn .arr { width: 17px; height: 17px; transition: transform .2s; }
.submit-btn:hover .arr { transform: translateX(3px); }

.err { font-size: 11px; color: var(--err); min-height: 14px; margin-top: 2px; }

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

@media(max-width:820px){
  body { flex-direction: column; background: #F5EFEB; }
  .left { width: 100%; min-height: auto; padding: 2rem 1.5rem 1.5rem; }
  .headline { font-size: 36px; }
  .desc { display: none; }
  .features { flex-direction: row; flex-wrap: wrap; gap: 8px; }
  .feat-sub { display: none; }
  .left-footer { display: none; }
  .right { padding: 2rem 1.5rem; min-height: auto; }
  .grid { grid-template-columns: 1fr; }
  .field.full { grid-column: 1; }
}
</style>
</head>
<body>

<div class="toast" id="toast">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
  Account created! Welcome aboard.
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

    <div class="headline">Lost something?<br><em>We'll find it.</em></div>

    <div class="tagline">
      <span class="tagline-dot"></span>
      Turning Campus Hide-and-Seek into Easy Finds
    </div>

    <p class="desc">A smarter way to reunite students and staff with their belongings — powered by AI matching and real-time alerts.</p>

    <div class="features">
      <div class="feat">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#C8D9E6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <div>
          <div class="feat-title">Verified campus community</div>
          <div class="feat-sub">Students, staff &amp; admins only</div>
        </div>
      </div>
      <div class="feat">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#C8D9E6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <div>
          <div class="feat-title">Instant match notifications</div>
          <div class="feat-sub">AI-powered similarity scoring</div>
        </div>
      </div>
      <div class="feat">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#C8D9E6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <div>
          <div class="feat-title">Fast claims &amp; admin review</div>
          <div class="feat-sub">Approve, reject, track — all in one</div>
        </div>
      </div>
    </div>

    <div class="left-footer">© 2025 CampusFind. All rights reserved.</div>
  </div>
</div>

<!-- RIGHT -->
<div class="right">
  <div class="form-card">
    <div class="fh">
      <h1>Create account</h1>
      <p>Already have an account? <a href="signin.php">Sign In</a></p>
    </div>


    <form id="signupForm" novalidate>
      <div class="grid">

        <div class="field">
          <label for="full_name">Full name</label>
          <div class="iw">
            <input type="text" id="full_name" name="full_name" placeholder="e.g. Aisha Patel" autocomplete="name"/>
            <svg class="fi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div class="err" id="e-full_name"></div>
        </div>

        <div class="field">
          <label for="phone">Phone (optional)</label>
          <div class="iw">
            <input type="tel" id="phone" name="phone" placeholder="+91 98765 43210" autocomplete="tel"/>
            <svg class="fi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.64 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.54 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div class="err" id="e-phone"></div>
        </div>

        <div class="field full">
          <label for="email">Campus email</label>
          <div class="iw">
            <input type="email" id="email" name="email" placeholder="you@campus.edu" autocomplete="email"/>
            <svg class="fi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div class="err" id="e-email"></div>
        </div>

        <div class="field full">
          <label>I am a</label>
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
          <div class="err" id="e-role"></div>
        </div>

        <div class="field full">
          <label for="department">Department</label>
          <div class="iw">
            <select id="department" name="department">
              <option value="" disabled selected>Select your department</option>
              <option>Computer Science</option>
              <option>Engineering</option>
              <option>Business Administration</option>
              <option>Arts &amp; Humanities</option>
              <option>Science &amp; Mathematics</option>
              <option>Medicine &amp; Health Sciences</option>
              <option>Law</option>
              <option>Architecture</option>
              <option>Administration / Staff</option>
              <option>Other</option>
            </select>
          </div>
          <div class="err" id="e-department"></div>
        </div>

        <div class="divider"><span>security</span></div>

        <div class="field full">
          <label for="password">Password</label>
          <div class="iw">
            <input type="password" id="password" name="password" placeholder="Min. 8 characters" autocomplete="new-password" oninput="checkStrength(this.value)"/>
            <svg class="fi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <button type="button" class="pw-tog" onclick="togglePw('password',this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="str-bars"><div class="sb" id="sb1"></div><div class="sb" id="sb2"></div><div class="sb" id="sb3"></div><div class="sb" id="sb4"></div></div>
          <div class="str-lbl" id="strLbl"></div>
          <div class="err" id="e-password"></div>
        </div>

        <div class="field full">
          <label for="cpw">Confirm password</label>
          <div class="iw">
            <input type="password" id="cpw" name="confirm_password" placeholder="Repeat your password" autocomplete="new-password"/>
            <svg class="fi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            <button type="button" class="pw-tog" onclick="togglePw('cpw',this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="err" id="e-cpw"></div>
        </div>

        <div class="terms-row">
          <input type="checkbox" id="terms" name="terms"/>
          <label for="terms" class="terms-text" style="text-transform:none;letter-spacing:normal;font-weight:400;">
            I agree to the <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a>. My data is used only to operate this campus service.
          </label>
        </div>
        <div class="err" id="e-terms" style="grid-column:1/-1;margin-top:-4px;"></div>

        <button type="submit" class="submit-btn">
          Create account
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
function checkStrength(v) {
  const bars = ['sb1','sb2','sb3','sb4'].map(id => document.getElementById(id));
  const lbl = document.getElementById('strLbl');
  const C = {1:'#E24B4A',2:'#EF9F27',3:'#5DCAA5',4:'#1D9E75'};
  const L = {1:'Weak',2:'Fair',3:'Good',4:'Strong'};
  let s = 0;
  if(v.length>=8) s++;
  if(/[A-Z]/.test(v)) s++;
  if(/[0-9]/.test(v)) s++;
  if(/[^A-Za-z0-9]/.test(v)) s++;
  bars.forEach((b,i) => b.style.background = i<s ? C[s] : 'var(--mist)');
  lbl.textContent = v.length>0 ? (L[s]||'') : '';
  lbl.style.color = v.length>0 ? C[s] : 'var(--steel)';
}
function setErr(id, msg) { document.getElementById('e-'+id).textContent = msg; }
function clearErrs() { document.querySelectorAll('.err').forEach(e => e.textContent = ''); }
function validate() {
  clearErrs(); let ok = true;
  const n  = document.getElementById('full_name').value.trim();
  const em = document.getElementById('email').value.trim();
  const d  = document.getElementById('department').value;
  const pw = document.getElementById('password').value;
  const cp = document.getElementById('cpw').value;
  const t  = document.getElementById('terms').checked;
  if(!n||n.length<2)   { setErr('full_name','Please enter your full name.'); ok=false; }
  if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { setErr('email','Enter a valid email address.'); ok=false; }
  if(!d)               { setErr('department','Please select your department.'); ok=false; }
  if(pw.length<8)      { setErr('password','Password must be at least 8 characters.'); ok=false; }
  if(pw!==cp)          { setErr('cpw','Passwords do not match.'); ok=false; }
  if(!t)               { setErr('terms','You must accept the terms to continue.'); ok=false; }
  return ok;
}
document.getElementById('signupForm').addEventListener('submit', function(e) {
  e.preventDefault();
  if (!validate()) return;
 
  const formData = new FormData();
  formData.append('full_name',  document.getElementById('full_name').value.trim());
  formData.append('email',      document.getElementById('email').value.trim());
  formData.append('password',   document.getElementById('password').value);
  formData.append('phone',      document.getElementById('phone').value.trim());
  formData.append('role',       role);
  formData.append('department', document.getElementById('department').value);
 
  fetch('register.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const toast = document.getElementById('toast');
        toast.classList.add('show');
        setTimeout(() => { window.location.href = 'signin.php'; }, 2000);
        setTimeout(() => toast.classList.remove('show'), 3500);
      } else {
        // Show error under email field
        document.getElementById('e-email').textContent = data.message || 'Registration failed.';
      }
    })
    .catch(() => {
      document.getElementById('e-email').textContent = 'Network error. Please try again.';
    });
});


</script>
</body>
</html>