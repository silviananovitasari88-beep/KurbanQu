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
    <div class="nav-section-label" style="margin-top:8px;">Data</div>
    <div class="nav-item" onclick="navTo('upload',this)">
      <span class="nav-ico">📤</span> Upload Excel
      <span class="nav-badge" id="badge-upload" style="display:none;background:var(--green);">✓</span>
    </div>
    <div class="nav-item" onclick="navTo('tabel',this)">
      <span class="nav-ico">📋</span> Tabel Distribusi
    </div>
    <div class="nav-item" onclick="navTo('warga',this)">
      <span class="nav-ico">🏘️</span> Data Warga
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
          <div class="stat-num" id="s-hewan">21</div>
          <div class="stat-sub">Sapi, Kambing, Domba</div>
          <div class="stat-icon">🐾</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Mudhohi</div>
          <div class="stat-num" id="s-mudhohi">35</div>
          <div class="stat-sub">Penerima daging kurban</div>
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
          <div class="stat-num" id="s-qr">35</div>
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
              <th>ID</th>
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
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Daftar lengkap penerima kurban</div>
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
              <th>Nama / No KK</th>
              <th>Tipe</th>
              <th>Hewan</th>
              <th>Bagian</th>
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
        <div style="font-size:13px;color:var(--text3);">Terverifikasi: <strong id="dist-count" style="color:var(--green);">0</strong> / <span id="dist-total">35</span></div>
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

    <!-- ══════════════════════ UPLOAD EXCEL PAGE ══ -->
    <div class="page" id="pg-upload">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">📤 Upload Data Excel</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Import daftar warga penerima kurban + pembeda Mudhohi / Penerima</div>
        </div>
      </div>

      <!-- Format panduan -->
      <div class="card" style="margin-bottom:18px;border-color:rgba(200,146,42,0.2);background:linear-gradient(135deg,#1e1810,#181410);">
        <div class="card-header"><div class="card-title">📋 Format Kolom Excel yang Diperlukan</div></div>
        <div class="card-body">
          <div style="overflow-x:auto;">
            <table class="data-table" style="min-width:700px;">
              <thead>
                <tr>
                  <th>No KK <span style="color:var(--red);">*</span></th>
                  <th>Nama KK <span style="color:var(--red);">*</span></th>
                  <th>Tipe <span style="color:var(--red);">*</span></th>
                  <th>Hewan</th>
                  <th>Bagian</th>
                  <th>Alamat</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><code style="font-size:11px;color:var(--blue);">3273011234567890</code></td>
                  <td>Ahmad Hidayat</td>
                  <td><span style="background:rgba(200,146,42,0.15);color:var(--gold2);border:1px solid rgba(200,146,42,0.3);border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;">mudhohi</span></td>
                  <td>Sapi Putih No.01</td>
                  <td>1/7 Sapi</td>
                  <td>Kp. Cikaret RT 02</td>
                </tr>
                <tr>
                  <td><code style="font-size:11px;color:var(--blue);">3273012345678901</code></td>
                  <td>Siti Rahmawati</td>
                  <td><span style="background:rgba(78,203,113,0.12);color:var(--green);border:1px solid rgba(78,203,113,0.25);border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;">penerima</span></td>
                  <td>Sapi Putih No.01</td>
                  <td>2/7 Sapi</td>
                  <td>RT 01/02</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div style="margin-top:14px;font-size:12px;color:var(--text3);line-height:1.8;">
            <strong style="color:var(--gold2);">Kolom Tipe:</strong> isi dengan <code style="background:var(--bg4);padding:1px 6px;border-radius:4px;color:var(--gold2);">mudhohi</code> untuk pemilik hewan kurban,
            atau <code style="background:var(--bg4);padding:1px 6px;border-radius:4px;color:var(--green);">penerima</code> untuk penerima daging biasa.
            Jika kolom tipe dikosongkan, sistem akan otomatis menganggap sebagai <strong style="color:var(--green);">penerima</strong>.
          </div>
        </div>
      </div>

      <!-- Upload area -->
      <div class="dash-grid">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><div class="card-title">📂 Import File Excel / CSV</div></div>
            <div class="card-body">
              <div id="drop-zone" style="border:2px dashed var(--border2);border-radius:12px;padding:36px;text-align:center;cursor:pointer;transition:all .2s;margin-bottom:16px;"
                onclick="document.getElementById('excel-input').click()"
                ondragover="event.preventDefault();this.style.borderColor='var(--gold)';this.style.background='var(--gold-dim)'"
                ondragleave="this.style.borderColor='var(--border2)';this.style.background=''"
                ondrop="handleFileDrop(event)">
                <div style="font-size:36px;margin-bottom:10px;">📊</div>
                <div style="font-size:14px;font-weight:600;color:var(--text2);margin-bottom:6px;">Klik atau drag & drop file di sini</div>
                <div style="font-size:12px;color:var(--text3);">Format: .xlsx, .xls, .csv — Maks. 10MB</div>
              </div>
              <input type="file" id="excel-input" accept=".xlsx,.xls,.csv" style="display:none;" onchange="handleFileSelect(this)"/>

              <!-- Manual input alternatif -->
              <div style="text-align:center;color:var(--text3);font-size:12px;margin-bottom:16px;">— atau input manual paste CSV —</div>
              <textarea id="csv-paste" rows="6" style="width:100%;background:var(--bg3);border:1px solid var(--border2);border-radius:10px;padding:12px;font-size:11px;color:var(--text2);font-family:monospace;outline:none;resize:vertical;line-height:1.6;" placeholder="Paste data CSV di sini:
