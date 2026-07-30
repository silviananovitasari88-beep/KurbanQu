<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>KurbanQu — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="{{ asset('css/admin.css') }}" />

<!-- ════════════════════════════════════════════════════════════════ -->
<!-- CSS UNTUK FITUR HAPUS -->
<!-- ════════════════════════════════════════════════════════════════ -->
<style>
/* Tombol Hapus */
.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.btn-danger:active {
    transform: translateY(0);
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-done {
    background: rgba(0, 200, 0, 0.15);
    color: #28a745;
}

.status-pending {
    background: rgba(255, 165, 0, 0.15);
    color: #ff8c00;
}

/* QR Badge */
.qr-badge {
    display: inline-block;
    background: rgba(200, 146, 42, 0.15);
    color: #c8922a;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
    font-weight: 700;
}

/* Toast Notification */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 14px 24px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    max-width: 400px;
    display: none;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    animation: slideInRight 0.3s ease;
}

.toast.success {
    background: #28a745;
    color: white;
}

.toast.error {
    background: #dc3545;
    color: white;
}

.toast.info {
    background: #17a2b8;
    color: white;
}

.toast.warning {
    background: #ffc107;
    color: #212529;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.btn-sm {
    padding: 4px 10px;
    font-size: 11px;
}
</style>
</head>
<body>

<!-- ─── TOAST ─────────────────────────────────── -->
<div class="toast" id="toast"></div>

<!-- ─── SIDEBAR OVERLAY ───────────────────────── -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ─── SIDEBAR ───────────────────────────────── -->
<aside class="sidebar">
  <div class="sidebar-logo">
   <img src="{{ asset('assets/img/FIN.png') }}" width="55" alt="KurbanQu">
    <div>
      <div class="logo-text">KurbanQu</div>
      <div class="logo-sub">Admin Dashboard</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Menu Utama</div>
    <div class="nav-item active" onclick="navTo('dashboard',this)">
      <span class="nav-ico">📊</span> Dashboard
    </div>
    <div class="nav-item" onclick="navTo('hewan',this)">
      <span class="nav-ico">🐾</span> Data Hewan
    </div>
    <div class="nav-item" onclick="navTo('mudhohi',this)">
      <span class="nav-ico">👥</span> Mudhohi
    </div>
    <div class="nav-item" onclick="navTo('tracking',this)">
      <span class="nav-ico">📍</span> Tracking
      <span class="nav-badge" id="badge-tracking">3</span>
    </div>
    <div class="nav-item" onclick="navTo('distribusi',this)">
      <span class="nav-ico">🎫</span> Distribusi QR
      <span class="nav-badge" id="badge-distribusi">0</span>
    </div>
    <div class="nav-section-label" style="margin-top:8px;">Penerima &amp; QR</div>
    <div class="nav-item" onclick="navTo('upload',this)">
      <span class="nav-ico">🎫</span> Penerima Kurban
      <span class="nav-badge" id="badge-penerima">0</span>
    </div>
    <div class="nav-item" onclick="navTo('tabel',this)">
      <span class="nav-ico">📋</span> Tabel Distribusi
    </div>
    <div class="nav-item" onclick="navTo('rekap',this)">
      <span class="nav-ico">📈</span> Rekap & Statistik
    </div>
    <div class="nav-section-label" style="margin-top:8px;">Pengaturan</div>
    <div class="nav-item" onclick="navTo('settings',this)">
      <span class="nav-ico">⚙️</span> Pengaturan Sistem
    </div>
  </nav>

  <div class="sidebar-footer">
    <div class="nav-item" onclick="logout()" style="color:var(--red);">
      <span class="nav-ico">⏻</span> Logout
    </div>
  </div>
</aside>

