<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin - KurbanQu</title>
  <link rel="stylesheet" href="{{ asset('css/kurbanqu.css') }}">
  <style>
    /* Style tambahan khusus tabel admin agar rapi */
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 13px;
    }
    .admin-table th, .admin-table td {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 1px solid #e0d5c0;
    }
    .admin-table th {
      background: #f0e8d8;
      color: #3d2510;
      font-weight: 700;
    }
    .btn-table-action {
      padding: 6px 12px;
      background: #5c3d1e;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 600;
      cursor: pointer;
    }
  </style>
</head>
<body>
<div class="app">

  <div class="page active" style="display: flex; flex-direction: column; width: 100%; height: 917px;">
    
    <div class="hdr" style="padding: 20px 22px 20px;">
      <div class="blob-lg"></div>
      <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">
        <div>
          <div class="hdr-title" style="font-size: 20px;">Dashboard Panitia</div>
          <div class="hdr-sub">Kelola Antrean Kupon Kurban</div>
        </div>
        <button onclick="window.location.href='/admin/login'" style="background: #faf6ee; color: #3d2510; border: none; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 600; cursor: pointer;">
          Keluar
        </button>
      </div>
    </div>

    <div class="summary-row" style="display: flex; gap: 8px; padding: 16px 20px 8px; flex-shrink: 0;">
      <div class="sum-card" style="flex: 1; background: #fff; border-radius: 12px; padding: 9px 10px; border: 0.5px solid #e0d5c0; text-align: center;">
        <div class="sum-num" style="font-size: 19px; font-weight: 700; color: #5c3d1e;">150</div>
        <div class="sum-lbl" style="font-size: 10px; color: #9a8060;">Total Kupon</div>
      </div>
      <div class="sum-card" style="flex: 1; background: #fff; border-radius: 12px; padding: 9px 10px; border: 0.5px solid #e0d5c0; text-align: center;">
        <div class="sum-num" style="font-size: 19px; font-weight: 700; color: #3b6d11;">45</div>
        <div class="sum-lbl" style="font-size: 10px; color: #3b6d11;">Sudah Diambil</div>
      </div>
      <div class="sum-card" style="flex: 1; background: #fff; border-radius: 12px; padding: 9px 10px; border: 0.5px solid #e0d5c0; text-align: center;">
        <div class="sum-num" style="font-size: 19px; font-weight: 700; color: #b85c00;">105</div>
        <div class="sum-lbl" style="font-size: 10px; color: #b85c00;">Belum Diambil</div>
      </div>
    </div>

    <div style="padding: 10px 20px 0;">
      <button class="btn-outline" style="padding: 12px; font-size: 13px;" onclick="alert('Fitur kamera scan QR code menyusul ya!')">
        📷 Scan QR Code Kupon Warga
      </button>
    </div>

    <div class="scroll-area" style="flex: 1; overflow-y: auto; padding: 15px 20px 30px;">
      <div class="card" style="padding: 16px 12px; overflow-x: auto;">
        <div style="font-size: 14px; font-weight: 700; color: #3d2510; margin-bottom: 12px;">
          Daftar Antrean Kupon Warga
        </div>
        
        <table class="admin-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div style="font-weight: 600; color: #3d2510;">Silviana Novita</div>
                <div style="font-size: 10px; color: #9a8060;">KK: 3273010101******</div>
              </td>
              <td><span style="color: #b85c00; font-weight: 600; font-size: 11px;">Belum</span></td>
              <td><button class="btn-table-action" onclick="alert('Status berhasil diubah jadi Sudah Diambil!')">Verif</button></td>
            </tr>
            <tr>
              <td>
                <div style="font-weight: 600; color: #3d2510;">Ahmad Hidayat</div>
                <div style="font-size: 10px; color: #9a8060;">KK: 3273010109******</div>
              </td>
              <td><span style="color: #3b6d11; font-weight: 600; font-size: 11px;">Selesai</span></td>
              <td><span style="font-size: 12px; color: #9a8060;">✔ Selesai</span></td>
            </tr>
            <tr>
              <td>
                <div style="font-weight: 600; color: #3d2510;">Budi Utomo</div>
                <div style="font-size: 10px; color: #9a8060;">KK: 3273010104******</div>
              </td>
              <td><span style="color: #b85c00; font-weight: 600; font-size: 11px;">Belum</span></td>
              <td><button class="btn-table-action" onclick="alert('Status berhasil diubah!')">Verif</button></td>
            </tr>
          </tbody>
        </table>
        
      </div>
    </div>

  </div>

</div>
</body>
</html>