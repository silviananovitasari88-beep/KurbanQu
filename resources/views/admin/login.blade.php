<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Panitia - KurbanQu</title>
  <link rel="stylesheet" href="{{ asset('css/kurbanqu.css') }}">
</head>
<body>
<div class="app">

  <div class="page active" style="display: flex; flex-direction: column; width: 100%; height: 917px;">
    <div class="hdr" style="background: #3d2510; padding: 30px 22px 34px;">
      <div class="blob-lg"></div>
      <div style="display:flex; align-items:center; gap:13px; position:relative; z-index:1; margin-bottom:18px;">
        <div style="width:46px; height:46px; border-radius:14px; background:#5c3d1e; display:flex; align-items:center; justify-content:center; font-size:24px;">
          💼
        </div>
        <div style="font-size:21px; font-weight:700; color:#fff; letter-spacing:-.2px;">KurbanQu Admin</div>
      </div>
      <div style="font-size:24px; font-weight:600; color:#fff; line-height:1.38; position:relative; z-index:1;">
        Masuk sebagai<br>panitia kurban
      </div>
    </div>

    <div class="scroll-area" style="flex: 1; overflow-y: auto;">
      <div style="padding:40px 20px;">
        <div class="card">
          <div style="font-size:17px; font-weight:700; color:#3d2510; text-align:center; margin-bottom:22px;">
            Sistem Kelola Kupon
          </div>

          <div class="field">
            <label>Username / Email Panitia <span style="color:#d94f4f;">*</span></label>
            <input type="text" id="admin-username" placeholder="Masukkan username" />
          </div>
          
          <div class="field" style="margin-bottom:28px;">
            <label>Password <span style="color:#d94f4f;">*</span></label>
            <input type="password" id="admin-password" placeholder="Masukkan password" />
          </div>

          <button class="btn-primary" onclick="window.location.href='/admin/dashboard'">
            Masuk Ke Dashboard
          </button>
          
          <div style="display:flex; align-items:center; justify-content:center; gap:5px; margin-top:16px; font-size:11px; color:#9a8060;">
            🔒 Akses terbatas hanya untuk panitia terdaftar
          </div>
        </div>
      </div>
    </div>
    
  </div>

</div>
</body>
</html>