<!-- ─── MAIN ───────────────────────────────────── -->
<main class="main">
  <header class="topbar">
    <div style="display:flex;align-items:center;gap:12px;">
      <button class="hamburger" id="hamburger" onclick="toggleSidebar()" aria-label="Toggle menu">
        <span></span><span></span><span></span>
      </button>
      <div>
        <div class="topbar-title" id="topbar-title">Dashboard Admin</div>
        <div class="topbar-sub" id="topbar-sub">Sistem Informasi Distribusi Kurban Berbasis QR</div>
      </div>
    </div>
    <div class="live-badge">
      <div class="live-dot"></div>
      LIVE TRACKING ACTIVE
    </div>
  </header>

  <div class="content">

    <!-- ══════════════════════ DASHBOARD PAGE ══ -->
    <div class="page active" id="pg-dashboard">

      <!-- Banner Tanggal Pelaksanaan -->
      <div class="card" style="margin-bottom:18px; border-left: 4px solid var(--gold);">
        <div class="card-body" style="padding:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:12px; color:var(--text3); text-transform:uppercase; font-weight:700;">📅 Tanggal Pelaksanaan Kurban</div>
            <div id="dash-tgl-kurban" style="font-size:18px; font-weight:800; color:var(--text); margin-top:4px;">Belum Diatur</div>
          </div>
          <button class="btn btn-outline btn-sm" onclick="navTo('settings', document.querySelector('.nav-item[onclick*=\'settings\']'))">Ubah / Hapus</button>
        </div>
      </div>

      <!-- Stats -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-label">Total Hewan</div>
          <div class="stat-num" id="s-hewan">0</div>
          <div class="stat-sub">Sapi, Kambing, Domba</div>
          <div class="stat-icon">🐾</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Mudhohi</div>
          <div class="stat-num" id="s-mudhohi">0</div>
          <div class="stat-sub">Pemilik / patungan hewan</div>
          <div class="stat-icon">👥</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Sudah Diambil</div>
          <div class="stat-num" id="s-diambil" style="color:var(--green);">0</div>
          <div class="stat-sub" id="s-diambil-pct">0% dari total</div>
          <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">QR Aktif</div>
          <div class="stat-num" id="s-qr">0</div>
          <div class="stat-sub">Siap discan panitia</div>
          <div class="stat-icon">🎫</div>
        </div>
      </div>

      <!-- Grid -->
      <div class="dash-grid">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header">
              <div class="card-title">🐾 Data Hewan Kurban</div>
              <button class="btn btn-gold btn-sm" onclick="navTo('hewan',null);openModalHewan()">+ Tambah Hewan</button>
            </div>
            <div class="card-body" id="dash-animal-list" style="padding:12px 16px;"></div>
          </div>

          <div class="card" style="background:linear-gradient(135deg,#2a1f12,#1a1208);border-color:rgba(200,146,42,0.2);">
            <div class="card-body" style="padding:24px;">
              <div style="font-size:10px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">QR DISTRIBUSI</div>
              <div style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;margin-bottom:8px;">Scan & Verifikasi</div>
              <div style="font-size:13px;color:var(--text3);margin-bottom:18px;line-height:1.6;">Sistem scan QR otomatis untuk validasi penerima kurban sesuai workflow distribusi.</div>
              <button class="btn btn-gold btn-lg" onclick="navTo('distribusi',document.querySelector('[onclick*=distribusi]'))">Buka Distribusi QR →</button>
            </div>
          </div>
        </div>

        <div>
          <div class="card" style="height:fit-content;">
            <div class="card-header">
              <div class="card-title">📍 Live Tracking</div>
              <button class="btn btn-ghost btn-sm" onclick="navTo('tracking',document.querySelector('[onclick*=tracking]'))">Edit →</button>
            </div>
            <div class="card-body" id="dash-tracking"></div>
          </div>

          <div class="card" style="margin-top:18px;">
            <div class="card-header"><div class="card-title">📊 Progress Distribusi</div></div>
            <div class="card-body">
              <div style="display:flex;align-items:center;gap:20px;margin-bottom:16px;">
                <div class="rekap-ring">
                  <svg width="120" height="120" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="48" fill="none" stroke="var(--bg4)" stroke-width="10"/>
                    <circle id="prog-circle" cx="60" cy="60" r="48" fill="none" stroke="var(--gold)" stroke-width="10"
                      stroke-linecap="round" stroke-dasharray="301.6" stroke-dashoffset="301.6" style="transition:stroke-dashoffset .8s ease;"/>
                  </svg>
                  <div class="rekap-ring-label">
                    <div class="rekap-ring-num" id="prog-pct">0%</div>
                    <div class="rekap-ring-sub">diambil</div>
                  </div>
                </div>
                <div style="flex:1;">
                  <div id="bar-chart"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /dashboard -->

    <!-- ══════════════════════ HEWAN PAGE ══ -->
    <div class="page" id="pg-hewan">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">Data Hewan Kurban</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Kelola semua data hewan kurban</div>
        </div>
        <button class="btn btn-gold" onclick="openModalHewan()">+ Tambah Hewan</button>
      </div>

      <div style="display:flex;gap:8px;margin-bottom:16px;align-items:center;">
        <div class="tab-row" style="width:fit-content;">
          <button class="tab-item active" onclick="filterHewan('semua',this)">Semua</button>
          <button class="tab-item" onclick="filterHewan('sapi',this)">🐄 Sapi</button>
          <button class="tab-item" onclick="filterHewan('kambing',this)">🐐 Kambing</button>
          <button class="tab-item" onclick="filterHewan('domba',this)">🐑 Domba</button>
        </div>
        <div class="search-box" style="flex:1;max-width:280px;">
          <span style="color:var(--text3);">🔍</span>
          <input type="text" id="hewan-search" placeholder="Cari hewan..." oninput="renderHewanTable()"/>
        </div>
      </div>

      <div class="card">
        <table class="data-table">
          <thead>
            <tr>
              <th>id_hewan</th>
              <th>Hewan</th>
              <th>Jenis</th>
              <th>Umur / Berat</th>
              <th>Mudhohi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="hewan-table-body"></tbody>
        </table>
      </div>
    </div><!-- /hewan -->

    <!-- ══════════════════════ MUDHOHI PAGE ══ -->
    <div class="page" id="pg-mudhohi">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">Data Mudhohi</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Pemilik hewan &amp; bagian kurban</div>
        </div>
        <button class="btn btn-gold" onclick="openModalMudhohi()">+ Tambah Mudhohi</button>
      </div>

      <div style="display:flex;gap:8px;margin-bottom:16px;">
        <div class="search-box" style="flex:1;max-width:300px;">
          <span style="color:var(--text3);">🔍</span>
          <input type="text" id="mudhohi-search" placeholder="Cari nama mudhohi..." oninput="renderMudhohiTable()"/>
        </div>
      </div>

      <div class="card">
        <table class="data-table">
          <thead>
            <tr>
              <th>QR / ID</th>
              <th>Nama / No KK</th>
              <th>Alamat</th>
              <th>No. Telp</th>
              <th>Hewan</th>
              <th>Bagian / Req</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="mudhohi-table-body"></tbody>
        </table>
      </div>
    </div><!-- /mudhohi -->

    <!-- ══════════════════════ TRACKING PAGE ══ -->
    <div class="page" id="pg-tracking">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">Live Tracking Proses</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Update status proses kurban secara real-time</div>
        </div>
        <div class="live-badge"><div class="live-dot"></div> LIVE</div>
      </div>

      <div class="dash-grid">
        <div class="card">
          <div class="card-header"><div class="card-title">📍 Status Proses Kurban</div></div>
          <div class="card-body" id="tracking-list" style="padding:20px;"></div>
        </div>
        <div>
          <div class="card">
            <div class="card-header"><div class="card-title">🕐 Riwayat Update</div></div>
            <div class="card-body" id="tracking-log" style="max-height:400px;overflow-y:auto;"></div>
          </div>
        </div>
      </div>
    </div><!-- /tracking -->

    <!-- ══════════════════════ DISTRIBUSI PAGE ══ -->
    <div class="page" id="pg-distribusi">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">Distribusi QR — Scan Kamera</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Scan QR code penerima dengan kamera untuk verifikasi otomatis</div>
        </div>
        <div style="font-size:13px;color:var(--text3);">Terverifikasi: <strong id="dist-count" style="color:var(--green);">0</strong> / <span id="dist-total">0</span></div>
      </div>

      <div class="dash-grid">
        <div>
          <div class="scanner-wrap">
            <div id="qr-reader" style="width: 100%; max-width: 240px; margin: 0 auto 24px; border-radius: 16px; overflow: hidden; background: transparent; display: none;"></div>
            <div class="scanner-frame" id="scanner-placeholder">
              <div class="sc-box">
                <div class="sc-corner sc-tl"></div>
                <div class="sc-corner sc-tr"></div>
                <div class="sc-corner sc-bl"></div>
                <div class="sc-corner sc-br"></div>
                <div class="sc-line"></div>
                <div style="position:absolute;inset:20px;border-radius:8px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;">
                  <div style="font-size:12px;color:rgba(255,255,255,0.3);text-align:center;">📷<br>Kamera belum aktif</div>
                </div>
              </div>
            </div>

            <div style="margin-bottom:15px; display:flex; justify-content:center; gap:10px;">
              <button id="btn-start" class="btn btn-gold" onclick="startScanner()">Mulai Scan</button>
              <button id="btn-stop" class="btn btn-danger d-none" onclick="stopScanner()">Stop</button>
            </div>

            <div style="font-size:14px;font-weight:600;color:var(--text2);">Arahkan QR ke kamera</div>
            <div style="font-size:12px;color:var(--text3);margin-top:4px;">QR akan terdeteksi otomatis setelah kamera diaktifkan</div>
          </div>
          <div style="margin-top:12px;text-align:center;">
            <button class="btn btn-outline" onclick="navTo('tabel',document.querySelector('[onclick*=tabel]'))">
              📋 Lihat &amp; Cari di Tabel Distribusi →
            </button>
          </div>
        </div>

        <div>
          <div id="scan-result" style="margin-bottom:18px;"></div>
          <div class="card">
            <div class="card-header"><div class="card-title">✅ Log Distribusi Hari Ini</div></div>
            <div style="max-height:340px;overflow-y:auto;" id="dist-log">
              <div class="empty-state"><div class="empty-ico">📋</div>Belum ada yang diverifikasi</div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /distribusi -->

    <!-- ══════════════════════ TABEL DISTRIBUSI PAGE ══ -->
    <div class="page" id="pg-tabel">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">📋 Tabel Distribusi Penerima</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">
            Data penerima dari <strong style="color:var(--gold2);">Excel upload</strong>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
          <div id="tabel-summary"></div>
          <button class="btn btn-outline btn-sm" onclick="exportTabelCSV()">⬇ Export CSV</button>
        </div>
      </div>

      <div id="tabel-chips" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;"></div>

      <div class="dash-grid" style="margin-bottom:18px;">
        <div class="card">
          <div class="card-header">
            <div class="card-title">🔍 Cari &amp; Verifikasi Penerima</div>
            <span style="font-size:11px;color:var(--text3);">Cari lalu tandai sudah diambil</span>
          </div>
          <div class="card-body">
            <div class="search-box" style="margin-bottom:12px;">
              <span style="color:var(--text3);">🔍</span>
              <input type="text" id="tabel-quick-search" placeholder="Ketik nama atau No KK penerima..." oninput="renderQuickScanList()"/>
            </div>
            <div id="quick-scan-list" style="max-height:220px;overflow-y:auto;"></div>
          </div>
        </div>
        <div>
          <div id="tabel-scan-result" style="margin-bottom:14px;"></div>
          <div class="card">
            <div class="card-header"><div class="card-title">✅ Log Verifikasi Hari Ini</div></div>
            <div style="max-height:180px;overflow-y:auto;" id="tabel-dist-log">
              <div class="empty-state" style="padding:20px;"><div class="empty-ico" style="font-size:24px;">📋</div>Belum ada</div>
            </div>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center;">
        <div class="search-box" style="flex:1;min-width:180px;max-width:280px;">
          <span style="color:var(--text3);">🔍</span>
          <input type="text" id="tabel-search" placeholder="Filter tabel: nama / no KK..." oninput="renderTabelDistribusi()"/>
        </div>
        <select id="tabel-filter-status" onchange="renderTabelDistribusi()" style="background:var(--bg3);border:1px solid var(--border2);border-radius:9px;padding:9px 12px;font-size:12px;color:var(--text2);font-family:inherit;outline:none;cursor:pointer;">
          <option value="semua">Semua Status</option>
          <option value="diambil">✅ Sudah Diambil</option>
          <option value="belum">⏳ Belum Diambil</option>
        </select>
        <select id="tabel-filter-metode" onchange="renderTabelDistribusi()" style="background:var(--bg3);border:1px solid var(--border2);border-radius:9px;padding:9px 12px;font-size:12px;color:var(--text2);font-family:inherit;outline:none;cursor:pointer;">
          <option value="semua">Semua Metode</option>
          <option value="QR">📱 Via QR</option>
          <option value="Manual">👆 Manual</option>
          <option value="-">— Belum Ada</option>
        </select>
        <select id="tabel-filter-qr" onchange="renderTabelDistribusi()" style="background:var(--bg3);border:1px solid var(--border2);border-radius:9px;padding:9px 12px;font-size:12px;color:var(--text2);font-family:inherit;outline:none;cursor:pointer;">
          <option value="semua">Semua QR</option>
          <option value="downloaded">⬇ Sudah Download</option>
          <option value="not_downloaded">📵 Belum Download</option>
        </select>
      </div>

      <div class="card" style="overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="data-table" style="min-width:900px;">
            <thead>
              <tr>
                <th style="width:54px;text-align:center;">id_stok</th>
                <th style="min-width:190px;">warga_no_kk &amp; Nama KK</th>
                <th style="min-width:130px;">QR_id_qr</th>
                <th style="text-align:center;min-width:110px;">Login</th>
                <th style="min-width:150px;">st_pengambilan</th>
                <th style="text-align:center;min-width:120px;">mtd_pengambilan</th>
                <th style="min-width:85px;">Waktu</th>
                <th style="text-align:center;min-width:110px;">Aksi Admin</th>
              </tr>
            </thead>
            <tbody id="tabel-distribusi-body"></tbody>
          </table>
        </div>
        <div id="tabel-empty" style="display:none;" class="empty-state">
          <div class="empty-ico">📊</div>
          Belum ada data penerima. Upload file Excel di menu <strong>Penerima Kurban</strong> lalu klik <em>"✓ Aktifkan sebagai Penerima"</em>.
        </div>
      </div>

      <div style="margin-top:14px;display:flex;gap:16px;flex-wrap:wrap;font-size:11px;color:var(--text3);">
        <span>🟢 = Otomatis via QR Scan</span>
        <span>🟡 = Manual diklik admin</span>
        <span>⬇ = User sudah download QR</span>
        <span>📵 = Belum download QR</span>
      </div>
    </div><!-- /tabel -->

    <!-- ══════════════════════ PENERIMA KURBAN PAGE ══ -->
    <div class="page" id="pg-upload">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">🎫 Penerima Kurban</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Upload Excel → login warga (No KK + Nama) → kode QR</div>
        </div>
        <button class="btn btn-gold" onclick="openModalPenerima()">+ Tambah Manual</button>
      </div>

      <div class="flow-steps" style="margin-bottom:20px;">
        <div class="flow-step">
          <div class="flow-step-num">1</div>
          <div><strong>Admin upload Excel/CSV</strong><div class="flow-step-desc"></div></div>
        </div>
        <div class="flow-step-arrow">→</div>
        <div class="flow-step">
          <div class="flow-step-num">2</div>
          <div><strong>Konfirmasi daftar</strong><div class="flow-step-desc"></div></div>
        </div>
        <div class="flow-step-arrow">→</div>
        <div class="flow-step">
          <div class="flow-step-num">3</div>
          <div><strong>Warga login</strong><div class="flow-step-desc"></div></div>
        </div>
      </div>

      <div id="penerima-stat-chips" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;"></div>

      <div class="dash-grid">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><div class="card-title">📂 Upload Excel / CSV</div></div>
            <div class="card-body">
              <div style="font-size:12px;color:var(--text3);margin-bottom:14px;line-height:1.7;">
                Simpan file Excel sebagai <strong>CSV UTF-8</strong> atau langsung drag .xlsx. 
                Kolom dan urutan apapun — sistem akan mendeteksi otomatis.
              </div> 
              <div id="drop-zone" class="drop-zone-penerima"
                onclick="document.getElementById('excel-input').click()"
                ondragenter="event.preventDefault();this.classList.add('drag')"
                ondragover="event.preventDefault();event.stopPropagation();this.classList.add('drag')"
                ondragleave="event.preventDefault();this.classList.remove('drag')"
                ondrop="handleFileDrop(event);this.classList.remove('drag')">
                <div style="font-size:32px;margin-bottom:8px;">📊</div>
                <div style="font-size:14px;font-weight:600;color:var(--text2);">Klik atau drag &amp; drop file di sini</div>
                <div style="font-size:11px;color:var(--text3);margin-top:6px;">.csv · .xlsx · .xls — Format kolom otomatis terdeteksi</div>
              </div>
              <input type="file" id="excel-input" accept=".csv,.xlsx,.xls" style="display:none;" onchange="handleFileSelect(this)"/>

              <div style="text-align:center;color:var(--text3);font-size:11px;margin:16px 0;">atau paste CSV</div>
              <textarea id="csv-paste" rows="5" class="csv-paste-penerima" placeholder="No KK,Nama Kepala Keluarga,Alamat,No Telp&#10;3273011234567890,Ahmad Hidayat,,&#10;3273012345678901,Siti Rahmawati,,"></textarea>

              <div style="margin-top:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <label style="font-size:12px;color:var(--text2);display:flex;align-items:center;gap:6px;cursor:pointer;">
                  <input type="radio" name="import-mode" value="append" checked/> Tambah ke daftar
                </label>
                <label style="font-size:12px;color:var(--text2);display:flex;align-items:center;gap:6px;cursor:pointer;">
                  <input type="radio" name="import-mode" value="replace"/> Ganti seluruh daftar
                </label>
              </div>
              <div style="display:flex;gap:10px;margin-top:12px;">
                <button class="btn btn-gold" style="flex:1;" onclick="parseCSVPaste()">🔍 Proses &amp; Preview</button>
                <button class="btn btn-ghost" onclick="clearAllPenerima()">Bersihkan</button>
              </div>
            </div>
          </div>
        </div>

        <div>
          <div class="card">
            <div class="card-header">
              <div class="card-title">👁 Preview sebelum simpan</div>
              <div id="preview-stats" style="font-size:12px;color:var(--text3);"></div>
            </div>
            <div id="preview-content" style="max-height:380px;overflow-y:auto;">
              <div class="empty-state"><div class="empty-ico">📋</div>Preview muncul setelah file diproses</div>
            </div>
            <div id="preview-actions" style="padding:16px;border-top:1px solid var(--border);display:none;">
              <button class="btn btn-gold btn-lg" style="width:100%;" onclick="importConfirm()">✓ Aktifkan sebagai Penerima (boleh login)</button>
              <div style="font-size:11px;color:var(--text3);text-align:center;margin-top:8px;">Setelah ini, warga bisa login dengan No KK &amp; nama yang sama</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card" style="margin-top:18px;" id="imported-list-card">
        <div class="card-header">
          <div class="card-title">✅ Daftar Penerima Terdaftar</div>
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span id="imported-count" style="font-size:12px;color:var(--text3);"></span>
            <button class="btn btn-outline btn-sm" onclick="exportImportedCSV()">⬇ Export CSV</button>
          </div>
        </div>
        <div class="search-box" style="margin:0 16px 12px;max-width:320px;">
          <span style="color:var(--text3);">🔍</span>
          <input type="text" id="penerima-search" placeholder="Cari No KK atau nama..." oninput="renderPenerimaTable()"/>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table" style="min-width:640px;">
            <thead>
              <tr>
                <th>#</th>
                <th>No KK</th>
                <th>Nama Kepala Keluarga</th>
                <th>Kode QR</th>
                <th>Alamat</th>
                <th>No. Telp</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="imported-table-body"></tbody>
          </table>
        </div>
        <div id="penerima-empty" class="empty-state" style="display:none;padding:24px;">
          <div class="empty-ico">🎫</div>Belum ada penerima. Upload Excel atau tambah manual.
        </div>
      </div>
    </div><!-- /upload -->

    <!-- ══════════════════════ REKAP PAGE ══ -->
    <div class="page" id="pg-rekap">
      <div style="margin-bottom:20px;">
        <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">Rekap & Statistik</div>
        <div style="font-size:12px;color:var(--text3);margin-top:3px;">Ringkasan lengkap pelaksanaan kurban 1446 H</div>
      </div>
      <div id="rekap-content"></div>
    </div><!-- /rekap -->

  </div><!-- /content -->
