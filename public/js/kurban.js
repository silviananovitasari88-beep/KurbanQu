// ══════════════════════════════════════════
// DATA
// ══════════════════════════════════════════
const AV = {
  brown:  { bg:"#f0e8d8", color:"#7a5230" },
  green:  { bg:"#eaf3de", color:"#3b6d11" },
  amber:  { bg:"#faeeda", color:"#854f0b" },
  purple: { bg:"#eeedfe", color:"#534ab7" },
};

// Timeline step data
const TIMELINE = [
  {
    label: "Penyembelihan",
    desc:  "Proses penyembelihan akan dimulai setelah status dikonfirmasi",
    status: "pending",
    time:  "—",
    icon:  "🔪"
  },
  {
    label: "Pengulitan",
    desc:  "Proses pengulitan hewan kurban akan dilakukan setelah penyembelihan selesai",
    status: "pending",
    time:  "—",
    icon:  "🐄"
  },
  {
    label: "Pencacahan",
    desc:  "Daging akan dipotong & dibersihkan setelah tahap sebelumnya selesai",
    status: "pending",
    time:  "—",
    icon:  "🥩"
  },
  {
    label: "Penimbangan",
    desc:  "Daging akan ditimbang & dikemas setelah pencacahan selesai",
    status: "pending",
    time:  "—",
    icon:  "⚖️"
  },
  {
    label: "Siap Diambil",
    desc:  "Daging akan siap diambil penerima setelah proses penimbangan selesai",
    status: "pending",
    time:  "—",
    icon:  "✅"
  },
];

