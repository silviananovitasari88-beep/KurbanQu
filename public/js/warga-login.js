/**
 * Daftar penerima kurban — sumber login (No KK + Nama KK) & kode QR.
 * Diisi admin via Upload Excel/CSV di dashboard.
 */
const STORAGE_PENERIMA = 'kurbanqu_penerima_kurban';
const STORAGE_PENERIMA_ID = 'kurbanqu_next_penerima_id';
/** @deprecated gunakan STORAGE_PENERIMA */
const STORAGE_CONFIRMED_WARGA = 'kurbanqu_confirmed_warga';
const STORAGE_WARGA_LOGIN = 'kurbanqu_warga_login';

function normNkk(s) {
  return String(s || '').replace(/\D/g, '').slice(0, 16);
}

function normNama(s) {
  return String(s || '').trim().toLowerCase().replace(/\s+/g, ' ');
}

function loadPenerima() {
  try {
    let raw = localStorage.getItem(STORAGE_PENERIMA);
    if (!raw) {
      raw = localStorage.getItem(STORAGE_CONFIRMED_WARGA);
      if (raw) {
        const legacy = JSON.parse(raw);
        savePenerima(legacy);
        return legacy;
      }
    }
    if (raw) return JSON.parse(raw);
  } catch (e) { /* ignore */ }
  return [];
}

function savePenerima(list) {
  localStorage.setItem(STORAGE_PENERIMA, JSON.stringify(list));
  localStorage.setItem(STORAGE_CONFIRMED_WARGA, JSON.stringify(list));
  syncLoginIndex(list);
}

function loadNextPenerimaId() {
  const stored = parseInt(localStorage.getItem(STORAGE_PENERIMA_ID) || '0', 10);
  let max = 0;
  loadPenerima().forEach(p => { if (p.id_penerima > max) max = p.id_penerima; });
  return Math.max(max + 1, stored || 1);
}

function saveNextPenerimaId(n) {
  localStorage.setItem(STORAGE_PENERIMA_ID, String(n));
}

function qrCodePenerima(id) {
  return 'P' + String(id).padStart(5, '0');
}

function normalizePenerimaRow(r, id_penerima) {
  const nkk = normNkk(r.nkk);
  const nama = String(r.nama || '').trim();
  const id = id_penerima || r.id_penerima;
  return {
    id_penerima: id,
    nkk,
    nama,
    qrCode: r.qrCode || qrCodePenerima(id),
    alamat: (r.alamat || '').trim(),
    notelp: (r.notelp || '').trim(),
  };
}

function syncLoginIndex(list) {
  const login = list
    .filter(p => p.nkk.length >= 10 && p.nama.length >= 2)
    .map(p => ({ nkk: p.nkk, nama: p.nama, id_penerima: p.id_penerima, qrCode: p.qrCode }));
  localStorage.setItem(STORAGE_WARGA_LOGIN, JSON.stringify(login));
}

/** @deprecated */
function loadConfirmedWarga() { return loadPenerima(); }
function saveConfirmedWarga(list) { savePenerima(list); }
function loadWargaLogin() { return loadPenerima(); }
function saveWargaLogin(list) { savePenerima(list); }

function findPenerimaByLogin(nkk, nama) {
  const n = normNkk(nkk);
  const nm = normNama(nama);
  return loadPenerima().find(p => normNkk(p.nkk) === n && normNama(p.nama) === nm);
}

function mergePenerimaRows(rows, mode) {
  let nextId = loadNextPenerimaId();
  const map = new Map();

  if (mode !== 'replace') {
    loadPenerima().forEach(p => {
      const key = normNkk(p.nkk) + '|' + normNama(p.nama);
      map.set(key, normalizePenerimaRow(p, p.id_penerima));
    });
  }

  (rows || []).forEach(r => {
    const nkk = normNkk(r.nkk);
    const nama = String(r.nama || '').trim();
    if (nkk.length < 10 || nama.length < 2) return;
    const key = nkk + '|' + normNama(nama);
    const existing = map.get(key);
    const id = existing?.id_penerima || nextId++;
    map.set(key, normalizePenerimaRow({ ...r, nkk, nama }, id));
  });

  const list = [...map.values()];
  savePenerima(list);
  saveNextPenerimaId(nextId);
  return list.length;
}

function validateWargaLogin(nkk, nama) {
  const list = loadPenerima();
  if (!list.length) {
    return {
      ok: false,
      msg: 'Daftar penerima kurban belum diunggah panitia. Hubungi admin.',
    };
  }
  const n = normNkk(nkk);
  const nm = normNama(nama);
  if (n.length < 10) {
    return { ok: false, msg: 'Nomor KK minimal 10 digit.' };
  }
  const p = findPenerimaByLogin(nkk, nama);
  if (!p) {
    return {
      ok: false,
      msg: 'No. KK atau Nama Kepala Keluarga tidak terdaftar sebagai penerima kurban.',
    };
  }
  return { ok: true, penerima: p };
}

function addPenerimaManual(nkk, nama, alamat, notelp) {
  return mergePenerimaRows([{ nkk, nama, alamat, notelp }], 'append');
}

function removePenerimaAt(index) {
  const list = loadPenerima();
  list.splice(index, 1);
  savePenerima(list);
}

function mergeWargaLogin(rows) {
  return mergePenerimaRows(rows, 'append');
}
