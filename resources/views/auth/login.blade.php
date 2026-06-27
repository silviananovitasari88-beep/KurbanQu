{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Daftar — KurbanQu</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

  <div class="wrapper">

    {{-- Brand --}}
    <div class="brand">
      <div class="logo-badge">
        <svg viewBox="0 0 24 24" fill="#241B0E" aria-hidden="true">
          <ellipse cx="12" cy="15.2" rx="5" ry="4"/>
          <ellipse cx="5"  cy="9"    rx="2" ry="2.6" transform="rotate(-15 5 9)"/>
          <ellipse cx="9"  cy="6"    rx="2" ry="2.6" transform="rotate(-5 9 6)"/>
          <ellipse cx="15" cy="6"    rx="2" ry="2.6" transform="rotate(5 15 6)"/>
          <ellipse cx="19" cy="9"    rx="2" ry="2.6" transform="rotate(15 19 9)"/>
        </svg>
      </div>
      <div class="brand-name">KurbanQu</div>
      <div class="brand-tag">Sistem Distribusi Kurban Berbasis QR</div>
    </div>

    {{-- Card --}}
    <div class="card">
      <h1 id="formTitle">Buat Akun Baru</h1>
      <p class="subtitle" id="formSubtitle">Daftar terlebih dahulu untuk mengakses sistem KurbanQu.</p>

      {{-- Form Login --}}
      <form id="loginForm" class="form-section hidden" novalidate>

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

      {{-- Form Register --}}
      <form id="registerForm" class="form-section" novalidate>

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

      <a href="#" class="secondary" id="toggleModeBtn">Sudah punya akun? Masuk</a>
    </div>

    <p class="footer-link" id="footerLink" style="display:none">
      Lupa password Anda? <a href="#">Reset Password</a>
    </p>

  </div>

  <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>