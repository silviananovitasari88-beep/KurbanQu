# 📊 VISUALISASI INTEGRASI EXCEL → DATABASE

## 1️⃣ DATA FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────┐
│                    ADMIN DASHBOARD                              │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Upload Excel/CSV                                        │  │
│  │  - No KK, Nama KK, Alamat, No Telp                      │  │
│  │  - Auto-detect kolom                                     │  │
│  │  - Preview sebelum simpan                                │  │
│  └────────────────────┬─────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                        │
                        ▼ (JavaScript admin.js)
┌─────────────────────────────────────────────────────────────────┐
│              PARSING & VALIDATION (Client-side)                 │
│  - Parse CSV/Excel format                                       │
│  - Normalize No KK (10-16 digit)                                │
│  - Validate Nama (min 2 char)                                   │
│  - Generate QR Code                                             │
│  - Preview data                                                 │
└────────────────────┬─────────────────────────────────────────────┘
                     │
                     ▼ (User klik "Aktifkan sebagai Penerima")
┌─────────────────────────────────────────────────────────────────┐
│            SAVE KE localStorage (Current Issue!)                 │
│  Key: kurbanqu_penerima_kurban                                   │
│  ⚠️ Masalah: Hanya di browser ini, tidak di server!            │
└────────────────────┬─────────────────────────────────────────────┘
                     │
                     ▼ (fetch POST /simpan-penerima)
┌─────────────────────────────────────────────────────────────────┐
│                 ENDPOINT /simpan-penerima                        │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  POST /simpan-penerima                                  │  │
│  │  {                                                       │  │
│  │    penerima: [                                           │  │
│  │      { nkk, nama, qrCode },  ← No alamat, no_telp!    │  │
│  │      ...                                                 │  │
│  │    ]                                                     │  │
│  │  }                                                       │  │
│  └──────────────────────────────────────────────────────────┘  │
│                      ↓                                           │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Warga::updateOrCreate()                                │  │
│  │  foreach $data as $row:                                 │  │
│  │    Warga::updateOrCreate(                               │  │
│  │      ['no_kk' => $row['nkk']],                          │  │
│  │      ['nama_kk', 'QR_id_qr']                            │  │
│  │    )                                                     │  │
│  │                                                          │  │
│  │  ⚠️ Masalah:                                            │  │
│  │  - Tidak return response ke client                      │  │
│  │  - Tidak save alamat, no_telp                           │  │
│  │  - Tidak log upload metadata                            │  │
│  │  - No error handling                                    │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────┬─────────────────────────────────────────────┘
                     │
                     ▼ (Insert/Update ke Database)
┌─────────────────────────────────────────────────────────────────┐
│            MYSQL DATABASE (phpMyAdmin)                          │
│  Table: warga                                                    │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ no_kk | nama_kk | QR_id_qr | alamat | no_telp | ...   │   │
│  ├─────────────────────────────────────────────────────────┤   │
│  │ 3273011234567890 | Ahmad | P00001 | NULL | NULL | ... │   │
│  │ 3273012345678901 | Siti  | P00002 | NULL | NULL | ... │   │
│  │                                                         │   │
│  │ ⚠️ Masalah: alamat & no_telp tidak tersimpan          │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2️⃣ DATABASE SCHEMA

### Current Table: `warga` ❌ Incomplete

```sql
CREATE TABLE warga (
  no_kk VARCHAR(16) PRIMARY KEY,          ← No KK
  nama_kk VARCHAR(255),                   ← Nama Kepala Keluarga
  QR_id_qr VARCHAR(50),                   ← Kode QR
  -- MISSING:
  -- alamat VARCHAR(255),              ← Dari Excel, tapi tidak ada
  -- no_telp VARCHAR(20),              ← Dari Excel, tapi tidak ada
  -- id_penerima INT,                  ← Unique ID untuk referensi
  -- created_at TIMESTAMP,             ← Kapan diinput
  -- updated_at TIMESTAMP              ← Kapan diubah
)
```

### Proposed Schema: Enhanced `warga` ✅

```sql
ALTER TABLE warga ADD COLUMN IF NOT EXISTS
  id_penerima INT UNIQUE NOT NULL AUTO_INCREMENT,
  
ALTER TABLE warga ADD COLUMN IF NOT EXISTS
  alamat VARCHAR(255) NULL,
  
ALTER TABLE warga ADD COLUMN IF NOT EXISTS
  no_telp VARCHAR(20) NULL,
  
ALTER TABLE warga ADD COLUMN IF NOT EXISTS
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
ALTER TABLE warga ADD COLUMN IF NOT EXISTS
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
```

