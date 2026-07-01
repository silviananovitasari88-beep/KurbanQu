// ═══════════════════════════════════════════
// DATA
// ═══════════════════════════════════════════
const TIMELINE = [
    { label:'Penyembelihan', desc:'Proses penyembelihan hewan kurban akan dimulai setelah status dikonfirmasi.', status:'pending', time:'—', icon:'🔪' },
    { label:'Pengulitan',    desc:'Pengulitan dan pembersihan akan dilakukan setelah penyembelihan selesai.', status:'pending', time:'—', icon:'🐄' },
    { label:'Pencacahan',    desc:'Daging sedang dipotong dan disiapkan untuk distribusi.', status:'pending',      time:'—', icon:'🥩' },
    { label:'Penimbangan',   desc:'Daging akan ditimbang dan dikemas per bagian.', status:'pending',              time:'—', icon:'⚖️' },
    { label:'Siap Diambil',  desc:'Distribusi QR akan dimulai setelah proses penimbangan selesai.', status:'pending', time:'—', icon:'✅' },
  ];
  
  const STORAGE_HEWAN = 'kurbanqu_hewan';
  const STORAGE_MUDHOHI = 'kurbanqu_mudhohi';
  const STORAGE_MUDHOHI_ID = 'kurbanqu_next_mudhohi_id';
  const STORAGE_HEWAN_ID = 'kurbanqu_next_hewan_id';
  const STORAGE_LEGACY_ANIMALS = 'kurbanqu_animals';
  const STORAGE_MIGRATED = 'kurbanqu_migrated_v2';

  const JENIS_HEWAN = {
    sapi:    { emoji: '🐄', label: 'Sapi' },
    kambing: { emoji: '🐐', label: 'Kambing' },
    domba:   { emoji: '🐑', label: 'Domba' },
  };

  function loadHewan() {
    try {
      const raw = localStorage.getItem(STORAGE_HEWAN);
      if (raw) return JSON.parse(raw);
    } catch (e) { /* ignore */ }
    return [];
  }

  function loadMudhohiList() {
    try {
      const raw = localStorage.getItem(STORAGE_MUDHOHI);
      if (raw) return JSON.parse(raw);
    } catch (e) { /* ignore */ }
    return [];
  }

  async function loadHewanFromServer() {
  try {
    const res = await fetch('/admin/api/hewan', { headers: { Accept: 'application/json' } });
    const payload = await res.json();
    if (payload.success) {
      HEWAN = payload.data.map(h => ({
        id_hewan: h.id_hewan,
        jenis: h.jenis,
        label: `${h.jenis} #${h.id_hewan}`,
        umur: h.umur,
        sehat: h.sehat,
        cacat: h.cacat,
        cacat_ket: h.cacat === 'Cacat' ? 'Ada cacat' : '',
        st_syariat: h.st_syariat ? 'Sah' : 'Tidak Sah',
        berat: '—',
      }));
    }
  } catch (e) { console.warn('Gagal load hewan dari server', e); }
}

