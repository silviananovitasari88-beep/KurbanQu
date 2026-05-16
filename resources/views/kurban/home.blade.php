<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KurbanQu</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: #c8bfb0;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      padding: 20px 0;
    }

    .app {
      width: 412px;
      height: 917px;
      background: #f5f0e8;
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    }

    /* PAGE SYSTEM */
    .page { display: none; flex-direction: column; width: 100%; height: 917px; position: absolute; top: 0; left: 0; }
    .page.active { display: flex; }

    /* BLOBS */
    .blob-lg { position: absolute; width: 150px; height: 150px; background: #7a5230; border-radius: 50%; top: -48px; right: -38px; opacity: 0.42; }
    .blob-sm { position: absolute; width: 85px; height: 85px; background: #7a5230; border-radius: 50%; bottom: -28px; right: 68px; opacity: 0.26; }

    /* HEADER */
    .hdr { background: #5c3d1e; padding: 20px 22px 24px; position: relative; overflow: hidden; flex-shrink: 0; }
    .hdr-title { font-size: 23px; font-weight: 700; color: #fff; position: relative; z-index: 1; }
    .hdr-sub   { font-size: 13px; color: #d4bfa0; margin-top: 3px; position: relative; z-index: 1; }

    /* BACK BTN */
    .btn-back { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #e8d9c0; background: none; border: none; cursor: pointer; margin-bottom: 14px; position: relative; z-index: 1; padding: 0; }

    /* SCROLL */
    .scroll-area { flex: 1; overflow-y: auto; }
    .scroll-area::-webkit-scrollbar { width: 3px; }
    .scroll-area::-webkit-scrollbar-thumb { background: #c8b89a; border-radius: 4px; }

    /* INPUTS */
    .field { margin-bottom: 18px; }
    .field label { display: block; font-size: 13px; color: #3d2510; margin-bottom: 7px; font-weight: 600; }
    .field input {
      width: 100%; padding: 13px 15px;
      border-radius: 12px; border: 1.5px solid #e0d5c0;
      font-size: 14px; background: #faf8f4; outline: none;
      transition: border-color .2s;
    }
    .field input:focus { border-color: #5c3d1e; }
    .field input.error { border-color: #d94f4f; }
    .field .err-msg { font-size: 11px; color: #d94f4f; margin-top: 5px; display: none; }
    .field .err-msg.show { display: block; }

    /* BUTTONS */
    .btn-primary { width: 100%; padding: 15px; background: #3d2510; border: none; border-radius: 14px; font-size: 15px; font-weight: 700; color: #fff; cursor: pointer; transition: opacity .15s; }
    .btn-primary:active { opacity: .85; }
    .btn-outline  { width: 100%; padding: 15px; background: #fff; border: 1.5px solid #3d2510; border-radius: 14px; font-size: 15px; font-weight: 700; color: #3d2510; cursor: pointer; transition: background .15s; }
    .btn-outline:active { background: #f0e8d8; }

    /* CARD */
    .card { background: #fff; border-radius: 20px; border: 0.5px solid #e0d5c0; padding: 22px; }

    /* TRACKING */
    .track-wrap { display: flex; flex-direction: column; gap: 0; }
    .track-item { display: flex; align-items: flex-start; gap: 14px; }
    .track-left { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
    .track-dot  { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; border: 2px solid; }
    .track-dot.done    { background: #5c3d1e; border-color: #5c3d1e; color: #fff; }
    .track-dot.active  { background: #fff; border-color: #5c3d1e; color: #5c3d1e; position: relative; }
    .track-dot.active::after { content:''; position:absolute; width:10px; height:10px; background:#5c3d1e; border-radius:50%; }
    .track-dot.pending { background: #fff; border-color: #d4c9b0; color: #c0b090; }
    .track-line { width: 2px; height: 28px; background: #e0d5c0; margin: 2px 0; }
    .track-line.done { background: #5c3d1e; }
    .track-body { padding-bottom: 20px; flex: 1; }
    .track-label  { font-size: 13px; font-weight: 600; color: #3d2510; margin-top: 4px; }
    .track-label.pending { color: #b0956a; }
    .track-desc   { font-size: 11px; color: #9a8060; margin-top: 2px; }
    .track-badge  { display: inline-flex; align-items: center; gap: 4px; margin-top: 5px; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; }
    .badge-done   { background: #eaf3de; color: #3b6d11; }
    .badge-active { background: #fff3e0; color: #b85c00; }

    /* MUDHOHI CARDS */
    .animal-section { margin-bottom: 4px; }
    .sec-lbl { font-size: 12px; font-weight: 700; color: #7a5230; padding: 8px 20px 6px; text-transform: uppercase; letter-spacing: .5px; }
    .group-card { margin: 0 20px 10px; background: #fff; border-radius: 16px; overflow: hidden; border: 0.5px solid #e0d5c0; }
    .group-hdr  { background: #f0e8d8; padding: 11px 16px; display: flex; align-items: center; gap: 12px; border-bottom: 0.5px solid #e0d5c0; }
    .animal-ico { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
    .group-name { font-size: 13px; font-weight: 700; color: #3d2510; }
    .group-meta { font-size: 11px; color: #9a8060; margin-top: 1px; }
    .grp-count  { margin-left: auto; background: #5c3d1e; color: #f5f0e8; font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; flex-shrink: 0; }
    .mudhohi-scroll { max-height: 176px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #c8b89a transparent; }
    .mudhohi-scroll::-webkit-scrollbar { width: 3px; }
    .mudhohi-scroll::-webkit-scrollbar-thumb { background: #c8b89a; border-radius: 4px; }
    .mudhohi-row { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-bottom: 0.5px solid #f0e8d8; }
    .mudhohi-row:last-child { border-bottom: none; }
    .avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
    .m-name { font-size: 13px; font-weight: 600; color: #3d2510; }
    .m-sub  { font-size: 11px; color: #9a8060; margin-top: 1px; }
    .scroll-hint { text-align: center; font-size: 10px; color: #b0956a; padding: 5px 0 7px; background: #faf6ee; border-top: 0.5px solid #f0e8d8; }

    /* FILTER CHIPS */
    .filter-row { display: flex; gap: 8px; padding: 12px 20px 8px; overflow-x: auto; scrollbar-width: none; flex-shrink: 0; }
    .filter-row::-webkit-scrollbar { display: none; }
    .chip { flex-shrink: 0; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; cursor: pointer; border: 1.5px solid #c8b89a; background: #fff; color: #5c3d1e; }
    .chip.active { background: #5c3d1e; color: #f5f0e8; border-color: #5c3d1e; }

    /* SUMMARY ROW */
    .summary-row { display: flex; gap: 8px; padding: 4px 20px 10px; flex-shrink: 0; }
    .sum-card { flex: 1; background: #fff; border-radius: 12px; padding: 9px 10px; border: 0.5px solid #e0d5c0; text-align: center; }
    .sum-num { font-size: 19px; font-weight: 700; color: #5c3d1e; }
    .sum-lbl { font-size: 10px; color: #9a8060; margin-top: 1px; }
  </style>
</head>
<body>
<div class="app">

  <div id="pg-dashboard" class="page active" style="background:#f5f0e8;">

    <div style="position:relative;height:210px;overflow:hidden;flex-shrink:0;">
      <div style="position:absolute;width:190px;height:190px;background:#d4bfa0;border-radius:50%;top:-65px;right:-45px;opacity:0.32;"></div>
      <div style="position:absolute;width:115px;height:115px;background:#c4aa88;border-radius:50%;top:18px;right:58px;opacity:0.2;"></div>
      <div style="position:absolute;width:75px;height:75px;background:#b89a70;border-radius:50%;top:-22px;left:-22px;opacity:0.16;"></div>
      <div style="position:absolute;bottom:22px;left:50%;transform:translateX(-50%);">
        <div style="width:92px;height:92px;border-radius:26px;background:#5c3d1e;overflow:hidden;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 28px rgba(92,61,30,0.3);">
          <img src="logo.jpg" alt="Logo KurbanQu" style="width:100%;height:100%;object-fit:cover;"
            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" />
          <div style="display:none;width:100%;height:100%;align-items:center;justify-content:center;font-size:42px;">🐐</div>
        </div>
      </div>
    </div>

    <div style="text-align:center;padding:0 28px;flex-shrink:0;">
      <div style="font-size:28px;font-weight:700;color:#3d2510;letter-spacing:-.5px;">KurbanQu</div>
      <div style="font-size:13px;color:#9a8060;margin-top:5px;">Platform Kurban Digital 1446 H</div>
    </div>

    <div style="margin:20px 20px 0;">
      <div class="card" style="text-align:center;">
        <div style="font-size:11px;font-weight:700;color:#7a5230;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">Selamat Datang</div>
        <div style="font-size:27px;font-weight:700;color:#5c3d1e;line-height:1.28;margin-bottom:13px;">
          Ambil kupon<br>daging kurban<br>Anda di sini
        </div>
        <div style="font-size:13.5px;color:#9a8060;line-height:1.65;">
          Tunjukkan QR code Anda kepada panitia<br>untuk mengambil daging kurban
        </div>
      </div>
    </div>

    <div style="padding:18px 20px 0;">
      <button class="btn-outline" onclick="goto('pg-login')">→&nbsp; Masuk &amp; Ambil QR Saya</button>
    </div>

    <div style="margin:16px 20px 0;">
      <div onclick="goto('pg-mudhohi')"
        style="display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;background:#f0e8d8;border-radius:14px;cursor:pointer;border:0.5px solid #d4c4a8;">
        <span style="font-size:18px;">🐄</span>
        <div>
          <div style="font-size:13px;font-weight:700;color:#5c3d1e;">Lihat Daftar Mudhohi</div>
          <div style="font-size:11px;color:#9a8060;margin-top:1px;">Siapa saja yang berkurban tahun ini</div>
        </div>
        <span style="margin-left:auto;font-size:16px;color:#9a8060;">›</span>
      </div>
    </div>

    <div style="text-align:center;margin-top:14px;font-size:12px;color:#9a8060;">
      Gunakan nomor KK &amp; nama lengkap Anda
    </div>

    <div style="display:flex;justify-content:center;gap:7px;margin-top:auto;padding-bottom:36px;">
      <div style="width:10px;height:10px;border-radius:50%;background:#5c3d1e;"></div>
      <div style="width:8px;height:8px;border-radius:50%;background:#c8b89a;margin-top:1px;"></div>
      <div style="width:8px;height:8px;border-radius:50%;background:#c8b89a;margin-top:1px;"></div>
    </div>
  </div>

  <div id="pg-login" class="page">

    <div class="hdr">
      <div class="blob-lg"></div>
      <button class="btn-back" onclick="goto('pg-dashboard')">← Kembali</button>
      <div style="display:flex;align-items:center;gap:13px;position:relative;z-index:1;margin-bottom:18px;">
        <div style="width:46px;height:46px;border-radius:14px;background:#7a5230;overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
          <img src="logo.jpg" alt="logo" style="width:100%;height:100%;object-fit:cover;"
            onerror="this.style.display='none';this.nextElementSibling.style.display='block';" />
          <span style="display:none;font-size:24px;">🐐</span>
        </div>
        <div style="font-size:21px;font-weight:700;color:#fff;letter-spacing:-.2px;">KurbanQu</div>
      </div>
      <div style="font-size:24px;font-weight:600;color:#fff;line-height:1.38;position:relative;z-index:1;">
        Masuk sebagai<br>penerima kurban
      </div>
    </div>

    <div style="background:#ede8de;padding:18px 26px;text-align:center;flex-shrink:0;border-bottom:0.5px solid #ddd4c0;">
      <div style="font-size:10.5px;font-weight:700;color:#9a8060;letter-spacing:.8px;text-transform:uppercase;margin-bottom:9px;">QS. Al-Kautsar Ayat 2</div>
      <div style="font-size:28px;color:#3d2510;margin-bottom:9px;font-family:Georgia,serif;line-height:1.5;">فَصَلِّ لِرَبِّكَ وَٱنْحَرْ</div>
      <div style="font-size:12.5px;color:#7a6040;font-style:italic;line-height:1.7;">
        "Maka dirikanlah salat karena Tuhanmu;<br>dan berqurbanlah."
      </div>
    </div>

    <div class="scroll-area">
      <div style="padding:22px 20px 36px;">
        <div class="card">
          <div style="font-size:17px;font-weight:700;color:#3d2510;text-align:center;margin-bottom:4px;">Verifikasi data diri</div>
          <div style="font-size:13px;color:#9a8060;text-align:center;margin-bottom:22px;line-height:1.5;">Pastikan data sesuai KK yang terdaftar</div>

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

  <div id="pg-qr" class="page">

    <div class="hdr">
      <div class="blob-lg"></div>
      <div class="blob-sm"></div>
      <button class="btn-back" onclick="goto('pg-login')">← Kembali</button>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;position:relative;z-index:1;">
        <div>
          <div class="hdr-title">Kupon Kurban</div>
          <div class="hdr-sub">Tunjukkan ke panitia</div>
        </div>
        <div style="border:1.5px solid #e8b84b;border-radius:20px;padding:6px 16px;font-size:12px;color:#e8b84b;font-weight:600;">
          Belum diambil
        </div>
      </div>
    </div>

    <div class="scroll-area" style="padding-bottom:24px;">

      <div style="margin:16px 20px 12px;">
        <div class="card" style="text-align:center;">
          <div style="font-size:14px;font-weight:700;color:#3d2510;margin-bottom:16px;">QR Code Anda</div>

          <div style="width:160px;height:160px;margin:0 auto 16px;border:2.5px solid #3d2510;border-radius:14px;display:flex;align-items:center;justify-content:center;position:relative;background:#fff;">
            <div style="position:absolute;width:36px;height:36px;border:3.5px solid #3d2510;border-radius:6px;top:11px;left:11px;"></div>
            <div style="position:absolute;width:36px;height:36px;border:3.5px solid #3d2510;border-radius:6px;top:11px;right:11px;"></div>
            <div style="position:absolute;width:36px;height:36px;border:3.5px solid #3d2510;border-radius:6px;bottom:11px;left:11px;"></div>
            <div style="position:absolute;width:14px;height:14px;background:#3d2510;border-radius:3px;bottom:24px;right:24px;"></div>
            <div style="font-size:11px;color:#b0956a;z-index:1;">QR Code</div>
          </div>

          <div id="qr-nama" style="font-size:17px;font-weight:700;color:#3d2510;">Silviana Novita Sari</div>
          <div id="qr-nkk"  style="font-size:13px;color:#9a8060;margin-top:4px;">3273010101234567</div>

          <div style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:8px 18px;border:1.5px solid #e0d5c0;border-radius:20px;font-size:12px;color:#9a8060;">
            🕐 Menunggu pengambilan
          </div>
        </div>
      </div>

      <div style="margin:0 20px 14px;">
        <div class="card">
          <div style="font-size:14px;font-weight:700;color:#3d2510;margin-bottom:16px;">Status Kurban</div>
          <div class="track-wrap">

            <div class="track-item">
              <div class="track-left">
                <div class="track-dot done">✓</div>
                <div class="track-line done"></div>
              </div>
              <div class="track-body">
                <div class="track-label">Sedang Disembelih</div>
                <div class="track-desc">Hewan kurban sedang dalam proses penyembelihan</div>
                <div class="track-badge badge-done">✓ Selesai</div>
              </div>
            </div>

            <div class="track-item">
              <div class="track-left">
                <div class="track-dot active"></div>
                <div class="track-line"></div>
              </div>
              <div class="track-body">
                <div class="track-label">Sedang Dipotong</div>
                <div class="track-desc">Daging sedang dibersihkan dan dipotong</div>
                <div class="track-badge badge-active">⏳ Sedang berjalan</div>
              </div>
            </div>

            <div class="track-item">
              <div class="track-left">
                <div class="track-dot pending">3</div>
                <div class="track-line"></div>
              </div>
              <div class="track-body">
                <div class="track-label pending">Sedang Dipacking</div>
                <div class="track-desc">Daging dikemas sesuai bagian masing-masing</div>
              </div>
            </div>

            <div class="track-item">
              <div class="track-left">
                <div class="track-dot pending">4</div>
              </div>
              <div class="track-body" style="padding-bottom:0;">
                <div class="track-label pending">Siap Dibagikan</div>
                <div class="track-desc">Daging siap diambil di lokasi pengambilan</div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div style="margin:0 20px 16px;">
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

      <div style="padding:0 20px;">
        <button class="btn-outline">⬇&nbsp; Simpan QR ke Galeri</button>
      </div>

    </div>
  </div>

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

</div><script>
// ── DATA ──────────────────────────────────────────────────────
const AV = {
  brown:  { bg:"#f0e8d8", color:"#7a5230" },
  green:  { bg:"#eaf3de", color:"#3b6d11" },
  amber:  { bg:"#faeeda", color:"#854f0b" },
  purple: { bg:"#eeedfe", color:"#534ab7" },
};

const DATA = {
  sapi: [
    { id:"S01", label:"Sapi Putih — No. 01", meta:"7 orang patungan · ±25 kg/orang",
      members:[
        {i:"AH",nama:"Ahmad Hidayat",    bagian:"1/7",warna:"brown"},
        {i:"SR",nama:"Siti Rahmawati",   bagian:"2/7",warna:"green"},
        {i:"BU",nama:"Budi Utomo",       bagian:"3/7",warna:"amber"},
        {i:"RN",nama:"Rina Nuraini",     bagian:"4/7",warna:"purple"},
        {i:"MS",nama:"Maman Suparman",   bagian:"5/7",warna:"brown"},
        {i:"DF",nama:"Dewi Fitriani",    bagian:"6/7",warna:"green"},
        {i:"YP",nama:"Yusuf Pratama",    bagian:"7/7",warna:"amber"},
      ]},
    { id:"S02", label:"Sapi Hitam — No. 02", meta:"7 orang patungan · ±25 kg/orang",
      members:[
        {i:"HM",nama:"Hendra Maulana",   bagian:"1/7",warna:"amber"},
        {i:"NR",nama:"Nurul Rizki",      bagian:"2/7",warna:"purple"},
        {i:"AS",nama:"Agus Santoso",     bagian:"3/7",warna:"brown"},
        {i:"LW",nama:"Lilis Wulandari",  bagian:"4/7",warna:"green"},
        {i:"FZ",nama:"Fajar Zulkifli",   bagian:"5/7",warna:"amber"},
        {i:"TH",nama:"Tini Hartati",     bagian:"6/7",warna:"brown"},
        {i:"RP",nama:"Rizal Permana",    bagian:"7/7",warna:"purple"},
      ]},
  ],
  kambing: [
    {id:"K04",label:"Kambing — No. 04",meta:"1 orang · kurban penuh",i:"DN",nama:"Drs. Haji Nurdian",  warna:"amber"},
    {id:"K05",label:"Kambing — No. 05",meta:"1 orang · kurban penuh",i:"FH",nama:"Fitri Handayani",    warna:"brown"},
    {id:"K06",label:"Kambing — No. 06",meta:"1 orang · kurban penuh",i:"ZA",nama:"Zainal Abidin",      warna:"green"},
  ],
  domba: [
    {id:"D02",label:"Domba — No. 02",meta:"1 orang · kurban penuh",i:"MP",nama:"Muhamad Prayogo",warna:"purple"},
    {id:"D03",label:"Domba — No. 03",meta:"1 orang · kurban penuh",i:"IK",nama:"Ibu Komariah",  warna:"brown"},
  ],
};

// ── NAVIGASI ──────────────────────────────────────────────────
function goto(id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

// ── LOGIN + VALIDASI ──────────────────────────────────────────
function submitLogin() {
  const nkkEl  = document.getElementById('inp-nkk');
  const namaEl = document.getElementById('inp-nama');
  const errNkk  = document.getElementById('err-nkk');
  const errNama = document.getElementById('err-nama');

  let valid = true;

  if (!nkkEl.value.trim()) {
    nkkEl.classList.add('error');
    errNkk.classList.add('show');
    valid = false;
  } else {
    nkkEl.classList.remove('error');
    errNkk.classList.remove('show');
  }

  if (!namaEl.value.trim()) {
    namaEl.classList.add('error');
    errNama.classList.add('show');
    valid = false;
  } else {
    namaEl.classList.remove('error');
    errNama.classList.remove('show');
  }

  if (!valid) return;

  document.getElementById('qr-nama').textContent = namaEl.value.trim();
  document.getElementById('qr-nkk').textContent  = nkkEl.value.trim();
  goto('pg-qr');
}

// hapus error saat user mulai ngetik
['inp-nkk','inp-nama'].forEach(id => {
  document.getElementById(id).addEventListener('input', function() {
    this.classList.remove('error');
    document.getElementById('err-' + id.split('-')[1]).classList.remove('show');
  });
});

// ── RENDER MUDHOHI ────────────────────────────────────────────
function av(inisial, warna) {
  const c = AV[warna] || AV.brown;
  return `<div class="avatar" style="background:${c.bg};color:${c.color};">${inisial}</div>`;
}

function groupHdr(emoji, bg, label, meta, count) {
  return `<div class="group-hdr">
    <div class="animal-ico" style="background:${bg};">${emoji}</div>
    <div><div class="group-name">${label}</div><div class="group-meta">${meta}</div></div>
    <div class="grp-count">${count}</div>
  </div>`;
}

function mudhohiRow(m, sub) {
  return `<div class="mudhohi-row">
    ${av(m.i, m.warna)}
    <div><div class="m-name">${m.nama}</div><div class="m-sub">${sub}</div></div>
  </div>`;
}

function updateSummary(filter) {
  const totalSapi    = DATA.sapi.reduce((a,s) => a + s.members.length, 0);
  const totalKambing = DATA.kambing.length;
  const totalDomba   = DATA.domba.length;

  if (filter === 'Semua') {
    document.getElementById('sum-total').textContent   = totalSapi + totalKambing + totalDomba;
    document.getElementById('sum-sapi').textContent    = totalSapi;
    document.getElementById('sum-kambing').textContent = totalKambing;
    document.getElementById('sum-domba').textContent   = totalDomba;
  } else if (filter === 'sapi') {
    document.getElementById('sum-total').textContent   = totalSapi;
    document.getElementById('sum-sapi').textContent    = DATA.sapi.length + ' grp';
    document.getElementById('sum-kambing').textContent = '-';
    document.getElementById('sum-domba').textContent   = '-';
  } else if (filter === 'kambing') {
    document.getElementById('sum-total').textContent   = totalKambing;
    document.getElementById('sum-sapi').textContent    = '-';
    document.getElementById('sum-kambing').textContent = totalKambing;
    document.getElementById('sum-domba').textContent   = '-';
  } else if (filter === 'domba') {
    document.getElementById('sum-total').textContent   = totalDomba;
    document.getElementById('sum-sapi').textContent    = '-';
    document.getElementById('sum-kambing').textContent = '-';
    document.getElementById('sum-domba').textContent   = totalDomba;
  }
}

function renderMudhohi(filter) {
  updateSummary(filter);
  const showSapi    = filter === 'Semua' || filter === 'sapi';
  const showKambing = filter === 'Semua' || filter === 'kambing';
  const showDomba   = filter === 'Semua' || filter === 'domba';
  let html = '';

  if (showSapi) {
    html += `<div class="sec-lbl">🐄 Sapi Patungan</div>`;
    DATA.sapi.forEach(item => {
      const rows = item.members.map(m => mudhohiRow(m, `Bagian ${m.bagian}`)).join('');
      const hint = item.members.length > 4 ? `<div class="scroll-hint">↕ Geser untuk lihat semua</div>` : '';
      html += `<div class="group-card">
        ${groupHdr('🐄','#e8f3de', item.label, item.meta, item.members.length + ' org')}
        <div class="mudhohi-scroll">${rows}</div>${hint}
      </div>`;
    });
  }

  if (showKambing) {
    html += `<div class="sec-lbl">🐐 Kambing</div>`;
    DATA.kambing.forEach(item => {
      html += `<div class="group-card">
        ${groupHdr('🐐','#faeeda', item.label, item.meta, '1 org')}
        ${mudhohiRow(item, 'Kurban penuh')}
      </div>`;
    });
  }

  if (showDomba) {
    html += `<div class="sec-lbl">🐑 Domba</div>`;
    DATA.domba.forEach(item => {
      html += `<div class="group-card">
        ${groupHdr('🐑','#eeedfe', item.label, item.meta, '1 org')}
        ${mudhohiRow(item, 'Kurban penuh')}
      </div>`;
    });
  }

  html += `<div style="text-align:center;font-size:11px;color:#9a8060;padding:12px 16px 30px;">Data diperbarui otomatis · Jumat, 6 Jun 2025</div>`;
  document.getElementById('mudhohi-list').innerHTML = html;
}

function filterMudhohi(filter, el) {
  document.querySelectorAll('#filter-row .chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  renderMudhohi(filter);
}

// init
renderMudhohi('Semua');
</script>
</body>
</html>