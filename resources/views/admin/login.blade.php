<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — KurbanQu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#0B0907;
    --card-bg-1:#1D160C;
    --card-bg-2:#120E09;
    --card-border:rgba(217,169,62,0.18);
    --gold:#D9A93E;
    --gold-soft:#F2D27E;
    --gold-deep:#B8860B;
    --text-light:#F4EFE2;
    --text-muted:#A89C87;
    --input-bg:#171109;
    --input-border:rgba(255,255,255,0.08);
    --placeholder:#6E6555;
    --text-dark:#241B0E;
    --error:#E0735C;
  }

  *{box-sizing:border-box;margin:0;padding:0;}

  body{
    min-height:100vh;
    font-family:'Inter',sans-serif;
    background:
      radial-gradient(ellipse 640px 420px at 50% 0%, rgba(217,169,62,0.12), transparent 70%),
      var(--bg);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:48px 20px;
    position:relative;
    overflow-x:hidden;
  }

  /* motif geometris halus terinspirasi bintang 8 sudut */
  body::before{
    content:'';
    position:fixed;
    inset:0;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 40 40'%3E%3Cpath d='M20 4 L24 16 L36 12 L26 20 L36 28 L24 24 L20 36 L16 24 L4 28 L14 20 L4 12 L16 16 Z' fill='none' stroke='%23D9A93E' stroke-width='0.6'/%3E%3C/svg%3E");
    background-size:80px 80px;
    opacity:0.06;
    mask-image:radial-gradient(circle at 50% 30%, transparent 15%, black 75%);
    -webkit-mask-image:radial-gradient(circle at 50% 30%, transparent 15%, black 75%);
    pointer-events:none;
    z-index:0;
  }

  .wrapper{
    width:100%;
    max-width:400px;
    position:relative;
    z-index:1;
    display:flex;
    flex-direction:column;
    align-items:center;
  }

  .brand{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-bottom:30px;
    animation:fadeUp .6s ease-out both;
  }

  .logo-badge{
    width:64px;
    height:64px;
    border-radius:18px;
    background:linear-gradient(135deg, var(--gold-soft), var(--gold) 70%);
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 12px 30px -8px rgba(217,169,62,0.5);
  }
  .logo-badge svg{width:30px;height:30px;}

  .brand-name{
    font-family:'Poppins',sans-serif;
    font-weight:700;
    font-size:23px;
    color:var(--text-light);
    margin-top:14px;
    letter-spacing:-0.3px;
  }
  .brand-tag{
    font-size:12px;
    color:var(--text-muted);
    margin-top:3px;
    text-align:center;
  }

  .card{
    width:100%;
    background:linear-gradient(160deg, var(--card-bg-1), var(--card-bg-2));
    border:1px solid var(--card-border);
    border-radius:20px;
    padding:36px 32px 32px;
    box-shadow:0 30px 80px -20px rgba(0,0,0,0.65), 0 20px 50px -10px rgba(217,169,62,0.07);
    animation:fadeUp .6s ease-out .1s both;
  }

  h1{
    font-family:'Poppins',sans-serif;
    font-weight:600;
    font-size:24px;
    color:var(--text-light);
    text-align:center;
  }

  .subtitle{
    font-size:14px;
    color:var(--text-muted);
    text-align:center;
    line-height:1.5;
    margin-top:8px;
    margin-bottom:26px;
  }

  .field{margin-bottom:16px;}

  .sr-only{
    position:absolute;width:1px;height:1px;padding:0;margin:-1px;
    overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;
  }

  .input-wrap{
    display:flex;
    align-items:center;
    gap:10px;
    background:var(--input-bg);
    border:1px solid var(--input-border);
    border-radius:12px;
    padding:13px 16px;
    transition:border-color .2s, box-shadow .2s;
  }

  .input-wrap:focus-within{
    border-color:var(--gold);
    box-shadow:0 0 0 3px rgba(217,169,62,0.16);
  }

  .input-wrap svg{flex-shrink:0;color:var(--gold);}

  .input-wrap input{
    flex:1;
    border:none;
    background:transparent;
    outline:none;
    font-family:'Inter',sans-serif;
    font-size:14px;
    color:var(--text-light);
    min-width:0;
  }

  .input-wrap input::placeholder{color:var(--placeholder);}

  .toggle-pass{
    cursor:pointer;
    color:var(--placeholder);
    display:flex;
    background:none;
    border:none;
    padding:0;
  }
  .toggle-pass:hover{color:var(--gold);}

  .error-msg{
    font-size:12px;
    color:var(--error);
    margin-top:6px;
    display:none;
  }
  .error-msg.show{display:block;}

  button.submit{
    width:100%;
    margin-top:8px;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg, var(--gold-soft), var(--gold) 70%);
    color:var(--text-dark);
    font-family:'Inter',sans-serif;
    font-weight:700;
    font-size:15px;
    letter-spacing:.2px;
    cursor:pointer;
    box-shadow:0 12px 24px -8px rgba(184,134,11,0.5);
    transition:transform .15s, box-shadow .15s;
  }
  button.submit:hover{transform:translateY(-1px);box-shadow:0 16px 28px -8px rgba(184,134,11,0.6);}
  button.submit:active{transform:translateY(0);}
  button.submit:focus-visible{outline:3px solid var(--gold);outline-offset:2px;}

  .divider{
    display:flex;
    align-items:center;
    gap:12px;
    margin:22px 0;
  }
  .divider::before, .divider::after{
    content:'';
    flex:1;
    height:1px;
    background:var(--input-border);
  }
  .divider span{font-size:12px;color:var(--text-muted);white-space:nowrap;}

  .secondary{
    display:block;
    width:100%;
    text-align:center;
    padding:13px;
    border-radius:12px;
    border:1.5px solid var(--gold);
    background:transparent;
    color:var(--gold);
    font-family:'Inter',sans-serif;
    font-weight:600;
    font-size:14px;
    text-decoration:none;
    cursor:pointer;
    transition:background .2s, color .2s;
  }
  .secondary:hover{background:rgba(217,169,62,0.1);}
  .secondary:focus-visible{outline:2px solid var(--gold);outline-offset:2px;}

  .footer-link{
    margin-top:24px;
    font-size:13px;
    color:var(--text-muted);
    text-align:center;
  }
  .footer-link a{
    color:var(--gold);
    font-weight:600;
    text-decoration:none;
  }
  .footer-link a:hover{text-decoration:underline;}
  .footer-link a:focus-visible{outline:2px solid var(--gold);outline-offset:2px;border-radius:2px;}

  .hidden{display:none;}
  .form-section{animation:fadeUp .4s ease-out both;}

  @keyframes fadeUp{
    from{opacity:0;transform:translateY(14px);}
    to{opacity:1;transform:none;}
  }

  @media (prefers-reduced-motion: reduce){
    *{animation:none !important;transition:none !important;}
  }

  @media (max-width:380px){
    .card{padding:30px 22px 26px;}
    h1{font-size:21px;}
  }