No KK,Nama KK,Tipe,Hewan,Bagian,Alamat
3273011234567890,Ahmad Hidayat,mudhohi,Sapi Putih No.01,1/7,Kp. Cikaret
3273012345678901,Siti Rahmawati,penerima,Sapi Putih No.01,2/7,RT 01/02"></textarea>
              <div style="display:flex;gap:10px;margin-top:12px;">
                <button class="btn btn-gold" style="flex:1;" onclick="parseCSVPaste()">🔍 Proses & Preview</button>
                <button class="btn btn-ghost" onclick="clearImport()">✕ Bersihkan</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Preview panel -->
        <div>
          <div class="card">
            <div class="card-header">
              <div class="card-title">👁 Preview Data</div>
              <div id="preview-stats" style="font-size:12px;color:var(--text3);"></div>
            </div>
            <div id="preview-content" style="max-height:420px;overflow-y:auto;">
              <div class="empty-state"><div class="empty-ico">📋</div>Data akan tampil di sini setelah diproses</div>
            </div>
            <div id="preview-actions" style="padding:16px;border-top:1px solid var(--border);display:none;">
              <button class="btn btn-gold btn-lg" style="width:100%;" onclick="importConfirm()">✓ Konfirmasi Import ke Sistem</button>
              <div style="font-size:11px;color:var(--text3);text-align:center;margin-top:8px;">Data yang diimport akan menggantikan/menambah daftar warga yang ada</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Daftar warga terimpor -->
      <div class="card" style="margin-top:18px;" id="imported-list-card" style="display:none;">
        <div class="card-header">
          <div class="card-title">✅ Daftar Warga Terimpor</div>
          <div style="display:flex;gap:8px;align-items:center;">
            <span id="imported-count" style="font-size:12px;color:var(--text3);"></span>
            <button class="btn btn-outline btn-sm" onclick="exportImportedCSV()">⬇ Export CSV</button>
          </div>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table" style="min-width:700px;">
            <thead>
              <tr>
                <th>#</th>
                <th>No KK</th>
                <th>Nama KK</th>
                <th>Tipe</th>
                <th>Hewan</th>
                <th>Bagian</th>
                <th>Alamat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="imported-table-body"></tbody>
          </table>
        </div>
      </div>
    </div><!-- /upload -->

    <!-- ══════════════════════ WARGA PAGE ══ -->
    <div class="page" id="pg-warga">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;">Data Warga</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">Informasi lengkap per warga / penerima</div>
        </div>
        <button class="btn btn-gold" onclick="openModalMudhohi()">+ Tambah Warga</button>
      </div>

      <div style="display:flex;gap:8px;margin-bottom:16px;">
        <div class="search-box" style="flex:1;max-width:300px;">
          <span style="color:var(--text3);">🔍</span>
          <input type="text" id="warga-search" placeholder="Cari nama / alamat..." oninput="renderWargaList()"/>
        </div>
      </div>

      <div class="dash-grid" id="warga-grid"></div>
    </div><!-- /warga -->

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
    <div class="form-group">
      <label>Alamat / Kandang</label>
      <input type="text" id="h-alamat" placeholder="Alamat kandang atau pemilik"/>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>No. Telp Pemilik</label>
        <input type="text" id="h-telp" placeholder="08xx-xxxx-xxxx"/>
      </div>
      <div class="form-group">
        <label>Kondisi / Catatan</label>
        <input type="text" id="h-catatan" placeholder="Sehat, ada cacat, dll"/>
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
        <label>No. KK</label>
        <input type="text" id="m-nkk" placeholder="16 digit nomor KK"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" id="m-telp" placeholder="08xx-xxxx-xxxx"/>
      </div>
      <div class="form-group">
        <label>Hewan Kurban *</label>
        <select id="m-hewan"></select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Bagian</label>
        <input type="text" id="m-bagian" placeholder="Contoh: 1/7 atau kurban penuh"/>
      </div>
      <div class="form-group">
        <label>Alamat</label>
        <input type="text" id="m-alamat" placeholder="RT/RW, Desa"/>
      </div>
    </div>
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

<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