</main>

<!-- ─── MODAL: TAMBAH PENERIMA KURBAN ───────────── -->
<div class="modal-overlay" id="modal-penerima">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">🎫 Tambah Penerima Kurban</div>
      <button class="modal-close" onclick="closeModal('modal-penerima')">✕</button>
    </div>
    <p style="font-size:12px;color:var(--text3);margin:0 0 16px;line-height:1.6;">Data ini dipakai warga saat login di halaman depan untuk mendapatkan QR.</p>
    <div class="form-row">
      <div class="form-group">
        <label>No. KK <span style="color:var(--red);">*</span></label>
        <input type="text" id="p-nkk" placeholder="16 digit nomor KK"/>
      </div>
      <div class="form-group">
        <label>Nama Kepala Keluarga <span style="color:var(--red);">*</span></label>
        <input type="text" id="p-nama" placeholder="Sesuai KK"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Alamat <span style="font-size:10px;color:var(--text3);">(opsional)</span></label>
        <input type="text" id="p-alamat" placeholder="RT/RW, desa"/>
      </div>
      <div class="form-group">
        <label>No. Telepon <span style="font-size:10px;color:var(--text3);">(opsional)</span></label>
        <input type="text" id="p-telp" placeholder="08xxxxxxxxxx"/>
      </div>
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
      <button class="btn btn-outline" onclick="closeModal('modal-penerima')">Batal</button>
      <button class="btn btn-gold btn-lg" onclick="submitPenerimaManual()">Simpan Penerima</button>
    </div>
  </div>