</style>
</head>
<body>

  <div class="wrapper">

    <div class="brand">
      <div class="logo-badge">
        <svg viewBox="0 0 24 24" fill="#241B0E" aria-hidden="true">
          <ellipse cx="12" cy="15.2" rx="5" ry="4"/>
          <ellipse cx="5" cy="9" rx="2" ry="2.6" transform="rotate(-15 5 9)"/>
          <ellipse cx="9" cy="6" rx="2" ry="2.6" transform="rotate(-5 9 6)"/>
          <ellipse cx="15" cy="6" rx="2" ry="2.6" transform="rotate(5 15 6)"/>
          <ellipse cx="19" cy="9" rx="2" ry="2.6" transform="rotate(15 19 9)"/>
        </svg>
      </div>
      <div class="brand-name">KurbanQu</div>
      <div class="brand-tag">Sistem Distribusi Kurban Berbasis QR</div>
    </div>

    <div class="card">
      <h1 id="formTitle">Selamat Datang Kembali</h1>
      <p class="subtitle" id="formSubtitle">Masukkan kredensial Anda untuk mengakses akun.</p>

      <!-- FORM LOGIN -->
      <form id="loginForm" class="form-section" novalidate>

        <div class="field">
          <label class="sr-only" for="username">Username</label>
          <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="8" r="4"/>
              <path d="M4 20c0-4.4 3.6-7 8-7s8 2.6 8 7"/>
            </svg>
            <input id="username" type="text" placeholder="Masukkan username Anda" autocomplete="username" required>
          </div>
        </div>

        <div class="field">
          <label class="sr-only" for="password">Password</label>
          <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="5" y="11" width="14" height="9" rx="2"/>
              <path d="M8 11V8a4 4 0 0 1 8 0v3"/>
            </svg>
            <input id="password" type="password" placeholder="Masukkan password Anda" autocomplete="current-password" required>
            <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="submit">Masuk</button>
      </form>

      <!-- FORM DAFTAR -->
      <form id="registerForm" class="form-section hidden" novalidate>

        <div class="field">
          <label class="sr-only" for="regUsername">Buat Username</label>
          <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="8" r="4"/>
              <path d="M4 20c0-4.4 3.6-7 8-7s8 2.6 8 7"/>
            </svg>
            <input id="regUsername" type="text" placeholder="Buat username" autocomplete="username" required>
          </div>
        </div>

        <div class="field">
          <label class="sr-only" for="regPassword">Buat Password</label>
          <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="5" y="11" width="14" height="9" rx="2"/>
              <path d="M8 11V8a4 4 0 0 1 8 0v3"/>
            </svg>
            <input id="regPassword" type="password" placeholder="Buat password" autocomplete="new-password" minlength="8" required>
            <button type="button" class="toggle-pass" id="toggleRegPass" aria-label="Tampilkan password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label class="sr-only" for="regConfirm">Konfirmasi Password</label>
          <div class="input-wrap" id="confirmWrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="5" y="11" width="14" height="9" rx="2"/>
              <path d="M8 11V8a4 4 0 0 1 8 0v3"/>
            </svg>
            <input id="regConfirm" type="password" placeholder="Konfirmasi password" autocomplete="new-password" required>
            <button type="button" class="toggle-pass" id="toggleRegConfirm" aria-label="Tampilkan password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
          <p class="error-msg" id="confirmError">Password tidak cocok. Coba lagi.</p>
        </div>

        <button type="submit" class="submit">Daftar</button>
      </form>

      <div class="divider"><span>atau</span></div>

      <a href="#" class="secondary" id="toggleModeBtn">Daftar Akun Baru</a>
    </div>

    <p class="footer-link" id="footerLink">Lupa password Anda? <a href="#">Reset Password</a></p>

  </div>