async function loadMudhohiFromServer() {
  try {
    const res = await fetch('/admin/api/mudhohi', { headers: { Accept: 'application/json' } });
    const payload = await res.json();
    if (payload.success) {
      MUDHOHI = payload.data.map((m, idx) => ({
        id_mudhohi: m.id_mudhohi,
        hewan_id_hewan: m.hewan_id_hewan,
        i: (m.nama_mudhohi || '??').split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2),
        nama: m.nama_mudhohi,
        nama_ayah: m.nama_ayah || '',
        alamat: m.alamat || '',
        notelp: m.notelp_mudhohi || '',
        nkk: '',
        req: m.req_bagian || '',
        bagian: m.req_bagian || 'Kurban penuh',
        warna: ['brown','green','amber','purple'][idx % 4],
      }));
    }
  } catch (e) { console.warn('Gagal load mudhohi dari server', e); }
}

  let HEWAN = loadHewan();
  let MUDHOHI = loadMudhohiList();
  let nextMudhohiId = 1;
  let nextHewanId = 1;

  function saveStore() {
    localStorage.setItem(STORAGE_HEWAN, JSON.stringify(HEWAN));
    localStorage.setItem(STORAGE_MUDHOHI, JSON.stringify(MUDHOHI));
  }

  function saveNextMudhohiId() {
    localStorage.setItem(STORAGE_MUDHOHI_ID, String(nextMudhohiId));
  }

  function saveNextHewanId() {
    localStorage.setItem(STORAGE_HEWAN_ID, String(nextHewanId));
  }

  function migrateLegacyAnimals() {
    if (localStorage.getItem(STORAGE_MIGRATED)) return;
    const raw = localStorage.getItem(STORAGE_LEGACY_ANIMALS);
    if (!raw) {
      localStorage.setItem(STORAGE_MIGRATED, '1');
      return;
    }
    try {
      const parsed = JSON.parse(raw);
      let hid = 1;
      const hewan = [];
      const mudhohi = [];
      ['sapi', 'kambing', 'domba'].forEach(jenis => {
        (parsed[jenis] || []).forEach(a => {
          const id_hewan = hid++;
          const sehatStr = (a.sehat || '').toString();
          const syariatStr = (a.syariat || '').toString();
          const cacatStr = (a.cacat || 'Tidak ada').toString();
          hewan.push({
            id_hewan,
            jenis,
            label: a.label || `${JENIS_HEWAN[jenis].label} #${id_hewan}`,
            umur: a.umur || '—',
            sehat: sehatStr.includes('Tidak') ? 'Tidak' : 'Ya',
            cacat: cacatStr === 'Tidak ada' ? 'Tidak' : 'Ada',
            cacat_ket: cacatStr,
            st_syariat: syariatStr.includes('Tidak') ? 'Tidak Sah' : 'Sah',
            berat: a.berat || '—',
          });
          (a.mudhohi || []).forEach(m => {
            mudhohi.push({
              ...m,
              hewan_id_hewan: id_hewan,
            });
            delete mudhohi[mudhohi.length - 1].tipe;
          });
        });
      });
      if (hewan.length) {
        HEWAN = hewan;
        MUDHOHI = mudhohi;
        saveStore();
      }
    } catch (e) { /* ignore */ }
    localStorage.setItem(STORAGE_MIGRATED, '1');
  }

  function initDataIds() {
    migrateLegacyAnimals();
    let maxH = 0;
    HEWAN.forEach(h => { if (h.id_hewan > maxH) maxH = h.id_hewan; });
    let maxM = 0;
    MUDHOHI.forEach(m => {
      if (m.id_mudhohi > maxM) maxM = m.id_mudhohi;
      delete m.tipe;
      if (!m.hewan_id_hewan && m.animalId) {
        const legacy = HEWAN.find(h => String(h.id_hewan) === String(m.animalId) || h.label === m.animalLabel);
        if (legacy) m.hewan_id_hewan = legacy.id_hewan;
      }
    });
    const storedH = parseInt(localStorage.getItem(STORAGE_HEWAN_ID) || '0', 10);
    const storedM = parseInt(localStorage.getItem(STORAGE_MUDHOHI_ID) || '0', 10);
    nextHewanId = Math.max(maxH + 1, storedH || 1);
    nextMudhohiId = Math.max(maxM + 1, storedM || 1);
    let changed = false;
    MUDHOHI.forEach(m => {
      if (!m.id_mudhohi) { m.id_mudhohi = nextMudhohiId++; changed = true; }
      if (!m.hewan_id_hewan && HEWAN[0]) { m.hewan_id_hewan = HEWAN[0].id_hewan; changed = true; }
    });
    let hewanChanged = false;
    HEWAN.forEach(h => {
      if ('alamat' in h) { delete h.alamat; hewanChanged = true; }
      if ('notelp' in h) { delete h.notelp; hewanChanged = true; }
    });
    if (changed || hewanChanged) saveStore();
    saveNextHewanId();
    saveNextMudhohiId();
  }

  function getHewanById(id) {
    return HEWAN.find(h => String(h.id_hewan) === String(id));
  }

  function hewanDisplay(h) {
    if (!h) return { emoji: '🐾', jenisLabel: '—', label: '—' };
    const meta = JENIS_HEWAN[h.jenis] || { emoji: '🐾', label: h.jenis };
    return { emoji: meta.emoji, jenisLabel: meta.label, label: h.label };
  }

  function filterMudhohiHewanSelect() {
    const jenis = document.getElementById('m-jenis')?.value || '';
    const sel = document.getElementById('m-hewan');
    if (!sel) return;
    if (!jenis) {
      sel.innerHTML = '<option value="">— Pilih jenis hewan terlebih dahulu —</option>';
      sel.disabled = true;
      return;
    }
    const list = HEWAN.filter(h => h.jenis.toLowerCase() === jenis.toLowerCase());
    sel.disabled = false;
    sel.innerHTML = '<option value="">-- Pilih Hewan (id_hewan) --</option>' +
      list.map(h => `<option value="${h.id_hewan}">#${h.id_hewan} · ${h.label}</option>`).join('') +
      (list.length ? '' : '<option value="" disabled>Belum ada hewan jenis ini — tambah di Data Hewan</option>');
  }

  function mudhohiKey(m) {
    return String(m.id_mudhohi);
  }

  function qrIdMudhohi(m) {
    return String(m.id_mudhohi);
  }
  
  // avatar color map
  const AVC = {
    brown:  { bg:'rgba(120,82,48,0.2)',  color:'#c8922a' },
    green:  { bg:'rgba(60,109,17,0.2)',  color:'#4ecb71' },
    amber:  { bg:'rgba(184,92,0,0.2)',   color:'#e8b84b' },
    purple: { bg:'rgba(83,74,183,0.2)',  color:'#a09cf8' },
  };
  
  // Runtime state
  let claimedSet   = new Set();
  let downloadedSet = new Set();     // keys of QR that have been downloaded
  let claimMethod  = {};             // key → 'QR' | 'Manual'
  let claimTime    = {};             // key → time string
  let trackingLog  = [];
  let distLog      = [];
  let hewanFilter  = 'semua';
  let currentPage  = 'dashboard';
  let html5QrCode = null;

  // ─── Penerima Distribusi State (berdasarkan id_penerima dari upload Excel) ───
  // Storage terpisah agar tidak terpengaruh MUDHOHI
  const STORAGE_DIST_CLAIMED   = 'kurbanqu_dist_claimed';
  const STORAGE_DIST_DOWNLOADED = 'kurbanqu_dist_downloaded';
  const STORAGE_DIST_METHOD    = 'kurbanqu_dist_method';
  const STORAGE_DIST_TIME      = 'kurbanqu_dist_time';

  // Claimed set untuk penerima Excel (key = id_penerima)
  let penerimaClaimedSet    = new Set(JSON.parse(localStorage.getItem(STORAGE_DIST_CLAIMED)   || '[]'));
  let penerimaDownloadedSet = new Set(JSON.parse(localStorage.getItem(STORAGE_DIST_DOWNLOADED) || '[]'));
  let penerimaClaimMethod   = JSON.parse(localStorage.getItem(STORAGE_DIST_METHOD) || '{}');
  let penerimaClaimTime     = JSON.parse(localStorage.getItem(STORAGE_DIST_TIME)   || '{}');
  let penerimaDistLog       = [];
  let backendDistribusiByKk = {};
  let backendDistribusiLoaded = false;
  let pendingImportTempFile = '';

  async function refreshDistribusiSnapshot() {
    try {
      const response = await fetch('/admin/api/distribusi/snapshot', {
        headers: { Accept: 'application/json' },
      });
      if (!response.ok) return;
      const payload = await response.json();
      // Normalisasi key: strip non-digit agar cocok dengan nkk localStorage
      const rawData = payload.data || {};
      backendDistribusiByKk = {};
      Object.entries(rawData).forEach(([k, v]) => {
        const normKey = String(k).replace(/\D/g, '');
        backendDistribusiByKk[normKey] = v;
      });
      backendDistribusiLoaded = true;
      if (currentPage === 'tabel') renderTabelDistribusi();
      updateDistStats();
    } catch (error) {
      console.warn('Gagal memuat snapshot distribusi', error);
    }
  }

  function getBackendDistribusiRow(key) {
    const normKey = String(key || '').replace(/\D/g, '');
    return backendDistribusiByKk[normKey] || null;
  }

  function savePenerimaDistState() {
    localStorage.setItem(STORAGE_DIST_CLAIMED,    JSON.stringify([...penerimaClaimedSet]));
    localStorage.setItem(STORAGE_DIST_DOWNLOADED, JSON.stringify([...penerimaDownloadedSet]));
    localStorage.setItem(STORAGE_DIST_METHOD,     JSON.stringify(penerimaClaimMethod));
    localStorage.setItem(STORAGE_DIST_TIME,       JSON.stringify(penerimaClaimTime));
  }
  
  // ═══════════════════════════════════════════
  // HELPERS
  // ═══════════════════════════════════════════
  const TAHUN_INI = new Date().getFullYear();

  function getAllAnimals() {
    return HEWAN.map(h => {
      const d = hewanDisplay(h);
      const mh = MUDHOHI.filter(m => m.hewan_id_hewan === h.id_hewan);
      return {
        id: String(h.id_hewan),
        id_hewan: h.id_hewan,
        emoji: d.emoji,
        label: h.label,
        jenis: h.jenis,
        jenisLabel: d.jenisLabel,
        umur: h.umur,
        tahun: h.tahun || null,
        sehat: h.sehat === 'Ya' ? '\u2713 Sehat' : '\u2717 Tidak Sehat',
        syariat: h.st_syariat === 'Sah' ? '\u2713 Sah' : '\u2717 Tidak Sah',
        cacat: h.cacat === 'Tidak' ? 'Tidak ada' : (h.cacat_ket || 'Ada cacat'),
        berat: h.berat,
        sehatRaw: h.sehat,
        cacatRaw: h.cacat,
        st_syariat: h.st_syariat,
        mudhohi: mh,
      };
    });
  }

  // Hanya hewan tahun ini (atau tanpa field tahun) — untuk Dashboard & Data Hewan
  function getAnimalsThisYear() {
    return getAllAnimals().filter(a => !a.tahun || String(a.tahun) === String(TAHUN_INI));
  }

  function getAllMudhohi() {
    return MUDHOHI.map(m => {
      const h = getHewanById(m.hewan_id_hewan);
      const d = hewanDisplay(h);
      return {
        ...m,
        hewan_id_hewan: m.hewan_id_hewan,
        animalId: h ? String(h.id_hewan) : '',
        animalLabel: h?.label || '—',
        animalEmoji: d.emoji,
        animalType: h?.jenis || '',
        jenisLabel: d.jenisLabel,
        nkk: m.nkk || '',
        nama_ayah: m.nama_ayah || '',
        alamat: m.alamat || '',
        notelp: m.notelp || '',
        req: m.req || '',
      };
    });
  }

  function findMudhohi(idMudhohi) {
    return getAllMudhohi().find(m => String(m.id_mudhohi) === String(idMudhohi));
  }
  function animalStatus(a) {
    const doneIdx = TIMELINE.findIndex(t => t.status === 'active');
    if (doneIdx >= 4) return 'done';
    if (doneIdx >= 2) return 'active';
    return 'pending';
  }
  function nowTime() {
    const n = new Date();
    return String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0') + ' WIB';
  }
  
  // ═══════════════════════════════════════════
  // NAVIGATION
  // ═══════════════════════════════════════════
  const PAGE_TITLES = {
    dashboard:  ['Dashboard Admin', 'Sistem Informasi Distribusi Kurban Berbasis QR'],
    hewan:      ['Data Hewan Kurban', 'Kelola data hewan kurban 1446 H'],
    mudhohi:    ['Data Mudhohi', 'Pemilik hewan & bagian — terpisah dari login penerima'],
    tracking:   ['Live Tracking', 'Update status proses kurban real-time'],
    distribusi: ['Distribusi QR', 'Scan & verifikasi penerima daging kurban'],
    upload:     ['Penerima Kurban', 'Upload Excel → login warga (No KK + Nama) → kode QR'],
    tabel:      ['Tabel Distribusi', 'Rekap lengkap status distribusi per penerima'],
    rekap:      ['Rekap & Statistik', 'Ringkasan pelaksanaan kurban 1446 H'],
  };
  
  function navTo(page, el) {
    if (page !== 'distribusi') {
      stopScanner();
    }
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('pg-' + page).classList.add('active');
    if (el) el.classList.add('active');
    const [title, sub] = PAGE_TITLES[page] || ['Admin', ''];
    document.getElementById('topbar-title').textContent = title;
    document.getElementById('topbar-sub').textContent   = sub;
    currentPage = page;
    // Render on navigate
    if (page === 'dashboard')  renderDashboard();
    if (page === 'hewan')      renderHewanTable();
    if (page === 'mudhohi')    renderMudhohiTable();
    if (page === 'tracking')   renderTracking();
    if (page === 'distribusi') { renderScanList(); updateDistStats(); }
    if (page === 'upload')     renderPenerimaPage();
    if (page === 'tabel')      { renderTabelDistribusi(); renderQuickScanList(); renderTabelDistLog(); }
    if (page === 'rekap')      renderRekap();
  }
  
  // ═══════════════════════════════════════════
  // TOAST
  // ═══════════════════════════════════════════
  let _toastTimer;
  function toast(msg, type = 'info') {
    const el = document.getElementById('toast');
    el.innerHTML = (type === 'success' ? '✓ ' : type === 'error' ? '✗ ' : 'ℹ ') + msg;
    el.className = 'toast ' + type + ' show';
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => el.classList.remove('show'), 3000);
  }
  
  // ═══════════════════════════════════════════
  // MODAL
  // ═══════════════════════════════════════════
  function openModal(id) { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }
  function openModalHewan() {
    ['h-jenis','h-label','h-umur','h-berat','h-cacat-ket'].forEach(i => { const e=document.getElementById(i); if(e) e.value=''; });
    const defs = { 'h-sehat':'Ya', 'h-cacat':'Tidak', 'h-syariat':'Sah' };
    Object.entries(defs).forEach(([id, val]) => { const e=document.getElementById(id); if(e) e.value=val; });
    openModal('modal-hewan');
  }
  function openModalMudhohi() {
    ['m-nama','m-nkk','m-ayah','m-telp','m-bagian','m-req','m-alamat','m-jenis'].forEach(i => { const e=document.getElementById(i); if(e) e.value=''; });
    filterMudhohiHewanSelect();
    openModal('modal-mudhohi');
  }
  
  // ═══════════════════════════════════════════
  // DASHBOARD
  // ═══════════════════════════════════════════
  function renderDashboard() {
    const allM = getAllMudhohi();
    const total = allM.length;
    const diambil = claimedSet.size;
    const pct = total ? Math.round(diambil / total * 100) : 0;
  
    document.getElementById('s-hewan').textContent    = getAnimalsThisYear().length;
    document.getElementById('s-mudhohi').textContent  = total;
    document.getElementById('s-diambil').textContent  = diambil;
    document.getElementById('s-diambil-pct').textContent = pct + '% dari total';
    document.getElementById('s-qr').textContent       = total - diambil;
    document.getElementById('badge-distribusi').textContent = diambil;
  
    // Progress ring
    const circ = 2 * Math.PI * 48;
    const off  = circ - (pct / 100) * circ;
    document.getElementById('prog-circle').style.strokeDashoffset = off;
    document.getElementById('prog-pct').textContent = pct + '%';
  
    // Bar chart — hanya hewan tahun ini
    const hewanTahunIni = getAnimalsThisYear();
    const sapi    = hewanTahunIni.filter(a => a.jenis === 'sapi').length;
    const kambing = hewanTahunIni.filter(a => a.jenis === 'kambing').length;
    const domba   = hewanTahunIni.filter(a => a.jenis === 'domba').length;
    const bars = [['🐄 Sapi', sapi, '#c8922a'], ['🐐 Kambing', kambing, '#e8b84b'], ['🐑 Domba', domba, '#a09cf8']];
    document.getElementById('bar-chart').innerHTML = bars.map(([lbl, val, col]) => `
      <div class="bar-row">
        <div class="bar-label" style="font-size:11px;">${lbl}</div>
        <div class="bar-track"><div class="bar-fill" style="width:${Math.round(val/total*100)}%;background:${col};"></div></div>
        <div class="bar-val">${val}</div>
      </div>`).join('');
  
    // Animal list (first 3)
    const dashList = document.getElementById('dash-animal-list');
    dashList.innerHTML = getAnimalsThisYear().slice(0, 3).map(a => {
      const st = animalStatus(a);
      return `<div class="animal-row" onclick="showDetailHewanFk('${a.id_hewan}')">
        <div class="animal-avatar">${a.emoji}</div>
        <div><div class="animal-name">${a.label}</div><div class="animal-sub">${a.sehat} · ${a.syariat} · ${a.umur}</div></div>
        <span class="status-badge ${st === 'done' ? 'status-done' : st === 'active' ? 'status-active' : 'status-pending'}">${st === 'done' ? 'Selesai' : st === 'active' ? 'Diproses' : 'Pending'}</span>
      </div>`;
    }).join('') + `<div style="text-align:center;margin-top:10px;"><button class="btn btn-ghost btn-sm" onclick="navTo('hewan',document.querySelectorAll('.nav-item')[1])">Lihat semua ${getAnimalsThisYear().length} hewan →</button></div>`;
  
    // Tracking
    renderTrackingWidget();
  }
  
  function renderTrackingWidget() {
    document.getElementById('dash-tracking').innerHTML = `<div class="timeline">` +
      TIMELINE.map((t, i) => `
        <div class="tl-item ${t.status}">
          ${i < TIMELINE.length - 1 ? '<div class="tl-line"></div>' : ''}
          <div class="tl-dot">${t.status === 'done' ? '✓' : t.status === 'active' ? t.icon : ''}</div>
          <div class="tl-content">
            <div class="tl-label">${t.label}</div>
            <div class="tl-desc">${t.desc}</div>
            <div class="tl-time">${t.time}</div>
          </div>
        </div>`).join('') + `</div>`;
  }
  
  // ═══════════════════════════════════════════
  // HEWAN TABLE
  // ═══════════════════════════════════════════
  let hewanFilterCurrent = 'semua';
  function filterHewan(type, el) {
    hewanFilterCurrent = type;
    document.querySelectorAll('#pg-hewan .tab-item').forEach(t => t.classList.remove('active'));
    if (el) el.classList.add('active');
    renderHewanTable();
  }
  function renderHewanTable() {
    const q = (document.getElementById('hewan-search')?.value || '').toLowerCase();
    let list = [];
    list = getAnimalsThisYear(); // Hanya hewan tahun ini — arsip ada di Rekap & Statistik
    if (hewanFilterCurrent !== 'semua') list = list.filter(a => a.jenis === hewanFilterCurrent);
    if (q) list = list.filter(a => a.label.toLowerCase().includes(q) || String(a.id_hewan).includes(q));
  
    const tbody = document.getElementById('hewan-table-body');
    if (!list.length) { tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="empty-ico">🐾</div>Tidak ada data</div></td></tr>`; return; }
    const st = animalStatus();
    tbody.innerHTML = list.map(a => {
      const done_idx = TIMELINE.findIndex(t => t.status === 'active');
      const status = done_idx >= 4 ? 'done' : done_idx >= 2 ? 'active' : 'pending';
      return `<tr>
        <td><strong>#${a.id_hewan}</strong></td>
        <td><div style="display:flex;align-items:center;gap:10px;">
          <div class="animal-avatar" style="width:34px;height:34px;font-size:16px;">${a.emoji}</div>
          <strong>${a.label}</strong></div></td>
        <td><span style="font-size:11px;font-weight:700;color:var(--gold2);">${a.jenisLabel}</span> <span style="color:var(--text3);font-size:10px;">(${a.jenis})</span></td>
        <td>${a.umur} · ${a.berat}</td>
        <td><span style="font-weight:700;color:var(--gold2);">${a.mudhohi.length}</span> orang</td>
        <td><span class="status-badge ${status === 'done' ? 'status-done' : status === 'active' ? 'status-active' : 'status-pending'}">${status === 'done' ? 'Selesai' : status === 'active' ? 'Diproses' : 'Pending'}</span></td>
        <td><button class="btn btn-ghost btn-sm" onclick="showDetailHewanFk('${a.id_hewan}')">Detail</button>
            <button class="btn btn-danger btn-sm" style="margin-left:4px;" onclick="deleteHewan('${a.id_hewan}')">Hapus</button></td>
      </tr>`;
    }).join('');
  }
  
  async function deleteHewan(idHewan) {
  if (!confirm('Yakin hapus hewan ini? Mudhohi terkait juga akan terhapus.')) return;
  try {
    const res = await fetch(`/admin/api/hewan/${idHewan}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
    });
    const payload = await res.json().catch(() => ({}));
    if (!res.ok || !payload.success) throw new Error(payload.message || 'Gagal menghapus');

    await loadHewanFromServer();
    await loadMudhohiFromServer();
    renderHewanTable();
    renderDashboard();
    if (currentPage === 'mudhohi') renderMudhohiTable();
    toast('Hewan dihapus', 'info');
  } catch (error) {
    toast(error.message || 'Gagal menghapus hewan', 'error');
  }
}

  function showDetailHewanFk(idHewan) {
    const h = getHewanById(idHewan);
    if (!h) return;
    const d = hewanDisplay(h);
    const mh = MUDHOHI.filter(m => m.hewan_id_hewan === h.id_hewan);
    const sehatBadge = h.sehat === 'Ya'
      ? '<span class="status-badge status-done">Sehat</span>'
      : '<span class="status-badge status-active">Tidak Sehat</span>';
    const cacatBadge = h.cacat === 'Tidak'
      ? '<span class="status-badge status-done">Tanpa Cacat</span>'
      : '<span class="status-badge status-active">Ada Cacat</span>';
    const syariatBadge = h.st_syariat === 'Sah'
      ? '<span class="status-badge status-done">Sesuai Syariat</span>'
      : '<span class="status-badge status-active">Tidak Sesuai</span>';

    document.getElementById('detail-hewan-title').textContent = d.emoji + ' ' + h.label;
    document.getElementById('detail-hewan-body').innerHTML = `
      <div class="detail-card">
        <div class="detail-row"><div class="detail-key">id_hewan (FK)</div><div class="detail-val" style="font-family:monospace;color:var(--blue);">#${h.id_hewan}</div></div>
        <div class="detail-row"><div class="detail-key">Jenis Hewan</div><div class="detail-val">${d.jenisLabel} <code style="font-size:10px;color:var(--text3);">enum: ${h.jenis}</code></div></div>
        <div class="detail-row"><div class="detail-key">Umur</div><div class="detail-val">${h.umur || '—'}</div></div>
        <div class="detail-row"><div class="detail-key">Sehat</div><div class="detail-val">${sehatBadge}</div></div>
        <div class="detail-row"><div class="detail-key">Cacat</div><div class="detail-val">${cacatBadge}${h.cacat_ket ? ' · ' + h.cacat_ket : ''}</div></div>
        <div class="detail-row"><div class="detail-key">Syariat</div><div class="detail-val">${syariatBadge}</div></div>
        <div class="detail-row"><div class="detail-key">Berat</div><div class="detail-val">${h.berat || '—'}</div></div>
      </div>
      <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Mudhohi terhubung (${mh.length})</div>
      ${mh.length ? mh.map(m => {
        const c = AVC[m.warna] || AVC.brown;
        const claimed = claimedSet.has(mudhohiKey(m));
        return `<div style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:var(--bg3);border-radius:10px;margin-bottom:6px;">
          <div class="avatar" style="background:${c.bg};color:${c.color};">${m.i}</div>
          <div style="flex:1;"><strong style="font-size:13px;">${m.nama}</strong><div style="font-size:11px;color:var(--text3);">Bagian: ${m.bagian||'kurban penuh'} · QR #${m.id_mudhohi}</div></div>
          ${claimed ? '<span class="status-badge status-done">✓ Diambil</span>' : '<span class="status-badge status-pending">Belum</span>'}
        </div>`;
      }).join('') : '<div class="empty-state" style="padding:20px;"><div class="empty-ico">👥</div>Belum ada mudhohi</div>'}`;
    openModal('modal-detail-hewan');
  }

  function showDetailHewan(id) {
    showDetailHewanFk(id);
  }
  
  // ═══════════════════════════════════════════
  // MUDHOHI TABLE
  // ═══════════════════════════════════════════
  function renderMudhohiTable() {
    const q = (document.getElementById('mudhohi-search')?.value || '').toLowerCase();
    let list = getAllMudhohi();
    if (q) list = list.filter(m =>
      m.nama.toLowerCase().includes(q) ||
      (m.nama_ayah || '').toLowerCase().includes(q) ||
      String(m.id_mudhohi).includes(q)
    );
    const tbody = document.getElementById('mudhohi-table-body');
    if (!list.length) { tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="empty-ico">👥</div>Tidak ada data</div></td></tr>`; return; }
    tbody.innerHTML = list.map(m => {
      const c = AVC[m.warna] || AVC.brown;
      const claimed = claimedSet.has(mudhohiKey(m));
      const qr = qrIdMudhohi(m);
      return `<tr>
        <td>
          <div style="display:flex;align-items:center;gap:8px;">
            ${miniQR(qr)}
            <div>
              <div style="font-family:monospace;font-size:11px;font-weight:700;color:var(--blue);">#${qr}</div>
              <div style="font-size:10px;color:var(--text3);">id_mudhohi</div>
            </div>
          </div>
        </td>
        <td><div style="display:flex;align-items:center;gap:10px;">
          <div class="avatar" style="background:${c.bg};color:${c.color};">${m.i}</div>
          <div><strong>${m.nama}</strong><div style="font-size:10px;color:var(--text3);margin-top:2px;">${m.nama_ayah ? 'Bin ' + m.nama_ayah : '—'}</div>
          <div style="font-size:10px;color:var(--text3);font-family:monospace;">KK: ${m.nkk||'—'}</div></div></div></td>
        <td style="font-size:12px;">${m.alamat||'—'}</td>
        <td style="font-size:12px;">${m.notelp||'—'}</td>
        <td>
          <button type="button" class="btn-hewan-fk" onclick="showDetailHewanFk('${m.hewan_id_hewan}')" title="Lihat data hewan FK #${m.hewan_id_hewan}">
            <span class="btn-hewan-fk-ico">${m.animalEmoji}</span>
            <span class="btn-hewan-fk-text">${m.jenisLabel}</span>
            <span class="btn-hewan-fk-id">#${m.hewan_id_hewan}</span>
          </button>
        </td>
        <td><div>${m.bagian||'Kurban penuh'}</div>${m.req ? `<div style="font-size:10px;color:var(--gold2);margin-top:2px;">Req: ${m.req}</div>` : ''}</td>
        <td><span class="status-badge ${claimed ? 'status-done' : 'status-pending'}">${claimed ? '✓ Diambil' : 'Belum'}</span></td>
        <td>${!claimed ? `<button class="btn btn-gold btn-sm" onclick="markClaimed('${m.id_mudhohi}')">✓ Tandai Diambil</button>` : `<button class="btn btn-ghost btn-sm" onclick="unmarkClaimed('${m.id_mudhohi}')">Batalkan</button>`}</td>
      </tr>`;
    }).join('');
  }
  
  // ═══════════════════════════════════════════
  // TRACKING
  // ═══════════════════════════════════════════
  function renderTracking() {
    const el = document.getElementById('tracking-list');
    el.innerHTML = TIMELINE.map((t, i) => `
      <div style="display:flex;gap:16px;align-items:flex-start;padding:16px;background:var(--bg3);border-radius:12px;margin-bottom:10px;border:1px solid ${t.status==='done'?'rgba(200,146,42,0.2)':t.status==='active'?'rgba(232,184,75,0.15)':'var(--border)'};">
        <div style="width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;
          background:${t.status==='done'?'var(--gold)':t.status==='active'?'rgba(232,184,75,0.15)':'var(--bg4)'};
          border:2px solid ${t.status==='done'?'var(--gold)':t.status==='active'?'var(--amber)':'var(--border2)'};">
          ${t.status==='done'?'✓':t.icon}
        </div>
        <div style="flex:1;">
          <div style="font-size:14px;font-weight:700;color:${t.status==='pending'?'var(--text3)':'var(--text)'};">${t.label}</div>
          <div style="font-size:12px;color:var(--text3);margin-top:3px;">${t.desc}</div>
          <div style="font-size:11px;color:var(--text3);margin-top:5px;font-weight:600;">${t.time}</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;flex-shrink:0;">
          <span class="status-badge ${t.status==='done'?'status-done':t.status==='active'?'status-active':'status-pending'}">${t.status==='done'?'Selesai':t.status==='active'?'Berjalan':'Menunggu'}</span>
          <div style="display:flex;gap:5px;">
            ${t.status !== 'done'   ? `<button class="btn btn-gold btn-sm" onclick="setStatus(${i},'done')">✓ Selesai</button>` : ''}
            ${t.status !== 'active' ? `<button class="btn btn-outline btn-sm" onclick="setStatus(${i},'active')">▶ Mulai</button>` : ''}
            ${t.status !== 'pending'? `<button class="btn btn-ghost btn-sm" onclick="setStatus(${i},'pending')">Reset</button>` : ''}
          </div>
        </div>
      </div>`).join('');
  
    renderTrackingLog();
  }
  
  function setStatus(idx, status) {
    const old = TIMELINE[idx].status;
    TIMELINE[idx].status = status;
    if (status !== 'pending') TIMELINE[idx].time = nowTime();
    else TIMELINE[idx].time = '—';
    const msg = `${TIMELINE[idx].label} → ${status === 'done' ? 'Selesai' : status === 'active' ? 'Berjalan' : 'Reset'}`;
    trackingLog.unshift({ time: nowTime(), msg, type: status === 'done' ? 'success' : 'info' });
    renderTracking();
    renderTrackingWidget();
    updateBadgeTracking();
    toast(msg, status === 'done' ? 'success' : 'info');

    // ── Simpan ke DB agar warga bisa lihat realtime ──────────────────────
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  fetch(`/admin/api/tracking/${idx + 1}`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
  body: JSON.stringify({ status: status })
  }).catch(e => console.warn('Gagal simpan tracking:', e));
  }
  
  function updateBadgeTracking() {
    const active = TIMELINE.filter(t => t.status !== 'done').length;
    document.getElementById('badge-tracking').textContent = active;
  }
  
  function renderTrackingLog() {
    const el = document.getElementById('tracking-log');
    if (!trackingLog.length) { el.innerHTML = '<div class="empty-state"><div class="empty-ico">📝</div>Belum ada update</div>'; return; }
    el.innerHTML = trackingLog.map(l => `
      <div style="display:flex;gap:10px;align-items:flex-start;padding:10px 16px;border-bottom:1px solid var(--border);">
        <div style="font-size:11px;color:var(--text3);white-space:nowrap;margin-top:1px;">${l.time}</div>
        <div style="font-size:12px;color:${l.type==='success'?'var(--green)':l.type==='error'?'var(--red)':'var(--text2)'};">${l.msg}</div>
      </div>`).join('');
  }
  
  // ═══════════════════════════════════════════
  // DISTRIBUSI / SCAN (kamera QR)
  // ═══════════════════════════════════════════
  function updateDistStats() {
    const penerima = loadPenerima();
    const total = penerima.length || getAllMudhohi().length;
    const backendRows = Object.values(backendDistribusiByKk || {});
    const claimed = backendDistribusiLoaded
      ? backendRows.filter(r => String(r.st_pengambilan || '').toLowerCase() === 'selesai').length
      : (penerima.length ? penerimaClaimedSet.size : claimedSet.size);
    document.getElementById('dist-count').textContent = claimed;
    document.getElementById('dist-total').textContent = total;
  }

  // Scan list digunakan di halaman distribusi — tidak ada lagi, hanya kamera
  function renderScanList() {
    // Fungsi ini dipanggil saat navTo('distribusi') — cukup update stats
    updateDistStats();
  }

  function startScanner() {
    const readerEl = document.getElementById('qr-reader');
    const placeholderEl = document.getElementById('scanner-placeholder');
    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');

    if (!readerEl) return;

    readerEl.style.display = 'block';
    placeholderEl.style.display = 'none';

    html5QrCode = new Html5Qrcode("qr-reader");

    html5QrCode.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: { width: 180, height: 180 } },
      (decodedText) => {
        console.log("QR terbaca:", decodedText);
        showScanResultByQR(decodedText);
      },
      (error) => { /* diabaikan */ }
    ).then(() => {
      if (btnStart) btnStart.classList.add('d-none');
      if (btnStop) btnStop.classList.remove('d-none');
    }).catch(err => {
      toast('Gagal akses kamera: ' + err, 'error');
      readerEl.style.display = 'none';
      placeholderEl.style.display = 'block';
    });
  }

  function stopScanner() {
    const readerEl = document.getElementById('qr-reader');
    const placeholderEl = document.getElementById('scanner-placeholder');
    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');

    if (html5QrCode) {
      const stopPromise = (html5QrCode.isScanning) ? html5QrCode.stop() : Promise.resolve();
      
      stopPromise.then(() => {
        html5QrCode = null;
        if (readerEl) readerEl.style.display = 'none';
        if (placeholderEl) placeholderEl.style.display = 'block';
        if (btnStart) btnStart.classList.remove('d-none');
        if (btnStop) btnStop.classList.add('d-none');
      }).catch(err => {
        console.error("Gagal menghentikan scanner:", err);
        html5QrCode = null;
        if (readerEl) readerEl.style.display = 'none';
        if (placeholderEl) placeholderEl.style.display = 'block';
        if (btnStart) btnStart.classList.remove('d-none');
        if (btnStop) btnStop.classList.add('d-none');
      });
    } else {
      if (readerEl) readerEl.style.display = 'none';
      if (placeholderEl) placeholderEl.style.display = 'block';
      if (btnStart) btnStart.classList.remove('d-none');
      if (btnStop) btnStop.classList.add('d-none');
    }
  }

  window.startScanner = startScanner;
  window.stopScanner = stopScanner;

  // Scan result ketika QR berhasil di-scan kamera (berdasarkan qrCode penerima)
  function showScanResultByQR(qrCode) {
    const penerima = loadPenerima();
    const p = penerima.find(p => (p.qrCode || '').toUpperCase() === String(qrCode).toUpperCase());
    if (!p) {
      document.getElementById('scan-result').innerHTML = `
        <div style="background:rgba(224,85,85,0.08);border:1px solid rgba(224,85,85,0.3);border-radius:14px;padding:20px;">
          <div style="display:flex;align-items:center;gap:14px;">
            <div style="font-size:36px;">❌</div>
            <div>
              <div style="font-size:17px;font-weight:700;color:var(--red);">QR Tidak Dikenali</div>
              <div style="font-size:12px;color:var(--text3);margin-top:4px;">Kode: <code>${qrCode}</code> — tidak terdaftar sebagai penerima.</div>
            </div>
          </div>
        </div>`;
      toast('QR tidak dikenali: ' + qrCode, 'error');
      return;
    }
    showScanResultPenerima(p.id_penerima);
  }

  function showScanResultPenerima(idPenerima) {
    const penerima = loadPenerima();
    const p = penerima.find(p => String(p.id_penerima) === String(idPenerima));
    if (!p) return;
    const key = String(p.id_penerima);
      const backend = getBackendDistribusiRow(p.nkk);
      const claimed = backend ? String(backend.st_pengambilan || '').toLowerCase() === 'selesai' : penerimaClaimedSet.has(key);
    document.getElementById('scan-result').innerHTML = `
      <div style="background:${claimed?'rgba(224,85,85,0.08)':'rgba(78,203,113,0.08)'};border:1px solid ${claimed?'rgba(224,85,85,0.3)':'rgba(78,203,113,0.3)'};border-radius:14px;padding:20px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
          <div style="width:52px;height:52px;border-radius:50%;background:${claimed?'rgba(224,85,85,0.15)':'var(--green-bg)'};display:flex;align-items:center;justify-content:center;font-size:24px;">${claimed?'⚠️':'✅'}</div>
          <div>
            <div style="font-size:17px;font-weight:700;color:var(--text);">${p.nama}</div>
            <div style="font-size:12px;color:var(--text3);">No KK: ${p.nkk} · QR: <strong>${p.qrCode}</strong></div>
            ${p.alamat ? `<div style="font-size:11px;color:var(--text3);margin-top:2px;">📍 ${p.alamat}</div>` : ''}
          </div>
          <span class="status-badge ${claimed?'status-active':'status-done'}" style="margin-left:auto;">${claimed?'⚠ Sudah Diambil':'✓ Valid'}</span>
        </div>
        ${claimed
          ? `<div style="font-size:13px;color:var(--red);background:rgba(224,85,85,0.08);padding:10px 14px;border-radius:8px;">Penerima ini <strong>sudah mengambil</strong> daging kurbannya.</div>`
          : `<button class="btn btn-gold btn-lg" style="width:100%;" onclick="markPenerimaClaimed('${p.id_penerima}','QR')">✓ Tandai Sudah Mengambil (QR)</button>`}
      </div>`;
  }

  // ─── Mark claimed untuk penerima dari Excel ───
  function markPenerimaClaimed(idPenerima, method) {
    const penerima = loadPenerima();
    const p = penerima.find(p => String(p.id_penerima) === String(idPenerima));
    if (!p) return;
    const key = String(p.id_penerima);
    if (penerimaClaimedSet.has(key)) { toast('Sudah pernah diambil!', 'error'); return; }

    penerimaClaimedSet.add(key);
    const mt = method || 'Manual';
    penerimaClaimMethod[key] = mt;
    penerimaClaimTime[key]   = nowTime();
    savePenerimaDistState();

    const backend = getBackendDistribusiRow(p.nkk);
    if (backend && backend.id_stok) {
        markDistribusiDenganMetode(backend.id_stok, p.nkk, p.qrCode, mt);
    }

    penerimaDistLog.unshift({ nama: p.nama, nkk: p.nkk, time: penerimaClaimTime[key], method: mt });
    renderDistLog();
    updateDistStats();
    renderDashboard();
    if (currentPage === 'tabel') renderTabelDistribusi();
    if (currentPage === 'distribusi') showScanResultPenerima(idPenerima);
    toast(p.nama + ' berhasil diverifikasi (' + mt + ')', 'success');
  }

  async function markDistribusiDenganMetode(idStok, noKk, qrCode, method) {
    try {
        const response = await fetch(`/admin/api/distribusi/${encodeURIComponent(idStok)}/manual`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                warga_no_kk: String(noKk || '').replace(/\D/g, ''),
                qr_id_qr: qrCode || '',
                metode: method, // ← kirim metode
            }),
        });
        await refreshDistribusiSnapshot();
        if (currentPage === 'tabel') renderTabelDistribusi();
    } catch (error) {
        console.error(error);
    }
}

  async function markDistribusiManual(idStok, noKk, qrCode) {
    try {
      const response = await fetch(`/admin/api/distribusi/${encodeURIComponent(idStok)}/manual`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify({
          warga_no_kk: String(noKk || '').replace(/\D/g, ''),
          qr_id_qr: qrCode || '',
          metode: 'Manual', //
        }),
      });

      const payload = await response.json().catch(() => ({}));
      if (!response.ok || !payload.success) {
        throw new Error(payload.message || 'Gagal memperbarui status distribusi');
      }

      const key = String(noKk || '').replace(/\D/g, '');
      const penerima = loadPenerima().find(p => String(p.nkk || '').replace(/\D/g, '') === key);
      if (penerima) {
        const idKey = String(penerima.id_penerima);
        penerimaClaimedSet.add(idKey);
        penerimaClaimMethod[idKey] = 'manual_admin';
        penerimaClaimTime[idKey] = payload.data?.updated_at || nowTime();
        savePenerimaDistState();
      }

      await refreshDistribusiSnapshot();
      if (currentPage === 'tabel') renderTabelDistribusi();
      renderDashboard();
      updateDistStats();
      toast('Distribusi selesai via manual admin', 'success');
    } catch (error) {
      console.error(error);
      toast(error.message || 'Gagal memperbarui status manual', 'error');
    }
  }

  function unmarkPenerimaClaimed(idPenerima) {
    const key = String(idPenerima);

    // Deklarasi dulu semua variabel
    const penerimaList = loadPenerima();
    const targetP = penerimaList.find(item => String(item.id_penerima) === key);

    penerimaClaimedSet.delete(key);
    delete penerimaClaimMethod[key];
    delete penerimaClaimTime[key];
    penerimaDownloadedSet.delete(key);
    savePenerimaDistState();

    if (targetP) {
        const backend = getBackendDistribusiRow(targetP.nkk);
        if (backend && backend.id_stok) {
           fetch(`/admin/api/distribusi/${backend.id_stok}/batalkan`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
    },
    body: JSON.stringify({
        warga_no_kk: targetP.nkk
    }),
})
.then(() => refreshDistribusiSnapshot())
.then(() => {
    if (currentPage === 'tabel') renderTabelDistribusi();
});
        }
    }

    updateDistStats();
    renderDashboard();
    if (currentPage === 'tabel') renderTabelDistribusi();
    toast('Status ' + (targetP?.nama || idPenerima) + ' dibatalkan', 'info');
  }

  function simulatePenerimaQRDownload(idPenerima) {
    const key = String(idPenerima);
    penerimaDownloadedSet.add(key);
    savePenerimaDistState();
    if (currentPage === 'tabel') renderTabelDistribusi();
    const penerima = loadPenerima();
    const p = penerima.find(p => String(p.id_penerima) === String(idPenerima));
    toast('QR ' + (p?.qrCode || key) + ' sudah didownload', 'info');
  }

  // ─── Legacy mudhohi claimed (untuk halaman Mudhohi) ───
  function markClaimed(idMudhohi, method) {
    const row = findMudhohi(idMudhohi);
    if (!row) return;
    const key = mudhohiKey(row);
    if (claimedSet.has(key)) { toast('Sudah pernah diambil!', 'error'); return; }
    claimedSet.add(key);
    const mt = method || 'QR';
    claimMethod[key] = mt;
    claimTime[key]   = nowTime();
    if (mt === 'QR') downloadedSet.add(key);
    distLog.unshift({ nama: row.nama, animal: row.animalLabel, time: claimTime[key], method: mt });
    renderDistLog();
    renderMudhohiTable();
    renderDashboard();
    toast(row.nama + ' berhasil diverifikasi (' + mt + ')', 'success');
  }

  function markClaimedManual(idMudhohi) {
    markClaimed(idMudhohi, 'Manual');
  }

  function simulateQRDownload(key) {
    downloadedSet.add(key);
    const row = findMudhohi(key);
    toast('QR #' + (row?.id_mudhohi || key) + ' sudah didownload', 'info');
  }

  function unmarkClaimed(idMudhohi) {
    const row = findMudhohi(idMudhohi);
    const key = mudhohiKey(row || { id_mudhohi: idMudhohi });
    claimedSet.delete(key);
    delete claimMethod[key];
    delete claimTime[key];
    downloadedSet.delete(key);
    renderMudhohiTable();
    renderDashboard();
    updateDistStats();
    toast('Status ' + (row?.nama || idMudhohi) + ' dibatalkan', 'info');
  }

  function renderDistLog() {
    const el = document.getElementById('dist-log');
    const logs = penerimaDistLog.length ? penerimaDistLog : distLog;
    if (!logs.length) {
      if (el) el.innerHTML = '<div class="empty-state"><div class="empty-ico">📋</div>Belum ada yang diverifikasi</div>';
      return;
    }
    if (el) el.innerHTML = logs.map((l, i) => `
      <div style="display:flex;align-items:center;gap:12px;padding:11px 16px;border-bottom:1px solid var(--border);">
        <div style="width:28px;height:28px;border-radius:50%;background:var(--green-bg);color:var(--green);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">${i+1}</div>
        <div style="flex:1;"><strong style="font-size:13px;">${l.nama}</strong><div style="font-size:11px;color:var(--text3);">${l.nkk || l.animal || ''} · ${l.method || 'QR'}</div></div>
        <div style="font-size:11px;color:var(--text3);font-weight:600;">${l.time}</div>
      </div>`).join('');
  }
  
  // ═══════════════════════════════════════════
  // TABEL DISTRIBUSI  — menggunakan data Penerima dari upload Excel
  // ═══════════════════════════════════════════
  // Mini SVG QR placeholder (unique per code string)
  function miniQR(code) {
    // deterministic cell pattern from code hash
    let seed = 0;
    for (let i = 0; i < code.length; i++) seed = (seed * 31 + code.charCodeAt(i)) >>> 0;
    const cells = [];
    for (let r = 0; r < 7; r++) {
      for (let c = 0; c < 7; c++) {
        seed = (seed * 1103515245 + 12345) >>> 0;
        // force finder pattern borders
        const isCorner = (r < 2 && c < 2) || (r < 2 && c > 4) || (r > 4 && c < 2);
        const filled = isCorner || (seed % 3 === 0);
        if (filled) cells.push(`<rect x="${c*4}" y="${r*4}" width="3" height="3" fill="currentColor"/>`);
      }
    }
    return `<svg width="28" height="28" viewBox="0 0 28 28" style="color:var(--text2);">${cells.join('')}</svg>`;
  }
  
  function renderTabelDistribusi() {
    const q       = (document.getElementById('tabel-search')?.value || '').toLowerCase();
    const fStatus = document.getElementById('tabel-filter-status')?.value || 'semua';
    const fMetode = document.getElementById('tabel-filter-metode')?.value || 'semua';
    const fQr     = document.getElementById('tabel-filter-qr')?.value    || 'semua';

    // ── Gunakan data dari upload Excel (Penerima Kurban) ──
    const allPenerima = loadPenerima();

    let list = allPenerima.map((p, idx) => {
      const key        = String(p.id_penerima);
      const backend    = getBackendDistribusiRow(p.nkk);
      const claimed    = backend ? ['selesai','sudah'].includes(String(backend.st_pengambilan || '').toLowerCase()) : penerimaClaimedSet.has(key);
      const downloaded = backend ? ['sudah_download','sudah_login','sudah'].includes(String(backend.dowload_qr || '').toLowerCase()) : penerimaDownloadedSet.has(key);
      const method     = backend?.mtd_pengambilan || penerimaClaimMethod[key] || (claimed ? 'manual_admin' : '-');
      const waktu      = backend?.updated_at || backend?.jam_pengambilan || penerimaClaimTime[key] || '-';
      return {
        ...p,
        key,
        claimed,
        downloaded,
        method,
        waktu,
        noKK: p.nkk || '—',
        qrCode: p.qrCode || ('P' + String(p.id_penerima).padStart(5,'0')),
        id_stok: backend?.id_stok || idx + 1,
      };
    });

    // filters
    if (q) list = list.filter(r => r.nama.toLowerCase().includes(q) || r.noKK.includes(q));
    if (fStatus === 'diambil') list = list.filter(r => r.claimed);
    if (fStatus === 'belum')   list = list.filter(r => !r.claimed);
    if (fMetode !== 'semua')   list = list.filter(r => r.method === fMetode);
    if (fQr === 'downloaded')     list = list.filter(r => r.downloaded);
    if (fQr === 'not_downloaded') list = list.filter(r => !r.downloaded);

    // summary chips — prioritas dari backend DB
    const total   = allPenerima.length;
    const diambil = backendDistribusiLoaded
      ? Object.values(backendDistribusiByKk).filter(b => String(b.st_pengambilan||'').toLowerCase() === 'selesai').length
      : penerimaClaimedSet.size;
    const dlCount = backendDistribusiLoaded
      ? Object.values(backendDistribusiByKk).filter(b => String(b.dowload_qr||'').toLowerCase() === 'sudah_login').length
      : penerimaDownloadedSet.size;
    const qrAuto  = backendDistribusiLoaded
      ? Object.values(backendDistribusiByKk).filter(b => String(b.mtd_pengambilan||'').toLowerCase() === 'qr').length
      : Object.values(penerimaClaimMethod).filter(v => v === 'QR').length;
    const manual  = backendDistribusiLoaded
      ? Object.values(backendDistribusiByKk).filter(b => ['manual','manual_admin'].includes(String(b.mtd_pengambilan||'').toLowerCase())).length
      : Object.values(penerimaClaimMethod).filter(v => v === 'Manual').length;

    document.getElementById('tabel-summary').innerHTML =
      `<span style="font-size:12px;color:var(--text3);">Menampilkan <strong style="color:var(--text);">${list.length}</strong> dari ${total} data</span>`;

    document.getElementById('tabel-chips').innerHTML = [
      [`🗂 Total`, total, 'var(--text2)', 'var(--bg4)'],
      [`✅ Diambil`, diambil, 'var(--green)', 'var(--green-bg)'],
      [`⏳ Belum`, total - diambil, 'var(--amber)', 'var(--amber-bg)'],
      [`⬇ QR Download`, dlCount, 'var(--blue)', 'rgba(91,156,246,0.1)'],
      [`📱 Via QR`, qrAuto, 'var(--blue)', 'rgba(91,156,246,0.08)'],
      [`👆 Manual`, manual, 'var(--gold2)', 'var(--gold-dim)'],
    ].map(([lbl, val, col, bg]) =>
      `<div style="background:${bg};border:1px solid ${col}22;border-radius:20px;padding:6px 14px;display:flex;align-items:center;gap:7px;">
        <span style="font-size:12px;color:var(--text3);">${lbl}</span>
        <strong style="font-size:14px;color:${col};">${val}</strong>
      </div>`
    ).join('');

    const tbody = document.getElementById('tabel-distribusi-body');
    const empty = document.getElementById('tabel-empty');

    if (!list.length) {
      tbody.innerHTML = '';
      if (empty) empty.style.display = 'block';
      return;
    }
    if (empty) empty.style.display = 'none';

    tbody.innerHTML = list.map(r => {
      // avatar warna berdasarkan hash nama
      let seed = 0;
      for (let i = 0; i < r.nama.length; i++) seed = (seed * 31 + r.nama.charCodeAt(i)) >>> 0;
      const warnas = ['brown','green','amber','purple'];
      const c = AVC[warnas[seed % 4]] || AVC.brown;
      const initials = r.nama.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);

      // ── download_qr badge
      const dlBadge = r.downloaded
         ? `<div style="display:inline-flex;align-items:center;gap:5px;background:rgba(78,203,113,0.12);border:1px solid rgba(78,203,113,0.25);border-radius:20px;padding:4px 10px;">
           <span style="font-size:10px;">✅</span>
           <span style="font-size:10px;font-weight:700;color:var(--green);">Sudah Login</span>
           </div>`
         : `<div style="display:inline-flex;align-items:center;gap:5px;background:var(--bg4);border:1px solid var(--border);border-radius:20px;padding:4px 10px;">
             <span style="font-size:10px;">📵</span>
           <span style="font-size:10px;font-weight:700;color:var(--text3);">Belum Login</span>
           </div>`;

      const dlBtn = !r.downloaded
         ? `<br><span style="font-size:10px;color:var(--text3);margin-top:4px;display:inline-block;">Belum login</span>`
         : `<br><span style="font-size:10px;color:var(--green);margin-top:4px;display:inline-block;font-weight:700;">✓ Sudah Login</span>`

      // ── st_pengambilan
      const stBadge = r.claimed
        ? `<div style="display:inline-flex;align-items:center;gap:6px;background:var(--green-bg);border:1px solid rgba(78,203,113,0.25);border-radius:8px;padding:5px 10px;">
             <span style="font-size:13px;">${String(r.method || '').toLowerCase() === 'manual_admin' ? '👆' : '📱'}</span>
             <div>
               <div style="font-size:11px;font-weight:700;color:var(--green);">Sudah Diambil</div>
               <div style="font-size:10px;color:var(--text3);">${String(r.method || '').toLowerCase() === 'manual_admin' ? 'Admin klik manual' : 'Otomatis via QR'}</div>
             </div>
           </div>`
        : `<div style="display:inline-flex;align-items:center;gap:6px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:5px 10px;">
             <span style="font-size:13px;">⏳</span>
             <div>
               <div style="font-size:11px;font-weight:700;color:var(--text3);">Belum Diambil</div>
               <div style="font-size:10px;color:var(--text3);">Menunggu pengambilan</div>
             </div>
           </div>`;

      // ── mtd_pengambilan
     const mtdBadge = String(r.method || '').toLowerCase() === 'qr'
    ? `<div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
         <span style="background:rgba(91,156,246,0.12);color:var(--blue);border:1px solid rgba(91,156,246,0.2);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;">📱 QR</span>
         <span style="font-size:9px;color:var(--text3);">Otomatis</span>
       </div>`
    : ['manual_admin', 'manual'].includes(String(r.method || '').toLowerCase())
        ? `<div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
             <span style="background:var(--amber-bg);color:var(--amber);border:1px solid rgba(232,184,75,0.2);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;">👆 Manual</span>
             <span style="font-size:9px;color:var(--text3);">Admin input</span>
           </div>`
        : `<span style="color:var(--text3);font-size:12px;">—</span>`;

      // ── Aksi admin
      const aksiBtn = !r.claimed
        ? `<button class="btn btn-gold btn-sm" style="width:100%;" title="Tandai selesai secara manual"
             onclick="markDistribusiManual('${r.id_stok}','${r.noKK}','${r.qrCode}')">
             👆 Tandai Manual
           </button>`
        : `<button class="btn btn-ghost btn-sm" style="width:100%;font-size:10px;"
             onclick="unmarkPenerimaClaimed('${r.id_penerima}');renderTabelDistribusi();">
             ↩ Batalkan
           </button>`;

      return `<tr>
        <td style="text-align:center;">
          <span style="font-family:monospace;font-size:11px;color:var(--text3);font-weight:700;">#${String(r.id_Stok).padStart(3,'0')}</span>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="avatar" style="background:${c.bg};color:${c.color};font-size:11px;width:36px;height:36px;">${initials}</div>
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text);">${r.nama}</div>
              <div style="font-size:10px;color:var(--text3);margin-top:2px;font-family:monospace;letter-spacing:.3px;">KK: ${r.noKK}</div>
              ${r.alamat ? `<div style="font-size:10px;color:var(--text3);">📍 ${r.alamat}</div>` : ''}
            </div>
          </div>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:8px;">
            <div style="opacity:${r.downloaded ? 1 : 0.4};transition:opacity .3s;">${miniQR(r.qrCode)}</div>
            <div>
              <div style="font-family:monospace;font-size:10px;color:var(--blue);background:rgba(91,156,246,0.08);border:1px solid rgba(91,156,246,0.15);padding:3px 7px;border-radius:5px;">${r.qrCode}</div>
              ${r.notelp ? `<div style="font-size:10px;color:var(--text3);margin-top:3px;">📞 ${r.notelp}</div>` : ''}
            </div>
          </div>
        </td>
        <td style="text-align:center;">${dlBadge}${dlBtn}</td>
        <td>${stBadge}</td>
        <td style="text-align:center;">${mtdBadge}</td>
        <td style="font-size:11px;">${r.waktu !== '-' ? `<span style="color:var(--text2);font-weight:600;">${r.waktu}</span>` : '<span style="color:var(--text3);">—</span>'}</td>
        <td style="text-align:center;">${aksiBtn}</td>
      </tr>`;
    }).join('');
  }
  
  function exportTabelCSV() {
    const allPenerima = loadPenerima();
    const rows = [['id_stok','warga_no_kk','Nama KK','QR_id_qr','download_qr','st_pengambilan','mtd_pengambilan','Waktu']];
    allPenerima.forEach((p, i) => {
      const key  = String(p.id_penerima);
      const noKK = p.nkk || '—';
      const qr   = p.qrCode || ('P' + String(p.id_penerima).padStart(5,'0'));
      rows.push([
        String(i+1).padStart(3,'0'),
        noKK,
        p.nama,
        qr,
        penerimaDownloadedSet.has(key) ? 'Ya' : 'Tidak',
        penerimaClaimedSet.has(key)    ? 'Sudah Diambil' : 'Belum Diambil',
        penerimaClaimMethod[key] || '-',
        penerimaClaimTime[key]   || '-'
      ]);
    });
    const csv  = rows.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = 'distribusi_kurbanqu.csv'; a.click();
    URL.revokeObjectURL(url);
    toast('CSV berhasil diexport', 'success');
  }
  
  // ═══════════════════════════════════════════
  // QUICK SCAN LIST (Tabel Distribusi — pencarian manual penerima)
  // ═══════════════════════════════════════════
  function renderQuickScanList() {
    const q = (document.getElementById('tabel-quick-search')?.value || '').trim().toLowerCase();
    const el = document.getElementById('quick-scan-list');
    if (!el) return;

    const penerima = loadPenerima();
    if (!penerima.length) {
      el.innerHTML = '<div class="empty-state" style="padding:20px;"><div class="empty-ico" style="font-size:24px;">📊</div>Belum ada penerima. Upload Excel di menu Penerima Kurban.</div>';
      return;
    }

    if (!q) {
      el.innerHTML = '<div style="padding:12px 16px;font-size:12px;color:var(--text3);">Ketik nama atau No KK untuk mencari penerima...</div>';
      return;
    }

    const filtered = penerima.filter(p =>
      p.nama.toLowerCase().includes(q) ||
      (p.nkk || '').includes(q) ||
      (p.qrCode || '').toLowerCase().includes(q)
    ).slice(0, 10);

    if (!filtered.length) {
      el.innerHTML = '<div class="empty-state" style="padding:20px;"><div class="empty-ico" style="font-size:24px;">🔍</div>Tidak ditemukan</div>';
      return;
    }

    el.innerHTML = filtered.map(p => {
      const key = String(p.id_penerima);
      const backend = getBackendDistribusiRow(p.nkk);
      const claimed = backend ? String(backend.st_pengambilan || '').toLowerCase() === 'selesai' : penerimaClaimedSet.has(key);
      let seed = 0;
      for (let i = 0; i < p.nama.length; i++) seed = (seed * 31 + p.nama.charCodeAt(i)) >>> 0;
      const warnas = ['brown','green','amber','purple'];
      const c = AVC[warnas[seed % 4]] || AVC.brown;
      const initials = p.nama.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
      return `<div style="display:flex;align-items:center;gap:12px;padding:11px 14px;background:var(--bg3);border-radius:10px;margin-bottom:7px;border:1px solid ${claimed?'rgba(78,203,113,0.2)':'var(--border)'};cursor:pointer;transition:background .15s;" onclick="showTabelScanResult('${p.id_penerima}')" onmouseover="this.style.background='var(--bg4)'" onmouseout="this.style.background='var(--bg3)'">
        <div class="avatar" style="background:${c.bg};color:${c.color};font-size:11px;">${initials}</div>
        <div style="flex:1;">
          <strong style="font-size:13px;">${p.nama}</strong>
          <div style="font-size:11px;color:var(--text3);">KK: ${p.nkk || '—'} · QR: <strong style="color:var(--gold2);">${p.qrCode}</strong>${p.alamat ? ' · ' + p.alamat : ''}</div>
        </div>
        ${claimed ? '<span class="status-badge status-done" style="font-size:10px;">✓ Diambil</span>' : '<span style="color:var(--text3);font-size:18px;">›</span>'}
      </div>`;
    }).join('');
  }

  function showTabelScanResult(idPenerima) {
    const penerima = loadPenerima();
    const p = penerima.find(p => String(p.id_penerima) === String(idPenerima));
    if (!p) return;
    const key = String(p.id_penerima);
    const backend = getBackendDistribusiRow(p.nkk);
    const claimed = backend ? String(backend.st_pengambilan || '').toLowerCase() === 'selesai' : penerimaClaimedSet.has(key);
    const el = document.getElementById('tabel-scan-result');
    if (!el) return;
    el.innerHTML = `
      <div style="background:${claimed?'rgba(224,85,85,0.08)':'rgba(78,203,113,0.08)'};border:1px solid ${claimed?'rgba(224,85,85,0.3)':'rgba(78,203,113,0.3)'};border-radius:14px;padding:20px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
          <div style="width:52px;height:52px;border-radius:50%;background:${claimed?'rgba(224,85,85,0.15)':'var(--green-bg)'};display:flex;align-items:center;justify-content:center;font-size:24px;">${claimed?'⚠️':'✅'}</div>
          <div style="flex:1;">
            <div style="font-size:17px;font-weight:700;color:var(--text);">${p.nama}</div>
            <div style="font-size:12px;color:var(--text3);">No KK: ${p.nkk} · QR: <strong style="color:var(--gold2);">${p.qrCode}</strong></div>
            ${p.alamat ? `<div style="font-size:11px;color:var(--text3);margin-top:2px;">📍 ${p.alamat}</div>` : ''}
          </div>
          <span class="status-badge ${claimed?'status-active':'status-done'}" style="margin-left:auto;">${claimed?'⚠ Sudah Diambil':'✓ Valid'}</span>
        </div>
        ${claimed
          ? `<div style="font-size:13px;color:var(--red);background:rgba(224,85,85,0.08);padding:10px 14px;border-radius:8px;">Penerima ini <strong>sudah mengambil</strong> daging kurbannya.</div>`
          : `<button class="btn btn-gold btn-lg" style="width:100%;" onclick="markPenerimaClaimed('${p.id_penerima}','Manual');showTabelScanResult('${p.id_penerima}');renderTabelDistribusi();">👆 Tandai Sudah Mengambil</button>`}
      </div>`;
    renderTabelDistLog();
  }

  function renderTabelDistLog() {
    const el = document.getElementById('tabel-dist-log');
    if (!el) return;
    if (!penerimaDistLog.length) {
      el.innerHTML = '<div class="empty-state" style="padding:20px;"><div class="empty-ico" style="font-size:24px;">📋</div>Belum ada</div>';
      return;
    }
    el.innerHTML = penerimaDistLog.slice(0, 10).map((l, i) => `
      <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--border);">
        <div style="width:24px;height:24px;border-radius:50%;background:var(--green-bg);color:var(--green);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">${i+1}</div>
        <div style="flex:1;"><strong style="font-size:12px;">${l.nama}</strong><div style="font-size:10px;color:var(--text3);">${l.method || 'Manual'}</div></div>
        <div style="font-size:10px;color:var(--text3);font-weight:600;">${l.time}</div>
      </div>`).join('');
  }

  // ═══════════════════════════════════════════
  // REKAP
  // ═══════════════════════════════════════════
  // ── localStorage key untuk data tahunan penerima ────────────────────────────
  const STORAGE_PENERIMA_TAHUNAN = 'kurbanqu_penerima_tahunan';

  function loadPenerimaTahunan() {
    try {
      const raw = localStorage.getItem(STORAGE_PENERIMA_TAHUNAN);
      if (raw) return JSON.parse(raw);
    } catch(e) {}
    return {};
  }

  function savePenerimaTahunan(data) {
    localStorage.setItem(STORAGE_PENERIMA_TAHUNAN, JSON.stringify(data));
  }

  function renderRekap() {
    // b. Progress distribusi — dihitung dari jumlah baris di distribusi DB
    const allDistRows   = Object.values(backendDistribusiByKk || {});
    const totalPenerima = backendDistribusiLoaded
      ? allDistRows.length
      : loadPenerima().length;

    const diambilDB = backendDistribusiLoaded
      ? allDistRows.filter(b => String(b.st_pengambilan || '').toLowerCase() === 'selesai').length
      : 0;

    const belumDB = totalPenerima - diambilDB;
    const pct     = totalPenerima ? Math.round(diambilDB / totalPenerima * 100) : 0;
    const circ    = 2 * Math.PI * 48;
    const off     = circ - (pct / 100) * circ;

    // a. Data hewan per tahun dari HEWAN localStorage (semua tahun)
    const tahunHewanSet = new Set();
    HEWAN.forEach(h => { if (h.tahun) tahunHewanSet.add(String(h.tahun)); });
    const tahunHewanList = [...tahunHewanSet].sort().slice(-5); // max 5 tahun

    const hewanStats = tahunHewanList.map(th => {
      const list = HEWAN.filter(h => String(h.tahun) === th);
      return {
        tahun:   th,
        sapi:    list.filter(h => h.jenis === 'sapi').length,
        kambing: list.filter(h => h.jenis === 'kambing').length,
        domba:   list.filter(h => h.jenis === 'domba').length,
        total:   list.length,
      };
    });

    // c. Data penerima tahunan dari storage terpisah
    const penerimaTahunan    = loadPenerimaTahunan();
    const penerimaTahunList  = Object.keys(penerimaTahunan).sort().slice(-5);
    const hasPenerimaTahunan = penerimaTahunList.length > 0;

    function trendBadge(list, data) {
      if (list.length < 2) return '';
      const last = data[list[list.length - 1]];
      const prev = data[list[list.length - 2]];
      const diff = last - prev;
      const color = diff > 0 ? 'var(--green)' : diff < 0 ? 'var(--red)' : 'var(--text3)';
      const arrow = diff > 0 ? '\u2191' : diff < 0 ? '\u2193' : '\u2192';
      return `<span style="font-size:11px;font-weight:700;color:${color};margin-left:8px;">${arrow} ${diff > 0 ? '+' : ''}${diff} dari tahun lalu</span>`;
    }

    const btnTahunan = `<button onclick="showTambahDataTahunan()" style="background:var(--gold);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;">+ Input Data Tahunan</button>`;

    function penerimaBarSvg(list, data) {
      const maxV = Math.max(...list.map(t => data[t] || 0), 1);
      const H = 100, bW = 32, gap = 12;
      const scale = (H - 22) / maxV;
      const svgW  = list.length * (bW + gap) + 10;
      const bars  = list.map((th, i) => {
        const v  = data[th] || 0;
        const x  = i * (bW + gap) + 5;
        const bH = Math.max(4, v * scale);
        const y  = H - bH - 18;
        const isLast = i === list.length - 1;
        return `<rect x="${x}" y="${y}" width="${bW}" height="${bH}" rx="4" fill="${isLast ? 'var(--gold)' : 'rgba(200,146,42,0.35)'}"/>` +
          `<text x="${x + bW/2}" y="${H - 2}" text-anchor="middle" font-size="8" fill="var(--text3)">${th}</text>` +
          `<text x="${x + bW/2}" y="${y - 3}" text-anchor="middle" font-size="9" font-weight="700" fill="var(--text)">${v}</text>`;
      }).join('');
      return `<svg width="${svgW}" height="${H}" viewBox="0 0 ${svgW} ${H}">${bars}</svg>`;
    }

    document.getElementById('rekap-content').innerHTML = `
      <!-- ROW 1: Progress Distribusi + Status Proses -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;">

        <!-- b. Progress Distribusi -->
        <div class="card">
          <div class="card-header"><div class="card-title">\u{1F4CA} Progress Distribusi</div></div>
          <div class="card-body" style="display:flex;align-items:center;gap:24px;">
            <div class="rekap-ring">
              <svg width="120" height="120" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="48" fill="none" stroke="var(--bg4)" stroke-width="10"/>
                <circle cx="60" cy="60" r="48" fill="none" stroke="var(--gold)" stroke-width="10"
                  stroke-linecap="round" stroke-dasharray="${circ.toFixed(2)}" stroke-dashoffset="${off.toFixed(2)}"
                  style="transition:stroke-dashoffset .8s;transform:rotate(-90deg);transform-origin:60px 60px;"/>
              </svg>
              <div class="rekap-ring-label">
                <div class="rekap-ring-num">${pct}%</div>
                <div class="rekap-ring-sub">selesai</div>
              </div>
            </div>
            <div style="flex:1;">
              <div style="margin-bottom:14px;">
                <div style="font-size:32px;font-weight:800;color:var(--green);">${diambilDB}</div>
                <div style="font-size:12px;color:var(--text3);">Sudah diambil</div>
              </div>
              <div style="margin-bottom:10px;">
                <div style="font-size:32px;font-weight:800;color:var(--amber);">${belumDB}</div>
                <div style="font-size:12px;color:var(--text3);">Belum diambil</div>
              </div>
              <div style="font-size:11px;color:var(--text3);padding-top:8px;border-top:1px solid var(--border);">
                dari <strong style="color:var(--text);">${totalPenerima}</strong> total penerima terdaftar
              </div>
            </div>
          </div>
        </div>

        <!-- Status Proses Kurban -->
        <div class="card">
          <div class="card-header"><div class="card-title">\u{1F4CD} Status Proses Kurban</div></div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;">
              ${TIMELINE.map(t => `
                <div style="text-align:center;background:${t.status==='done'?'rgba(200,146,42,0.1)':t.status==='active'?'rgba(232,184,75,0.07)':'var(--bg3)'};border:1px solid ${t.status==='done'?'rgba(200,146,42,0.2)':t.status==='active'?'rgba(232,184,75,0.15)':'var(--border)'};border-radius:10px;padding:10px 4px;">
                  <div style="font-size:18px;margin-bottom:4px;">${t.status==='done'?'\u2705':t.icon}</div>
                  <div style="font-size:10px;font-weight:700;color:${t.status==='pending'?'var(--text3)':'var(--text)'};">${t.label}</div>
                  <div style="font-size:9px;font-weight:700;margin-top:3px;color:${t.status==='done'?'var(--gold2)':t.status==='active'?'var(--amber)':'var(--text3)'};">${t.time}</div>
                </div>`).join('')}
            </div>
          </div>
        </div>
      </div>

      <!-- a. Grafik Hewan Kurban Tahunan -->
      <div class="card" style="margin-bottom:18px;">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
          <div>
            <div class="card-title">\u{1F404} Data Hewan Kurban Tahunan</div>
            <div style="font-size:11px;color:var(--text3);margin-top:2px;">Hanya tampil di sini — Data Hewan & Dashboard hanya menampilkan tahun ini</div>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="downloadSemuaDataTahunan()" style="padding:6px 12px;font-size:11px;font-weight:700;border:1px solid var(--text3);background:var(--bg3);color:var(--text2);border-radius:8px;cursor:pointer;">\u2193 Download Semua</button>
            ${btnTahunan}
          </div>
        </div>
        <div class="card-body">
          ${tahunHewanList.length === 0
            ? `<div class="empty-state"><div class="empty-ico">\u{1F4CA}</div>Belum ada data hewan tahunan. Klik "+ Input Data Tahunan".</div>`
            : `<div style="display:flex;gap:28px;align-items:flex-end;overflow-x:auto;padding-bottom:8px;">
                ${hewanStats.map(stat => {
                  const maxV = Math.max(stat.sapi, stat.kambing, stat.domba, 1);
                  const H=110,bW=22,gap=7;
                  const scale=(H-20)/maxV;
                  const vals=[stat.sapi,stat.kambing,stat.domba];
                  const cols=['#c8922a','#e8b84b','#a09cf8'];
                  const lbs=['Sapi','Kbg','Domba'];
                  const svgW=vals.length*(bW+gap)+10;
                  const bars=vals.map((v,i)=>{
                    const x=i*(bW+gap)+5;
                    const bH=Math.max(4,v*scale);
                    const y=H-bH-14;
                    return `<rect x="${x}" y="${y}" width="${bW}" height="${bH}" rx="3" fill="${cols[i]}"/>`+
                      `<text x="${x+bW/2}" y="${H-1}" text-anchor="middle" font-size="7" fill="var(--text3)">${lbs[i]}</text>`+
                      `<text x="${x+bW/2}" y="${y-2}" text-anchor="middle" font-size="8" font-weight="700" fill="var(--text)">${v}</text>`;
                  }).join('');
                  return `<div style="text-align:center;flex-shrink:0;min-width:90px;">
                    <div style="font-size:12px;font-weight:700;color:var(--gold2);margin-bottom:6px;">${stat.tahun}</div>
                    <svg width="${svgW}" height="${H}" viewBox="0 0 ${svgW} ${H}">${bars}</svg>
                    <div style="font-size:10px;color:var(--text3);margin-top:3px;">Total: <strong>${stat.total}</strong> ekor</div>
                    <button onclick="downloadHewanTahunanExcel('${stat.tahun}')" title="Download data ${stat.tahun}"
                      style="margin-top:5px;padding:3px 8px;font-size:10px;font-weight:600;border:1px solid var(--gold2);background:rgba(200,146,42,0.08);color:var(--gold2);border-radius:5px;cursor:pointer;">\u2193 ${stat.tahun}</button>
                  </div>`;
                }).join('')}
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:10px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
                <div style="display:flex;gap:14px;">
                  ${[['\u{1F404}','Sapi','#c8922a'],['\u{1F410}','Kambing','#e8b84b'],['\u{1F411}','Domba','#a09cf8']].map(([em,lb,col])=>
                    `<div style="display:flex;align-items:center;gap:5px;font-size:12px;"><div style="width:10px;height:10px;border-radius:2px;background:${col};flex-shrink:0;"></div><span>${em} ${lb}</span></div>`
                  ).join('')}
                </div>
              </div>`
          }
        </div>
      </div>

      <!-- c. Grafik Penerima Pertahun -->
      <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
          <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
            <div class="card-title">\u{1F465} Data Penerima Pertahun</div>
            ${trendBadge(penerimaTahunList, penerimaTahunan)}
          </div>
          ${btnTahunan}
        </div>
        <div class="card-body">
          ${!hasPenerimaTahunan
            ? `<div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                <div>
                  <div style="font-size:36px;font-weight:800;color:var(--gold);">${totalPenerima}</div>
                  <div style="font-size:12px;color:var(--text3);">Penerima tahun ini</div>
                </div>
                <div style="color:var(--text3);font-size:12px;line-height:1.7;">
                  Data historis penerima belum tersedia.<br>
                  Klik "+ Input Data Tahunan" untuk mengisi.
                </div>
              </div>`
            : `<div style="display:flex;align-items:flex-end;gap:24px;flex-wrap:wrap;">
                ${penerimaBarSvg(penerimaTahunList, penerimaTahunan)}
                <div>
                  ${penerimaTahunList.map((th, i) => {
                    const jml  = penerimaTahunan[th] || 0;
                    const prev = i > 0 ? (penerimaTahunan[penerimaTahunList[i-1]] || 0) : null;
                    const diff = prev !== null ? jml - prev : null;
                    const arrow = diff === null ? '' : diff > 0 ? '\u2191' : diff < 0 ? '\u2193' : '\u2192';
                    const col   = diff === null ? '' : diff > 0 ? 'var(--green)' : diff < 0 ? 'var(--red)' : 'var(--text3)';
                    const isLast = i === penerimaTahunList.length - 1;
                    return `<div style="display:flex;align-items:center;gap:10px;padding:6px 0;${i < penerimaTahunList.length-1 ? 'border-bottom:1px solid var(--border);' : ''}">
                      <div style="font-size:12px;color:var(--text3);width:36px;">${th}</div>
                      <div style="font-size:${isLast?'22':'17'}px;font-weight:800;color:${isLast?'var(--gold)':'var(--text)'};">${jml}</div>
                      <div style="font-size:11px;color:var(--text3);">penerima</div>
                      ${diff !== null ? `<span style="font-size:11px;font-weight:700;color:${col};">${arrow} ${diff > 0 ? '+' : ''}${diff}</span>` : ''}
                    </div>`;
                  }).join('')}
                </div>
              </div>`
          }
        </div>
      </div>
    `;
  }

  function showTambahDataTahunan() {
    document.getElementById('modal-tahunan')?.remove();
    const tahunNow = new Date().getFullYear();
    const penData  = loadPenerimaTahunan();
    const html = `<div style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;" id="modal-tahunan">
      <div style="background:var(--bg2);border-radius:16px;padding:24px;width:360px;max-width:100%;max-height:90vh;overflow-y:auto;">
        <div style="font-size:16px;font-weight:700;margin-bottom:4px;">\u{1F4CA} Input Data Tahunan</div>
        <div style="font-size:12px;color:var(--text3);margin-bottom:18px;">Isi data hewan dan penerima per tahun untuk grafik statistik di Rekap.</div>

        <label style="font-size:12px;color:var(--text3);font-weight:600;">Tahun</label>
        <input id="inp-tahun" type="number" value="${tahunNow}" min="2000" max="2099"
          style="width:100%;margin-top:4px;margin-bottom:14px;padding:9px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);color:var(--text);box-sizing:border-box;font-size:14px;">

        <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">\u{1F404} Jumlah Hewan Kurban</div>

        <label style="font-size:12px;color:var(--text3);">Sapi</label>
        <input id="inp-sapi" type="number" value="0" min="0"
          style="width:100%;margin-top:4px;margin-bottom:10px;padding:9px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);color:var(--text);box-sizing:border-box;">

        <label style="font-size:12px;color:var(--text3);">Kambing</label>
        <input id="inp-kambing" type="number" value="0" min="0"
          style="width:100%;margin-top:4px;margin-bottom:10px;padding:9px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);color:var(--text);box-sizing:border-box;">

        <label style="font-size:12px;color:var(--text3);">Domba</label>
        <input id="inp-domba" type="number" value="0" min="0"
          style="width:100%;margin-top:4px;margin-bottom:16px;padding:9px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);color:var(--text);box-sizing:border-box;">

        <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">\u{1F465} Jumlah Penerima Kurban</div>

        <label style="font-size:12px;color:var(--text3);">Jumlah Penerima</label>
        <input id="inp-penerima" type="number" value="${penData[tahunNow] || 0}" min="0"
          style="width:100%;margin-top:4px;margin-bottom:20px;padding:9px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);color:var(--text);box-sizing:border-box;">

        <div style="display:flex;gap:10px;">
          <button onclick="document.getElementById('modal-tahunan').remove()"
            style="flex:1;padding:10px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);color:var(--text);cursor:pointer;font-size:13px;">Batal</button>
          <button onclick="simpanDataTahunan()"
            style="flex:1;padding:10px;border-radius:8px;border:none;background:var(--gold);color:#fff;font-weight:700;cursor:pointer;font-size:13px;">Simpan</button>
        </div>
      </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('inp-tahun').addEventListener('input', function() {
      const t  = this.value;
      const pd = loadPenerimaTahunan();
      document.getElementById('inp-penerima').value = pd[t] || 0;
    });
  }

  function simpanDataTahunan() {
    const tahun    = parseInt(document.getElementById('inp-tahun').value, 10);
    const sapi     = parseInt(document.getElementById('inp-sapi').value, 10)     || 0;
    const kambing  = parseInt(document.getElementById('inp-kambing').value, 10)  || 0;
    const domba    = parseInt(document.getElementById('inp-domba').value, 10)    || 0;
    const penerima = parseInt(document.getElementById('inp-penerima').value, 10) || 0;

    if (!tahun || tahun < 2000) { toast('Tahun tidak valid', 'error'); return; }

    // Hapus hewan lama untuk tahun ini, lalu isi ulang
    HEWAN = HEWAN.filter(h => String(h.tahun) !== String(tahun));
    for (let i = 0; i < sapi;    i++) HEWAN.push({ id_hewan: nextHewanId++, jenis: 'sapi',    label: 'Sapi ' + tahun,    tahun, umur: '\u2014', sehat: 'Ya', cacat: 'Tidak', st_syariat: 'Sah', berat: '\u2014' });
    for (let i = 0; i < kambing; i++) HEWAN.push({ id_hewan: nextHewanId++, jenis: 'kambing', label: 'Kambing ' + tahun, tahun, umur: '\u2014', sehat: 'Ya', cacat: 'Tidak', st_syariat: 'Sah', berat: '\u2014' });
    for (let i = 0; i < domba;   i++) HEWAN.push({ id_hewan: nextHewanId++, jenis: 'domba',   label: 'Domba ' + tahun,   tahun, umur: '\u2014', sehat: 'Ya', cacat: 'Tidak', st_syariat: 'Sah', berat: '\u2014' });
    saveStore();
    saveNextHewanId();

    const penData = loadPenerimaTahunan();
    penData[String(tahun)] = penerima;
    savePenerimaTahunan(penData);

    document.getElementById('modal-tahunan')?.remove();
    toast(`Data tahun ${tahun} berhasil disimpan`, 'success');
    renderRekap();
  }

  // ── Download Excel data hewan per tahun ────────────────────────────────────
  function downloadHewanTahunanExcel(tahun) {
    tahun = String(tahun);
    const hewanTahun  = HEWAN.filter(h => String(h.tahun) === tahun);
    const penData     = loadPenerimaTahunan();
    const jmlPenerima = penData[tahun] || 0;

    const rows = [
      ['KurbanQu - Data Tahunan ' + tahun],
      [],
      ['=== DATA HEWAN KURBAN ' + tahun + ' ==='],
      ['ID Hewan', 'Label', 'Jenis', 'Jumlah Ekor', 'Umur', 'Berat', 'Kondisi', 'Status Syariat'],
    ];

    // Summary per jenis
    ['sapi','kambing','domba'].forEach(jenis => {
      const list = hewanTahun.filter(h => h.jenis === jenis);
      if (list.length) {
        rows.push(['', jenis.charAt(0).toUpperCase()+jenis.slice(1), jenis, list.length, '-', '-', 'Sehat', 'Sah']);
      }
    });

    rows.push([]);
    rows.push(['Ringkasan Hewan ' + tahun]);
    rows.push(['Jenis', 'Jumlah']);
    rows.push(['Sapi',    hewanTahun.filter(h => h.jenis === 'sapi').length]);
    rows.push(['Kambing', hewanTahun.filter(h => h.jenis === 'kambing').length]);
    rows.push(['Domba',   hewanTahun.filter(h => h.jenis === 'domba').length]);
    rows.push(['TOTAL',   hewanTahun.length]);
    rows.push([]);
    rows.push(['=== DATA PENERIMA ' + tahun + ' ===']);
    rows.push(['Jumlah Penerima', jmlPenerima]);

    const csv  = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const bom  = '\uFEFF';
    const blob = new Blob([bom + csv], { type: 'text/csv;charset=utf-8;' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'kurbanqu_' + tahun + '.csv';
    a.click();
    toast('Data tahun ' + tahun + ' diunduh', 'success');
  }

  // ── Download ringkasan semua tahun ──────────────────────────────────────────
  function downloadSemuaDataTahunan() {
    const tahunSet = new Set(HEWAN.filter(h => h.tahun).map(h => String(h.tahun)));
    const penData  = loadPenerimaTahunan();
    Object.keys(penData).forEach(t => tahunSet.add(t));
    const tahunList = [...tahunSet].sort();

    if (!tahunList.length) { toast('Belum ada data tahunan', 'info'); return; }

    const rows = [
      ['KurbanQu - Rekapitulasi Data Tahunan'],
      ['Dibuat: ' + new Date().toLocaleDateString('id-ID')],
      [],
      ['Tahun', 'Total Hewan', 'Sapi', 'Kambing', 'Domba', 'Total Penerima'],
    ];
    tahunList.forEach(th => {
      const list = HEWAN.filter(h => String(h.tahun) === th);
      rows.push([
        th,
        list.length,
        list.filter(h => h.jenis === 'sapi').length,
        list.filter(h => h.jenis === 'kambing').length,
        list.filter(h => h.jenis === 'domba').length,
        penData[th] || 0,
      ]);
    });

    const csv  = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const bom  = '\uFEFF';
    const blob = new Blob([bom + csv], { type: 'text/csv;charset=utf-8;' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'kurbanqu_rekap_tahunan.csv';
    a.click();
    toast('Rekap semua tahun diunduh', 'success');
  }

  window.showTambahDataTahunan    = showTambahDataTahunan;
  window.simpanDataTahunan        = simpanDataTahunan;
  window.downloadHewanTahunanExcel = downloadHewanTahunanExcel;
  window.downloadSemuaDataTahunan  = downloadSemuaDataTahunan;
  // ═══════════════════════════════════════════
  // SUBMIT FORMS
  // ═══════════════════════════════════════════
  async function submitHewan() {
  const jenisRaw = document.getElementById('h-jenis').value;
  const jenis = jenisRaw.charAt(0).toUpperCase() + jenisRaw.slice(1);
  const label  = document.getElementById('h-label').value.trim();
  const umur   = document.getElementById('h-umur').value.trim();
  const berat  = document.getElementById('h-berat').value.trim();
  const sehatRaw = document.getElementById('h-sehat').value;
  const sehat = sehatRaw === 'Ya' ? 'Sehat' : 'Tidak Sehat';
  const cacatRaw = document.getElementById('h-cacat').value;
  const cacat = cacatRaw === 'Ada' ? 'Cacat' : 'Tidak Cacat';
  const syariat = document.getElementById('h-syariat').value === 'Sah' ? 1 : 0;

  if (!jenis) { toast('Jenis hewan wajib diisi!', 'error'); return; }

  try {
    const res = await fetch('/admin/api/hewan', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        jenis: jenis,
        sehat: sehat,
        cacat: cacat,
        umur: umur || null,
        berat: berat || null,
        st_syariat: syariat,
      }),
    });
    const payload = await res.json().catch(() => ({}));
    if (!res.ok || !payload.success) {
      throw new Error(payload.message || 'Gagal menambahkan hewan');
    }

    await loadHewanFromServer();
    closeModal('modal-hewan');
    renderHewanTable();
    renderDashboard();
    toast((label || 'Hewan') + ' berhasil ditambahkan', 'success');
  } catch (error) {
    console.error(error);
    toast(error.message || 'Gagal menambahkan hewan', 'error');
  }
}

async function submitMudhohi() {
  const nama = document.getElementById('m-nama').value.trim();
  const hewanId = document.getElementById('m-hewan').value;
  if (!nama || !hewanId) { toast('Nama & hewan wajib diisi!', 'error'); return; }

  try {
    const res = await fetch('/admin/api/mudhohi', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        nama_mudhohi: nama,
        nama_ayah: document.getElementById('m-ayah').value.trim() || null,
        alamat: document.getElementById('m-alamat').value.trim() || null,
        notelp_mudhohi: document.getElementById('m-telp').value.trim() || null,
        req_bagian: document.getElementById('m-bagian').value.trim() || null,
        hewan_id_hewan: hewanId,
      }),
    });
    const payload = await res.json().catch(() => ({}));
    if (!res.ok || !payload.success) throw new Error(payload.message || 'Gagal menambahkan mudhohi');

    await loadMudhohiFromServer();
    closeModal('modal-mudhohi');
    renderMudhohiTable();
    renderDashboard();
    toast(nama + ' berhasil ditambahkan', 'success');
  } catch (error) {
    console.error(error);
    toast(error.message || 'Gagal menambahkan mudhohi', 'error');
  }
}
  
  function logout() {
    if (confirm('Yakin ingin logout?')) {
      toast('Logout berhasil', 'info');
      setTimeout(() => { document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#0f0d0b;color:#a09080;font-family:sans-serif;font-size:16px;">Session berakhir. Silakan refresh halaman.</div>'; }, 800);
    }
  }
  
  // ═══════════════════════════════════════════
  // PENERIMA KURBAN — Excel → login warga + QR
  // ═══════════════════════════════════════════
  
  let importedPenerima = [];
  let confirmedPenerima = loadPenerima();
  
  function handleFileDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    const dz = document.getElementById('drop-zone');
    if (dz) { dz.classList.remove('drag'); }
    const file = e.dataTransfer?.files?.[0];
    if (!file) { toast('Tidak ada file yang terdeteksi', 'error'); return; }
    const ext = (file.name || '').split('.').pop().toLowerCase();
    if (!['csv','xlsx','xls'].includes(ext)) {
      toast('Format file tidak didukung. Gunakan .csv, .xlsx, atau .xls', 'error');
      return;
    }
    readFileAsCSV(file);
  }
  // Expose ke window agar bisa dipanggil dari ondrop di HTML
  window.handleFileDrop = handleFileDrop;

  function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    readFileAsCSV(file);
  }
  window.handleFileSelect = handleFileSelect;
  
  function readFileAsCSV(file) {
      const ext = (file.name || '').split('.').pop().toLowerCase();
      const isExcel = ext === 'xlsx' || ext === 'xls';
      if (isExcel && typeof window.XLSX === 'undefined') {
        toast('Library Excel belum siap. Coba lagi atau simpan file sebagai CSV UTF-8.', 'error');
        return;
      }
      // If Excel file, read as array buffer and convert to CSV using SheetJS (xlsx)
      if (isExcel) {
        const reader = new FileReader();
        reader.onload = e => {
          try {
            const data = new Uint8Array(e.target.result);
            const wb = window.XLSX.read(data, { type: 'array' });
            const first = wb.SheetNames[0];
            const csv = window.XLSX.utils.sheet_to_csv(wb.Sheets[first] || {});
            const rows = parseCSV(csv);
            previewImport(rows, file.name);
          } catch (err) {
            console.error('Excel conversion failed', err);
            toast('Gagal membaca file Excel. Simpan sebagai CSV UTF-8 sebagai alternatif.', 'error');
          }
        };
        reader.onerror = () => {
          toast('Gagal membaca file Excel. Pastikan file tidak rusak.', 'error');
        };
        reader.readAsArrayBuffer(file);
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        const text = e.target.result;
        const rows = parseCSV(text);
        previewImport(rows, file.name);
      };
      reader.onerror = () => {
        toast('Gagal membaca file CSV. Pastikan file menggunakan encoding UTF-8.', 'error');
      };
      reader.readAsText(file, 'UTF-8');
  }
  
  function parseCSVPaste() {
    const text = document.getElementById('csv-paste').value.trim();
    if (!text) { toast('Tidak ada data untuk diproses', 'error'); return; }
    const rows = parseCSV(text);
    previewImport(rows, 'paste');
  }
  window.parseCSVPaste = parseCSVPaste;
  
  function getImportMode() {
    const el = document.querySelector('input[name="import-mode"]:checked');
    return el?.value === 'replace' ? 'replace' : 'append';
  }

  /**
   * Auto-detect CSV format:
   * - separator: comma / semicolon / tab
   * - header kolom: No KK, Nama KK, Nama Kepala Keluarga, Alamat, Telepon, dll
   * - posisi kolom: bisa urutan apapun, dideteksi dari header
   */
  function parseCSV(text) {
    if (!text) return [];
    // Hapus BOM UTF-8 dan normalize line endings
    text = text.replace(/^\uFEFF/, '').replace(/\r\n?/g, '\n');
    const lines = text.split('\n').map(l => l.trim()).filter(l => l);
    if (!lines.length) return [];

    // ── Deteksi separator (comma, semicolon, tab)
    const firstLine = lines[0];
    const countSemi = (firstLine.match(/;/g) || []).length;
    const countComma = (firstLine.match(/,/g) || []).length;
    const countTab = (firstLine.match(/\t/g) || []).length;
    let sep = ',';
    if (countSemi > countComma && countSemi > countTab) sep = ';';
    else if (countTab > countComma) sep = '\t';

    // ── Fungsi split satu baris sesuai separator (handle quoted fields)
    function splitLine(line) {
      const re = sep === ',' 
        ? /,(?=(?:[^"]*"[^"]*")*[^"]*$)/
        : sep === ';'
          ? /;(?=(?:[^"]*"[^"]*")*[^"]*$)/
          : /\t/;
      return line.split(re).map(c => c.replace(/^"|"$/g,'').trim());
    }

    // ── Deteksi header baris pertama
    const headerLine = lines[0].toLowerCase();
    const isHeader = headerLine.includes('nama') || headerLine.includes('kk') ||
                     headerLine.includes('no') || headerLine.includes('nkk') ||
                     headerLine.includes('kepala') || headerLine.includes('keluarga') ||
                     headerLine.includes('telp') || headerLine.includes('alamat');

    let colNkk = -1, colNama = -1, colAlamat = -1, colTelp = -1;

    if (isHeader) {
      // Petakan kolom dari header
      const headers = splitLine(lines[0]).map(h => h.toLowerCase().replace(/[^a-z0-9]/g,''));
      headers.forEach((h, i) => {
        // Deteksi kolom No KK
        if (colNkk < 0 && (h.includes('nkk') || h.includes('nokk') || h.includes('nomorkk') ||
            h.includes('nokk') || h.includes('nok') || h === 'kk' || h.includes('kartukeluarga'))) {
          colNkk = i;
        }
        // Deteksi kolom Nama
        if (colNama < 0 && (h.includes('nama') || h.includes('kepala') || h.includes('namakk') ||
            h.includes('namakeluarga') || h.includes('namalenngkap') || h === 'name')) {
          colNama = i;
        }
        // Deteksi kolom Alamat
        if (colAlamat < 0 && (h.includes('alamat') || h.includes('address') || h.includes('jalan') ||
            h.includes('domisili') || h.includes('rtrw'))) {
          colAlamat = i;
        }
        // Deteksi kolom Telepon
        if (colTelp < 0 && (h.includes('telp') || h.includes('telepon') || h.includes('hp') ||
            h.includes('phone') || h.includes('nohp') || h.includes('notelp') ||
            h.includes('handphone') || h.includes('wa') || h.includes('whatsapp'))) {
          colTelp = i;
        }
      });
    }

    // Fallback: jika header tidak terdeteksi, pakai posisi default
    const dataLines = isHeader ? lines.slice(1) : lines;
    const useHeader = colNkk >= 0 || colNama >= 0;

    // Jika tidak ada header sama sekali, coba tebak posisi dari baris pertama data
    if (!useHeader) {
      const firstData = splitLine(dataLines[0] || '');
      // Cari kolom yang terlihat seperti No KK (panjang, hanya angka)
      firstData.forEach((val, i) => {
        const digits = val.replace(/\D/g,'');
        if (colNkk < 0 && digits.length >= 10) colNkk = i;
      });
      // Kolom nama = kolom lain yang bukan angka murni
      firstData.forEach((val, i) => {
        if (i !== colNkk && colNama < 0 && val.replace(/[a-zA-Z\s]/g,'').length < val.length / 2 && val.length >= 2) {
          colNama = i;
        }
      });
    }

    // Default akhir jika masih tidak ketemu
    if (colNkk < 0) colNkk = 0;
    if (colNama < 0) colNama = 1;
    if (colAlamat < 0) colAlamat = 2;
    if (colTelp < 0) colTelp = 3;

    return dataLines.map(line => {
      if (!line.trim()) return null;
      const cols = splitLine(line);
      const nkk  = normNkk(cols[colNkk] || '');
      const nama  = (cols[colNama] || '').trim();
      // Toleran: terima No KK minimal 6 digit, nama minimal 1 karakter
      if (!nama || nama.length < 1) return null;
      if (nkk.length < 6) return null;
      return {
        nkk,
        nama,
        alamat: colAlamat < cols.length ? (cols[colAlamat] || '').trim() : '',
        notelp: colTelp  < cols.length ? (cols[colTelp]  || '').trim() : '',
      };
    }).filter(r => r !== null);
  }

  function updatePenerimaBadge() {
    const n = loadPenerima().length;
    const badge = document.getElementById('badge-penerima');
    if (badge) {
      badge.textContent = n;
      badge.style.display = n ? 'inline-flex' : 'none';
    }
  }

function renderPenerimaPage() {
    updatePenerimaBadge();
    confirmedPenerima = loadPenerima();
    const chips = document.getElementById('penerima-stat-chips');
    if (chips) {
      const n = confirmedPenerima.length;
      chips.innerHTML = [
        ['🎫 Terdaftar', n, 'var(--gold2)', 'var(--gold-dim)'],
        ['🔐 Bisa login', n, 'var(--green)', 'var(--green-bg)'],
        ['📱 Dapat QR', n, 'var(--blue)', 'rgba(91,156,246,0.1)'],
      ].map(([lbl, val, col, bg]) =>
        `<div style="background:${bg};border:1px solid ${col}33;border-radius:20px;padding:8px 16px;">
          <span style="font-size:11px;color:var(--text3);">${lbl}</span>
          <strong style="font-size:18px;color:${col};margin-left:8px;">${val}</strong>
        </div>`
          ).join('');
    }
    renderPenerimaTable();
  }

  function previewImport(rows, source) {
    importedPenerima = rows;
    document.getElementById('preview-stats').innerHTML =
      `<span style="color:var(--green);font-weight:700;">${rows.length} penerima</span> siap diaktifkan`;

    if (!rows.length) {
      document.getElementById('preview-content').innerHTML =
        '<div class="empty-state"><div class="empty-ico">⚠️</div>Data tidak valid. Wajib: No KK (min. 10 digit) + Nama Kepala Keluarga.</div>';
      document.getElementById('preview-actions').style.display = 'none';
      return;
    }

    document.getElementById('preview-content').innerHTML = 
      `<table class="data-table" style="min-width:480px;">
        <thead><tr><th>#</th><th>No KK</th><th>Nama Kepala Keluarga</th><th>Alamat</th><th>Telp</th></tr></thead>
        <tbody>
          ${rows.slice(0, 25).map((r, i) => `<tr>
            <td style="color:var(--text3);font-size:11px;">${i + 1}</td>
            <td><code style="font-size:10px;color:var(--blue);">${r.nkk}</code></td>
            <td><strong>${r.nama}</strong></td>
            <td style="font-size:12px;color:var(--text3);">${r.alamat || '—'}</td>
            <td style="font-size:12px;color:var(--text3);">${r.notelp || '—'}</td>
          </tr>`).join('')}
          ${rows.length > 25 ? `<tr><td colspan="5" style="text-align:center;color:var(--text3);font-size:12px;">... +${rows.length - 25} baris</td></tr>` : ''}
        </tbody>
      </table>`;
    document.getElementById('preview-actions').style.display = 'block';
    toast(rows.length + ' baris dari ' + source, 'success');
  }

  function importConfirm() {
    if (!importedPenerima.length) return;
    const mode = getImportMode();
    const prev = mode === 'replace' ? 0 : confirmedPenerima.length;
    const total = mergePenerimaRows(importedPenerima, mode);
    // Kirim ke database
    const payload = importedPenerima.slice();
    fetch('/simpan-penerima', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
    },
    body: JSON.stringify({ penerima: payload, mode: mode }),
})
.then(r => r.json())
.then(res => {
    if (res.success) toast(res.message, 'success');
    else toast(res.message || 'Gagal simpan ke database', 'error');
})
.catch(() => toast('Gagal koneksi ke server', 'error'));  
    importedPenerima = [];
    confirmedPenerima = loadPenerima();

    renderPenerimaPage();
    updateDistStats();
    document.getElementById('preview-content').innerHTML =
      '<div class="empty-state"><div class="empty-ico">✅</div>Penerima aktif — warga sudah bisa login &amp; lihat QR. Data muncul di <strong>Tabel Distribusi</strong>.</div>';
    document.getElementById('preview-actions').style.display = 'none';
    document.getElementById('preview-stats').innerHTML = '';
    // Update badge distribusi
    const badgeDist = document.getElementById('badge-distribusi');
    if (badgeDist) badgeDist.textContent = penerimaClaimedSet.size;
    toast(
      mode === 'replace'
        ? total + ' penerima terdaftar (daftar diganti) — cek Tabel Distribusi'
        : (total - prev) + ' penerima ditambahkan · total ' + total + ' — cek Tabel Distribusi',
      'success'
    );
    setTimeout(() => document.getElementById('imported-list-card')?.scrollIntoView({ behavior: 'smooth' }), 300);
  }

  function renderPenerimaTable() {
    const tbody = document.getElementById('imported-table-body');
    const empty = document.getElementById('penerima-empty');
    if (!tbody) return;

    confirmedPenerima = loadPenerima();
    const q = (document.getElementById('penerima-search')?.value || '').toLowerCase();
    let list = confirmedPenerima.map((p, i) => ({ ...p, _idx: i }));
    if (q) {
      list = list.filter(p =>
        p.nama.toLowerCase().includes(q) ||
        p.nkk.includes(q) ||
        (p.qrCode || '').toLowerCase().includes(q)
      );
    }

    document.getElementById('imported-count').textContent =
      list.length + ' dari ' + confirmedPenerima.length + ' penerima';

    if (!confirmedPenerima.length) {
      tbody.innerHTML = '';
      if (empty) empty.style.display = 'block';
      return;
    }
    if (empty) empty.style.display = 'none';

    tbody.innerHTML = list.map(p => {
      const backend = getBackendDistribusiRow(p.nkk);

      const claimed = backend
    ? String(backend.st_pengambilan || '').toLowerCase() === 'selesai'
    : false;
      return `<tr>
        <td style="color:var(--text3);font-size:11px;">${p._idx + 1}</td>
        <td><code style="font-size:11px;color:var(--blue);">${p.nkk}</code></td>
        <td><strong>${p.nama}</strong></td>
        <td><span style="font-family:monospace;font-size:12px;font-weight:700;color:var(--gold2);">${p.qrCode || '—'}</span></td>
        <td style="font-size:12px;color:var(--text3);">${p.alamat || '—'}</td>
        <td style="font-size:12px;color:var(--text3);">${p.notelp || '—'}</td>
        <td><span class="status-badge ${claimed ? 'status-done' : 'status-pending'}">${claimed ? '✓ Sudah ambil' : 'Belum ambil'}</span></td>
        <td><button class="btn btn-danger btn-sm" onclick="removePenerima(${p._idx})">Hapus</button></td>
      </tr>`;
    }).join('');
  }

  function removePenerima(idx) {
    removePenerimaAt(idx);
    confirmedPenerima = loadPenerima();
    renderPenerimaPage();
    toast('Penerima dihapus dari daftar login', 'info');
  }

  function exportImportedCSV() {
    const list = loadPenerima();
    if (!list.length) return;
    const rows = [['No KK', 'Nama Kepala Keluarga', 'Kode QR', 'Alamat', 'No Telp'],
      ...list.map(p => [p.nkk, p.nama, p.qrCode, p.alamat, p.notelp])];
    const csv = rows.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'penerima_kurban_kurbanqu.csv';
    a.click();
    toast('CSV penerima diexport', 'success');
  }

  function openModalPenerima() {
    ['p-nkk', 'p-nama', 'p-alamat', 'p-telp'].forEach(id => {
      const e = document.getElementById(id);
      if (e) e.value = '';
    });
    openModal('modal-penerima');
  }

  function submitPenerimaManual() {
    const nkk = document.getElementById('p-nkk').value.trim();
    const nama = document.getElementById('p-nama').value.trim();
    if (!nkk || !nama) { toast('No KK & Nama wajib diisi', 'error'); return; }
    if (normNkk(nkk).length < 10) { toast('No KK minimal 10 digit', 'error'); return; }
    mergePenerimaRows([{
      nkk,
      nama,
      alamat: document.getElementById('p-alamat').value.trim(),
      notelp: document.getElementById('p-telp').value.trim(),
    }], 'append');
    confirmedPenerima = loadPenerima();
    closeModal('modal-penerima');
    renderPenerimaPage();
    toast(nama + ' ditambahkan sebagai penerima', 'success');
  }

  function clearAllPenerima() {
    const hasData = loadPenerima().length > 0 || importedPenerima.length > 0;
    if (!hasData && !pendingImportTempFile) {
      toast('Tidak ada data penerima untuk dibersihkan', 'info');
      return;
    }

    if (!confirm('Hapus semua data penerima, distribusi, dan riwayat terkait?')) {
      return;
    }

    fetch('/admin/api/penerima', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
    }).then(async response => {
      const payload = await response.json().catch(() => ({}));
      if (!response.ok || !payload.success) {
        throw new Error(payload.message || 'Gagal menghapus semua data penerima');
      }

      importedPenerima = [];
      confirmedPenerima = [];
      backendDistribusiByKk = {};
      backendDistribusiLoaded = false;
      penerimaClaimedSet = new Set();
      penerimaDownloadedSet = new Set();
      penerimaClaimMethod = {};
      penerimaClaimTime = {};
      penerimaDistLog = [];
      pendingImportTempFile = '';

      [
        STORAGE_PENERIMA,
        STORAGE_CONFIRMED_WARGA,
        STORAGE_WARGA_LOGIN,
        STORAGE_PENERIMA_ID,
        STORAGE_DIST_CLAIMED,
        STORAGE_DIST_DOWNLOADED,
        STORAGE_DIST_METHOD,
        STORAGE_DIST_TIME,
      ].forEach(key => localStorage.removeItem(key));

      const pasteEl = document.getElementById('csv-paste');
      const inputEl = document.getElementById('excel-input');
      const dropZone = document.getElementById('drop-zone');
      if (pasteEl) pasteEl.value = '';
      if (inputEl) inputEl.value = '';
      if (dropZone) {
        dropZone.classList.remove('drag');
        dropZone.innerHTML = `
          <div style="font-size:32px;margin-bottom:8px;">📊</div>
          <div style="font-size:14px;font-weight:600;color:var(--text2);">Klik atau drag &amp; drop file di sini</div>
          <div style="font-size:11px;color:var(--text3);margin-top:6px;">.csv · .xlsx · .xls — Format kolom otomatis terdeteksi</div>
        `;
      }

      const previewContent = document.getElementById('preview-content');
      const previewActions = document.getElementById('preview-actions');
      const previewStats  = document.getElementById('preview-stats');
      if (previewContent) previewContent.innerHTML = '<div class="empty-state"><div class="empty-ico">📋</div>Data akan tampil di sini setelah diproses</div>';
      if (previewActions) previewActions.style.display = 'none';
      if (previewStats)  previewStats.innerHTML = '';

      updatePenerimaBadge();
      renderPenerimaPage();
      renderDashboard();
      updateDistStats();
      renderTabelDistribusi();
      renderQuickScanList();
      renderTabelDistLog();
      toast('Semua data penerima berhasil dihapus', 'success');
    }).catch(error => {
      console.error(error);
      toast(error.message || 'Gagal menghapus semua data penerima', 'error');
    });
  }
  window.registerPendingImportTempFile = function(tempFile) {
    pendingImportTempFile = tempFile || '';
  };
  window.clearAllPenerima = clearAllPenerima;
  window.importConfirm = importConfirm;
  
  // Close modal on overlay click
  document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
  });
  
  // ─── SIDEBAR RESPONSIVE TOGGLE ─────────────────
  function toggleSidebar() {
    const sidebar  = document.querySelector('.sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const hamburger = document.getElementById('hamburger');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
    hamburger.classList.toggle('open');
  }
  function closeSidebar() {
    document.querySelector('.sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('open');
    document.getElementById('hamburger').classList.remove('open');
  }
  
  // Close sidebar when nav item is clicked on mobile
  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
      if (window.innerWidth <= 900) closeSidebar();
    });
  });
  
  // ─── INIT ──────────────────────────────────
  initDataIds();
  loadHewanFromServer().then(() => { if (currentPage === 'hewan') renderHewanTable(); renderDashboard(); });
  loadMudhohiFromServer().then(() => { if (currentPage === 'mudhohi') renderMudhohiTable(); });
  updatePenerimaBadge();
  renderPenerimaPage();
  renderDashboard();
  renderScanList();
  updateBadgeTracking();
  refreshDistribusiSnapshot();

  // ── Auto-refresh snapshot dari DB setiap 10 detik (realtime status download & scan)
  setInterval(async () => {
    await refreshDistribusiSnapshot();
    // Re-render tabel distribusi jika sedang aktif
    if (document.getElementById('pg-distribusi')?.classList.contains('active') ||
        document.getElementById('pg-tabel-distribusi')?.classList.contains('active')) {
      renderDistribusiPage();
    }
  }, 10000);