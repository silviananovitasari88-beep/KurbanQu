// ===== login.js — KurbanQu =====
// Menangani: toggle password, switch mode login/register, validasi form

const eyeIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
  <circle cx="12" cy="12" r="3"/>
</svg>`;

const eyeOffIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
  <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a18.5 18.5 0 0 1 4.22-5.06M9.9 4.24A10.94 10.94 0 0 1 12 5c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
  <path d="M1 1l22 22"/>
</svg>`;

// ---- Toggle show/hide password ----
function setupToggle(btnId, inputId) {
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

// ---- Toggle mode Login <-> Register ----
const loginForm     = document.getElementById('loginForm');
const registerForm  = document.getElementById('registerForm');
const formTitle     = document.getElementById('formTitle');
const formSubtitle  = document.getElementById('formSubtitle');
const toggleModeBtn = document.getElementById('toggleModeBtn');
const footerLink    = document.getElementById('footerLink');

const copy = {
  login: {
    title:    'Selamat Datang Kembali',
    subtitle: 'Masukkan kredensial Anda untuk mengakses akun.',
    toggle:   'Belum punya akun? Daftar'
  },
  register: {
    title:    'Buat Akun Baru',
    subtitle: 'Daftar terlebih dahulu untuk mengakses sistem KurbanQu.',
    toggle:   'Sudah punya akun? Masuk'
  }
};

let mode = 'register';

function replay(el) {
  el.style.animation = 'none';
  void el.offsetHeight;
  el.style.animation = '';
}

function setMode(next) {
  mode = next;
  const c = copy[mode];
  formTitle.textContent    = c.title;
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

// ---- Submit Login ----
loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;

  try {
    const response = await fetch('/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ username, password })
    });

    const data = await response.json();

    if (response.ok) {
      // Redirect ke dashboard setelah berhasil login
      window.location.href = data.redirect ?? '/admin/dashboard';
    } else {
      alert(data.message ?? 'Username atau password salah.');
    }
  } catch (err) {
    console.error('Login error:', err);
    alert('Terjadi kesalahan. Coba lagi.');
  }
});

// ---- Submit Register ----
registerForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const username = document.getElementById('regUsername').value.trim();
  const password = document.getElementById('regPassword').value;
  const confirm  = document.getElementById('regConfirm').value;
  const errorMsg   = document.getElementById('confirmError');
  const confirmWrap = document.getElementById('confirmWrap');

  // Validasi password cocok
  if (password !== confirm) {
    errorMsg.classList.add('show');
    confirmWrap.style.borderColor = 'var(--error)';
    return;
  }

  try {
    const response = await fetch('/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ username, password, password_confirmation: confirm })
    });

    const data = await response.json();

    if (response.ok) {
      // Setelah daftar, arahkan ke login agar admin masuk dengan akunnya
      alert('Akun berhasil dibuat! Silakan masuk dengan akun Anda.');
      setMode('login');
    } else {
      alert(data.message ?? 'Pendaftaran gagal. Coba lagi.');
    }
  } catch (err) {
    console.error('Register error:', err);
    alert('Terjadi kesalahan. Coba lagi.');
  }
});

// Reset error konfirmasi saat user mengetik ulang
document.getElementById('regConfirm').addEventListener('input', () => {
  document.getElementById('confirmError').classList.remove('show');
  document.getElementById('confirmWrap').style.borderColor = '';
});