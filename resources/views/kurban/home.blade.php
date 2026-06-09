<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KurbanQu</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/kurban.css') }}" />
</head>
<body>
<div class="app">
   
       HALAMAN DASHBOARD (PANITIA)
  ════════════════════════════════════ -->
  <div id="pg-dashboard" class="page active" style="background:#f5f0e8;">

    <!-- Header with animal count chips -->
    <div class="dash-hdr">
      <div style="position:absolute;width:160px;height:160px;background:#7a5230;border-radius:50%;top:-60px;right:-45px;opacity:0.3;"></div>
      <div style="position:absolute;width:90px;height:90px;background:#7a5230;border-radius:50%;bottom:-30px;right:60px;opacity:0.18;"></div>

      <div class="dash-hdr-logo">
        <img src="{{ asset('assets/img/logo.jpeg') }}" width="55">

        <span>KurbanQu</span>
      </div>
      <div class="dash-hdr-title">Informasi Mudhohi dan hewan qurban</div>

      <div class="animal-count-row">
        <!-- Chip kosong / placeholder -->
        <div class="ac-chip" id="chip-all" onclick="selectAnimalChip('all',this)" style="flex:0.6;">
          <div class="ac-chip-num" style="font-size:16px;">📋</div>
          <div class="ac-chip-lbl">Semua</div>
        </div>
        <div class="ac-chip" id="chip-sapi" onclick="selectAnimalChip('sapi',this)">
          <div class="ac-chip-num" id="cnt-sapi">6</div>
          <div class="ac-chip-lbl">Sapi</div>
        </div>
        <div class="ac-chip selected" id="chip-kambing" onclick="selectAnimalChip('kambing',this)">
          <div class="ac-chip-num" id="cnt-kambing">7</div>
          <div class="ac-chip-lbl">Kambing</div>
        </div>
        <div class="ac-chip" id="chip-domba" onclick="selectAnimalChip('domba',this)">
          <div class="ac-chip-num" id="cnt-domba">8</div>
          <div class="ac-chip-lbl">Domba</div>
        </div>
      </div>
    </div>

    <!-- Animal list panel (dropdown) -->
    <div class="animal-list-panel open" id="animal-panel">
      <div class="animal-list-inner" id="animal-list-inner"></div>
      <div id="see-more-btn" class="see-more-btn" onclick="toggleSeeMore()" style="display:none;">
        <span id="see-more-text">Lihat semua</span>
        <span class="see-more-arrow" id="see-more-arrow">⌄</span>
      </div>
    </div>

    <!-- Tracking Timeline -->
    <div class="scroll-area" style="position:relative;" id="dash-scroll">
      <div style="padding:20px 20px 10px;">
        <div style="font-size:11px;font-weight:700;color:#7a5230;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">
          📍 Status Proses Kurban
        </div>
        <div style="font-size:10px;color:#9a8060;">Live update · Jum'at 6 Jun 2025</div>
      </div>

      <div class="dash-track-wrap" id="dash-timeline">
        <!-- rendered by JS -->
      </div>

      <div style="height:100px;"></div><!-- swipe space -->
    </div>

    <!-- Gradient fade above card -->
    <div class="qr-float-fade"></div>

    <!-- Floating QR CTA Card -->
    <div class="qr-float-card" id="qr-float-card">
      <div class="qr-float-inner" id="qr-float-inner">
        <div class="qr-float-handle"></div>
        <div class="qr-float-label">Selamat Datang 👋</div>
        <div class="qr-float-title">Ambil kupon<br>daging kurban<br>Anda di sini</div>
        <div class="qr-float-sub">Tunjukkan QR code Anda kepada<br>panitia untuk mengambil daging kurban</div>
        <button class="qr-float-btn" onclick="goto('pg-login')">
          <span>🎫</span> Masuk &amp; Ambil QR Saya
        </button>
        <div class="qr-float-hint">Gunakan nomor KK &amp; nama lengkap Anda</div>
        <div class="qr-float-dots">
          <div class="qr-dot active"></div>
          <div class="qr-dot"></div>
          <div class="qr-dot"></div>
        </div>
        <!-- Close / minimize button -->
        <button onclick="dismissQrCard()" style="position:absolute;top:14px;right:16px;background:rgba(61,37,16,0.08);border:none;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;color:#7a5230;">✕</button>
      </div>
    </div>

    <!-- Mini tab shown after dismiss -->
    <div class="qr-tab" id="qr-tab" onclick="showQrCard()">
      🎫 Ambil QR Saya
    </div>


  </div>

  <!-- ════════════════════════════════════
       HALAMAN LOGIN
  ════════════════════════════════════ -->
  <div id="pg-login" class="page">
    <div class="hdr">
      <div class="blob-lg"></div>
      <button class="btn-back" onclick="goto('pg-dashboard')">← Kembali</button>
      <div style="display:flex;align-items:center;gap:13px;position:relative;z-index:1;margin-bottom:18px;">
        <div style="width:46px;height:46px;border-radius:14px;background:#7a5230;overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
          <img src="{{ asset('assets/img/logo.jpeg') }}" width="55">
        </div>
        <div style="font-size:21px;font-weight:800;color:#fff;">KurbanQu</div>
      </div>
      <div style="font-size:24px;font-weight:700;color:#fff;line-height:1.38;position:relative;z-index:1;">
        Masuk sebagai<br>penerima kurban
      </div>
    </div>
    <div style="background:#ede8de;padding:18px 26px;text-align:center;flex-shrink:0;border-bottom:0.5px solid #ddd4c0;">
      <div style="font-size:10.5px;font-weight:700;color:#9a8060;letter-spacing:.8px;text-transform:uppercase;margin-bottom:9px;">QS. Al-Kautsar Ayat 2</div>
      <div style="font-size:28px;color:#3d2510;margin-bottom:9px;font-family:Georgia,serif;line-height:1.5;">فَصَلِّ لِرَبِّكَ وَٱنْحَرْ</div>
      <div style="font-size:12.5px;color:#7a6040;font-style:italic;line-height:1.7;">"Maka dirikanlah salat karena Tuhanmu;<br>dan berqurbanlah."</div>
    </div>
    <div class="scroll-area">
      <div style="padding:22px 20px 36px;">
        <div class="card">
          <div style="font-size:17px;font-weight:800;color:#3d2510;text-align:center;margin-bottom:4px;">Verifikasi data diri</div>
          <div style="font-size:13px;color:#9a8060;text-align:center;margin-bottom:22px;line-height:1.5;">Masukkan <strong>No KK</strong> dan <strong>Nama Kepala Keluarga</strong> sesuai data yang didaftarkan panitia kurban</div>
          <div class="field">
            <label>Nomor Kartu Keluarga <span style="color:#d94f4f;">*</span></label>
            <input type="text" id="inp-nkk" placeholder="Masukkan nomor KK" />
            <div class="err-msg" id="err-nkk">Nomor KK wajib diisi</div>
          </div>
          <div class="field" style="margin-bottom:24px;">
            <label>Nama Kepala Keluarga <span style="color:#d94f4f;">*</span></label>
            <input type="text" id="inp-nama" placeholder="Masukkan nama lengkap" />
            <div class="err-msg" id="err-nama">Nama wajib diisi</div>
          </div>
          <button class="btn-primary" onclick="submitLogin()">Tampilkan QR Saya</button>
          <div style="display:flex;align-items:center;justify-content:center;gap:5px;margin-top:14px;font-size:12px;color:#9a8060;">
            🔒 Data aman &amp; hanya untuk verifikasi
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════
       HALAMAN QR
  ════════════════════════════════════ -->
  <div id="pg-qr" class="page">
    <!-- Dimmed background effect with blur -->
    <div style="position:absolute;inset:0;background:linear-gradient(180deg,#5c3d1e 0%,#3d2510 40%,#2a1a0a 100%);z-index:0;"></div>
    <div style="position:absolute;width:200px;height:200px;background:#e8b84b;border-radius:50%;top:-80px;right:-60px;opacity:0.12;z-index:0;"></div>
    <div style="position:absolute;width:120px;height:120px;background:#e8b84b;border-radius:50%;bottom:200px;left:-40px;opacity:0.08;z-index:0;"></div>

    <!-- Header -->
    <div style="position:relative;z-index:1;padding:52px 22px 20px;">
      <button class="btn-back" onclick="goto('pg-login')" style="color:#d4bfa0;margin-bottom:10px;">← Kembali</button>
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <div>
          <div style="font-size:22px;font-weight:800;color:#fff;">Kupon Kurban</div>
          <div style="font-size:13px;color:#d4bfa0;margin-top:2px;">Tunjukkan ke panitia</div>
        </div>
        <div style="border:1.5px solid #e8b84b;border-radius:20px;padding:6px 14px;font-size:11px;color:#e8b84b;font-weight:700;background:rgba(232,184,75,0.1);">⏳ Belum diambil</div>
      </div>
    </div>

    <!-- QR HERO — eye-catching, centered, shake animation -->
    <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;padding:0 24px 24px;">

      <!-- Call to action label -->
      <div class="qr-hero-badge">
        <span style="font-size:16px;">🎫</span> Ambil Daging Kurban Anda
      </div>

      <!-- Shake card -->
      <div class="qr-shake-card" style="width:100%;background:#fff;border-radius:22px;padding:28px 24px 24px;text-align:center;border:2px solid #e8b84b;">

        <div style="font-size:13px;font-weight:600;color:#9a8060;margin-bottom:18px;text-transform:uppercase;letter-spacing:.8px;">QR Code Anda</div>

        <!-- QR Code visual -->
        <div style="width:180px;height:180px;margin:0 auto 20px;border:3px solid #3d2510;border-radius:16px;display:flex;align-items:center;justify-content:center;position:relative;background:#fff;box-shadow:inset 0 2px 8px rgba(61,37,16,0.08);">
          <div style="position:absolute;width:42px;height:42px;border:4px solid #3d2510;border-radius:7px;top:12px;left:12px;"></div>
          <div style="position:absolute;width:42px;height:42px;border:4px solid #3d2510;border-radius:7px;top:12px;right:12px;"></div>
          <div style="position:absolute;width:42px;height:42px;border:4px solid #3d2510;border-radius:7px;bottom:12px;left:12px;"></div>
          <!-- Inner fills -->
          <div style="position:absolute;width:22px;height:22px;background:#3d2510;border-radius:4px;top:22px;left:22px;"></div>
          <div style="position:absolute;width:22px;height:22px;background:#3d2510;border-radius:4px;top:22px;right:22px;"></div>
          <div style="position:absolute;width:22px;height:22px;background:#3d2510;border-radius:4px;bottom:22px;left:22px;"></div>
          <!-- Center dots pattern -->
          <div style="display:grid;grid-template-columns:repeat(5,10px);gap:3px;">
            <div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div><div style="width:10px;height:10px;background:transparent;border-radius:2px;"></div><div style="width:10px;height:10px;background:#3d2510;border-radius:2px;"></div>
          </div>
        </div>

        <div id="qr-kode" style="font-family:monospace;font-size:15px;font-weight:800;color:#c8922a;letter-spacing:2px;margin-bottom:10px;">P00001</div>
        <div id="qr-nama" style="font-size:18px;font-weight:800;color:#3d2510;">—</div>
        <div id="qr-nkk"  style="font-size:13px;color:#9a8060;margin-top:4px;font-weight:500;">—</div>

        <div style="margin-top:16px;display:flex;align-items:center;justify-content:center;gap:8px;padding:10px 18px;background:#fff8e8;border:1.5px solid #f5d080;border-radius:20px;font-size:12px;color:#854f0b;font-weight:600;">
          <span style="font-size:15px;">🕐</span> Menunggu pengambilan
        </div>

        <!-- Instruction hint -->
        <div style="margin-top:14px;font-size:11px;color:#b0956a;line-height:1.6;">
          Tunjukkan kode ini kepada panitia<br>untuk mengambil daging kurban Anda
        </div>
      </div>

      <button id="download-qr-btn" class="btn-outline" style="margin-top:16px;background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.3);color:#fff;" onclick="downloadMyQr()">⬇&nbsp; Simpan QR ke Galeri</button>
    </div>

    <div class="scroll-area" style="position:relative;z-index:1;background:#f5f0e8;border-radius:28px 28px 0 0;margin-top:16px;padding-top:8px;">
      <div style="width:36px;height:4px;background:#d4c9b0;border-radius:4px;margin:10px auto 16px;"></div>
      <div style="margin:0 20px 24px;">
        <div style="background:#faf6ee;border-radius:18px;padding:18px 20px;border:0.5px solid #e0d5c0;">
          <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:14px;">
            <div style="width:38px;height:38px;border-radius:11px;background:#f0e8d8;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;">📍</div>
            <div>
              <div style="font-size:11px;font-weight:700;color:#9a8060;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">Lokasi Pengambilan</div>
              <div style="font-size:14px;font-weight:500;color:#3d2510;line-height:1.45;">Masjid Al-Ikhlas,<br>Jl. Merdeka No. 12</div>
            </div>
          </div>
          <div style="height:0.5px;background:#e8dfd0;margin-bottom:14px;"></div>
          <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:38px;height:38px;border-radius:11px;background:#f0e8d8;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;">📅</div>
            <div>
              <div style="font-size:11px;font-weight:700;color:#9a8060;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">Waktu Pengambilan</div>
              <div style="font-size:14px;font-weight:500;color:#3d2510;">Jumat, 6 Jun 2025</div>
              <div style="font-size:13px;color:#7a5230;margin-top:2px;font-weight:600;">08.00 – 12.00 WIB</div>
            </div>
          </div>
        </div>
      </div>
    </div>


  </div>

  <!-- ════════════════════════════════════
       HALAMAN DAFTAR MUDHOHI
  ════════════════════════════════════ -->
  <div id="pg-mudhohi" class="page">
    <div class="hdr">
      <div class="blob-lg"></div>
      <div class="blob-sm"></div>
      <button class="btn-back" onclick="goto('pg-dashboard')">← Kembali</button>
      <div class="hdr-title">Daftar Mudhohi</div>
      <div class="hdr-sub">Kurban 1446 H — Masjid Al-Ikhlas</div>
    </div>
    <div class="filter-row" id="filter-row">
      <div class="chip active" onclick="filterMudhohi('Semua', this)">Semua</div>
      <div class="chip" onclick="filterMudhohi('sapi', this)">🐄 Sapi</div>
      <div class="chip" onclick="filterMudhohi('kambing', this)">🐐 Kambing</div>
      <div class="chip" onclick="filterMudhohi('domba', this)">🐑 Domba</div>
    </div>
    <div class="summary-row">
      <div class="sum-card"><div class="sum-num" id="sum-total">-</div><div class="sum-lbl">Total</div></div>
      <div class="sum-card"><div class="sum-num" id="sum-sapi">-</div><div class="sum-lbl">Sapi</div></div>
      <div class="sum-card"><div class="sum-num" id="sum-kambing">-</div><div class="sum-lbl">Kambing</div></div>
      <div class="sum-card"><div class="sum-num" id="sum-domba">-</div><div class="sum-lbl">Domba</div></div>
    </div>
    <div class="scroll-area" id="mudhohi-list"></div>
  </div>

  <!-- ════════════════════════════════════
       HALAMAN DETAIL HEWAN
  ════════════════════════════════════ -->
  <div id="pg-detail" class="page" style="background:#f5f0e8;">
    <div class="detail-hdr">
      <div style="position:absolute;width:120px;height:120px;background:#7a5230;border-radius:50%;top:-40px;right:-30px;opacity:0.3;"></div>
      <button class="btn-back" id="detail-back-btn" onclick="goto('pg-dashboard')">← Kembali</button>
      <div style="position:relative;z-index:1;padding-bottom:18px;">
        <div style="font-size:11px;font-weight:700;color:#d4bfa0;text-transform:uppercase;letter-spacing:1px;" id="detail-id-lbl">ID KAMBING</div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:4px;">
          <div>
            <div style="font-size:20px;font-weight:800;color:#fff;" id="detail-mudhohi-name">Pak AEP</div>
            <div style="font-size:13px;color:#d4bfa0;margin-top:2px;" id="detail-mudhohi-sub">Bin pak aseop</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:10px;font-weight:700;color:#d4bfa0;text-transform:uppercase;letter-spacing:.5px;">NOTELP</div>
            <div style="font-size:12px;color:#e8b84b;font-weight:700;margin-top:2px;" id="detail-notelp">—</div>
            <div style="font-size:10px;color:#d4bfa0;margin-top:3px;" id="detail-req-bagian">REQ BAGIAN</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Photo area -->
    <div class="detail-photo-area" id="detail-photo-area">
      <div class="detail-photo-placeholder" id="detail-photo-placeholder">🐐</div>
    </div>

    <!-- Badges row -->
    <div class="detail-badge-row" id="detail-badges">
      <!-- rendered by JS -->
    </div>

    <!-- Address block -->
    <div style="padding:12px 20px 0;">
      <div style="font-size:11px;color:#9a8060;font-weight:600;margin-bottom:3px;">alamat</div>
      <div style="font-size:13px;color:#3d2510;font-weight:500;" id="detail-alamat">—</div>
    </div>

    <div class="scroll-area" style="margin-top:4px;">
      <!-- Info grid -->
      <div style="margin:10px 20px;background:#fff;border-radius:16px;border:0.5px solid #e0d5c0;overflow:hidden;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
          <div style="padding:12px 16px;border-right:0.5px solid #f0e8d8;border-bottom:0.5px solid #f0e8d8;">
            <div style="font-size:10px;color:#9a8060;margin-bottom:4px;font-weight:600;">JENIS</div>
            <div style="font-size:14px;font-weight:700;color:#3d2510;" id="di-jenis">—</div>
          </div>
          <div style="padding:12px 16px;border-bottom:0.5px solid #f0e8d8;">
            <div style="font-size:10px;color:#9a8060;margin-bottom:4px;font-weight:600;">UMUR</div>
            <div style="font-size:14px;font-weight:700;color:#3d2510;" id="di-umur">—</div>
          </div>
          <div style="padding:12px 16px;border-right:0.5px solid #f0e8d8;border-bottom:0.5px solid #f0e8d8;">
            <div style="font-size:10px;color:#9a8060;margin-bottom:4px;font-weight:600;">KONDISI SEHAT</div>
            <div style="font-size:14px;font-weight:700;color:#3d2510;" id="di-sehat">—</div>
          </div>
          <div style="padding:12px 16px;border-bottom:0.5px solid #f0e8d8;">
            <div style="font-size:10px;color:#9a8060;margin-bottom:4px;font-weight:600;">STATUS SYARIAT</div>
            <div style="font-size:14px;font-weight:700;color:#3d2510;" id="di-syariat">—</div>
          </div>
          <div style="padding:12px 16px;border-right:0.5px solid #f0e8d8;">
            <div style="font-size:10px;color:#9a8060;margin-bottom:4px;font-weight:600;">CACAT</div>
            <div style="font-size:14px;font-weight:700;color:#3d2510;" id="di-cacat">—</div>
          </div>
          <div style="padding:12px 16px;">
            <div style="font-size:10px;color:#9a8060;margin-bottom:4px;font-weight:600;">BERAT EST.</div>
            <div style="font-size:14px;font-weight:700;color:#3d2510;" id="di-berat">—</div>
          </div>
        </div>
      </div>

      <!-- Tracking timeline for this animal -->
      <div class="detail-sec">📍 Status Proses</div>
      <div style="margin:0 20px 12px;background:#fff;border-radius:16px;border:0.5px solid #e0d5c0;padding:16px;" id="detail-track">
        <!-- rendered by JS -->
      </div>

      <!-- Mudhohi attached -->
      <div class="detail-sec" id="detail-mudhohi-sec">👥 Mudhohi</div>
      <div style="margin:0 20px 30px;background:#fff;border-radius:16px;border:0.5px solid #e0d5c0;overflow:hidden;" id="detail-mudhohi-list">
        <!-- rendered by JS -->
      </div>
    </div>
  </div>


</div>

<script src="{{ asset('js/warga-login.js') }}"></script>
<script src="{{ asset('js/kurban.js') }}"></script>
</body>
</html>