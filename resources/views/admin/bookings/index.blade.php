<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan Kos - Admin</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn-wa {
            background-color: #25D366;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }
        .btn-wa:hover { background-color: #128C7E; }
        .status { font-weight: bold; text-transform: uppercase; color: #e67e22; }
    </style>
</head>
<body>

    <h1>Daftar Pesanan Kos (Admin)</h1>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Penyewa</th>
                <th>Nomor HP</th>
                <th>Kos</th>
                <th>Total Harga</th>
                <th>Deadline Bayar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->user->name }}</td>
                <td>{{ $booking->user->no_hp ?? 'No HP Kosong' }}</td>
                <td>{{ $booking->kos->nama_kost }}</td>
                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                <td>{{ $booking->payment_deadline }}</td>
                <td class="status">{{ $booking->status }}</td>
                <td>
                    @if($booking->user->no_hp)
                        @php
                            // Membuat pesan otomatis
                            $pesan = "Halo " . $booking->user->name . ", mengingatkan pembayaran kos " . $booking->kos->nama_kost . " jatuh tempo pada " . $booking->payment_deadline . ". Mohon segera diselesaikan ya. Terima kasih!";
                            
                            // Link WhatsApp link generator
                            $linkWA = "https://wa.me/" . preg_replace('/[^0-9]/', '', $booking->user->no_hp) . "?text=" . urlencode($pesan);
                        @endphp
                        
                        <a href="{{ $linkWA }}" target="_blank" class="btn-wa">
                            📱 Kirim WA
                        </a>
                    @else
                        <span style="color: red; font-size: 12px;">No HP Belum Diisi</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>