**Result:**
```sql
┌─ warga table ──────────────────────────────────────────────────┐
│ no_kk (PK)  | nama_kk | alamat | no_telp | QR_id_qr | id_penerima (UQ)
├─────────────┬─────────┬────────┬─────────┬──────────┬─────────────────┤
│ 3273011234567890 │ Ahmad │ Kp. Cikaret │ 0812xxxx │ P00001 │ 1
│ 3273012345678901 │ Siti  │ Jl. Tanjung │ 0813xxxx │ P00002 │ 2
└─────────────────────────────────────────────────────────────────┘
```

### New Table: `warga_uploads` (Audit Trail) ✅

```sql
CREATE TABLE warga_uploads (
  id INT PRIMARY KEY AUTO_INCREMENT,
  
  filename VARCHAR(255) NOT NULL,        ← Nama file Excel/CSV
  jumlah_baris INT NOT NULL,             ← Jumlah baris diupload
  mode ENUM('append','replace'),         ← Mode: tambah atau ganti
  admin_id INT,                          ← Admin yang upload
  
  status ENUM('pending','success','failed'),
  error_message TEXT,                    ← Jika ada error
  
  uploaded_at TIMESTAMP DEFAULT NOW(),   ← Waktu upload
  processed_at TIMESTAMP NULL            ← Waktu diproses
);

Example records:
┌─────────┬────────────┬──────┬─────────┬──────────┬────────────────────────┬───────────────┐
│ id │ filename   │ rows │ mode   │ admin_id │ status  │ uploaded_at            │ processed_at
├────┼────────────┼──────┼────────┼──────────┼─────────┼────────────────────────┼──────────────┤
│ 1  │ data.xlsx  │ 45   │ append │ 1        │ success │ 2026-06-05 10:30:15   │ 2026-06-05 10:30:20
│ 2  │ warga.csv  │ 120  │ replace│ 1        │ success │ 2026-06-05 11:45:00   │ 2026-06-05 11:45:05
│ 3  │ test.xlsx  │ 10   │ failed │ 1        │ failed  │ 2026-06-05 12:00:00   │ NULL
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3️⃣ ENDPOINT IMPROVEMENT

### Current Endpoint (routes/web.php) ❌ Masalah

```php
Route::post('/simpan-penerima', function(\Illuminate\Http\Request $request) {
    $data = $request->input('penerima', []);
    foreach ($data as $row) {
        \App\Models\Warga::updateOrCreate(
            ['no_kk' => $row['nkk']],
            [
                'nama_kk'   => $row['nama'],
                'QR_id_qr'  => $row['qrCode'] ?? null,
            ]
        );
    }
    return response()->json(['success' => true]);  ← Tidak informatif!
});
```

**Masalah:**
- ❌ Tidak validasi data
- ❌ Tidak simpan alamat, no_telp
- ❌ Tidak log ke warga_uploads
- ❌ Tidak return error detail
- ❌ Tidak handle transaction

### Proposed Endpoint ✅ Improved

```php
Route::post('/simpan-penerima', function(\Illuminate\Http\Request $request) {
    // ✅ Validasi input
    $request->validate([
        'penerima' => 'required|array|min:1',
        'penerima.*.nkk' => 'required|min:10',
        'penerima.*.nama' => 'required|string|min:2',
    ]);
    
    $data = $request->input('penerima', []);
    $mode = $request->input('mode', 'append');
    
    // ✅ Log ke warga_uploads
    $upload = WargaUpload::create([
        'filename' => 'web_upload',
        'jumlah_baris' => count($data),
        'mode' => $mode,
        'admin_id' => auth()->id(),
        'status' => 'pending',
    ]);
    
    try {
        DB::beginTransaction();
        
        // ✅ Handle replace mode
        if ($mode === 'replace') {
            Warga::truncate();
        }
        
        $created = 0;
        $updated = 0;
        $errors = [];
        
        foreach ($data as $row) {
            try {
                // ✅ Normalisasi data
                $nkk = preg_replace('/\D/', '', $row['nkk']);
                
                // ✅ Validasi
                if (strlen($nkk) < 10) {
                    $errors[] = "No KK '{$row['nkk']}' kurang dari 10 digit";
                    continue;
                }
                
                // ✅ Update atau create dengan semua kolom
                $result = Warga::updateOrCreate(
                    ['no_kk' => $nkk],
                    [
                        'nama_kk'  => $row['nama'],
                        'alamat'   => $row['alamat'] ?? null,
                        'no_telp'  => $row['notelp'] ?? null,
                        'QR_id_qr' => $row['qrCode'] ?? null,
                    ]
                );
                
                if ($result->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        
        DB::commit();
        
        // ✅ Update upload status
        $upload->update([
            'status' => 'success',
            'processed_at' => now(),
        ]);
        
        // ✅ Return detailed response
        return response()->json([
            'success' => true,
            'message' => "✓ {$created} penerima baru, {$updated} diperbarui",
            'data' => [
                'created' => $created,
                'updated' => $updated,
                'total' => $created + $updated,
                'errors' => $errors,
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        // ✅ Log error
        $upload->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'processed_at' => now(),
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan: ' . $e->getMessage(),
        ], 422);
    }
});
```

---

## 4️⃣ VERIFICATION DI phpMyAdmin

### Setelah Admin Upload Excel, Cek:

**1. Table `warga`:**
```
Database: kurbanqu → Table: warga

Columns terlihat:
- no_kk ✓
- nama_kk ✓
- alamat ✓ (sebelumnya NULL)
- no_telp ✓ (sebelumnya NULL)
- QR_id_qr ✓
- id_penerima ✓ (auto-increment)
- created_at ✓
- updated_at ✓

Sample data:
no_kk | nama_kk | alamat | no_telp | QR_id_qr | created_at
3273011234567890 | Ahmad | Kp. Cikaret | 0812xxxx | P00001 | 2026-06-05 10:30:15
```

**2. Table `warga_uploads` (Audit Log):**
```
Database: kurbanqu → Table: warga_uploads

Records menunjukkan:
id | filename | jumlah_baris | mode | status | uploaded_at
1  | web_upload | 45 | append | success | 2026-06-05 10:30:15
```

---

## 5️⃣ FILE DATA FLOW (Teknis)

### Input (dari Excel/CSV file):
```
no_kk,nama_kk,alamat,no_telp
3273011234567890,Ahmad Hidayat,Kp. Cikaret,0812345678
3273012345678901,Siti Rahmawati,Jl. Tanjung,0813345678
```

### Browser Processing (admin.js):
```js
// Parse CSV
const rows = parseCSV(csvContent);
// Generate QR Code
const withQR = rows.map((r, i) => ({
  ...r,
  id_penerima: i + 1,
  qrCode: 'P' + String(i + 1).padStart(5, '0')
}));
// Save to localStorage
localStorage.setItem('kurbanqu_penerima_kurban', JSON.stringify(withQR));
// Send to server
fetch('/simpan-penerima', {
  method: 'POST',
  body: JSON.stringify({ penerima: withQR, mode: 'append' })
})
```

### Server Processing (endpoint):
```php
// Receive
$penerima = $request->input('penerima');
$mode = $request->input('mode');

// Validate & Transform
foreach ($penerima as $row) {
  $nkk = preg_replace('/\D/', '', $row['nkk']); // 10-16 digit
  
  // Save to DB
  Warga::updateOrCreate(
    ['no_kk' => $nkk],
    [
      'nama_kk' => $row['nama'],
      'alamat' => $row['alamat'],
      'no_telp' => $row['notelp'],
      'QR_id_qr' => $row['qrCode'],
    ]
  );
  
  // Log upload
  WargaUpload::create([...]);
}

// Response
return ['success' => true, 'message' => '45 penerima tersimpan'];
```

### Database Result:
```sql
SELECT * FROM warga WHERE no_kk LIKE '3273%';
→ 45 rows dengan semua data (alamat, no_telp sudah ada)

SELECT * FROM warga_uploads ORDER BY uploaded_at DESC LIMIT 1;
→ Log upload dengan timestamp dan status
```

---

## 📌 SUMMARY

| Aspek | Sebelum | Sesudah |
|-------|---------|--------|
| **Storage** | localStorage (browser) | Database MySQL |
| **Data** | no_kk, nama, qrCode | + alamat, no_telp |
| **Tracking** | Tidak ada | warga_uploads log |
| **Validation** | Minimal | Lengkap |
| **Error Response** | Tidak ada | Detail error |
| **Persistence** | Per browser | Global server |
| **phpMyAdmin** | Data ada tapi incomplete | Data lengkap & auditable |

---

**Status**: Ready untuk implementasi sesuai dengan approval dari user