</div>

<!-- ─── MODAL: TAMBAH HEWAN ─────────────────────── -->
<div class="modal-overlay" id="modal-hewan">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">🐾 Tambah Hewan Kurban</div>
      <button class="modal-close" onclick="closeModal('modal-hewan')">✕</button>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Jenis Hewan *</label>
        <select id="h-jenis">
          <option value="">Pilih jenis</option>
          <option value="sapi">🐄 Sapi</option>
          <option value="kambing">🐐 Kambing</option>
          <option value="domba">🐑 Domba</option>
        </select>
      </div>
      <div class="form-group">
        <label>Nama / Label *</label>
        <input type="text" id="h-label" placeholder="Contoh: Sapi Merah No.03"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Umur Hewan</label>
        <input type="text" id="h-umur" placeholder="Contoh: 2 Tahun"/>
      </div>
      <div class="form-group">
        <label>Berat Estimasi</label>
        <input type="text" id="h-berat" placeholder="Contoh: ±35 kg"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Sehat? *</label>
        <select id="h-sehat">
          <option value="Sehat">✅ Sehat</option>
          <option value="Tidak Sehat">❌ Tidak Sehat</option>
        </select>
      </div>
      <div class="form-group">
        <label>Cacat? *</label>
        <select id="h-cacat">
          <option value="Tidak Cacat">✅ Tidak Cacat</option>
          <option value="Cacat">⚠️ Cacat</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Sesuai Syariat? *</label>
        <select id="h-syariat">
          <option value="Sah">Sah — Sesuai syariat</option>
          <option value="Tidak Sah">Tidak Sah</option>
        </select>
      </div>
      <div class="form-group">
        <label>Keterangan Cacat</label>
        <input type="text" id="h-cacat-ket" placeholder="Isi jika ada cacat (mata, kaki, dll)"/>
      </div>
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
      <button class="btn btn-outline" onclick="closeModal('modal-hewan')">Batal</button>
      <button class="btn btn-gold btn-lg" onclick="submitHewan()">Simpan Hewan</button>
    </div>
  </div>