<script>
  const eyeIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>`;
  const eyeOffIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a18.5 18.5 0 0 1 4.22-5.06M9.9 4.24A10.94 10.94 0 0 1 12 5c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>`;

  function setupToggle(btnId, inputId){
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    let visible = false;
    btn.addEventListener('click', () => {
      visible = !visible;
      input.type = visible ? 'text' : 'password';
      btn.innerHTML = visible ? eyeOffIcon : eyeIcon;
      btn.setAttribute('aria-label', visible ? 'Sembunyikan password' : 'Tampilkan password');
    });
  }
  setupToggle('togglePass', 'password');
  setupToggle('toggleRegPass', 'regPassword');
  setupToggle('toggleRegConfirm', 'regConfirm');

  // --- Toggle antara mode Masuk <-> Daftar ---
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const formTitle = document.getElementById('formTitle');
  const formSubtitle = document.getElementById('formSubtitle');
  const toggleModeBtn = document.getElementById('toggleModeBtn');
  const footerLink = document.getElementById('footerLink');

  const copy = {
    login: {
      title: 'Selamat Datang Kembali',
      subtitle: 'Masukkan kredensial Anda untuk mengakses akun.',
      toggle: 'Daftar Akun Baru'
    },
    register: {
      title: 'Buat Akun Baru',
      subtitle: 'Lengkapi data di bawah untuk mendaftar di KurbanQu.',
      toggle: 'Sudah punya akun? Masuk'
    }
  };

  let mode = 'login';

  function replay(el){
    el.style.animation = 'none';
    void el.offsetHeight;
    el.style.animation = '';
  }

  function setMode(next){
    mode = next;
    const c = copy[mode];
    formTitle.textContent = c.title;
    formSubtitle.textContent = c.subtitle;
    toggleModeBtn.textContent = c.toggle;
    footerLink.style.display = mode === 'login' ? 'block' : 'none';

    if (mode === 'login') {
      registerForm.classList.add('hidden');
      loginForm.classList.remove('hidden');
      replay(loginForm);
    } else {
      loginForm.classList.add('hidden');
      registerForm.classList.remove('hidden');
      replay(registerForm);
    }
  }

  toggleModeBtn.addEventListener('click', (e) => {
    e.preventDefault();
    setMode(mode === 'login' ? 'register' : 'login');
  });

  loginForm.addEventListener('submit', (e) => {
    e.preventDefault();
    // Hubungkan ke proses autentikasi Anda di sini
  });

  registerForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const pass = document.getElementById('regPassword').value;
    const confirm = document.getElementById('regConfirm').value;
    const errorMsg = document.getElementById('confirmError');
    const confirmWrap = document.getElementById('confirmWrap');

    if (pass !== confirm) {
      errorMsg.classList.add('show');
      confirmWrap.style.borderColor = 'var(--error)';
    } else {
      errorMsg.classList.remove('show');
      confirmWrap.style.borderColor = '';
      // Lanjutkan proses pendaftaran akun di sini
    }
  });

  document.getElementById('regConfirm').addEventListener('input', () => {
    document.getElementById('confirmError').classList.remove('show');
    document.getElementById('confirmWrap').style.borderColor = '';
  });
</script>

</body>
</html>

<h1>LOGIN ADMIN BERHASIL 🎉</h1>

</body>
</html>