// ── Polling status tracking dari admin (realtime) ───────────────────────────
async function fetchTrackingFromServer() {
  try {
    const res = await fetch('/api/tracking', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return;
    const data = await res.json();
    if (!data.success || !Array.isArray(data.steps)) return;
    // Update TIMELINE dari server
    data.steps.forEach((s, i) => {
      if (TIMELINE[i]) {
        TIMELINE[i].status = s.status || 'pending';
        TIMELINE[i].time   = s.time   || '—';
      }
    });
    // Re-render jika dashboard aktif
    if (document.getElementById('dash-timeline')) {
      renderDashTimeline();
    }
    // Re-render detail hewan jika aktif
    if (typeof renderDetailTrack === 'function') renderDetailTrack();
  } catch (e) { /* silent fail */ }
}

// Polling setiap 10 detik
setInterval(fetchTrackingFromServer, 10000);
// Load sekali saat halaman dibuka
fetchTrackingFromServer();


let ANIMALS = {
  sapi: [
    { id:"S01", emoji:"🐄", label:"Sapi Putih No.01", jenis:"Sapi", umur:"3 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±230 kg",
      alamat:"Kp. Cikaret RT 02/03", notelp:"0812-xxxx-1234", reqBagian:"7 bagian",
      mudhohi:[
        {i:"AH",nama:"Ahmad Hidayat",    bagian:"1/7",warna:"brown"},
        {i:"SR",nama:"Siti Rahmawati",   bagian:"2/7",warna:"green"},
        {i:"BU",nama:"Budi Utomo",       bagian:"3/7",warna:"amber"},
        {i:"RN",nama:"Rina Nuraini",     bagian:"4/7",warna:"purple"},
        {i:"MS",nama:"Maman Suparman",   bagian:"5/7",warna:"brown"},
        {i:"DF",nama:"Dewi Fitriani",    bagian:"6/7",warna:"green"},
        {i:"YP",nama:"Yusuf Pratama",    bagian:"7/7",warna:"amber"},
      ]},
    { id:"S02", emoji:"🐄", label:"Sapi Hitam No.02", jenis:"Sapi", umur:"4 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±250 kg",
      alamat:"Jl. Mawar No.7 RT 01/04", notelp:"0821-xxxx-5678", reqBagian:"7 bagian",
      mudhohi:[
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
    { id:"K01", emoji:"🐐", label:"Kambing No.01", jenis:"Kambing Jawa", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±32 kg",
      alamat:"Kp. Babakan RT 03/05", notelp:"0838-xxxx-0011", reqBagian:"1 bagian",
      mudhohi:[{i:"DN",nama:"Drs. Haji Nurdian",  warna:"amber"}]},
    { id:"K02", emoji:"🐐", label:"Kambing No.02", jenis:"Kambing PE", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±35 kg",
      alamat:"Jl. Kenanga No.3", notelp:"0857-xxxx-2233", reqBagian:"1 bagian",
      mudhohi:[{i:"FH",nama:"Fitri Handayani",    warna:"brown"}]},
    { id:"K03", emoji:"🐐", label:"Kambing No.03", jenis:"Kambing Boer", umur:"3 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±40 kg",
      alamat:"Perum Griya Asri Blok B2", notelp:"0812-xxxx-4455", reqBagian:"1 bagian",
      mudhohi:[{i:"ZA",nama:"Zainal Abidin",      warna:"green"}]},
    { id:"K04", emoji:"🐐", label:"Kambing No.04", jenis:"Kambing Jawa", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±30 kg",
      alamat:"RT 04 RW 02", notelp:"0877-xxxx-6677", reqBagian:"1 bagian",
      mudhohi:[{i:"ML",nama:"Mulyadi",             warna:"purple"}]},
    { id:"K05", emoji:"🐐", label:"Kambing No.05", jenis:"Kambing Kacang", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Mata kiri", berat:"±28 kg",
      alamat:"Kp. Sindangjaya", notelp:"0821-xxxx-8899", reqBagian:"1 bagian",
      mudhohi:[{i:"IS",nama:"Ibu Sumiati",         warna:"brown"}]},
    { id:"K06", emoji:"🐐", label:"Kambing No.06", jenis:"Kambing PE", umur:"3 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±38 kg",
      alamat:"Jl. Merdeka Blok C", notelp:"0856-xxxx-0012", reqBagian:"1 bagian",
      mudhohi:[{i:"RJ",nama:"Rudi Juanda",         warna:"green"}]},
    { id:"K07", emoji:"🐐", label:"Kambing No.07", jenis:"Kambing Boer", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±36 kg",
      alamat:"RT 01/07 Desa Sukamaju", notelp:"0899-xxxx-3344", reqBagian:"1 bagian",
      mudhohi:[{i:"SA",nama:"Samsul Arifin",       warna:"amber"}]},
  ],
  domba: [
    { id:"D01", emoji:"🐑", label:"Domba No.01", jenis:"Domba Garut", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±28 kg",
      alamat:"Kp. Cikaret Hilir", notelp:"0813-xxxx-5566", reqBagian:"1 bagian",
      mudhohi:[{i:"MP",nama:"Muhamad Prayogo",warna:"purple"}]},
    { id:"D02", emoji:"🐑", label:"Domba No.02", jenis:"Domba Garut", umur:"3 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±30 kg",
      alamat:"Jl. Anggrek No.5", notelp:"0812-xxxx-7788", reqBagian:"1 bagian",
      mudhohi:[{i:"IK",nama:"Ibu Komariah",  warna:"brown"}]},
    { id:"D03", emoji:"🐑", label:"Domba No.03", jenis:"Domba Lokal", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±26 kg",
      alamat:"RT 06/02 Blok Timur", notelp:"0878-xxxx-9900", reqBagian:"1 bagian",
      mudhohi:[{i:"AB",nama:"Agus Budiman",   warna:"green"}]},
    { id:"D04", emoji:"🐑", label:"Domba No.04", jenis:"Domba Garut", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±29 kg",
      alamat:"Perum Bukit Indah No.12", notelp:"0838-xxxx-1122", reqBagian:"1 bagian",
      mudhohi:[{i:"YL",nama:"Yuli Lestari",   warna:"amber"}]},
    { id:"D05", emoji:"🐑", label:"Domba No.05", jenis:"Domba Lokal", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±25 kg",
      alamat:"Gang Masjid No.3", notelp:"0857-xxxx-3344", reqBagian:"1 bagian",
      mudhohi:[{i:"DK",nama:"Dadang Kurnia",  warna:"purple"}]},
    { id:"D06", emoji:"🐑", label:"Domba No.06", jenis:"Domba Garut", umur:"3 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±32 kg",
      alamat:"Kp. Warungdowo", notelp:"0812-xxxx-5566", reqBagian:"1 bagian",
      mudhohi:[{i:"NH",nama:"Nining Hernawati",warna:"brown"}]},
    { id:"D07", emoji:"🐑", label:"Domba No.07", jenis:"Domba Lokal", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±24 kg",
      alamat:"RT 03/05 Desa Tanjung", notelp:"0821-xxxx-7788", reqBagian:"1 bagian",
      mudhohi:[{i:"HF",nama:"Hendra Firmansyah",warna:"green"}]},
    { id:"D08", emoji:"🐑", label:"Domba No.08", jenis:"Domba Garut", umur:"2 Tahun", sehat:"✓ Sehat", syariat:"✓ Sah", cacat:"Tidak ada", berat:"±27 kg",
      alamat:"Blok D No.9 Perumahan", notelp:"0877-xxxx-9900", reqBagian:"1 bagian",
      mudhohi:[{i:"SN",nama:"Siti Nurhasanah",warna:"amber"}]},
  ],
};

const STORAGE_HEWAN = 'kurbanqu_hewan';
const STORAGE_MUDHOHI = 'kurbanqu_mudhohi';

function loadSharedAnimalData() {
  try {
    const rawH = localStorage.getItem(STORAGE_HEWAN);
    const rawM = localStorage.getItem(STORAGE_MUDHOHI);
    const hewan = rawH === null ? [] : JSON.parse(rawH || '[]');
    const mudhohi = rawM === null ? [] : JSON.parse(rawM || '[]');
    if (!Array.isArray(hewan) || !Array.isArray(mudhohi)) return false;

    const mudhohiByHewan = {};
    mudhohi.forEach(m => {
      const uid = String(m.hewan_id_hewan || '');
      if (!mudhohiByHewan[uid]) mudhohiByHewan[uid] = [];
      mudhohiByHewan[uid].push({
        i: (m.nama || '??').slice(0, 2).toUpperCase(),
        nama: m.nama || '—',
        warna: m.warna || 'brown',
      });
    });

    const grouped = { sapi: [], kambing: [], domba: [] };
    hewan.forEach(h => {
      const jenis = String(h.jenis || '').toLowerCase();
      const key = ['sapi', 'kambing', 'domba'].includes(jenis) ? jenis : 'sapi';
      grouped[key].push({
        id: `${key[0].toUpperCase()}${String(h.id_hewan || '').padStart(2, '0')}`,
        emoji: key === 'sapi' ? '🐄' : key === 'kambing' ? '🐐' : '🐑',
        label: h.label || `${key.charAt(0).toUpperCase() + key.slice(1)} #${h.id_hewan}`,
        jenis: key,
        umur: h.umur || '—',
        sehat: h.sehat === 'Ya' ? '✓ Sehat' : '✗ Tidak Sehat',
        syariat: h.st_syariat === 'Sah' ? '✓ Sah' : '✗ Tidak Sah',
        cacat: h.cacat === 'Tidak' ? 'Tidak ada' : (h.cacat_ket || 'Ada cacat'),
        berat: h.berat || '—',
        alamat: h.alamat || '—',
        notelp: h.notelp || '—',
        reqBagian: h.req || '1 bagian',
        mudhohi: mudhohiByHewan[String(h.id_hewan || '')] || [],
      });
    });

    ANIMALS = grouped;
    return true;
  } catch (e) {
    return false;
  }
}

loadSharedAnimalData();

// Update counts in chips
document.getElementById('cnt-sapi').textContent    = ANIMALS.sapi.length;
document.getElementById('cnt-kambing').textContent = ANIMALS.kambing.length;
document.getElementById('cnt-domba').textContent   = ANIMALS.domba.length;

// ══════════════════════════════════════════
// NAVIGATION
// ══════════════════════════════════════════
function goto(id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

// ══════════════════════════════════════════
// DASHBOARD — ANIMAL CHIP SELECT
// ══════════════════════════════════════════
let currentFilter = 'kambing';
let panelOpen = true;

function selectAnimalChip(type, el) {
  document.querySelectorAll('.ac-chip').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  currentFilter = type;
  renderAnimalList(type);
  // make sure panel is open
  document.getElementById('animal-panel').classList.add('open');
  panelOpen = true;
}

let seeMoreExpanded = false;
let currentAnimalList = [];

function renderAnimalList(type) {
  seeMoreExpanded = false;
  const inner = document.getElementById('animal-list-inner');
  let list = [];
  if (type === 'all') {
    list = [...ANIMALS.sapi, ...ANIMALS.kambing, ...ANIMALS.domba];
  } else {
    list = ANIMALS[type] || [];
  }
  currentAnimalList = list;
  if (!list.length) { inner.innerHTML = '<div style="padding:18px;text-align:center;color:#9a8060;font-size:13px;">Tidak ada data</div>'; document.getElementById('see-more-btn').style.display='none'; return; }

  const showList = list.slice(0, 3);
  inner.innerHTML = showList.map(a => renderAlRow(a, type)).join('');

  const btn = document.getElementById('see-more-btn');
  const arrow = document.getElementById('see-more-arrow');
  const text = document.getElementById('see-more-text');
  if (list.length > 3) {
    btn.style.display = 'flex';
    btn.classList.remove('expanded');
    arrow.textContent = '⌄';
    text.textContent = `Lihat semua (${list.length})`;
  } else {
    btn.style.display = 'none';
  }
}

function toggleSeeMore() {
  const inner = document.getElementById('animal-list-inner');
  const panel = document.getElementById('animal-panel');
  const btn   = document.getElementById('see-more-btn');
  const arrow = document.getElementById('see-more-arrow');
  const text  = document.getElementById('see-more-text');
  seeMoreExpanded = !seeMoreExpanded;
  const type = currentFilter;
  if (seeMoreExpanded) {
    inner.innerHTML = currentAnimalList.map(a => renderAlRow(a, type)).join('');
    btn.classList.add('expanded');
    arrow.textContent = '⌃';
    text.textContent = 'Tutup';
    // expand panel to fit all rows + see-more button
    panel.style.maxHeight = (currentAnimalList.length * 72 + 44) + 'px';
  } else {
    inner.innerHTML = currentAnimalList.slice(0,3).map(a => renderAlRow(a, type)).join('');
    btn.classList.remove('expanded');
    arrow.textContent = '⌄';
    text.textContent = `Lihat semua (${currentAnimalList.length})`;
    panel.style.maxHeight = '260px';
  }
}

function renderAlRow(a, type) {
  const t = type === 'all' ? guessType(a.id) : type;
  return `
    <div class="al-row" onclick="showDetail('${a.id}','${t}')">
      <div class="al-thumb">${a.emoji}</div>
      <div style="flex:1;">
        <div class="al-id">${a.id}</div>
        <div class="al-name">${a.mudhohi[0]?.nama || a.label}</div>
        <div class="al-meta">${a.label} · ${a.mudhohi.length > 1 ? a.mudhohi.length + ' org patungan' : '1 org · kurban penuh'}</div>
      </div>
      <div class="al-arrow">›</div>
    </div>
  `;
}

function guessType(id) {
  if (id.startsWith('S')) return 'sapi';
  if (id.startsWith('K')) return 'kambing';
  return 'domba';
}

// ══════════════════════════════════════════
// DASHBOARD — DUOLINGO PATH TIMELINE
// ══════════════════════════════════════════
function renderDashTimeline() {
  const wrap = document.getElementById('dash-timeline');
  const doneCount = TIMELINE.filter(t => t.status === 'done').length;
  const activeIdx = TIMELINE.findIndex(t => t.status === 'active');
  const pct = Math.round((doneCount / TIMELINE.length) * 100);

  // Progress bar + clock
  let html = `
    <div class="track-clock-bar">
      <span class="tcb-label">⏱ Status Live</span>
      <span class="tcb-time" id="live-clock">--:--:--</span>
    </div>
    <div class="track-prog-wrap">
      <div class="track-prog-bg"><div class="track-prog-fill" style="width:${pct}%"></div></div>
      <div class="track-prog-pct">${doneCount} dari ${TIMELINE.length} tahap selesai</div>
    </div>
    <div class="dl-path">
  `;

  TIMELINE.forEach((t, i) => {
    const isLast = i === TIMELINE.length - 1;
    const isOdd  = i % 2 === 0;

    // zigzag: even index → right side, odd index → left side
    const alignStyle = isOdd
      ? 'align-self:flex-end; margin-right:48px;'
      : 'align-self:flex-start; margin-left:48px;';

    // Tooltip for active node
    const tooltipHtml = t.status === 'active' ? `
      <div class="dl-tooltip">▶ Sedang Berlangsung</div>
      <div class="dl-tooltip-arrow"></div>
    ` : '';

    // Checkmark badge for done
    const checkHtml = t.status === 'done'
      ? `<div class="dl-check">✓</div>`
      : '';

    // Timestamp display
    const tsText = t.time;

    // Curved connector SVG between nodes — alternates direction for zigzag
    const connectorHtml = !isLast ? (() => {
      // nodeA is current (isOdd → right side), nodeB is next (isOdd XOR → left)
      // We draw a dashed curved path from current circle center to next
      const strokeColor = t.status === 'done' ? '#e8b84b' : '#d8cfc4';
      const dashArr = t.status === 'done' ? 'none' : '6 5';
      // Path curves from right→left or left→right
      const fromRight = isOdd;
      const x1 = fromRight ? 280 : 92;
      const x2 = fromRight ? 92  : 280;
      const cy = 40;
      const cpx = 186;
      return `
        <svg class="dl-connector" viewBox="0 0 372 80" height="64" xmlns="http://www.w3.org/2000/svg">
          <path d="M${x1},8 C${cpx},8 ${cpx},72 ${x2},72"
            fill="none" stroke="${strokeColor}" stroke-width="5"
            stroke-linecap="round" stroke-dasharray="${dashArr}"
            ${t.status === 'active' ? 'opacity="0.5"' : ''}
          />
        </svg>
      `;
    })() : '';

    html += `
      <div class="dl-row">
        <div class="dl-node-wrap" style="${alignStyle}">
          ${tooltipHtml}
          <div class="dl-circle ${t.status}">
            <span>${t.icon}</span>
            ${checkHtml}
          </div>
          <div class="dl-label-wrap">
            <div class="dl-label ${t.status === 'pending' ? 'pending' : ''}">${t.label}</div>
            <div class="dl-ts ${t.status}">🕐 ${tsText}</div>
          </div>
        </div>
      </div>
      ${connectorHtml ? `<div style="width:100%;">${connectorHtml}</div>` : ''}
    `;
  });

  html += `</div>`; // .dl-path
  wrap.innerHTML = html;

  // Start live clock
  startLiveClock();
}

function startLiveClock() {
  function tick() {
    const el = document.getElementById('live-clock');
    if (!el) return;
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const s = String(now.getSeconds()).padStart(2,'0');
    el.textContent = `${h}:${m}:${s} WIB`;
  }
  tick();
  setInterval(tick, 1000);
}
// ══════════════════════════════════════════
// DETAIL PAGE
// ══════════════════════════════════════════
let detailFromPage = 'pg-dashboard';

function showDetail(id, type, fromPage) {
  const list = ANIMALS[type] || [];
  const a = list.find(x => x.id === id);
  if (!a) return;
  detailFromPage = fromPage || 'pg-dashboard';

  // header
  document.getElementById('detail-id-lbl').textContent      = 'ID ' + (type === 'sapi' ? 'SAPI' : type === 'kambing' ? 'KAMBING' : 'DOMBA') + ' — ' + a.id;
  document.getElementById('detail-mudhohi-name').textContent = a.mudhohi[0]?.nama || '—';
  document.getElementById('detail-mudhohi-sub').textContent  = a.label;
  document.getElementById('detail-notelp').textContent       = a.notelp;
  document.getElementById('detail-req-bagian').textContent   = a.reqBagian;
  document.getElementById('detail-alamat').textContent       = a.alamat;

  // photo placeholder emoji
  document.getElementById('detail-photo-placeholder').textContent = a.emoji;

  // info grid
  document.getElementById('di-jenis').textContent   = a.jenis;
  document.getElementById('di-umur').textContent    = a.umur;
  document.getElementById('di-sehat').textContent   = a.sehat;
  document.getElementById('di-syariat').textContent = a.syariat;
  document.getElementById('di-cacat').textContent   = a.cacat;
  document.getElementById('di-berat').textContent   = a.berat;

  // badges
  const badgeMap = [
    { label: a.jenis,    cls: 'db-neutral' },
    { label: a.umur,     cls: 'db-info'    },
    { label: a.sehat,    cls: 'db-ok'      },
    { label: a.syariat,  cls: 'db-ok'      },
    { label: a.cacat !== 'Tidak ada' ? '⚠ ' + a.cacat : '',  cls: 'db-warn', skip: a.cacat === 'Tidak ada' },
  ];
  document.getElementById('detail-badges').innerHTML = badgeMap
    .filter(b => !b.skip)
    .map(b => `<div class="detail-badge ${b.cls}">${b.label}</div>`)
    .join('');

  // tracking
  const trackEl = document.getElementById('detail-track');
  trackEl.innerHTML = `<div class="track-wrap">${
    TIMELINE.map((t, i) => {
      const isLast = i === TIMELINE.length - 1;
    const isOdd  = i % 2 === 0;
      const dotClass = t.status;
      const dotContent = t.status === 'done' ? '✓' : (i + 1);
      const lineClass  = t.status === 'done' ? 'done' : '';
      const labelClass = t.status === 'pending' ? 'pending' : '';
      const timeCls    = t.status === 'done' ? '' : (t.status === 'active' ? 'wip' : 'pending-lbl');
      return `
        <div class="track-item">
          <div class="track-left">
            <div class="track-dot ${dotClass}">${dotContent}</div>
            ${!isLast ? `<div class="track-line ${lineClass}"></div>` : ''}
          </div>
          <div class="track-body" ${isLast ? 'style="padding-bottom:0"' : ''}>
            <div class="track-label ${labelClass}">${t.icon} ${t.label}</div>
            <div class="track-desc">${t.desc}</div>
            <div class="track-time ${timeCls}">🕐 ${t.time}</div>
            ${t.status === 'done' ? '<div class="track-badge badge-done">✓ Selesai</div>' : ''}
            ${t.status === 'active' ? '<div class="track-badge badge-active">⏳ Berjalan</div>' : ''}
          </div>
        </div>
      `;
    }).join('')
  }</div>`;

  // mudhohi list
  document.getElementById('detail-mudhohi-sec').textContent = `👥 Mudhohi (${a.mudhohi.length} orang)`;
  document.getElementById('detail-mudhohi-list').innerHTML = a.mudhohi.map(m => {
    const c = AV[m.warna] || AV.brown;
    return `<div class="detail-mudhohi-row">
      <div class="avatar" style="background:${c.bg};color:${c.color};">${m.i}</div>
      <div>
        <div class="m-name">${m.nama}</div>
        <div class="m-sub">${m.bagian ? 'Bagian ' + m.bagian : 'Kurban penuh'}</div>
      </div>
    </div>`;
  }).join('');

  // back button
  document.getElementById('detail-back-btn').onclick = () => goto(detailFromPage);

  goto('pg-detail');
}

// ══════════════════════════════════════════
// LOGIN + VALIDASI
// ══════════════════════════════════════════
function submitLogin() {
  const nkkEl  = document.getElementById('inp-nkk');
  const namaEl = document.getElementById('inp-nama');
  const errNkk  = document.getElementById('err-nkk');
  const errNama = document.getElementById('err-nama');
  let valid = true;

  errNkk.classList.remove('show');
  errNama.classList.remove('show');
  nkkEl.classList.remove('error');
  namaEl.classList.remove('error');

  if (!/^\d+$/.test(nkkEl.value.trim())) {
    nkkEl.classList.add('error');
    errNkk.textContent = 'Nomor KK wajib berupa angka';
    errNkk.classList.add('show');
    valid = false;
  }
  if (!namaEl.value.trim()) {
    namaEl.classList.add('error');
    errNama.textContent = 'Nama Kepala Keluarga wajib diisi';
    errNama.classList.add('show');
    valid = false;
  }
  if (!valid) return;

  const auth = typeof validateWargaLogin === 'function'
    ? validateWargaLogin(nkkEl.value.trim(), namaEl.value.trim())
    : { ok: true };

  if (!auth.ok) {
    nkkEl.classList.add('error');
    namaEl.classList.add('error');
    errNama.textContent = auth.msg;
    errNama.classList.add('show');
    return;
  }

  const nama = namaEl.value.trim();
  const nkk = normNkk(nkkEl.value.trim());

  if (auth.penerima) {
    sessionStorage.setItem('kurbanqu_current_warga', JSON.stringify(auth.penerima));
  }

 document.getElementById('qr-nama').textContent = nama;
  document.getElementById('qr-nkk').textContent  = nkk;

  const qrEl = document.getElementById('qr-kode');
  if (qrEl && auth.penerima) {
    const qrPayload = auth.penerima.qrCode || ('P' + String(auth.penerima.id_penerima || '').padStart(5, '0'));
    qrEl.textContent = qrPayload;
    // Render QR Code asli ke canvas
    renderQrCanvas(qrPayload, auth.penerima.id_penerima);
  }

  // Update status login ke backend
  fetch('/warga/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    body: JSON.stringify({ nkk, nama })
  }).catch(e => console.warn('Gagal update status login:', e));

  if (auth.penerima) {
    sessionStorage.setItem('kurbanqu_current_warga', JSON.stringify(auth.penerima));
  }

  goto('pg-qr');
  
}
['inp-nkk','inp-nama'].forEach(id => {
  document.getElementById(id).addEventListener('input', function() {
    this.classList.remove('error');
    document.getElementById('err-' + id.split('-')[1]).classList.remove('show');
  });
});

// ══════════════════════════════════════════
// MUDHOHI PAGE
// ══════════════════════════════════════════
function av(inisial, warna) {
  const c = AV[warna] || AV.brown;
  return `<div class="avatar" style="background:${c.bg};color:${c.color};">${inisial}</div>`;
}

function updateSummary(filter) {
  const totalSapi    = ANIMALS.sapi.reduce((a,s) => a + s.mudhohi.length, 0);
  const totalKambing = ANIMALS.kambing.length;
  const totalDomba   = ANIMALS.domba.length;
  if (filter === 'Semua') {
    document.getElementById('sum-total').textContent   = totalSapi + totalKambing + totalDomba;
    document.getElementById('sum-sapi').textContent    = totalSapi;
    document.getElementById('sum-kambing').textContent = totalKambing;
    document.getElementById('sum-domba').textContent   = totalDomba;
  } else if (filter === 'sapi') {
    document.getElementById('sum-total').textContent   = totalSapi;
    document.getElementById('sum-sapi').textContent    = ANIMALS.sapi.length + ' grp';
    document.getElementById('sum-kambing').textContent = '-'; document.getElementById('sum-domba').textContent = '-';
  } else if (filter === 'kambing') {
    document.getElementById('sum-total').textContent   = totalKambing;
    document.getElementById('sum-sapi').textContent    = '-';
    document.getElementById('sum-kambing').textContent = totalKambing; document.getElementById('sum-domba').textContent = '-';
  } else if (filter === 'domba') {
    document.getElementById('sum-total').textContent   = totalDomba;
    document.getElementById('sum-sapi').textContent    = '-'; document.getElementById('sum-kambing').textContent = '-';
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
    ANIMALS.sapi.forEach(item => {
      const rows = item.mudhohi.map(m => `
        <div class="mudhohi-row" onclick="showDetail('${item.id}','sapi','pg-mudhohi')">
          ${av(m.i, m.warna)}
          <div><div class="m-name">${m.nama}</div><div class="m-sub">Bagian ${m.bagian}</div></div>
          <div style="margin-left:auto;font-size:14px;color:#c8b89a;">›</div>
        </div>`).join('');
      const hint = item.mudhohi.length > 4 ? `<div class="scroll-hint">↕ Geser untuk lihat semua</div>` : '';
      html += `<div class="group-card">
        <div class="group-hdr">
          <div class="animal-ico" style="background:#e8f3de;">🐄</div>
          <div><div class="group-name">${item.label}</div><div class="group-meta">7 orang patungan · ±25 kg/orang</div></div>
          <div class="grp-count">${item.mudhohi.length} org</div>
        </div>
        <div class="mudhohi-scroll">${rows}</div>${hint}
      </div>`;
    });
  }
  if (showKambing) {
    html += `<div class="sec-lbl">🐐 Kambing</div>`;
    ANIMALS.kambing.forEach(item => {
      html += `<div class="group-card">
        <div class="group-hdr">
          <div class="animal-ico" style="background:#faeeda;">🐐</div>
          <div><div class="group-name">${item.label}</div><div class="group-meta">1 orang · kurban penuh</div></div>
          <div class="grp-count">1 org</div>
        </div>
        <div class="mudhohi-row" onclick="showDetail('${item.id}','kambing','pg-mudhohi')">
          ${av(item.mudhohi[0].i, item.mudhohi[0].warna)}
          <div><div class="m-name">${item.mudhohi[0].nama}</div><div class="m-sub">Kurban penuh</div></div>
          <div style="margin-left:auto;font-size:14px;color:#c8b89a;">›</div>
        </div>
      </div>`;
    });
  }
  if (showDomba) {
    html += `<div class="sec-lbl">🐑 Domba</div>`;
    ANIMALS.domba.forEach(item => {
      html += `<div class="group-card">
        <div class="group-hdr">
          <div class="animal-ico" style="background:#eeedfe;">🐑</div>
          <div><div class="group-name">${item.label}</div><div class="group-meta">1 orang · kurban penuh</div></div>
          <div class="grp-count">1 org</div>
        </div>
        <div class="mudhohi-row" onclick="showDetail('${item.id}','domba','pg-mudhohi')">
          ${av(item.mudhohi[0].i, item.mudhohi[0].warna)}
          <div><div class="m-name">${item.mudhohi[0].nama}</div><div class="m-sub">Kurban penuh</div></div>
          <div style="margin-left:auto;font-size:14px;color:#c8b89a;">›</div>
        </div>
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



// ══════════════════════════════════════════
// FLOATING QR CARD
// ══════════════════════════════════════════
let qrCardDotTimer = null;
let qrCardDotIndex = 0;

function showQrCard() {
  const inner = document.getElementById('qr-float-inner');
  const tab   = document.getElementById('qr-tab');
  inner.classList.add('show');
  tab.classList.remove('show');
  startQrDots();
}

function dismissQrCard() {
  const inner = document.getElementById('qr-float-inner');
  const tab   = document.getElementById('qr-tab');
  inner.classList.remove('show');
  setTimeout(() => tab.classList.add('show'), 300);
  stopQrDots();
}

function startQrDots() {
  stopQrDots();
  const dots = document.querySelectorAll('.qr-dot');
  qrCardDotIndex = 0;
  qrCardDotTimer = setInterval(() => {
    dots.forEach((d,i) => d.classList.toggle('active', i === qrCardDotIndex));
    qrCardDotIndex = (qrCardDotIndex + 1) % dots.length;
  }, 1800);
}
function stopQrDots() {
  if (qrCardDotTimer) { clearInterval(qrCardDotTimer); qrCardDotTimer = null; }
}

function getCurrentWargaLogin() {
  try {
    const raw = sessionStorage.getItem('kurbanqu_current_warga');
    return raw ? JSON.parse(raw) : null;
  } catch (error) {
    return null;
  }
}

// ── Render QR Code asli ke canvas + isi info antrian ────────────────────────
function renderQrCanvas(qrPayload, idPenerima) {
  const canvas = document.getElementById('qr-canvas');
  if (!canvas) return;

  // Generate QR Code ke canvas pakai QRious
  if (typeof QRious !== 'undefined') {
    new QRious({
      element: canvas,
      value: qrPayload,
      size: 180,
      level: 'H',
      background: '#ffffff',
      foreground: '#3d2510',
      padding: 6,
    });
  }

  // Hitung info antrian dari id_penerima
  const noAntrian = idPenerima || 1;
  const durasi    = 15; // menit default
  const baseMin   = 8 * 60; // 08:00
  const estMin    = baseMin + ((noAntrian - 1) * durasi);
  const jamH      = Math.floor(estMin / 60) % 24;
  const jamM      = estMin % 60;
  const jamStr    = String(jamH).padStart(2,'0') + ':' + String(jamM).padStart(2,'0') + ' WIB (perkiraan)';

  // Isi elemen info
  const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  setEl('qr-antrian', noAntrian);
  setEl('qr-durasi',  durasi + ' menit');
  setEl('qr-lokasi',  'Masjid Al-Ikhlas');
  setEl('qr-jam',     jamStr);

  // Tampilkan kotak info
  const box = document.getElementById('qr-info-box');
  if (box) box.style.display = 'block';
}

async function downloadMyQr() {
  const currentWarga = getCurrentWargaLogin();
  const downloadBtn = document.getElementById('download-qr-btn');

  if (!currentWarga) {
    alert('Silakan login terlebih dahulu untuk mengunduh QR.');
    goto('pg-login');
    return;
  }

  if (downloadBtn) {
    downloadBtn.disabled = true;
    downloadBtn.textContent = 'Mengunduh...';
  }

  try {
   const response = await fetch('/warga/download-qr', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'image/png',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        nkk: currentWarga.nkk,
        nama: currentWarga.nama,
      }),
    });

    if (!response.ok) {
      const errorPayload = await response.json().catch(() => ({}));
      throw new Error(errorPayload.message || 'Gagal mengunduh QR');
    }

    const blob = await response.blob();
    const url = URL.createObjectURL(blob);
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.download = `qr-${String(currentWarga.qrCode || 'warga').replace(/[^A-Za-z0-9_-]+/g, '-')}.png`;
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    URL.revokeObjectURL(url);

    sessionStorage.setItem('kurbanqu_qr_downloaded', '1');
    if (downloadBtn) {
      downloadBtn.textContent = 'QR tersimpan di galeri';
      downloadBtn.disabled = false;
    }
  } catch (error) {
    console.error(error);
    alert(error.message || 'Gagal mengunduh QR');
    if (downloadBtn) {
      downloadBtn.disabled = false;
      downloadBtn.textContent = '⬇  Simpan QR ke Galeri';
    }
  }
}

window.downloadMyQr = downloadMyQr;
// ── Polling status pengambilan dari server (realtime) ───────────────────────
async function pollStatusPengambilan() {
  const session = sessionStorage.getItem('kurbanqu_current_warga');
  if (!session) return;
  const warga = JSON.parse(session);
  const nkk = String(warga.nkk || '').replace(/\D/g, '');
  if (!nkk) return;

  try {
    const res = await fetch('/warga/status?nkk=' + nkk, {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const data = await res.json();
    if (!data.success) return;

    const st = String(data.st_pengambilan || '').toLowerCase();
    const sudahDiambil = ['selesai', 'sudah'].includes(st);

    // Update badge kanan atas
    const badge = document.getElementById('qr-status-badge');
    if (badge) {
      if (sudahDiambil) {
        badge.style.border = '1.5px solid #4caf50';
        badge.style.color = '#4caf50';
        badge.style.background = 'rgba(76,175,80,0.1)';
        badge.textContent = '✅ Sudah diambil';
      } else {
        badge.style.border = '1.5px solid #e8b84b';
        badge.style.color = '#e8b84b';
        badge.style.background = 'rgba(232,184,75,0.1)';
        badge.textContent = '⏳ Belum diambil';
      }
    }

    // Update status box & overlay
    const statusBox    = document.getElementById('qr-status-box');
    const sudahAmbil   = document.getElementById('qr-sudah-ambil');
    const waktuEl      = document.getElementById('qr-waktu-ambil');

    if (sudahDiambil) {
      if (statusBox)  statusBox.style.display  = 'none';
      if (sudahAmbil) {
        sudahAmbil.style.display = 'flex';
        if (waktuEl && data.updated_at) {
          try {
            const dt = new Date(data.updated_at);
            const jam = dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            waktuEl.textContent = 'Diambil pukul ' + jam + ' WIB';
          } catch(e) {}
        }
      }
      // Nonaktifkan QR agar tidak bisa scan ulang
      const canvas = document.getElementById('qr-canvas');
      if (canvas) canvas.style.opacity = '0.3';
    } else {
      if (statusBox)  statusBox.style.display  = 'flex';
      if (sudahAmbil) sudahAmbil.style.display = 'none';
    }
  } catch(e) { /* silent */ }
}

// Polling setiap 5 detik saat halaman QR aktif
setInterval(() => {
  if (document.getElementById('pg-qr')?.classList.contains('active')) {
    pollStatusPengambilan();
  }
}, 5000);


// Auto-show after 400ms on page load
setTimeout(showQrCard, 400);

// Also show card whenever user navigates back to dashboard
const _origGoto = goto;
goto = function(id) {
  _origGoto(id);
  if (id === 'pg-dashboard') {
    setTimeout(showQrCard, 200);
  }
};
renderDashTimeline();
renderAnimalList('kambing'); // default chip selected = kambing
renderMudhohi('Semua');

// untuk bukan admin login dengan shortcut Ctrl + Shift + A
document.addEventListener('keydown', function(e){

    if(e.ctrlKey && e.shiftKey && e.key === 'A'){
        window.location.href = '/admin/login';
    }

});