</div>

<!-- ─── MODAL: TAMBAH MUDHOHI ───────────────────── -->
<div class="modal-overlay" id="modal-mudhohi">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">👤 Tambah Mudhohi</div>
      <button class="modal-close" onclick="closeModal('modal-mudhohi')">✕</button>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Nama Lengkap *</label>
        <input type="text" id="m-nama" placeholder="Nama lengkap mudhohi"/>
      </div>
      <div class="form-group">
        <label>Nama Ayah</label>
        <input type="text" id="m-ayah" placeholder="Nama ayah / bin"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>No. KK</label>
        <input type="text" id="m-nkk" placeholder="16 digit nomor KK"/>
      </div>
      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" id="m-telp" placeholder="08xx-xxxx-xxxx"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Jenis Hewan (enum) *</label>
        <select id="m-jenis" onchange="filterMudhohiHewanSelect()">
          <option value="">Pilih jenis</option>
          <option value="sapi">🐄 Sapi</option>
          <option value="kambing">🐐 Kambing</option>
          <option value="domba">🐑 Domba</option>
        </select>
      </div>
      <div class="form-group">
        <label>Hewan FK (id_hewan) *</label>
        <select id="m-hewan" disabled>
          <option value="">— Pilih jenis hewan terlebih dahulu —</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Req Bagian</label>
        <input type="text" id="m-req" placeholder="Contoh: 1/7 sapi, dada, paha"/>
      </div>
      <div class="form-group">
        <label>Bagian</label>
        <input type="text" id="m-bagian" placeholder="Contoh: 1/7 atau kurban penuh"/>
      </div>
    </div>
    <div class="form-group">
      <label>Alamat</label>
      <input type="text" id="m-alamat" placeholder="RT/RW, Desa"/>
    </div>
    <p style="font-size:11px;color:var(--text3);margin:-4px 0 12px;">QR code otomatis dibuat dari <strong style="color:var(--gold2);">id_mudhohi</strong> setelah disimpan.</p>
    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
      <button class="btn btn-outline" onclick="closeModal('modal-mudhohi')">Batal</button>
      <button class="btn btn-gold btn-lg" onclick="submitMudhohi()">Simpan Mudhohi</button>
    </div>
  </div>
