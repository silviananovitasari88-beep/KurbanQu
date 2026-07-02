<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->resetTables();
            $this->seedUsers();
            $this->seedTrackingSteps();
            $this->seedWargaRows();
            $this->seedDistribusiRows();
            $this->seedHewanRows();
            $this->seedMudhohiRows();
        });
    }

    private function resetTables(): void
    {
        foreach (['tracking', 'mudhohi', 'hewan', 'distribusi', 'warga', 'qr', 'tracking_steps', 'users'] as $table) {
            if (DB::table($table)->exists()) {
                DB::table($table)->delete();
            }
        }
    }

    private function seedUsers(): void
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'admin1'],
            [
                'id' => 1,
                'name' => 'admin1',
                'email' => null,
                'password' => bcrypt('asdw1234'),
                'role' => 'admin',
                'remember_token' => null,
                'created_at' => '2026-06-30 05:41:32',
                'updated_at' => '2026-06-30 05:41:32',
            ]
        );
    }

    private function seedTrackingSteps(): void
    {
        $rows = [
            [
                'id' => 1,
                'urutan' => 1,
                'label' => 'Penyembelihan',
                'status' => 'pending',
                'time' => null,
            ],
            [
                'id' => 2,
                'urutan' => 2,
                'label' => 'Pengulitan',
                'status' => 'pending',
                'time' => null,
            ],
        ];

        foreach ($rows as $row) {
            DB::table('tracking_steps')->updateOrInsert(
                ['urutan' => $row['urutan']],
                $row
            );
        }
    }

    private function seedWargaRows(): void
    {
        $rows = [
            ['no_kk' => '3273011234567890', 'nama_kk' => 'Ahmad Hidayat', 'alamat' => 'Kp. Cikaret RT 05 RW 08', 'no_telp' => '081234567890', 'id_penerima' => 1, 'QR_id_qr' => null],
            ['no_kk' => '3273012345678901', 'nama_kk' => 'Siti Rahmawati', 'alamat' => 'Jl. Tanjung Nomor 12', 'no_telp' => '081345678901', 'id_penerima' => 2, 'QR_id_qr' => null],
            ['no_kk' => '3273013456789012', 'nama_kk' => 'Rudi Santoso', 'alamat' => 'Jl. Merdeka Blok B No 45', 'no_telp' => '081456789012', 'id_penerima' => 3, 'QR_id_qr' => null],
            ['no_kk' => '3273014567890123', 'nama_kk' => 'Nur Azizah', 'alamat' => 'Jl. Gatot Subroto Kompleks Perumahan', 'no_telp' => '081567890123', 'id_penerima' => 4, 'QR_id_qr' => null],
            ['no_kk' => '3273015678901234', 'nama_kk' => 'Budi Hermawan', 'alamat' => 'Kp. Pasir Gintung RT 02 RW 03', 'no_telp' => '081678901234', 'id_penerima' => 5, 'QR_id_qr' => null],
            ['no_kk' => '3273016789012345', 'nama_kk' => 'Siti Nurhaliza', 'alamat' => 'Jl. Sudirman RT 06 RW 09', 'no_telp' => '081789012345', 'id_penerima' => 6, 'QR_id_qr' => null],
            ['no_kk' => '3273017890123456', 'nama_kk' => 'Haji Suryanto', 'alamat' => 'Jl. Diponegoro Nomor 88', 'no_telp' => '081890123456', 'id_penerima' => 7, 'QR_id_qr' => null],
            ['no_kk' => '3273018901234567', 'nama_kk' => 'Dewi Puspita', 'alamat' => 'Kp. Sawah Baru RT 04 RW 07', 'no_telp' => '081901234567', 'id_penerima' => 8, 'QR_id_qr' => null],
            ['no_kk' => '3273019012345678', 'nama_kk' => 'Karyo Wijaya', 'alamat' => 'Jl. Ahmad Yani Nomor 25', 'no_telp' => '082012345678', 'id_penerima' => 9, 'QR_id_qr' => null],
            ['no_kk' => '3273010123456789', 'nama_kk' => 'Evy Sumarni', 'alamat' => 'Jl. Gunung Salak RT 03 RW 05', 'no_telp' => '082123456789', 'id_penerima' => 10, 'QR_id_qr' => null],
        ];

        foreach ($rows as $row) {
            DB::table('warga')->updateOrInsert(
                ['no_kk' => $row['no_kk']],
                $row
            );
        }
    }

    private function seedDistribusiRows(): void
    {
        $rows = [
            ['id_stok' => 68, 'warga_no_kk' => '3273011234567890', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 69, 'warga_no_kk' => '3273012345678901', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 70, 'warga_no_kk' => '3273013456789012', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 71, 'warga_no_kk' => '3273014567890123', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 72, 'warga_no_kk' => '3273015678901234', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 73, 'warga_no_kk' => '3273016789012345', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 74, 'warga_no_kk' => '3273017890123456', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 75, 'warga_no_kk' => '3273018901234567', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 76, 'warga_no_kk' => '3273019012345678', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
            ['id_stok' => 77, 'warga_no_kk' => '3273010123456789', 'QR_id_qr' => null, 'dowload_qr' => null, 'st_pengambilan' => 'pending', 'mtd_pengambilan' => null, 'login' => 'belum_login', 'status_login' => 'Belum Login'],
        ];

        foreach ($rows as $row) {
            DB::table('distribusi')->updateOrInsert(
                ['id_stok' => $row['id_stok']],
                $row
            );
        }
    }

    private function seedHewanRows(): void
    {
        $rows = [
            ['id_hewan' => 17, 'jenis' => 'Sapi', 'sehat' => 'Sehat', 'cacat' => 'Tidak Cacat', 'umur' => '5 tahun', 'berat' => '40 kg', 'st_syariat' => 1, 'admin_id_admin' => 1, 'tracking_id_tracking' => null],
            ['id_hewan' => 18, 'jenis' => 'Sapi', 'sehat' => 'Sehat', 'cacat' => 'Tidak Cacat', 'umur' => '2 tahun', 'berat' => '20 kg', 'st_syariat' => 1, 'admin_id_admin' => 1, 'tracking_id_tracking' => null],
            ['id_hewan' => 19, 'jenis' => 'Domba', 'sehat' => 'Sehat', 'cacat' => 'Tidak Cacat', 'umur' => '2 tahun', 'berat' => '20 kg', 'st_syariat' => 1, 'admin_id_admin' => 1, 'tracking_id_tracking' => null],
            ['id_hewan' => 20, 'jenis' => 'Kambing', 'sehat' => 'Sehat', 'cacat' => 'Tidak Cacat', 'umur' => '1 tahun', 'berat' => '15 kg', 'st_syariat' => 1, 'admin_id_admin' => 1, 'tracking_id_tracking' => null],
        ];

        foreach ($rows as $row) {
            DB::table('hewan')->updateOrInsert(
                ['id_hewan' => $row['id_hewan']],
                $row
            );
        }
    }

    private function seedMudhohiRows(): void
    {
        $rows = [
            ['id_mudhohi' => 1, 'nama_mudhohi' => 'Test', 'nama_ayah' => 'Ayah', 'alamat' => 'Alamat', 'notelp_mudhohi' => '8888', 'req_bagian' => '1/2 sapi', 'admin_id_admin' => 1, 'hewan_id_hewan' => 17],
            ['id_mudhohi' => 2, 'nama_mudhohi' => 'Test', 'nama_ayah' => 'Ayah', 'alamat' => 'Alamat', 'notelp_mudhohi' => '8888', 'req_bagian' => '1/2 sapi', 'admin_id_admin' => 1, 'hewan_id_hewan' => 18],
            ['id_mudhohi' => 3, 'nama_mudhohi' => 'Silviana Novita sari', 'nama_ayah' => 'Park Seo Joon', 'alamat' => '01/11 Desa. Karyalaksana', 'notelp_mudhohi' => '088975536160', 'req_bagian' => 'kurban penuh', 'admin_id_admin' => 1, 'hewan_id_hewan' => 19],
            ['id_mudhohi' => 4, 'nama_mudhohi' => 'bunga', 'nama_ayah' => 'yaghqbsjk', 'alamat' => '9/10 sarijadi', 'notelp_mudhohi' => '088975514628', 'req_bagian' => 'kurban penuh', 'admin_id_admin' => 1, 'hewan_id_hewan' => 20],
        ];

        foreach ($rows as $row) {
            DB::table('mudhohi')->updateOrInsert(
                ['id_mudhohi' => $row['id_mudhohi']],
                $row
            );
        }
    }
}