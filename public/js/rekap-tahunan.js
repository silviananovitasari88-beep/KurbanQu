/* ============================================================
   KurbanQu — Rekap & Statistik per Periode (otomatis dari database)
   Dimuat SETELAH admin.js. Menggantikan renderRekap() yang lama
   (input manual "+ Input Data Tahunan" tidak dipakai lagi).
   ============================================================ */
(function () {
  'use strict';

  var CSRF = function () {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
  };

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function notify(msg, type) {
    if (typeof window.toast === 'function') window.toast(msg, type || 'info');
    else alert(msg);
  }

  // ─── 1) Alasan wajib untuk aksi hapus (dicatat di riwayat perubahan) ───
  window.__alasanAudit = null;
  var _fetch = window.fetch.bind(window);
  window.fetch = function (input, init) {
    init = init || {};
    var url = typeof input === 'string' ? input : (input && input.url) || '';
    var method = String(init.method || 'GET').toUpperCase();
    var perluHeader = url.indexOf('/admin/api/') === 0 || url.indexOf('/simpan-penerima') === 0;
    if (window.__alasanAudit && perluHeader && method !== 'GET') {
      init.headers = Object.assign({}, init.headers || {}, {
        'X-Alasan': encodeURIComponent(window.__alasanAudit)
      });
    }
    return _fetch(input, init);
  };

  function mintaAlasan(judul) {
    var a = prompt(judul + '\n\nAlasan (wajib, akan tercatat di riwayat perubahan):');
    if (!a || !a.trim()) {
      notify('Dibatalkan: alasan wajib diisi', 'error');
      return null;
    }
    return a.trim();
  }

  function bungkusDenganAlasan(namaFungsi, judul) {
    var asli = window[namaFungsi];
    if (typeof asli !== 'function') return;
    window[namaFungsi] = function () {
      var a = mintaAlasan(judul);
      if (!a) return;
      window.__alasanAudit = a;
      try { return asli.apply(this, arguments); }
      finally { window.__alasanAudit = null; }
    };
  }

  bungkusDenganAlasan('deleteHewan', 'Hapus data hewan');
  bungkusDenganAlasan('deleteWarga', 'Hapus data penerima');
  bungkusDenganAlasan('clearAllPenerima', 'Hapus SEMUA penerima periode ini');

  // Impor: alasan hanya diminta kalau mode "Ganti seluruh daftar"
  (function () {
    var asli = window.importConfirm;
    if (typeof asli !== 'function') return;
    window.importConfirm = function () {
      var mode = document.querySelector('input[name="import-mode"]:checked');
      if (mode && mode.value === 'replace') {
        var a = mintaAlasan('Ganti seluruh daftar penerima periode ini');
        if (!a) return;
        window.__alasanAudit = a;
        try { return asli.apply(this, arguments); }
        finally { window.__alasanAudit = null; }
      }
      return asli.apply(this, arguments);
    };
  })();

  // ─── 2) Rekap per periode ───
  var REKAP_DATA = [];
  var REKAP_SEL = null;

  if (window.PAGE_TITLES) {
    window.PAGE_TITLES.rekap = ['Rekap & Statistik', 'Ringkasan pelaksanaan kurban per periode'];
  }

  function getJson(url) {
    return _fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
      .then(function (r) { return r.json(); });
  }

  function barPenerimaSvg(list) {
    var maxV = Math.max.apply(null, list.map(function (p) { return p.penerima; }).concat([1]));
    var H = 100, bW = 32, gap = 12, scale = (H - 22) / maxV;
    var svgW = list.length * (bW + gap) + 10;
    var bars = list.map(function (p, i) {
      var x = i * (bW + gap) + 5;
      var bH = Math.max(4, p.penerima * scale);
      var y = H - bH - 18;
      var last = i === list.length - 1;
      return '<rect x="' + x + '" y="' + y + '" width="' + bW + '" height="' + bH + '" rx="4" fill="' + (last ? 'var(--gold)' : 'rgba(200,146,42,0.35)') + '"/>' +
        '<text x="' + (x + bW / 2) + '" y="' + (H - 2) + '" text-anchor="middle" font-size="8" fill="var(--text3)">' + esc(p.tahun) + '</text>' +
        '<text x="' + (x + bW / 2) + '" y="' + (y - 3) + '" text-anchor="middle" font-size="9" font-weight="700" fill="var(--text)">' + p.penerima + '</text>';
    }).join('');
    return '<svg width="' + svgW + '" height="' + H + '" viewBox="0 0 ' + svgW + ' ' + H + '">' + bars + '</svg>';
  }

  function barHewanSvg(p) {
    var vals = [p.sapi, p.kambing, p.domba];
    var cols = ['#c8922a', '#e8b84b', '#a09cf8'];
    var lbs = ['Sapi', 'Kbg', 'Domba'];
    var maxV = Math.max.apply(null, vals.concat([1]));
    var H = 110, bW = 22, gap = 7, scale = (H - 20) / maxV;
    var svgW = vals.length * (bW + gap) + 10;
    var bars = vals.map(function (v, i) {
      var x = i * (bW + gap) + 5;
      var bH = Math.max(4, v * scale);
      var y = H - bH - 14;
      return '<rect x="' + x + '" y="' + y + '" width="' + bW + '" height="' + bH + '" rx="3" fill="' + cols[i] + '"/>' +
        '<text x="' + (x + bW / 2) + '" y="' + (H - 1) + '" text-anchor="middle" font-size="7" fill="var(--text3)">' + lbs[i] + '</text>' +
        '<text x="' + (x + bW / 2) + '" y="' + (y - 2) + '" text-anchor="middle" font-size="8" font-weight="700" fill="var(--text)">' + v + '</text>';
    }).join('');
    return '<svg width="' + svgW + '" height="' + H + '" viewBox="0 0 ' + svgW + ' ' + H + '">' + bars + '</svg>';
  }

  function trendTeks(list) {
    if (list.length < 2) return '';
    var diff = list[list.length - 1].penerima - list[list.length - 2].penerima;
    var warna = diff > 0 ? 'var(--green)' : diff < 0 ? 'var(--red)' : 'var(--text3)';
    var panah = diff > 0 ? '\u2191' : diff < 0 ? '\u2193' : '\u2192';
    return '<span style="font-size:11px;font-weight:700;color:' + warna + ';margin-left:8px;">' +
      panah + ' ' + (diff > 0 ? '+' : '') + diff + ' dari tahun lalu</span>';
  }

  function labelPeriode(p) {
    return p.tahun + (p.hijriah ? ' (' + p.hijriah + ')' : '') + (p.aktif ? ' \u2014 aktif' : ' \u2014 arsip');
  }

  async function renderRekap() {
    var el = document.getElementById('rekap-content');
    if (!el) return;

    try {
      var j = await getJson('/admin/api/rekap-tahunan');
      REKAP_DATA = (j && j.data) || [];
    } catch (e) {
      el.innerHTML = '<div class="empty-state"><div class="empty-ico">\u26A0\uFE0F</div>Gagal memuat rekap. Coba muat ulang halaman.</div>';
      return;
    }

    if (!REKAP_DATA.length) {
      el.innerHTML = '<div class="empty-state"><div class="empty-ico">\uD83D\uDCCA</div>Belum ada periode kurban.</div>';
      return;
    }

    var aktif = REKAP_DATA.filter(function (p) { return p.aktif; })[0] || REKAP_DATA[REKAP_DATA.length - 1];
    if (!REKAP_SEL || !REKAP_DATA.some(function (p) { return p.tahun === REKAP_SEL; })) REKAP_SEL = aktif.tahun;
    var sel = REKAP_DATA.filter(function (p) { return p.tahun === REKAP_SEL; })[0];

    var sub = document.querySelector('#pg-rekap > div:first-child > div:nth-child(2)');
    if (sub) sub.textContent = 'Ringkasan pelaksanaan kurban ' + (sel.hijriah || sel.tahun);

    var total = sel.penerima, diambil = sel.diambil, belum = total - diambil;
    var pct = total ? Math.round(diambil / total * 100) : 0;
    var circ = 2 * Math.PI * 48, off = circ - (pct / 100) * circ;

    var opsi = REKAP_DATA.slice().reverse().map(function (p) {
      return '<option value="' + esc(p.tahun) + '"' + (p.tahun === REKAP_SEL ? ' selected' : '') + '>' + esc(labelPeriode(p)) + '</option>';
    }).join('');

    var badgeArsip = sel.aktif ? '' :
      '<span style="background:rgba(160,156,248,0.15);color:#a09cf8;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;">\uD83D\uDD12 Arsip \u2014 hanya dilihat</span>';

    var tracking;
    if (sel.aktif && window.TIMELINE) {
      tracking = '<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;">' +
        window.TIMELINE.map(function (t) {
          var bg = t.status === 'done' ? 'rgba(200,146,42,0.1)' : t.status === 'active' ? 'rgba(232,184,75,0.07)' : 'var(--bg3)';
          var bd = t.status === 'done' ? 'rgba(200,146,42,0.2)' : t.status === 'active' ? 'rgba(232,184,75,0.15)' : 'var(--border)';
          var wl = t.status === 'done' ? 'var(--gold2)' : t.status === 'active' ? 'var(--amber)' : 'var(--text3)';
          return '<div style="text-align:center;background:' + bg + ';border:1px solid ' + bd + ';border-radius:10px;padding:10px 4px;">' +
            '<div style="font-size:18px;margin-bottom:4px;">' + (t.status === 'done' ? '\u2705' : t.icon) + '</div>' +
            '<div style="font-size:10px;font-weight:700;color:' + (t.status === 'pending' ? 'var(--text3)' : 'var(--text)') + ';">' + esc(t.label) + '</div>' +
            '<div style="font-size:9px;font-weight:700;margin-top:3px;color:' + wl + ';">' + esc(t.time) + '</div></div>';
        }).join('') + '</div>';
    } else {
      tracking = '<div class="empty-state" style="padding:20px;">Status proses hanya dilacak untuk periode yang sedang aktif.</div>';
    }

    var hewanKolom = REKAP_DATA.map(function (p) {
      return '<div style="text-align:center;flex-shrink:0;min-width:90px;">' +
        '<div style="font-size:12px;font-weight:700;color:var(--gold2);margin-bottom:6px;">' + esc(p.tahun) + '</div>' +
        barHewanSvg(p) +
        '<div style="font-size:10px;color:var(--text3);margin-top:3px;">Total: <strong>' + p.total_hewan + '</strong> ekor</div></div>';
    }).join('');

    var penerimaBaris = REKAP_DATA.map(function (p, i) {
      var prev = i > 0 ? REKAP_DATA[i - 1].penerima : null;
      var diff = prev === null ? null : p.penerima - prev;
      var panah = diff === null ? '' : diff > 0 ? '\u2191' : diff < 0 ? '\u2193' : '\u2192';
      var warna = diff === null ? '' : diff > 0 ? 'var(--green)' : diff < 0 ? 'var(--red)' : 'var(--text3)';
      var last = i === REKAP_DATA.length - 1;
      return '<div style="display:flex;align-items:center;gap:10px;padding:6px 0;' + (last ? '' : 'border-bottom:1px solid var(--border);') + '">' +
        '<div style="font-size:12px;color:var(--text3);width:40px;">' + esc(p.tahun) + '</div>' +
        '<div style="font-size:' + (last ? 22 : 17) + 'px;font-weight:800;color:' + (last ? 'var(--gold)' : 'var(--text)') + ';">' + p.penerima + '</div>' +
        '<div style="font-size:11px;color:var(--text3);">penerima (' + p.diambil + ' sudah ambil)</div>' +
        (diff !== null ? '<span style="font-size:11px;font-weight:700;color:' + warna + ';">' + panah + ' ' + (diff > 0 ? '+' : '') + diff + '</span>' : '') +
        '</div>';
    }).join('');

    el.innerHTML =
      '<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:18px;">' +
        '<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">' +
          '<label style="font-size:12px;color:var(--text3);font-weight:600;">Periode</label>' +
          '<select id="rekap-periode" onchange="rekapPilihPeriode(this.value)" style="background:var(--bg3);border:1px solid var(--border2);border-radius:9px;padding:8px 12px;font-size:13px;color:var(--text);font-family:inherit;">' + opsi + '</select>' +
          badgeArsip +
        '</div>' +
        '<div style="display:flex;gap:8px;flex-wrap:wrap;">' +
          '<button onclick="downloadSemuaDataTahunan()" style="padding:6px 12px;font-size:11px;font-weight:700;border:1px solid var(--text3);background:var(--bg3);color:var(--text2);border-radius:8px;cursor:pointer;">\u2193 Download Semua</button>' +
          '<button onclick="mulaiPeriodeBaru()" style="padding:6px 14px;font-size:12px;font-weight:700;border:none;background:var(--gold);color:#fff;border-radius:8px;cursor:pointer;">+ Mulai Periode Baru</button>' +
        '</div>' +
      '</div>' +

      '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px;margin-bottom:18px;">' +
        '<div class="card"><div class="card-header"><div class="card-title">\uD83D\uDCCA Progress Distribusi ' + esc(sel.tahun) + '</div></div>' +
          '<div class="card-body" style="display:flex;align-items:center;gap:24px;">' +
            '<div class="rekap-ring"><svg width="120" height="120" viewBox="0 0 120 120">' +
              '<circle cx="60" cy="60" r="48" fill="none" stroke="var(--bg4)" stroke-width="10"/>' +
              '<circle cx="60" cy="60" r="48" fill="none" stroke="var(--gold)" stroke-width="10" stroke-linecap="round" stroke-dasharray="' + circ.toFixed(2) + '" stroke-dashoffset="' + off.toFixed(2) + '" style="transform:rotate(-90deg);transform-origin:60px 60px;"/></svg>' +
              '<div class="rekap-ring-label"><div class="rekap-ring-num">' + pct + '%</div><div class="rekap-ring-sub">selesai</div></div></div>' +
            '<div style="flex:1;">' +
              '<div style="margin-bottom:14px;"><div style="font-size:32px;font-weight:800;color:var(--green);">' + diambil + '</div><div style="font-size:12px;color:var(--text3);">Sudah diambil</div></div>' +
              '<div style="margin-bottom:10px;"><div style="font-size:32px;font-weight:800;color:var(--amber);">' + belum + '</div><div style="font-size:12px;color:var(--text3);">Belum diambil</div></div>' +
              '<div style="font-size:11px;color:var(--text3);padding-top:8px;border-top:1px solid var(--border);">dari <strong style="color:var(--text);">' + total + '</strong> total penerima terdaftar</div>' +
            '</div></div></div>' +
        '<div class="card"><div class="card-header"><div class="card-title">\uD83D\uDCCD Status Proses Kurban</div></div><div class="card-body">' + tracking + '</div></div>' +
      '</div>' +

      '<div class="card" style="margin-bottom:18px;"><div class="card-header"><div>' +
        '<div class="card-title">\uD83D\uDC04 Data Hewan Kurban per Tahun</div>' +
        '<div style="font-size:11px;color:var(--text3);margin-top:2px;">Terisi otomatis dari data hewan tiap periode</div></div></div>' +
        '<div class="card-body"><div style="display:flex;gap:28px;align-items:flex-end;overflow-x:auto;padding-bottom:8px;">' + hewanKolom + '</div>' +
        '<div style="display:flex;gap:14px;margin-top:12px;padding-top:10px;border-top:1px solid var(--border);font-size:12px;">' +
          '<span><b style="color:#c8922a;">\u25A0</b> Sapi</span><span><b style="color:#e8b84b;">\u25A0</b> Kambing</span><span><b style="color:#a09cf8;">\u25A0</b> Domba</span></div></div></div>' +

      '<div class="card" style="margin-bottom:18px;"><div class="card-header"><div style="display:flex;align-items:center;flex-wrap:wrap;">' +
        '<div class="card-title">\uD83D\uDC65 Data Penerima per Tahun</div>' + trendTeks(REKAP_DATA) + '</div></div>' +
        '<div class="card-body"><div style="display:flex;align-items:flex-end;gap:24px;flex-wrap:wrap;">' + barPenerimaSvg(REKAP_DATA) + '<div>' + penerimaBaris + '</div></div></div></div>' +

      '<div class="card"><div class="card-header"><div>' +
        '<div class="card-title">\uD83D\uDD52 Riwayat Perubahan Data</div>' +
        '<div style="font-size:11px;color:var(--text3);margin-top:2px;">Tercatat otomatis. Tidak bisa diubah atau dihapus.</div></div></div>' +
        '<div id="audit-list" style="max-height:320px;overflow-y:auto;"><div class="empty-state" style="padding:20px;">Memuat riwayat\u2026</div></div></div>';

    muatAudit();
  }

  // ─── 3) Riwayat perubahan (diperbarui otomatis tiap 10 detik) ───
  var AKSI = {
    tambah_hewan: 'Tambah hewan',
    hapus_hewan: 'Hapus hewan',
    hapus_warga: 'Hapus penerima',
    hapus_semua_penerima: 'Hapus semua penerima',
    impor_penerima: 'Impor penerima',
    mulai_periode: 'Mulai periode baru'
  };

  function ringkas(a) {
    var l = a.lama || {}, b = a.baru || {};
    if (a.aksi === 'hapus_warga' && l.nama_kk) return 'Penerima ' + l.nama_kk + ' (KK ' + (a.data_id || '') + ')';
    if (a.aksi === 'hapus_hewan' && l.jenis) return 'Hewan #' + (a.data_id || '') + ' (' + l.jenis + ')';
    if (a.aksi === 'tambah_hewan' && b.jenis) return 'Hewan #' + (a.data_id || '') + ' (' + b.jenis + ')';
    if (a.aksi === 'mulai_periode' && b.tahun) return 'Periode ' + b.tahun + (b.hijriah ? ' (' + b.hijriah + ')' : '');
    if (a.aksi === 'impor_penerima') return (b.baru || 0) + ' baru, ' + (b.diperbarui || 0) + ' diperbarui' + (b.mode === 'replace' ? ' (ganti seluruh daftar)' : '');
    if (a.aksi === 'hapus_semua_penerima') return (b.distribusi_terhapus || 0) + ' data penerima';
    return '';
  }

  async function muatAudit() {
    var box = document.getElementById('audit-list');
    if (!box) return;
    try {
      var j = await getJson('/admin/api/audit-log?limit=50');
      var rows = (j && j.data) || [];
      if (!rows.length) {
        box.innerHTML = '<div class="empty-state" style="padding:20px;">Belum ada perubahan tercatat.</div>';
        return;
      }
      box.innerHTML = rows.map(function (a) {
        var waktu = String(a.created_at || '').replace('T', ' ').slice(0, 19);
        var detail = ringkas(a);
        return '<div style="padding:10px 16px;border-bottom:1px solid var(--border);">' +
          '<div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;">' +
            '<strong style="font-size:12px;">' + esc(AKSI[a.aksi] || a.aksi) + '</strong>' +
            '<span style="font-size:11px;color:var(--text3);">' + esc(waktu) + '</span></div>' +
          '<div style="font-size:11px;color:var(--text3);margin-top:2px;">oleh <b style="color:var(--text2);">' + esc(a.aktor) + '</b>' +
            (detail ? ' \u00B7 ' + esc(detail) : '') + '</div>' +
          (a.alasan ? '<div style="font-size:11px;color:var(--gold2);margin-top:2px;">Alasan: ' + esc(a.alasan) + '</div>' : '') +
          '</div>';
      }).join('');
    } catch (e) {
      box.innerHTML = '<div class="empty-state" style="padding:20px;">Gagal memuat riwayat.</div>';
    }
  }

  setInterval(function () {
    var pg = document.getElementById('pg-rekap');
    if (pg && pg.classList.contains('active')) muatAudit();
  }, 10000);

  // ─── 4) Aksi di halaman rekap ───
  window.rekapPilihPeriode = function (tahun) {
    REKAP_SEL = tahun;
    renderRekap();
  };

  window.mulaiPeriodeBaru = async function () {
    var tahun = prompt('Tahun periode baru (contoh: 2027):');
    if (!tahun) return;
    tahun = tahun.trim();
    if (!/^\d{4}$/.test(tahun)) { notify('Tahun harus 4 angka, contoh 2027', 'error'); return; }
    var hijriah = prompt('Tahun Hijriah (boleh dikosongkan, contoh: 1448 H):') || '';
    var alasan = mintaAlasan('Mulai periode ' + tahun);
    if (!alasan) return;

    var ok = confirm(
      'Mulai periode ' + tahun + '?\n\n' +
      '\u2022 Data periode sekarang menjadi ARSIP (hanya dilihat)\n' +
      '\u2022 Status proses (tracking) dan stok daging di-reset ke awal\n' +
      '\u2022 Halaman kerja kosong, siap diisi data ' + tahun + '\n\n' +
      'Lanjutkan?'
    );
    if (!ok) return;

    try {
      var res = await _fetch('/admin/api/periode/mulai', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': CSRF(),
          'X-Alasan': encodeURIComponent(alasan)
        },
        body: JSON.stringify({ tahun: tahun, hijriah: hijriah.trim() || null })
      });
      var j = await res.json().catch(function () { return {}; });
      if (!res.ok || !j.success) {
        var pesan = j.message || (j.errors && Object.values(j.errors)[0][0]) || 'Gagal memulai periode baru';
        throw new Error(pesan);
      }

      // Bersihkan salinan daftar penerima di browser supaya tidak tercampur periode lama
      ['STORAGE_PENERIMA', 'STORAGE_CONFIRMED_WARGA', 'STORAGE_WARGA_LOGIN', 'STORAGE_PENERIMA_ID',
       'STORAGE_DIST_CLAIMED', 'STORAGE_DIST_DOWNLOADED', 'STORAGE_DIST_METHOD', 'STORAGE_DIST_TIME']
        .forEach(function (nama) {
          try {
            var kunci = (new Function('return typeof ' + nama + ' !== "undefined" ? ' + nama + ' : null'))();
            if (kunci) localStorage.removeItem(kunci);
          } catch (e) { /* dilewati */ }
        });

      notify(j.message, 'success');
      setTimeout(function () { location.reload(); }, 900);
    } catch (e) {
      notify(e.message || 'Gagal memulai periode baru', 'error');
    }
  };

  window.downloadSemuaDataTahunan = function () {
    if (!REKAP_DATA.length) { notify('Belum ada data', 'info'); return; }
    var rows = [
      ['KurbanQu - Rekapitulasi per Periode'],
      ['Dibuat: ' + new Date().toLocaleDateString('id-ID')],
      [],
      ['Tahun', 'Hijriah', 'Status', 'Total Hewan', 'Sapi', 'Kambing', 'Domba', 'Total Penerima', 'Sudah Diambil', 'Belum Diambil']
    ];
    REKAP_DATA.forEach(function (p) {
      rows.push([p.tahun, p.hijriah || '-', p.aktif ? 'Aktif' : 'Arsip', p.total_hewan, p.sapi, p.kambing, p.domba,
        p.penerima, p.diambil, p.penerima - p.diambil]);
    });
    var csv = rows.map(function (r) {
      return r.map(function (c) { return '"' + String(c).replace(/"/g, '""') + '"'; }).join(',');
    }).join('\n');
    var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'kurbanqu_rekap_per_periode.csv';
    a.click();
    notify('Rekap semua periode diunduh', 'success');
  };

  // Menggantikan fungsi lama dari admin.js
  window.renderRekap = renderRekap;
  window.showTambahDataTahunan = function () {
    notify('Rekap tahunan sekarang otomatis dari database, tidak perlu input manual.', 'info');
  };
})();