</div>

<!-- ─── MODAL: DETAIL HEWAN ────────────────────── -->
<div class="modal-overlay" id="modal-detail-hewan">
  <div class="modal" style="width:580px;">
    <div class="modal-header">
      <div class="modal-title" id="detail-hewan-title">Detail Hewan</div>
      <button class="modal-close" onclick="closeModal('modal-detail-hewan')">✕</button>
    </div>
    <div id="detail-hewan-body"></div>
  </div>
</div>

<!-- ══════════════════════ SETTINGS PAGE ══ -->
<div class="page" id="pg-settings">
  <div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
      <div class="card-title">⚙️ Pengaturan Sistem</div>
    </div>
    <div class="card-body">
      <div style="margin-bottom: 20px;">
        <label style="display:block; font-size: 14px; font-weight: 600; margin-bottom: 8px;">Tanggal Pelaksanaan Kurban</label>
        <p style="font-size: 12px; color: var(--text3); margin-bottom: 12px;">Pilih tanggal hari H penyembelihan. Sistem akan menggunakan ini untuk menghitung mundur H-1, H-2, dsb.</p>
        <input type="date" id="setting-tanggal-kurban" class="form-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-card); color: var(--text);">
      </div>
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <button class="btn btn-danger" onclick="document.getElementById('setting-tanggal-kurban').value=''; saveSettings()">Hapus Tanggal</button>
        <button class="btn btn-gold" onclick="saveSettings()">Simpan Pengaturan</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/warga-login.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
  if (typeof window.XLSX === 'undefined') {
    const s = document.createElement('script');
    s.src = 'https://unpkg.com/xlsx/dist/xlsx.full.min.js';
    s.onload = () => console.info('SheetJS loaded fallback');
    s.onerror = () => console.warn('SheetJS CDN gagal dimuat; .xlsx mungkin tidak bisa diproses.');
    document.head.appendChild(s);
  }
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>

