// ═══════════════════════════════════════════
// DATA
// ═══════════════════════════════════════════
const TIMELINE = [
    { label:'Penyembelihan', desc:'Proses penyembelihan hewan kurban telah selesai dilakukan.', status:'done',    time:'06:30 WIB', icon:'🔪' },
    { label:'Pengulitan',    desc:'Pengulitan dan pembersihan selesai dilakukan tim operasional.', status:'done', time:'07:10 WIB', icon:'🐄' },
    { label:'Pencacahan',    desc:'Daging sedang dipotong dan disiapkan untuk distribusi.', status:'active',       time:'07:45 WIB', icon:'🥩' },
    { label:'Penimbangan',   desc:'Daging akan ditimbang dan dikemas per bagian.', status:'pending',              time:'~09:00 WIB', icon:'⚖️' },
    { label:'Siap Diambil',  desc:'Distribusi QR akan dimulai setelah proses penimbangan selesai.', status:'pending', time:'~10:30 WIB', icon:'✅' },
  ];
  
  const ANIMALS = {
    sapi: [
      { id:'S01', emoji:'🐄', label:'Sapi Putih No.01', jenis:'Sapi', umur:'3 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±230 kg', alamat:'Kp. Cikaret RT 02/03', notelp:'0812-xxxx-1234', reqBagian:'7 bagian',
        mudhohi:[{i:'AH',nama:'Ahmad Hidayat',bagian:'1/7',warna:'brown',tipe:'mudhohi',nkk:'3273011234567890'},{i:'SR',nama:'Siti Rahmawati',bagian:'2/7',warna:'green',tipe:'penerima',nkk:'3273012345678901'},{i:'BU',nama:'Budi Utomo',bagian:'3/7',warna:'amber',tipe:'penerima',nkk:'3273013456789012'},{i:'RN',nama:'Rina Nuraini',bagian:'4/7',warna:'purple',tipe:'penerima',nkk:'3273014567890123'},{i:'MS',nama:'Maman Suparman',bagian:'5/7',warna:'brown',tipe:'penerima',nkk:'3273015500000001'},{i:'DF',nama:'Dewi Fitriani',bagian:'6/7',warna:'green',tipe:'penerima',nkk:'3273015500000002'},{i:'YP',nama:'Yusuf Pratama',bagian:'7/7',warna:'amber',tipe:'penerima',nkk:'3273015500000003'}]},
      { id:'S02', emoji:'🐄', label:'Sapi Hitam No.02', jenis:'Sapi', umur:'4 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±250 kg', alamat:'Jl. Mawar No.7 RT 01/04', notelp:'0821-xxxx-5678', reqBagian:'7 bagian',
        mudhohi:[{i:'HM',nama:'Hendra Maulana',bagian:'1/7',warna:'amber',tipe:'mudhohi',nkk:'3273016789012345'},{i:'NR',nama:'Nurul Rizki',bagian:'2/7',warna:'purple',tipe:'penerima',nkk:'3273017890123456'},{i:'AS',nama:'Agus Santoso',bagian:'3/7',warna:'brown',tipe:'penerima',nkk:'3273018001000001'},{i:'LW',nama:'Lilis Wulandari',bagian:'4/7',warna:'green',tipe:'penerima',nkk:'3273018001000002'},{i:'FZ',nama:'Fajar Zulkifli',bagian:'5/7',warna:'amber',tipe:'penerima',nkk:'3273018001000003'},{i:'TH',nama:'Tini Hartati',bagian:'6/7',warna:'brown',tipe:'penerima',nkk:'3273018001000004'},{i:'RP',nama:'Rizal Permana',bagian:'7/7',warna:'purple',tipe:'penerima',nkk:'3273018001000005'}]},
    ],
    kambing: [
      { id:'K01', emoji:'🐐', label:'Kambing No.01', jenis:'Kambing Jawa', umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±32 kg', alamat:'Kp. Babakan RT 03/05', notelp:'0838-xxxx-0011', reqBagian:'1 bagian', mudhohi:[{i:'DN',nama:'Drs. Haji Nurdian',warna:'amber',tipe:'mudhohi',nkk:'3273015678901234'}]},
      { id:'K02', emoji:'🐐', label:'Kambing No.02', jenis:'Kambing PE',   umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±35 kg', alamat:'Jl. Kenanga No.3', notelp:'0857-xxxx-2233', reqBagian:'1 bagian', mudhohi:[{i:'FH',nama:'Fitri Handayani',warna:'brown',tipe:'penerima',nkk:'3273018901234567'}]},
      { id:'K03', emoji:'🐐', label:'Kambing No.03', jenis:'Kambing Boer',  umur:'3 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±40 kg', alamat:'Perum Griya Asri Blok B2', notelp:'0812-xxxx-4455', reqBagian:'1 bagian', mudhohi:[{i:'ZA',nama:'Zainal Abidin',warna:'green',tipe:'mudhohi',nkk:'3273019012345678'}]},
      { id:'K04', emoji:'🐐', label:'Kambing No.04', jenis:'Kambing Jawa',  umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±30 kg', alamat:'RT 04 RW 02', notelp:'0877-xxxx-6677', reqBagian:'1 bagian', mudhohi:[{i:'ML',nama:'Mulyadi',warna:'purple',tipe:'mudhohi',nkk:'3273019200000001'}]},
      { id:'K05', emoji:'🐐', label:'Kambing No.05', jenis:'Kambing Kacang',umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Mata kiri', berat:'±28 kg', alamat:'Kp. Sindangjaya', notelp:'0821-xxxx-8899', reqBagian:'1 bagian', mudhohi:[{i:'IS',nama:'Ibu Sumiati',warna:'brown',tipe:'mudhohi',nkk:'3273019200000002'}]},
      { id:'K06', emoji:'🐐', label:'Kambing No.06', jenis:'Kambing PE',    umur:'3 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±38 kg', alamat:'Jl. Merdeka Blok C', notelp:'0856-xxxx-0012', reqBagian:'1 bagian', mudhohi:[{i:'RJ',nama:'Rudi Juanda',warna:'green',tipe:'mudhohi',nkk:'3273019200000003'}]},
      { id:'K07', emoji:'🐐', label:'Kambing No.07', jenis:'Kambing Boer',  umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±36 kg', alamat:'RT 01/07 Desa Sukamaju', notelp:'0899-xxxx-3344', reqBagian:'1 bagian', mudhohi:[{i:'SA',nama:'Samsul Arifin',warna:'amber',tipe:'mudhohi',nkk:'3273019200000004'}]},
    ],
    domba: [
      { id:'D01', emoji:'🐑', label:'Domba No.01', jenis:'Domba Garut', umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±28 kg', alamat:'Kp. Cikaret Hilir', notelp:'0813-xxxx-5566', reqBagian:'1 bagian', mudhohi:[{i:'MP',nama:'Muhamad Prayogo',warna:'purple',tipe:'mudhohi',nkk:'3273010011223344'}]},
      { id:'D02', emoji:'🐑', label:'Domba No.02', jenis:'Domba Garut', umur:'3 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±30 kg', alamat:'Jl. Anggrek No.5', notelp:'0812-xxxx-7788', reqBagian:'1 bagian', mudhohi:[{i:'IK',nama:'Ibu Komariah',warna:'brown',tipe:'penerima',nkk:'3273011122334455'}]},
      { id:'D03', emoji:'🐑', label:'Domba No.03', jenis:'Domba Lokal', umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±26 kg', alamat:'RT 06/02 Blok Timur', notelp:'0878-xxxx-9900', reqBagian:'1 bagian', mudhohi:[{i:'AB',nama:'Agus Budiman',warna:'green',tipe:'penerima',nkk:'3273020000000001'}]},
      { id:'D04', emoji:'🐑', label:'Domba No.04', jenis:'Domba Garut', umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±29 kg', alamat:'Perum Bukit Indah No.12', notelp:'0838-xxxx-1122', reqBagian:'1 bagian', mudhohi:[{i:'YL',nama:'Yuli Lestari',warna:'amber',tipe:'penerima',nkk:'3273012233445566'}]},
      { id:'D05', emoji:'🐑', label:'Domba No.05', jenis:'Domba Lokal', umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±25 kg', alamat:'Gang Masjid No.3', notelp:'0857-xxxx-3344', reqBagian:'1 bagian', mudhohi:[{i:'DK',nama:'Dadang Kurnia',warna:'purple',tipe:'penerima',nkk:'3273020000000002'}]},
      { id:'D06', emoji:'🐑', label:'Domba No.06', jenis:'Domba Garut', umur:'3 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±32 kg', alamat:'Kp. Warungdowo', notelp:'0812-xxxx-5566', reqBagian:'1 bagian', mudhohi:[{i:'NH',nama:'Nining Hernawati',warna:'brown',tipe:'penerima',nkk:'3273013344556677'}]},
      { id:'D07', emoji:'🐑', label:'Domba No.07', jenis:'Domba Lokal', umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±24 kg', alamat:'RT 03/05 Desa Tanjung', notelp:'0821-xxxx-7788', reqBagian:'1 bagian', mudhohi:[{i:'HF',nama:'Hendra Firmansyah',warna:'green',tipe:'penerima',nkk:'3273020000000003'}]},
      { id:'D08', emoji:'🐑', label:'Domba No.08', jenis:'Domba Garut', umur:'2 Tahun', sehat:'✓ Sehat', syariat:'✓ Sah', cacat:'Tidak ada', berat:'±27 kg', alamat:'Blok D No.9 Perumahan', notelp:'0877-xxxx-9900', reqBagian:'1 bagian', mudhohi:[{i:'SN',nama:'Siti Nurhasanah',warna:'amber',tipe:'penerima',nkk:'3273020000000004'}]},
    ],
  };
  
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
  
  // ═══════════════════════════════════════════
  // HELPERS
  // ═══════════════════════════════════════════
  function getAllAnimals() {
    return [...ANIMALS.sapi, ...ANIMALS.kambing, ...ANIMALS.domba];
  }
  function getAllMudhohi() {
    const list = [];
    getAllAnimals().forEach(a => {
      a.mudhohi.forEach(m => list.push({ ...m, animalId: a.id, animalLabel: a.label, animalEmoji: a.emoji, animalType: a.id.startsWith('S') ? 'sapi' : a.id.startsWith('K') ? 'kambing' : 'domba', tipe: m.tipe || 'penerima', nkk: m.nkk || '' }));
    });
    return list;
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
    mudhohi:    ['Data Mudhohi', 'Daftar penerima daging kurban'],
    tracking:   ['Live Tracking', 'Update status proses kurban real-time'],
    distribusi: ['Distribusi QR', 'Scan & verifikasi penerima daging kurban'],
    upload:     ['Upload Data Excel', 'Import daftar warga penerima kurban dari file Excel'],
    tabel:      ['Tabel Distribusi', 'Rekap lengkap status distribusi per penerima'],
    warga:      ['Data Warga', 'Informasi lengkap per warga / penerima'],
    rekap:      ['Rekap & Statistik', 'Ringkasan pelaksanaan kurban 1446 H'],
  };
  
  function navTo(page, el) {
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
    if (page === 'upload')     renderImportedTable();
    if (page === 'tabel')      renderTabelDistribusi();
    if (page === 'warga')      renderWargaList();
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
    ['h-jenis','h-label','h-umur','h-berat','h-alamat','h-telp','h-catatan'].forEach(i => { const e=document.getElementById(i); if(e) e.value=''; });
    openModal('modal-hewan');
  }
  function openModalMudhohi() {
    ['m-nama','m-nkk','m-telp','m-bagian','m-alamat'].forEach(i => { const e=document.getElementById(i); if(e) e.value=''; });
    // populate hewan select
    const sel = document.getElementById('m-hewan');
    sel.innerHTML = '<option value="">-- Pilih Hewan --</option>' +
      getAllAnimals().map(a => `<option value="${a.id}">${a.emoji} ${a.label}</option>`).join('');
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
  
    document.getElementById('s-hewan').textContent    = getAllAnimals().length;
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
  
    // Bar chart
    const sapi = ANIMALS.sapi.reduce((a,s) => a + s.mudhohi.length, 0);
    const kambing = ANIMALS.kambing.length;
    const domba = ANIMALS.domba.length;
    const bars = [['🐄 Sapi', sapi, '#c8922a'], ['🐐 Kambing', kambing, '#e8b84b'], ['🐑 Domba', domba, '#a09cf8']];
    document.getElementById('bar-chart').innerHTML = bars.map(([lbl, val, col]) => `
      <div class="bar-row">
        <div class="bar-label" style="font-size:11px;">${lbl}</div>
        <div class="bar-track"><div class="bar-fill" style="width:${Math.round(val/total*100)}%;background:${col};"></div></div>
        <div class="bar-val">${val}</div>
      </div>`).join('');
  
    // Animal list (first 3)
    const dashList = document.getElementById('dash-animal-list');
    dashList.innerHTML = getAllAnimals().slice(0, 3).map(a => {
      const st = animalStatus(a);
      return `<div class="animal-row" onclick="showDetailHewan('${a.id}')">
        <div class="animal-avatar">${a.emoji}</div>
        <div><div class="animal-name">${a.label}</div><div class="animal-sub">${a.sehat} · ${a.syariat} · ${a.umur}</div></div>
        <span class="status-badge ${st === 'done' ? 'status-done' : st === 'active' ? 'status-active' : 'status-pending'}">${st === 'done' ? 'Selesai' : st === 'active' ? 'Diproses' : 'Pending'}</span>
      </div>`;
    }).join('') + `<div style="text-align:center;margin-top:10px;"><button class="btn btn-ghost btn-sm" onclick="navTo('hewan',document.querySelectorAll('.nav-item')[1])">Lihat semua ${getAllAnimals().length} hewan →</button></div>`;
  
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
    if (hewanFilterCurrent === 'semua') list = getAllAnimals();
    else list = ANIMALS[hewanFilterCurrent] || [];
    if (q) list = list.filter(a => a.label.toLowerCase().includes(q) || a.id.toLowerCase().includes(q));
  
    const tbody = document.getElementById('hewan-table-body');
    if (!list.length) { tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="empty-ico">🐾</div>Tidak ada data</div></td></tr>`; return; }
    const st = animalStatus();
    tbody.innerHTML = list.map(a => {
      const done_idx = TIMELINE.findIndex(t => t.status === 'active');
      const status = done_idx >= 4 ? 'done' : done_idx >= 2 ? 'active' : 'pending';
      return `<tr>
        <td><strong>${a.id}</strong></td>
        <td><div style="display:flex;align-items:center;gap:10px;">
          <div class="animal-avatar" style="width:34px;height:34px;font-size:16px;">${a.emoji}</div>
          <strong>${a.label}</strong></div></td>
        <td>${a.jenis}</td>
        <td>${a.umur} · ${a.berat}</td>
        <td><span style="font-weight:700;color:var(--gold2);">${a.mudhohi.length}</span> orang</td>
        <td><span class="status-badge ${status === 'done' ? 'status-done' : status === 'active' ? 'status-active' : 'status-pending'}">${status === 'done' ? 'Selesai' : status === 'active' ? 'Diproses' : 'Pending'}</span></td>
        <td><button class="btn btn-ghost btn-sm" onclick="showDetailHewan('${a.id}')">Detail</button>
            <button class="btn btn-danger btn-sm" style="margin-left:4px;" onclick="deleteHewan('${a.id}')">Hapus</button></td>
      </tr>`;
    }).join('');
  }
  
  function deleteHewan(id) {
    const type = id.startsWith('S') ? 'sapi' : id.startsWith('K') ? 'kambing' : 'domba';
    const idx = ANIMALS[type].findIndex(a => a.id === id);
    if (idx < 0) return;
    const name = ANIMALS[type][idx].label;
    ANIMALS[type].splice(idx, 1);
    renderHewanTable();
    renderDashboard();
    toast(name + ' dihapus', 'info');
  }
  
  function showDetailHewan(id) {
    const a = getAllAnimals().find(x => x.id === id);
    if (!a) return;
    document.getElementById('detail-hewan-title').textContent = a.emoji + ' ' + a.label;
    document.getElementById('detail-hewan-body').innerHTML = `
      <div class="detail-card">
        ${[['ID','id'],['Jenis','jenis'],['Umur','umur'],['Berat','berat'],['Kondisi','sehat'],['Syariat','syariat'],['Cacat','cacat'],['Alamat','alamat'],['No. Telp','notelp']].map(([k,v]) => `
          <div class="detail-row"><div class="detail-key">${k}</div><div class="detail-val">${a[v]||'—'}</div></div>`).join('')}
      </div>
      <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Daftar Mudhohi (${a.mudhohi.length})</div>
      ${a.mudhohi.map(m => {
        const c = AVC[m.warna] || AVC.brown;
        const claimed = claimedSet.has(m.nama + a.id);
        return `<div style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:var(--bg3);border-radius:10px;margin-bottom:6px;">
          <div class="avatar" style="background:${c.bg};color:${c.color};">${m.i}</div>
          <div style="flex:1;"><strong style="font-size:13px;">${m.nama}</strong><div style="font-size:11px;color:var(--text3);">Bagian: ${m.bagian||'kurban penuh'}</div></div>
          ${claimed ? '<span class="status-badge status-done">✓ Diambil</span>' : '<span class="status-badge status-pending">Belum</span>'}
        </div>`;
      }).join('')}`;
    openModal('modal-detail-hewan');
  }
  
  // ═══════════════════════════════════════════
  // MUDHOHI TABLE
  // ═══════════════════════════════════════════
  function renderMudhohiTable() {
    const q = (document.getElementById('mudhohi-search')?.value || '').toLowerCase();
    let list = getAllMudhohi();
    if (q) list = list.filter(m => m.nama.toLowerCase().includes(q));
    const tbody = document.getElementById('mudhohi-table-body');
    if (!list.length) { tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><div class="empty-ico">👥</div>Tidak ada data</div></td></tr>`; return; }
    tbody.innerHTML = list.map(m => {
      const c = AVC[m.warna] || AVC.brown;
      const claimed = claimedSet.has(m.nama + m.animalId);
      const tipeBadge = m.tipe === 'mudhohi'
        ? `<span style="background:rgba(200,146,42,0.15);color:var(--gold2);border:1px solid rgba(200,146,42,0.3);border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;">🌟 Mudhohi</span>`
        : `<span style="background:rgba(78,203,113,0.12);color:var(--green);border:1px solid rgba(78,203,113,0.25);border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;">🟢 Penerima</span>`;
      return `<tr>
        <td><div style="display:flex;align-items:center;gap:10px;">
          <div class="avatar" style="background:${c.bg};color:${c.color};">${m.i}</div>
          <div><strong>${m.nama}</strong><div style="font-size:10px;color:var(--text3);margin-top:2px;font-family:monospace;">${m.nkk||'—'}</div></div></div></td>
        <td>${tipeBadge}</td>
        <td>${m.animalEmoji} ${m.animalLabel}</td>
        <td>${m.bagian||'Kurban penuh'}</td>
        <td><span class="status-badge ${claimed ? 'status-done' : 'status-pending'}">${claimed ? '✓ Diambil' : 'Belum'}</span></td>
        <td>${!claimed ? `<button class="btn btn-gold btn-sm" onclick="markClaimed('${m.nama}','${m.animalId}','${m.animalType}')">✓ Tandai Diambil</button>` : `<button class="btn btn-ghost btn-sm" onclick="unmarkClaimed('${m.nama}','${m.animalId}')">Batalkan</button>`}</td>
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
    const msg = `${TIMELINE[idx].label} → ${status === 'done' ? 'Selesai' : status === 'active' ? 'Berjalan' : 'Reset'}`;
    trackingLog.unshift({ time: nowTime(), msg, type: status === 'done' ? 'success' : 'info' });
    renderTracking();
    renderTrackingWidget();
    updateBadgeTracking();
    toast(msg, status === 'done' ? 'success' : 'info');
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
  // DISTRIBUSI / SCAN
  // ═══════════════════════════════════════════
  function updateDistStats() {
    const total = getAllMudhohi().length;
    document.getElementById('dist-count').textContent = claimedSet.size;
    document.getElementById('dist-total').textContent = total;
  }
  
  function renderScanList() {
    const q = (document.getElementById('scan-search')?.value || '').toLowerCase();
    const all = getAllMudhohi();
    const filtered = q ? all.filter(m => m.nama.toLowerCase().includes(q) || m.animalLabel.toLowerCase().includes(q)) : all.slice(0, 8);
    const el = document.getElementById('scan-list');
    if (!el) return;
    if (!filtered.length) { el.innerHTML = '<div class="empty-state"><div class="empty-ico">🔍</div>Tidak ditemukan</div>'; return; }
    el.innerHTML = filtered.map(m => {
      const c = AVC[m.warna] || AVC.brown;
      const claimed = claimedSet.has(m.nama + m.animalId);
      return `<div style="display:flex;align-items:center;gap:12px;padding:11px 14px;background:var(--bg3);border-radius:10px;margin-bottom:7px;border:1px solid ${claimed?'rgba(78,203,113,0.2)':'var(--border)'};cursor:pointer;transition:background .15s;" onclick="showScanResult('${m.nama}','${m.animalId}','${m.animalType}')" onmouseover="this.style.background='var(--bg4)'" onmouseout="this.style.background='var(--bg3)'">
        <div class="avatar" style="background:${c.bg};color:${c.color};">${m.i}</div>
        <div style="flex:1;"><strong style="font-size:13px;">${m.nama}</strong><div style="font-size:11px;color:var(--text3);">${m.animalEmoji} ${m.animalLabel} · ${m.bagian||'Kurban penuh'}</div></div>
        ${claimed ? '<span class="status-badge status-done" style="font-size:10px;">✓ Diambil</span>' : '<span style="color:var(--text3);font-size:18px;">›</span>'}
      </div>`;
    }).join('');
  }
  
  function showScanResult(nama, animalId, type) {
    const a = ANIMALS[type]?.find(x => x.id === animalId);
    const m = a?.mudhohi.find(x => x.nama === nama);
    if (!a || !m) return;
    const key = nama + animalId;
    const claimed = claimedSet.has(key);
    const c = AVC[m.warna] || AVC.brown;
    document.getElementById('scan-result').innerHTML = `
      <div style="background:${claimed?'rgba(224,85,85,0.08)':'rgba(78,203,113,0.08)'};border:1px solid ${claimed?'rgba(224,85,85,0.3)':'rgba(78,203,113,0.3)'};border-radius:14px;padding:20px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
          <div style="width:52px;height:52px;border-radius:50%;background:${claimed?'rgba(224,85,85,0.15)':'var(--green-bg)'};display:flex;align-items:center;justify-content:center;font-size:24px;">${claimed?'⚠️':'✅'}</div>
          <div>
            <div style="font-size:17px;font-weight:700;color:var(--text);">${nama}</div>
            <div style="font-size:12px;color:var(--text3);">${a.emoji} ${a.label} · Bagian ${m.bagian||'penuh'}</div>
          </div>
          <span class="status-badge ${claimed?'status-active':'status-done'}" style="margin-left:auto;">${claimed?'⚠ Sudah Diambil':'✓ Valid'}</span>
        </div>
        ${claimed
          ? `<div style="font-size:13px;color:var(--red);background:rgba(224,85,85,0.08);padding:10px 14px;border-radius:8px;margin-bottom:14px;">Penerima ini <strong>sudah mengambil</strong> daging kurbannya.</div>`
          : `<button class="btn btn-gold btn-lg" style="width:100%;" onclick="markClaimed('${nama}','${animalId}','${type}')">✓ Tandai Sudah Mengambil</button>`}
      </div>`;
  }
  
  function markClaimed(nama, animalId, type, method) {
    const key = nama + animalId;
    if (claimedSet.has(key)) { toast('Sudah pernah diambil!', 'error'); return; }
    claimedSet.add(key);
    const mt = method || 'QR';
    claimMethod[key] = mt;
    claimTime[key]   = nowTime();
    if (mt === 'QR') downloadedSet.add(key); // QR scan implies downloaded
    distLog.unshift({ nama, animal: getAllAnimals().find(a=>a.id===animalId)?.label, time: claimTime[key], method: mt });
    renderDistLog();
    renderScanList();
    showScanResult(nama, animalId, type);
    updateDistStats();
    renderDashboard();
    renderMudhohiTable();
    if (currentPage === 'tabel') renderTabelDistribusi();
    toast(nama + ' berhasil diverifikasi (' + mt + ')', 'success');
  }
  
  function markClaimedManual(nama, animalId, type) {
    markClaimed(nama, animalId, type, 'Manual');
  }
  
  function simulateQRDownload(key) {
    downloadedSet.add(key);
    if (currentPage === 'tabel') renderTabelDistribusi();
    const nama = key.replace(/[A-Z][0-9]+$/, '');
    toast('QR ' + nama + ' sudah didownload', 'info');
  }
  
  function unmarkClaimed(nama, animalId) {
    const key = nama + animalId;
    claimedSet.delete(key);
    delete claimMethod[key];
    delete claimTime[key];
    renderMudhohiTable();
    renderDashboard();
    updateDistStats();
    if (currentPage === 'tabel') renderTabelDistribusi();
    toast('Status ' + nama + ' dibatalkan', 'info');
  }
  
  function renderDistLog() {
    const el = document.getElementById('dist-log');
    if (!distLog.length) { el.innerHTML = '<div class="empty-state"><div class="empty-ico">📋</div>Belum ada yang diverifikasi</div>'; return; }
    el.innerHTML = distLog.map((l, i) => `
      <div style="display:flex;align-items:center;gap:12px;padding:11px 16px;border-bottom:1px solid var(--border);">
        <div style="width:28px;height:28px;border-radius:50%;background:var(--green-bg);color:var(--green);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">${i+1}</div>
        <div style="flex:1;"><strong style="font-size:13px;">${l.nama}</strong><div style="font-size:11px;color:var(--text3);">${l.animal}</div></div>
        <div style="font-size:11px;color:var(--text3);font-weight:600;">${l.time}</div>
      </div>`).join('');
  }
  
  // ═══════════════════════════════════════════
  // TABEL DISTRIBUSI  (skema: distribusi)
  // ═══════════════════════════════════════════
  // Generate deterministic 16-digit no_kk from name
  function fakeNoKK(nama) {
    let h = 3200000000000000n;
    for (let i = 0; i < nama.length; i++) h = (h * 31n + BigInt(nama.charCodeAt(i))) % 10000000000000000n;
    return h.toString().padStart(16,'3');
  }
  
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
  
    const allM = getAllMudhohi();
    let list = allM.map((m, idx) => {
      const key        = m.nama + m.animalId;
      const claimed    = claimedSet.has(key);
      const downloaded = downloadedSet.has(key);
      const method     = claimMethod[key] || (claimed ? 'QR' : '-');
      const waktu      = claimTime[key]   || '-';
      const noKK       = r.nkk || fakeNoKK(m.nama);
      // QR_id_qr: unique code per person
      const qrCode     = m.animalId + '-' + m.nama.replace(/\s+/g,'').toUpperCase().slice(0,6) + '-' + String(idx+1).padStart(3,'0');
      return { ...m, key, claimed, downloaded, method, waktu, noKK, idStok: idx + 1, qrCode };
    });
  
    // filters
    if (q) list = list.filter(r => r.nama.toLowerCase().includes(q) || r.noKK.includes(q));
    if (fStatus === 'diambil') list = list.filter(r => r.claimed);
    if (fStatus === 'belum')   list = list.filter(r => !r.claimed);
    if (fMetode !== 'semua')   list = list.filter(r => r.method === fMetode);
    if (fQr === 'downloaded')     list = list.filter(r => r.downloaded);
    if (fQr === 'not_downloaded') list = list.filter(r => !r.downloaded);
  
    // summary chips
    const total   = allM.length;
    const diambil = claimedSet.size;
    const dlCount = downloadedSet.size;
    const qrAuto  = Object.values(claimMethod).filter(v => v === 'QR').length;
    const manual  = Object.values(claimMethod).filter(v => v === 'Manual').length;
  
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
      empty.style.display = 'block';
      return;
    }
    empty.style.display = 'none';
  
    tbody.innerHTML = list.map(r => {
      const c = AVC[r.warna] || AVC.brown;
  
      // ── dowload_qr: ENUM('Ya','Tidak') — otomatis berubah saat user download
      const dlBadge = r.downloaded
        ? `<div style="display:inline-flex;align-items:center;gap:5px;background:rgba(91,156,246,0.12);border:1px solid rgba(91,156,246,0.25);border-radius:20px;padding:4px 10px;">
             <span style="font-size:10px;">⬇</span>
             <span style="font-size:10px;font-weight:700;color:var(--blue);">Ya</span>
           </div>`
        : `<div style="display:inline-flex;align-items:center;gap:5px;background:var(--bg4);border:1px solid var(--border);border-radius:20px;padding:4px 10px;">
             <span style="font-size:10px;">📵</span>
             <span style="font-size:10px;font-weight:700;color:var(--text3);">Tidak</span>
           </div>`;
  
      const dlBtn = !r.downloaded
        ? `<br><button class="btn btn-sm" style="margin-top:5px;background:rgba(91,156,246,0.1);color:var(--blue);border:1px solid rgba(91,156,246,0.2);font-size:10px;padding:3px 9px;" 
             onclick="simulateQRDownload('${r.key}')">⬇ Download QR</button>`
        : `<br><span style="font-size:10px;color:var(--text3);margin-top:4px;display:inline-block;">✓ File tersimpan</span>`;
  
      // ── st_pengambilan — auto via QR, manual via button
      const stBadge = r.claimed
        ? `<div style="display:inline-flex;align-items:center;gap:6px;background:var(--green-bg);border:1px solid rgba(78,203,113,0.25);border-radius:8px;padding:5px 10px;">
             <span style="font-size:13px;">${r.method === 'QR' ? '📱' : '👆'}</span>
             <div>
               <div style="font-size:11px;font-weight:700;color:var(--green);">Sudah Diambil</div>
               <div style="font-size:10px;color:var(--text3);">${r.method === 'QR' ? 'Otomatis via QR' : 'Admin klik manual'}</div>
             </div>
           </div>`
        : `<div style="display:inline-flex;align-items:center;gap:6px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:5px 10px;">
             <span style="font-size:13px;">⏳</span>
             <div>
               <div style="font-size:11px;font-weight:700;color:var(--text3);">Belum Diambil</div>
               <div style="font-size:10px;color:var(--text3);">Menunggu pengambilan</div>
             </div>
           </div>`;
  
      // ── mtd_pengambilan: ENUM('QR','Manual') — sesuai DB schema
      const mtdBadge = r.method === 'QR'
        ? `<div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
             <span style="background:rgba(91,156,246,0.12);color:var(--blue);border:1px solid rgba(91,156,246,0.2);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;">📱 QR</span>
             <span style="font-size:9px;color:var(--text3);">Otomatis</span>
           </div>`
        : r.method === 'Manual'
          ? `<div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
               <span style="background:var(--amber-bg);color:var(--amber);border:1px solid rgba(232,184,75,0.2);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;">👆 Manual</span>
               <span style="font-size:9px;color:var(--text3);">Admin input</span>
             </div>`
          : `<span style="color:var(--text3);font-size:12px;">—</span>`;
  
      // ── Aksi admin button
      const aksiBtn = !r.claimed
        ? `<button class="btn btn-gold btn-sm" style="width:100%;" title="Tandai diambil secara manual"
             onclick="markClaimedManual('${r.nama}','${r.animalId}','${r.animalType}');renderTabelDistribusi();">
             👆 Tandai Manual
           </button>`
        : `<button class="btn btn-ghost btn-sm" style="width:100%;font-size:10px;"
             onclick="unmarkClaimed('${r.nama}','${r.animalId}');renderTabelDistribusi();">
             ↩ Batalkan
           </button>`;
  
      return `<tr>
        <td style="text-align:center;">
          <span style="font-family:monospace;font-size:11px;color:var(--text3);font-weight:700;">#${String(r.idStok).padStart(3,'0')}</span>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="avatar" style="background:${c.bg};color:${c.color};font-size:11px;width:36px;height:36px;">${r.i}</div>
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text);">${r.nama}</div>
              <div style="font-size:10px;color:var(--text3);margin-top:2px;font-family:monospace;letter-spacing:.3px;">${r.noKK}</div>
              <div style="margin-top:3px;">${r.tipe==='mudhohi'?`<span style="background:rgba(200,146,42,0.15);color:var(--gold2);border:1px solid rgba(200,146,42,0.3);border-radius:20px;padding:1px 7px;font-size:9px;font-weight:700;">🌟 Mudhohi</span>`:`<span style="background:rgba(78,203,113,0.12);color:var(--green);border:1px solid rgba(78,203,113,0.25);border-radius:20px;padding:1px 7px;font-size:9px;font-weight:700;">🟢 Penerima</span>`}</div>
            </div>
          </div>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:8px;">
            <div style="opacity:${r.downloaded ? 1 : 0.4};transition:opacity .3s;">${miniQR(r.qrCode)}</div>
            <div>
              <div style="font-family:monospace;font-size:10px;color:var(--blue);background:rgba(91,156,246,0.08);border:1px solid rgba(91,156,246,0.15);padding:3px 7px;border-radius:5px;">${r.qrCode}</div>
              <div style="font-size:10px;color:var(--text3);margin-top:3px;">${r.animalEmoji} ${r.animalLabel} · ${r.bagian||'Penuh'}</div>
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
    const allM = getAllMudhohi();
    const rows = [['id_stok','warga_no_kk','Nama KK','QR_id_qr','dowload_qr','st_pengambilan','mtd_pengambilan','Waktu']];
    allM.forEach((m, i) => {
      const key  = m.nama + m.animalId;
      const noKK = fakeNoKK(m.nama);
      const qr   = m.animalId + '-' + m.nama.replace(/\s+/g,'').toUpperCase().slice(0,6) + '-' + String(i+1).padStart(3,'0');
      rows.push([
        String(i+1).padStart(3,'0'),
        noKK,
        m.nama,
        qr,
        downloadedSet.has(key) ? 'Ya' : 'Tidak',
        claimedSet.has(key)    ? 'Sudah Diambil' : 'Belum Diambil',
        claimMethod[key] || '-',
        claimTime[key]   || '-'
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
  // WARGA
  // ═══════════════════════════════════════════
  function renderWargaList() {
    const q = (document.getElementById('warga-search')?.value || '').toLowerCase();
    let list = getAllMudhohi();
    if (q) list = list.filter(m => m.nama.toLowerCase().includes(q));
    const el = document.getElementById('warga-grid');
    if (!list.length) { el.innerHTML = '<div class="empty-state" style="grid-column:1/-1;"><div class="empty-ico">🏘️</div>Tidak ada data</div>'; return; }
    el.innerHTML = list.map(m => {
      const c = AVC[m.warna] || AVC.brown;
      const a = getAllAnimals().find(x => x.id === m.animalId);
      const claimed = claimedSet.has(m.nama + m.animalId);
      return `<div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:18px;transition:border-color .15s;" onmouseover="this.style.borderColor='var(--border2)'" onmouseout="this.style.borderColor='var(--border)'">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
          <div class="avatar" style="width:44px;height:44px;font-size:15px;background:${c.bg};color:${c.color};">${m.i}</div>
          <div style="flex:1;"><strong style="font-size:14px;">${m.nama}</strong><div style="font-size:11px;color:var(--text3);margin-top:2px;">${a?.alamat||'—'}</div></div>
          <span class="status-badge ${claimed?'status-done':'status-pending'}">${claimed?'✓':'Belum'}</span>
        </div>
        <div class="divider" style="margin:10px 0;"></div>
        <div style="font-size:12px;color:var(--text3);">Hewan: <span style="color:var(--text2);font-weight:600;">${m.animalEmoji} ${m.animalLabel}</span></div>
        <div style="font-size:12px;color:var(--text3);margin-top:4px;">Bagian: <span style="color:var(--text2);font-weight:600;">${m.bagian||'Kurban penuh'}</span></div>
        <div style="margin-top:14px;">
          ${!claimed
            ? `<button class="btn btn-gold btn-sm" style="width:100%;" onclick="markClaimedManual('${m.nama}','${m.animalId}','${m.animalType}');renderWargaList();">✓ Tandai Diambil</button>`
            : `<button class="btn btn-ghost btn-sm" style="width:100%;" onclick="unmarkClaimed('${m.nama}','${m.animalId}');renderWargaList();">Batalkan</button>`}
        </div>
      </div>`;
    }).join('');
  }
  
  // ═══════════════════════════════════════════
  // REKAP
  // ═══════════════════════════════════════════
  function renderRekap() {
    const allM = getAllMudhohi();
    const total = allM.length;
    const diambil = claimedSet.size;
    const pct = total ? Math.round(diambil / total * 100) : 0;
    const sapi = ANIMALS.sapi.reduce((a,s) => a + s.mudhohi.length, 0);
    const kambing = ANIMALS.kambing.length;
    const domba = ANIMALS.domba.length;
    const circ = 2 * Math.PI * 48;
    const off  = circ - (pct / 100) * circ;
  
    document.getElementById('rekap-content').innerHTML = `
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;">
        <!-- Ring card -->
        <div class="card">
          <div class="card-header"><div class="card-title">📊 Progress Distribusi</div></div>
          <div class="card-body" style="display:flex;align-items:center;gap:24px;">
            <div class="rekap-ring">
              <svg width="120" height="120" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="48" fill="none" stroke="var(--bg4)" stroke-width="10"/>
                <circle cx="60" cy="60" r="48" fill="none" stroke="var(--gold)" stroke-width="10"
                  stroke-linecap="round" stroke-dasharray="${circ}" stroke-dashoffset="${off}" style="transition:stroke-dashoffset .8s;"/>
              </svg>
              <div class="rekap-ring-label">
                <div class="rekap-ring-num">${pct}%</div>
                <div class="rekap-ring-sub">selesai</div>
              </div>
            </div>
            <div style="flex:1;">
              <div style="margin-bottom:12px;"><div style="font-size:28px;font-weight:800;color:var(--green);">${diambil}</div><div style="font-size:12px;color:var(--text3);">Sudah diambil</div></div>
              <div><div style="font-size:28px;font-weight:800;color:var(--amber);">${total - diambil}</div><div style="font-size:12px;color:var(--text3);">Belum diambil</div></div>
            </div>
          </div>
        </div>
  
        <!-- Hewan breakdown -->
        <div class="card">
          <div class="card-header"><div class="card-title">🐾 Breakdown Hewan</div></div>
          <div class="card-body">
            ${[['🐄 Sapi', ANIMALS.sapi.length, sapi, '#c8922a'],['🐐 Kambing', ANIMALS.kambing.length, kambing, '#e8b84b'],['🐑 Domba', ANIMALS.domba.length, domba, '#a09cf8']].map(([n,hewan,mh,col]) => `
              <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
                <div style="font-size:22px;">${n.split(' ')[0]}</div>
                <div style="flex:1;"><div style="font-size:13px;font-weight:600;">${n}</div><div style="font-size:11px;color:var(--text3);">${hewan} ekor · ${mh} penerima</div></div>
                <div style="font-size:20px;font-weight:800;color:${col};">${mh}</div>
              </div>`).join('')}
          </div>
        </div>
      </div>
  
      <!-- Status proses -->
      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><div class="card-title">📍 Status Proses Kurban</div></div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;">
            ${TIMELINE.map(t => `
              <div style="text-align:center;background:${t.status==='done'?'rgba(200,146,42,0.1)':t.status==='active'?'rgba(232,184,75,0.07)':'var(--bg3)'};border:1px solid ${t.status==='done'?'rgba(200,146,42,0.2)':t.status==='active'?'rgba(232,184,75,0.15)':'var(--border)'};border-radius:12px;padding:14px;">
                <div style="font-size:22px;margin-bottom:6px;">${t.status==='done'?'✅':t.icon}</div>
                <div style="font-size:12px;font-weight:700;color:${t.status==='pending'?'var(--text3)':'var(--text)'};">${t.label}</div>
                <div style="font-size:10px;font-weight:700;margin-top:5px;color:${t.status==='done'?'var(--gold2)':t.status==='active'?'var(--amber)':'var(--text3)'};">${t.time}</div>
              </div>`).join('')}
          </div>
        </div>
      </div>
  
      <!-- Belum diambil -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">⏳ Belum Diambil (${total - diambil})</div>
        </div>
        <table class="data-table">
          <thead><tr><th>Nama</th><th>Hewan</th><th>Bagian</th></tr></thead>
          <tbody>${allM.filter(m => !claimedSet.has(m.nama+m.animalId)).map(m => `
            <tr><td><strong>${m.nama}</strong></td><td>${m.animalEmoji} ${m.animalLabel}</td><td>${m.bagian||'Penuh'}</td></tr>`).join('') || '<tr><td colspan="3"><div class="empty-state"><div class="empty-ico">🎉</div>Semua sudah diambil!</div></td></tr>'}
          </tbody>
        </table>
      </div>`;
  }
  
  // ═══════════════════════════════════════════
  // SUBMIT FORMS
  // ═══════════════════════════════════════════
  function submitHewan() {
    const jenis  = document.getElementById('h-jenis').value;
    const label  = document.getElementById('h-label').value.trim();
    if (!jenis || !label) { toast('Jenis & nama wajib diisi!', 'error'); return; }
    const list   = ANIMALS[jenis];
    const prefix = jenis === 'sapi' ? 'S' : jenis === 'kambing' ? 'K' : 'D';
    const id     = prefix + String(list.length + 1).padStart(2, '0');
    const emoji  = jenis === 'sapi' ? '🐄' : jenis === 'kambing' ? '🐐' : '🐑';
    list.push({
      id, emoji, label, jenis: label,
      umur:   document.getElementById('h-umur').value.trim()   || '—',
      sehat:  '✓ Sehat', syariat: '✓ Sah',
      cacat:  document.getElementById('h-catatan').value.trim() || 'Tidak ada',
      berat:  document.getElementById('h-berat').value.trim()   || '—',
      alamat: document.getElementById('h-alamat').value.trim()  || '—',
      notelp: document.getElementById('h-telp').value.trim()    || '—',
      reqBagian: '—', mudhohi: []
    });
    closeModal('modal-hewan');
    renderHewanTable();
    renderDashboard();
    toast(label + ' berhasil ditambahkan', 'success');
  }
  
  function submitMudhohi() {
    const nama    = document.getElementById('m-nama').value.trim();
    const hewanId = document.getElementById('m-hewan').value;
    if (!nama || !hewanId) { toast('Nama & hewan wajib diisi!', 'error'); return; }
    const type = hewanId.startsWith('S') ? 'sapi' : hewanId.startsWith('K') ? 'kambing' : 'domba';
    const a = ANIMALS[type].find(x => x.id === hewanId);
    if (!a) { toast('Hewan tidak ditemukan', 'error'); return; }
    const initials = nama.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    const warnas = ['brown','green','amber','purple'];
    a.mudhohi.push({ i: initials, nama, bagian: document.getElementById('m-bagian').value.trim() || 'Kurban penuh', warna: warnas[a.mudhohi.length % 4] });
    closeModal('modal-mudhohi');
    renderMudhohiTable();
    renderDashboard();
    toast(nama + ' berhasil ditambahkan', 'success');
  }
  
  function logout() {
    if (confirm('Yakin ingin logout?')) {
      toast('Logout berhasil', 'info');
      setTimeout(() => { document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#0f0d0b;color:#a09080;font-family:sans-serif;font-size:16px;">Session berakhir. Silakan refresh halaman.</div>'; }, 800);
    }
  }
  
  // ═══════════════════════════════════════════
  // UPLOAD EXCEL / CSV — Import Data Warga
  // ═══════════════════════════════════════════
  
  let importedWarga = []; // staging area sebelum dikonfirmasi
  let confirmedWarga = []; // setelah konfirmasi
  
  function handleFileDrop(e) {
    e.preventDefault();
    const dz = document.getElementById('drop-zone');
    dz.style.borderColor = 'var(--border2)'; dz.style.background = '';
    const file = e.dataTransfer.files[0];
    if (!file) return;
    readFileAsCSV(file);
  }
  function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    readFileAsCSV(file);
  }
  
  function readFileAsCSV(file) {
    const reader = new FileReader();
    reader.onload = e => {
      const text = e.target.result;
      const rows = parseCSV(text);
      previewImport(rows, file.name);
    };
    reader.readAsText(file, 'UTF-8');
  }
  
  function parseCSVPaste() {
    const text = document.getElementById('csv-paste').value.trim();
    if (!text) { toast('Tidak ada data untuk diproses', 'error'); return; }
    const rows = parseCSV(text);
    previewImport(rows, 'paste');
  }
  
  function parseCSV(text) {
    const lines = text.split('\n').map(l => l.trim()).filter(l => l);
    if (!lines.length) return [];
    // Detect header — skip if first line looks like header (contains 'nama' / 'kk' / 'no')
    let startIdx = 0;
    const firstLower = lines[0].toLowerCase();
    if (firstLower.includes('nama') || firstLower.includes('no kk') || firstLower.includes('tipe')) startIdx = 1;
  
    return lines.slice(startIdx).map(line => {
      // Handle quoted CSV fields
      const cols = line.split(/,(?=(?:[^"]*"[^"]*")*[^"]*$)/).map(c => c.replace(/^"|"$/g,'').trim());
      return {
        nkk:    (cols[0] || '').replace(/\D/g,'').slice(0,16),
        nama:   cols[1] || '',
        tipe:   ((cols[2] || '').toLowerCase().includes('mudhohi') ? 'mudhohi' : 'penerima'),
        hewan:  cols[3] || '—',
        bagian: cols[4] || 'Kurban penuh',
        alamat: cols[5] || '—',
      };
    }).filter(r => r.nkk.length >= 10 && r.nama.length >= 2);
  }
  
  function previewImport(rows, source) {
    importedWarga = rows;
    const mudhohi = rows.filter(r => r.tipe === 'mudhohi').length;
    const penerima = rows.filter(r => r.tipe === 'penerima').length;
  
    document.getElementById('preview-stats').innerHTML =
      `<span style="color:var(--green);font-weight:700;">${rows.length} data</span> &nbsp;·&nbsp;
       <span style="color:var(--gold2);">🌟 ${mudhohi} Mudhohi</span> &nbsp;·&nbsp;
       <span style="color:var(--green);">🟢 ${penerima} Penerima</span>`;
  
    if (!rows.length) {
      document.getElementById('preview-content').innerHTML = '<div class="empty-state"><div class="empty-ico">⚠️</div>Tidak ada data valid ditemukan. Periksa format file.</div>';
      document.getElementById('preview-actions').style.display = 'none';
      return;
    }
  
    document.getElementById('preview-content').innerHTML = `
      <table class="data-table" style="min-width:560px;">
        <thead><tr><th>#</th><th>No KK</th><th>Nama KK</th><th>Tipe</th><th>Hewan</th><th>Bagian</th></tr></thead>
        <tbody>
          ${rows.slice(0,20).map((r, i) => `<tr>
            <td style="color:var(--text3);font-size:11px;">${i+1}</td>
            <td><code style="font-size:10px;color:var(--blue);">${r.nkk}</code></td>
            <td><strong>${r.nama}</strong></td>
            <td>${r.tipe==='mudhohi'
              ? `<span style="background:rgba(200,146,42,0.15);color:var(--gold2);border:1px solid rgba(200,146,42,0.3);border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;">🌟 Mudhohi</span>`
              : `<span style="background:rgba(78,203,113,0.12);color:var(--green);border:1px solid rgba(78,203,113,0.25);border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;">🟢 Penerima</span>`}</td>
            <td style="font-size:12px;">${r.hewan}</td>
            <td style="font-size:12px;">${r.bagian}</td>
          </tr>`).join('')}
          ${rows.length > 20 ? `<tr><td colspan="6" style="text-align:center;color:var(--text3);font-size:12px;">... dan ${rows.length-20} data lainnya</td></tr>` : ''}
        </tbody>
      </table>`;
    document.getElementById('preview-actions').style.display = 'block';
    toast(rows.length + ' data berhasil diparse dari ' + source, 'success');
  }
  
  function importConfirm() {
    if (!importedWarga.length) return;
    confirmedWarga = [...importedWarga];
    importedWarga = [];
  
    // Tandai badge upload sudah berisi data
    document.getElementById('badge-upload').style.display = 'inline-block';
  
    renderImportedTable();
    document.getElementById('preview-content').innerHTML = '<div class="empty-state"><div class="empty-ico">✅</div>Data berhasil diimport ke sistem!</div>';
    document.getElementById('preview-actions').style.display = 'none';
    document.getElementById('preview-stats').innerHTML = '';
    toast(confirmedWarga.length + ' data warga berhasil diimport!', 'success');
  
    // Scroll ke tabel
    setTimeout(() => document.getElementById('imported-list-card').scrollIntoView({behavior:'smooth'}), 300);
  }
  
  function renderImportedTable() {
    const card = document.getElementById('imported-list-card');
    const tbody = document.getElementById('imported-table-body');
    if (!confirmedWarga.length) {
      card.style.display = 'none'; return;
    }
    card.style.display = '';
    const mudhohi = confirmedWarga.filter(r => r.tipe === 'mudhohi').length;
    document.getElementById('imported-count').textContent = confirmedWarga.length + ' data · 🌟 ' + mudhohi + ' Mudhohi · 🟢 ' + (confirmedWarga.length - mudhohi) + ' Penerima';
    tbody.innerHTML = confirmedWarga.map((r, i) => `<tr>
      <td style="color:var(--text3);font-size:11px;">${i+1}</td>
      <td><code style="font-size:11px;color:var(--blue);">${r.nkk}</code></td>
      <td><strong>${r.nama}</strong></td>
      <td>${r.tipe==='mudhohi'
        ? `<span style="background:rgba(200,146,42,0.15);color:var(--gold2);border:1px solid rgba(200,146,42,0.3);border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;">🌟 Mudhohi</span>`
        : `<span style="background:rgba(78,203,113,0.12);color:var(--green);border:1px solid rgba(78,203,113,0.25);border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;">🟢 Penerima</span>`}</td>
      <td style="font-size:12px;">${r.hewan}</td>
      <td style="font-size:12px;">${r.bagian}</td>
      <td style="font-size:12px;color:var(--text3);">${r.alamat}</td>
      <td><button class="btn btn-danger btn-sm" onclick="removeImported(${i})">Hapus</button></td>
    </tr>`).join('');
  }
  
  function removeImported(idx) {
    confirmedWarga.splice(idx, 1);
    renderImportedTable();
    if (!confirmedWarga.length) document.getElementById('badge-upload').style.display = 'none';
    toast('Data dihapus dari daftar', 'info');
  }
  
  function exportImportedCSV() {
    if (!confirmedWarga.length) return;
    const rows = [['No KK','Nama KK','Tipe','Hewan','Bagian','Alamat'],
      ...confirmedWarga.map(r => [r.nkk, r.nama, r.tipe, r.hewan, r.bagian, r.alamat])];
    const csv = rows.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
    const blob = new Blob([csv], {type:'text/csv'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'warga_terdaftar_kurbanqu.csv';
    a.click();
    toast('CSV berhasil diexport', 'success');
  }
  
  function clearImport() {
    importedWarga = [];
    document.getElementById('csv-paste').value = '';
    document.getElementById('excel-input').value = '';
    document.getElementById('preview-content').innerHTML = '<div class="empty-state"><div class="empty-ico">📋</div>Data akan tampil di sini setelah diproses</div>';
    document.getElementById('preview-actions').style.display = 'none';
    document.getElementById('preview-stats').innerHTML = '';
    toast('Form dibersihkan', 'info');
  }
  
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
  renderDashboard();
  renderScanList();
  updateBadgeTracking();