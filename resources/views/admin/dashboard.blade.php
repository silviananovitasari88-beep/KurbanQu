<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>KurbanQu — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>

<link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
</head>
<body>

<!-- ─── TOAST ─────────────────────────────────── -->
<div class="toast" id="toast"></div>

<!-- ─── SIDEBAR OVERLAY ───────────────────────── -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ─── SIDEBAR ───────────────────────────────── -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🐄</div>
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
        <!-- Left: Animal list + QR card -->
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header">
              <div class="card-title">🐾 Data Hewan Kurban</div>
              <button class="btn btn-gold btn-sm" onclick="navTo('hewan',null);openModalHewan()">+ Tambah Hewan</button>
            </div>
            <div class="card-body" id="dash-animal-list" style="padding:12px 16px;"></div>
          </div>

          <!-- QR Distribusi promo card -->
          <div class="card" style="background:linear-gradient(135deg,#2a1f12,#1a1208);border-color:rgba(200,146,42,0.2);">
            <div class="card-body" style="padding:24px;">
              <div style="font-size:10px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">QR DISTRIBUSI</div>
              <div style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;margin-bottom:8px;">Scan & Verifikasi</div>
              <div style="font-size:13px;color:var(--text3);margin-bottom:18px;line-height:1.6;">Sistem scan QR otomatis untuk validasi penerima kurban sesuai workflow distribusi.</div>
              <button class="btn btn-gold btn-lg" onclick="navTo('distribusi',document.querySelector('[onclick*=distribusi]'))">Buka Distribusi QR →</button>
            </div>
          </div>
        </div>

        <!-- Right: Live tracking -->
        <div>
          <div class="card" style="height:fit-content;">
            <div class="card-header">
              <div class="card-title">📍 Live Tracking</div>
              <button class="btn btn-ghost btn-sm" onclick="navTo('tracking',document.querySelector('[onclick*=tracking]'))">Edit →</button>
            </div>
            <div class="card-body" id="dash-tracking"></div>
          </div>

          <!-- Progress card -->
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

      <!-- Filter tabs -->
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
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Pemilik hewan &amp; bagian kurban (bukan daftar login warga)</div>
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
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">Distribusi QR</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Scan & verifikasi penerima daging kurban</div>
        </div>
        <div style="font-size:13px;color:var(--text3);">Terverifikasi: <strong id="dist-count" style="color:var(--green);">0</strong> / <span id="dist-total">0</span></div>
      </div>

      <div class="dash-grid">
        <!-- Scanner -->
        <div>
          <div class="scanner-wrap">
            <div class="scanner-frame">
              <div class="sc-box">
                <div class="sc-corner sc-tl"></div>
                <div class="sc-corner sc-tr"></div>
                <div class="sc-corner sc-bl"></div>
                <div class="sc-corner sc-br"></div>
                <div class="sc-line"></div>
                <div style="position:absolute;inset:20px;border-radius:8px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;">
                  <div style="font-size:12px;color:rgba(255,255,255,0.3);text-align:center;">📷<br>Kamera aktif</div>
                </div>
              </div>
            </div>
            <div style="font-size:14px;font-weight:600;color:var(--text2);">Arahkan QR ke kamera</div>
            <div style="font-size:12px;color:var(--text3);margin-top:4px;">atau cari nama secara manual</div>
          </div>

          <!-- Manual search -->
          <div class="card">
            <div class="card-header"><div class="card-title">🔍 Cari Manual</div></div>
            <div class="card-body">
              <div class="search-box" style="margin-bottom:14px;">
                <span style="color:var(--text3);">🔍</span>
                <input type="text" id="scan-search" placeholder="Nama penerima / hewan..." oninput="renderScanList()"/>
              </div>
              <div id="scan-list"></div>
            </div>
          </div>
        </div>

        <!-- Result + log -->
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

      <!-- Page header -->
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">📋 Tabel Distribusi</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">
            Berdasarkan skema database &nbsp;
            <code style="background:var(--bg3);border:1px solid var(--border2);padding:2px 8px;border-radius:5px;font-size:10px;color:var(--blue);">distribusi</code>
            &nbsp;—&nbsp; id_stok · dowload_qr · warga_no_kk · QR_id_qr · st_pengambilan · mtd_pengambilan
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
          <div id="tabel-summary"></div>
          <button class="btn btn-outline btn-sm" onclick="exportTabelCSV()">⬇ Export CSV</button>
        </div>
      </div>

      <!-- Summary stat chips -->
      <div id="tabel-chips" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;"></div>

      <!-- Filter bar -->
      <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center;">
        <div class="search-box" style="flex:1;min-width:180px;max-width:280px;">
          <span style="color:var(--text3);">🔍</span>
          <input type="text" id="tabel-search" placeholder="Cari nama / no KK..." oninput="renderTabelDistribusi()"/>
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

      <!-- Table card -->
      <div class="card" style="overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="data-table" style="min-width:960px;">
            <thead>
              <tr>
                <th style="width:54px;text-align:center;">id_stok</th>
                <th style="min-width:180px;">warga_no_kk &amp; Nama KK</th>
                <th style="min-width:140px;">QR_id_qr</th>
                <th style="text-align:center;min-width:120px;">dowload_qr</th>
                <th style="min-width:150px;">st_pengambilan</th>
                <th style="text-align:center;min-width:120px;">mtd_pengambilan</th>
                <th style="min-width:90px;">Waktu</th>
                <th style="text-align:center;min-width:110px;">Aksi Admin</th>
              </tr>
            </thead>
            <tbody id="tabel-distribusi-body"></tbody>
          </table>
        </div>
        <div id="tabel-empty" style="display:none;" class="empty-state">
          <div class="empty-ico">📋</div>Tidak ada data yang cocok
        </div>
      </div>

      <!-- Legend -->
      <div style="margin-top:14px;display:flex;gap:16px;flex-wrap:wrap;font-size:11px;color:var(--text3);">
        <span>🟢 = Otomatis via QR Scan</span>
        <span>🟡 = Manual diklik admin</span>
        <span>⬇ = User sudah download QR</span>
        <span>📵 = Belum download QR</span>
      </div>
    </div><!-- /tabel -->

    <!-- ══════════════════════ PENERIMA KURBAN (upload excel → login & QR) ══ -->
    <div class="page" id="pg-upload">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">🎫 Penerima Kurban</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;max-width:520px;line-height:1.6;">
            Unggah daftar penerima daging kurban. Mereka login di aplikasi warga dengan <strong>No KK</strong> + <strong>Nama Kepala Keluarga</strong>, lalu mendapat kode QR.
          </div>
        </div>
        <button class="btn btn-gold" onclick="openModalPenerima()">+ Tambah Manual</button>
      </div>

      <!-- Alur -->
      <div class="flow-steps" style="margin-bottom:20px;">
        <div class="flow-step">
          <div class="flow-step-num">1</div>
          <div><strong>Admin upload Excel/CSV</strong><div class="flow-step-desc">No KK + Nama Kepala Keluarga</div></div>
        </div>
        <div class="flow-step-arrow">→</div>
        <div class="flow-step">
          <div class="flow-step-num">2</div>
          <div><strong>Konfirmasi daftar</strong><div class="flow-step-desc">Sistem buat kode QR unik</div></div>
        </div>
        <div class="flow-step-arrow">→</div>
        <div class="flow-step">
          <div class="flow-step-num">3</div>
          <div><strong>Warga login</strong><div class="flow-step-desc">Halaman depan → tampil QR</div></div>
        </div>
      </div>

      <div id="penerima-stat-chips" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;"></div>

      <div class="dash-grid">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><div class="card-title">📂 Upload Excel / CSV</div></div>
            <div class="card-body">
              <div style="font-size:12px;color:var(--text3);margin-bottom:14px;line-height:1.7;">
                Simpan file Excel sebagai <strong>CSV UTF-8</strong>. Hanya 2 kolom wajib; kolom alamat &amp; telepon opsional.
              </div>
              <div style="background:var(--bg3);border-radius:10px;padding:12px;margin-bottom:14px;font-family:monospace;font-size:11px;color:var(--text2);line-height:1.8;">
                No KK,Nama Kepala Keluarga,Alamat,No Telp<br>
                3273011234567890,Ahmad Hidayat,Kp. Cikaret,0812xxxx<br>
                3273012345678901,Siti Rahmawati,RT 01/02,0857xxxx
              </div>
              <div id="drop-zone" class="drop-zone-penerima"
                onclick="document.getElementById('excel-input').click()"
                ondragover="event.preventDefault();this.classList.add('drag')"
                ondragleave="this.classList.remove('drag')"
                ondrop="handleFileDrop(event)">
                <div style="font-size:32px;margin-bottom:8px;">📊</div>
                <div style="font-size:14px;font-weight:600;color:var(--text2);">Klik atau seret file CSV ke sini</div>
                <div style="font-size:11px;color:var(--text3);margin-top:6px;">.csv disarankan · .xlsx simpan dulu sebagai CSV</div>
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
                <button class="btn btn-ghost" onclick="clearImport()">Bersihkan</button>
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
          <option value="Ya">Ya — Sehat</option>
          <option value="Tidak">Tidak — Tidak Sehat</option>
        </select>
      </div>
      <div class="form-group">
        <label>Cacat? *</label>
        <select id="h-cacat">
          <option value="Tidak">Tidak ada cacat</option>
          <option value="Ada">Ada cacat</option>
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

<script src="{{ asset('js/warga-login.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