<!-- ════════════════════════════════════════════════════════════════ -->
<!-- ⭐ JAVASCRIPT UNTUK FITUR HAPUS -->
<!-- ════════════════════════════════════════════════════════════════ -->
<script>
/**
 * Hapus 1 data warga berdasarkan No KK
 * Data distribusi terkait juga akan ikut terhapus
 */
function deleteWarga(noKk, nama) {
    if (!noKk) {
        alert('❌ No KK tidak valid');
        return;
    }

    if (!confirm(`⚠️ Yakin ingin menghapus data warga?\n\nNama: ${nama || '-'}\nNo KK: ${noKk}\n\n⚠️ Data distribusi terkait juga akan dihapus!`)) {
        return;
    }

    showToast('⏳ Menghapus data...', 'info');

    fetch(`/admin/api/warga/${encodeURIComponent(noKk)}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`✅ ${data.message}`, 'success');
            
            setTimeout(() => {
                // Refresh semua tabel
                if (typeof renderPenerimaTable === 'function') {
                    renderPenerimaTable();
                }
                if (typeof renderTabelDistribusi === 'function') {
                    renderTabelDistribusi();
                }
                if (typeof loadDashboardStats === 'function') {
                    loadDashboardStats();
                }
                if (typeof updatePenerimaStats === 'function') {
                    updatePenerimaStats();
                }
                if (typeof updateBadges === 'function') {
                    updateBadges();
                }
            }, 500);
        } else {
            showToast(`❌ ${data.message || 'Gagal menghapus data'}`, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Terjadi kesalahan: ' + error.message, 'error');
    });
}

/**
 * Toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) {
        alert(message);
        return;
    }
    
    toast.textContent = message;
    toast.className = 'toast ' + type;
    toast.style.display = 'block';
    
    clearTimeout(toast._hideTimeout);
    toast._hideTimeout = setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

/**
 * Render tabel penerima dengan tombol hapus
 * Override fungsi yang ada di admin.js
 */
function renderPenerimaTable() {
    const search = document.getElementById('penerima-search')?.value?.toLowerCase() || '';
    const tbody = document.getElementById('imported-table-body');
    const empty = document.getElementById('penerima-empty');
    const count = document.getElementById('imported-count');
    
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text3);">⏳ Memuat data...</td></tr>';
    
    fetch('/admin/api/penerima/list', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:var(--red);">❌ ${data.message || 'Gagal memuat data'}</td></tr>`;
            return;
        }
        
        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = '';
            if (empty) empty.style.display = 'block';
            if (count) count.textContent = '0 data';
            return;
        }
        
        if (empty) empty.style.display = 'none';
        if (count) count.textContent = `${data.data.length} data`;
        
        let filtered = data.data;
        if (search) {
            filtered = filtered.filter(item => 
                (item.no_kk && item.no_kk.includes(search)) ||
                (item.nama_kk && item.nama_kk.toLowerCase().includes(search))
            );
        }
        
        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text3);">Tidak ada data yang cocok</td></tr>';
            return;
        }
        
        let html = '';
        filtered.forEach((item, index) => {
            const status = item.status || 'BELUM AMBIL';
            const statusClass = status === 'SUDAH AMBIL' ? 'status-done' : 'status-pending';
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.no_kk || '-'}</strong></td>
                    <td><strong>${item.nama_kk || '-'}</strong></td>
                    <td>
                        <span class="qr-badge">${item.qr_code || 'P' + String(item.id_penerima || '').padStart(5, '0')}</span>
                    </td>
                    <td>${item.alamat || '-'}</td>
                    <td>${item.no_telp || '-'}</td>
                    <td><span class="status-badge ${statusClass}">${status}</span></td>
                    <td>
                        <button class="btn btn-danger btn-sm" 
                                onclick="deleteWarga('${item.no_kk}', '${item.nama_kk}')"
                                title="Hapus data warga dan distribusi terkait">
                            🗑️ Hapus
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:var(--red);">❌ Gagal memuat data: ${error.message}</td></tr>`;
    });
}

// Jalankan render saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    if (typeof renderPenerimaTable === 'function') {
        renderPenerimaTable();
    }
});
</script>
<!-- ⭐ AKHIR TAMBAHAN JAVASCRIPT -->

<!-- Auto-logout -->
<script>
  (function(){
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let skipAutoLogout = false;

    document.addEventListener('click', function(e){
      const a = e.target.closest('a');
      if (!a || !a.href) return;
      try {
        const u = new URL(a.href, window.location.origin);
        skipAutoLogout = u.pathname.startsWith('/admin');
      } catch(err){ skipAutoLogout = false; }
    }, {capture:true});

    function sendLogout() {
      if (skipAutoLogout) return;
      const url = '/logout';
      if (navigator.sendBeacon) {
        const fd = new FormData(); fd.append('_token', token);
        try { navigator.sendBeacon(url, fd); } catch(e) {}
        return;
      }
      try {
        fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': token }, keepalive: true });
      } catch(e) {}
    }

    window.addEventListener('pagehide', sendLogout);
    window.addEventListener('beforeunload', sendLogout);
  })();
</script>
</body>
</html>