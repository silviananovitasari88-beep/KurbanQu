# 📋 INSTRUKSI EKSEKUSI IMPLEMENTASI
## Excel → Database Integration

---

## ✅ STATUS SEKARANG

### Files yang sudah di-update:
- ✅ `app/Models/Warga.php` - Updated fillable & timestamps
- ✅ `app/Models/WargaUpload.php` - Created (new model)
- ✅ `routes/web.php` - Updated endpoint `/simpan-penerima`
- ✅ `migrations_execute.php` - Script untuk run migration

---

## 🚀 LANGKAH EKSEKUSI

### **LANGKAH 1: Database Migration (TAHAP 1 & 2)**

**Via Browser (Cara Mudah):**

1. Buka browser → `http://localhost/KurbanQu/migrations_execute.php`
2. Lihat output - seharusnya semua ✅ hijau
3. Catat hasilnya

**Via phpMyAdmin (Cara Manual):**

1. Buka `http://localhost/phpmyadmin`
2. Login ke database `kurbanqu`
3. Buka tab "SQL"
4. Copy-paste SQL commands di bawah
5. Click "Go" untuk execute

**SQL Commands (jika perlu manual):**

```sql
-- TAHAP 1: Alter table warga
ALTER TABLE `warga` 
ADD COLUMN `alamat` VARCHAR(255) NULL AFTER `nama_kk`,
ADD COLUMN `no_telp` VARCHAR(20) NULL AFTER `alamat`,
ADD COLUMN `id_penerima` INT UNIQUE NULL AFTER `no_telp`,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- TAHAP 2: Create warga_uploads
CREATE TABLE IF NOT EXISTS `warga_uploads` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `jumlah_baris` INT NOT NULL,
  `mode` ENUM('append', 'replace') DEFAULT 'append',
  `admin_id` INT,
  `status` ENUM('pending', 'success', 'failed') DEFAULT 'pending',
  `error_message` TEXT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `processed_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

---

### **LANGKAH 2: Verify Database**

Buka phpMyAdmin dan cek:

1. **Table `warga`:**
   - Buka database `kurbanqu` → table `warga`
   - Cek struktur (tab "Structure")
   - Seharusnya ada kolom baru: `alamat`, `no_telp`, `id_penerima`, `created_at`, `updated_at`
   
2. **Table `warga_uploads`:**
   - Buka database `kurbanqu`
   - Cek ada table `warga_uploads`
   - Lihat struktur (tab "Structure")

---

### **LANGKAH 3: Test Integration**

1. **Buka Admin Dashboard:** `http://localhost/KurbanQu/admin`
2. **Menu:** Penerima Kurban → Upload Excel/CSV
3. **Upload file Excel/CSV** dengan data:
   ```
   No KK,Nama Kepala Keluarga,Alamat,No Telp
   3273011234567890,Ahmad Hidayat,Kp. Cikaret,0812345678
   3273012345678901,Siti Rahmawati,Jl. Tanjung,0813345678
   ```
4. **Klik:** "✓ Aktifkan sebagai Penerima"
5. **Lihat response** - seharusnya:
   ```json
   {
     "success": true,
     "message": "✅ 2 penerima baru, 0 diperbarui",
     "data": {
       "created": 2,
       "updated": 0,
       "failed": 0,
       "total": 2,
       "errors": []
     }
   }
   ```

---

### **LANGKAH 4: Verify Data di phpMyAdmin**

1. **Buka phpMyAdmin**
2. **Database:** kurbanqu → table: warga
3. **Lihat data yang baru:**
   ```
   no_kk | nama_kk | alamat | no_telp | QR_id_qr | id_penerima | created_at | updated_at
   3273011234567890 | Ahmad | Kp. Cikaret | 0812345678 | P00001 | 1 | 2026-06-05... | 2026-06-05...
   ```
4. **Check table:** warga_uploads
   ```
   id | filename | jumlah_baris | mode | status | uploaded_at | processed_at
   1  | web_upload_... | 2 | append | success | 2026-06-05... | 2026-06-05...
   ```

---

## 📊 CHECKLIST SEBELUM LANJUT

- [ ] Database migration berhasil (TAHAP 1 & 2)
- [ ] Table `warga` punya kolom baru
- [ ] Table `warga_uploads` sudah ada
- [ ] Upload test Excel berhasil
- [ ] Data muncul di phpMyAdmin (warga table)
- [ ] Log muncul di phpMyAdmin (warga_uploads table)

---

## ❓ JIKA ADA ERROR

### Error 1: "Table already exists"
**Solusi:** Table `warga_uploads` sudah ada. Lanjut ke LANGKAH 2.

### Error 2: "Column already exists"
**Solusi:** Kolom sudah ada. Lanjut ke LANGKAH 2.

### Error 3: Upload gagal / response error
**Solusi:** 
1. Check browser console (F12)
2. Check error di phpMyAdmin table `warga_uploads` (column `error_message`)
3. Share error message ke saya

### Error 4: Data tidak tersimpan ke database
**Solusi:**
1. Restore dari backup: `C:\backup\kurbanqu_backup_2026-06-05.sql`
2. Run migration lagi
3. Report error ke saya

---

## 🎯 HASIL AKHIR YANG DIHARAPKAN

Setelah semua step:
- ✅ Data Excel bisa diupload dari admin dashboard
- ✅ Data otomatis tersimpan ke database `warga` dengan kolom lengkap (alamat, no_telp)
- ✅ Setiap upload dicatat di table `warga_uploads` (audit trail)
- ✅ Admin bisa lihat data di phpMyAdmin
- ✅ Data persistent & tidak hilang setelah refresh

---

## 📌 NEXT STEPS

**Setelah semua langkah dijalankan:**
1. Kasih tau saya hasilnya (sukses atau ada error)
2. Saya bantu troubleshoot jika ada masalah
3. Setelah sukses, clean up file `migrations_execute.php` (opsional)

---

**Siap mulai LANGKAH 1?** 🚀

Pilih:
- **Opsi A:** Buka browser → `http://localhost/KurbanQu/migrations_execute.php`
- **Opsi B:** Manual di phpMyAdmin copy-paste SQL

