<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Kosan Aulia</title>
    <link rel="stylesheet" href="{{ asset('css/kosan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .hidden { display: none; }
        .section-active { display: block !important; }
        .admin-sidebar a.active { background: var(--primary-light); }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1>Kosan Aulia - Admin Panel</h1>
            </div>
            <nav class="nav">
                <a href="{{ route('home') }}">Kembali ke Website</a>
            </nav>
        </div>
    </header>

    @if(session('success'))
    <div class="popup show">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="popup error show">{{ session('error') }}</div>
    @endif

    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h2>Menu Admin</h2>
            <ul>
                <li><a href="{{ route('admin.dashboard', ['section' => 'dashboard']) }}" class="{{ $section == 'dashboard' ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('admin.dashboard', ['section' => 'manage-rooms']) }}" class="menu-h1 {{ $section == 'manage-rooms' ? 'active' : '' }}">Kelola Kamar</a></li>
                <li><a href="{{ route('admin.dashboard', ['section' => 'manage-bookings']) }}" class="menu-h1 {{ $section == 'manage-bookings' ? 'active' : '' }}">Kelola Booking</a></li>
                <li><a href="{{ route('admin.dashboard', ['section' => 'settings']) }}" class="menu-h1 {{ $section == 'settings' ? 'active' : '' }}">Pengaturan</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <form action="{{ route('admin.logout') }}" method="POST" style="display: inline; float: right;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>

            <div class="admin-content">
                {{-- DASHBOARD SECTION --}}
                <section id="dashboard" class="admin-card {{ $section == 'dashboard' ? 'section-active' : 'hidden' }}">
                    <h1>Selamat Datang di Dashboard Admin</h1>
                    <p>Ringkasan data kosan.</p>
                    <div class="stat-cards">
                        <div class="stat-card">
                            <div class="stat-icon">🏠</div>
                            <div class="stat-number">{{ $totalRooms }}</div>
                            <div class="stat-label">Total Kamar</div>
                        </div>
                        <div class="stat-card available">
                            <div class="stat-icon">✅</div>
                            <div class="stat-number">{{ $availableRooms }}</div>
                            <div class="stat-label">Kamar Tersedia</div>
                        </div>
                        <div class="stat-card occupied">
                            <div class="stat-icon">❌</div>
                            <div class="stat-number">{{ $occupiedRooms }}</div>
                            <div class="stat-label">Kamar Ditempati</div>
                        </div>
                    </div>
                    <section id="about" class="about-section">
            <div class="container">
                <h2 class="section-title">Tentang Aulia Kost</h2>
                <div class="about-content">
                    <p>Kosan Aulia menyediakan kost dan kamar dengan kualitas yang sama, dilengkapi dengan fasilitas lengkap untuk kenyamanan penghuni. Setiap kamar memiliki standar kualitas tinggi dengan harga terjangkau.</p>
                    <div class="location-info">
                        <h3>Lokasi Strategis</h3>
                        <p style="margin-bottom: 1.5rem; color: var(--text-light);">Kosan Aulia berada di lokasi yang strategis dan mudah dijangkau dari berbagai kampus terkemuka di Bandung:</p>
                        <div class="distance-list">
                            <div class="distance-item">
                                <div class="distance-icon">🎓</div>
                                <div class="distance-content">
                                    <h4>Universitas Logistik dan Bisnis</h4>
                                    <p class="distance-text">Jarak: <strong>± 2.5 km</strong> (5-10 menit dengan kendaraan)</p>
                                </div>
                            </div>
                            <div class="distance-item">
                                <div class="distance-icon">🏛️</div>
                                <div class="distance-content">
                                    <h4>Politeknik Negeri Bandung</h4>
                                    <p class="distance-text">Jarak: <strong>± 3.0 km</strong> (7-12 menit dengan kendaraan)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <center><h1>Berdiri Sejak 1990</h1></center>

                {{-- MANAGE ROOMS SECTION --}}
                <section id="manage-rooms" class="admin-card {{ $section == 'manage-rooms' ? 'section-active' : 'hidden' }}">
                    <h1>Kelola Kamar</h1>
                    <p>Kelola data kamar, status, dan harga kamar kosan.</p>
                    <div class="rooms-toolbar">
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('roomModal').classList.remove('hidden')">+ Tambah Kamar Baru</button>
                    </div>
                    <table class="room-table">
                        <thead>
                            <tr>
                                <th>No. Kamar</th>
                                <th>Harga (Rp)</th>
                                <th>Status</th>
                                <th>Penyewa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                            <tr>
                                <td>{{ $room->number }}</td>
                                <td>Rp {{ number_format($room->harga, 0, ',', '.') }}</td>
                            <td>
                                <span class="status-badge {{ $room->status == 'tersedia' ? 'status-tersedia' : 'status-ditempati' }}">
                                    {{ $room->status == 'tersedia' ? 'Tersedia' : 'Ditempati' }}
                                </span>
                            </td>
                            <td>{{ $room->penyewa ?? '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-primary btn-small" onclick="editRoom({{ $room->id }}, '{{ $room->number }}', {{ $room->harga }}, '{{ $room->status }}', '{{ $room->penyewa ?? '' }}')">Edit</button>
                                    <button type="button" class="btn btn-danger btn-small" onclick="confirmDelete('room', {{ $room->id }})">Hapus</button>
                                </div>
                            </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px;">Belum ada data kamar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>

                {{-- MANAGE BOOKINGS SECTION --}}
                <section id="manage-bookings" class="admin-card {{ $section == 'manage-bookings' ? 'section-active' : 'hidden' }}">
                    <h1>Kelola Booking</h1>
                    <p>Lihat dan kelola semua booking kamar beserta status pembayaran dan approval.</p>
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Nama Penyewa</th>
                                <th>Kamar</th>
                                <th>Tanggal Daftar</th>
                                <th>Status Approval</th>
                                <th>Status Pembayaran</th>
                                <th>Tenggat Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr class="deadline-{{ $booking->deadline_status }}">
                                <td>{{ $booking->user->name ?? $booking->name ?? 'N/A' }}</td>
                                <td>{{ $booking->kos->number ?? 'N/A' }}</td>
                                <td>{{ $booking->registration_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($booking->approval_status === 'menunggu')
                                        <span class="approval-badge approval-pending">⏳ Menunggu</span>
                                    @elseif($booking->approval_status === 'disetujui')
                                        <span class="approval-badge approval-approved">✅ Disetujui</span>
                                    @elseif($booking->approval_status === 'ditolak')
                                        <span class="approval-badge approval-rejected">❌ Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $booking->payment_status == 'paid' ? 'status-paid' : 'status-unpaid' }}">
                                        {{ $booking->payment_status == 'paid' ? '✅ Sudah Bayar' : '❌ Belum Bayar' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="deadline-badge deadline-{{ $booking->deadline_status }}">
                                        {{ $booking->payment_deadline->format('d/m/Y') }}<br>
                                        <small>{{ $booking->deadline_badge }}</small>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-primary btn-small" onclick="editBooking({{ $booking->id }}, '{{ $booking->user->name ?? $booking->name ?? 'N/A' }}', '{{ $booking->kos->number ?? 'N/A' }}', '{{ $booking->registration_date->format('Y-m-d') }}', '{{ $booking->approval_status }}', '{{ $booking->payment_status }}', '{{ $booking->payment_deadline->format('Y-m-d') }}', '{{ $booking->notes ?? '' }}')">Edit</button>
                                        <button type="button" class="btn btn-danger btn-small" onclick="confirmDelete('booking', {{ $booking->id }})">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">Belum ada data booking</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>

                {{-- SETTINGS SECTION --}}
                <section id="settings" class="admin-card {{ $section == 'settings' ? 'section-active' : 'hidden' }}">
                    <h1>Pengaturan</h1>
                    <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
                        @csrf
                        <div class="form-group">
                            <label for="oldPassword">Password Lama (diperlukan untuk mengubah):</label>
                            <input type="password" id="oldPassword" name="old_password" required>
                        </div>
                        <div class="form-group">
                            <label for="adminUsername">Username Admin Baru:</label>
                            <input type="text" id="adminUsername" name="username" value="admin">
                        </div>
                        <div class="form-group">
                            <label for="adminPassword">Password Admin Baru:</label>
                            <input type="password" id="adminPassword" name="password">
                        </div>
                        <button type="submit" class="btn">Simpan Pengaturan</button>
                    </form>
                </section>
            </div>
        </main>
    </div>

    {{-- MODAL EDIT/TAMBAH KAMAR --}}
    <div id="roomModal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="roomModalTitle">Tambah Kamar Baru</h2>
                <button type="button" class="modal-close" onclick="document.getElementById('roomModal').classList.add('hidden')">&times;</button>
            </div>
            <form id="roomForm" class="modal-form" action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <input type="hidden" id="roomId" name="room_id">
                <div class="form-group">
                    <label for="roomNumber">Nomor Kamar:</label>
                    <input type="text" id="roomNumber" name="number" required>
                </div>
                <div class="form-group">
                    <label for="roomPrice">Harga (Rp):</label>
                    <input type="number" id="roomPrice" name="harga" required min="0">
                </div>
                <div class="form-group">
                    <label for="roomStatus">Status:</label>
                    <select id="roomStatus" name="status" required>
                        <option value="tersedia">Tersedia</option>
                        <option value="ditempati">Ditempati</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="roomTenant">Nama Penyewa (jika ditempati):</label>
                    <input type="text" id="roomTenant" name="penyewa">
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('roomModal').classList.add('hidden')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT BOOKING --}}
    <div id="bookingModal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Booking</h2>
                <button type="button" class="modal-close" onclick="document.getElementById('bookingModal').classList.add('hidden')">&times;</button>
            </div>
            <form id="bookingForm" class="modal-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="bookingId" name="booking_id">
                <input type="hidden" id="bookingKosId" name="kos_id">
                <input type="hidden" id="bookingUserId" name="user_id">
                <div class="form-group">
                    <label for="bookingName">Nama Penyewa:</label>
                    <input type="text" id="bookingName" name="name" required disabled>
                </div>
                <div class="form-group">
                    <label for="bookingRoom">Kamar:</label>
                    <input type="text" id="bookingRoom" name="room_number" required disabled>
                </div>
                <div class="form-group">
                    <label for="bookingRegDate">Tanggal Daftar:</label>
                    <input type="date" id="bookingRegDate" name="registration_date" required disabled>
                </div>
                <div class="form-group">
                    <label for="bookingApprovalStatus">Status Pendaftaran:</label>
                    <select id="bookingApprovalStatus" name="approval_status" required>
                        <option value="menunggu">Menunggu Disetujui</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bookingPaymentStatus">Status Pembayaran:</label>
                    <select id="bookingPaymentStatus" name="payment_status" required>
                        <option value="unpaid">Belum Bayar</option>
                        <option value="paid">Sudah Bayar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bookingPaymentDue">Tenggat Pembayaran:</label>
                    <input type="date" id="bookingPaymentDue" name="payment_deadline" required>
                </div>
                <div class="form-group">
                    <label for="bookingNotes">Catatan:</label>
                    <textarea id="bookingNotes" name="notes" rows="3"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('bookingModal').classList.add('hidden')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>

    <script>
        // Simple JavaScript for modal interactions (minimal, no complex logic)
        function editRoom(id, number, harga, status, penyewa) {
            document.getElementById('roomModalTitle').textContent = 'Edit Kamar';
            document.getElementById('roomId').value = id;
            document.getElementById('roomNumber').value = number;
            document.getElementById('roomPrice').value = harga;
            document.getElementById('roomStatus').value = status;
            document.getElementById('roomTenant').value = penyewa || '';
            
            // Change form action to update route
            const form = document.getElementById('roomForm');
            form.action = '{{ url("admin/rooms") }}/' + id;
            
            // Add method spoofing for PUT
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
            
            document.getElementById('roomModal').classList.remove('hidden');
        }

        function editBooking(id, name, roomNumber, regDate, approvalStatus, paymentStatus, paymentDue, notes) {
            document.getElementById('bookingId').value = id;
            document.getElementById('bookingName').value = name;
            document.getElementById('bookingRoom').value = roomNumber;
            document.getElementById('bookingRegDate').value = regDate;
            document.getElementById('bookingApprovalStatus').value = approvalStatus;
            document.getElementById('bookingPaymentStatus').value = paymentStatus;
            document.getElementById('bookingPaymentDue').value = paymentDue;
            document.getElementById('bookingNotes').value = notes || '';
            
            // Change form action to update route
            const form = document.getElementById('bookingForm');
            form.action = '{{ url("admin/bookings") }}/' + id;
            
            // Add method spoofing for PUT
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
            
            document.getElementById('bookingModal').classList.remove('hidden');
        }

        // Reset form when opening add modal
        document.querySelectorAll('.rooms-toolbar .btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (e.target.textContent.includes('Tambah Kamar Baru') || e.target.textContent.includes('Tambah')) {
                    document.getElementById('roomModalTitle').textContent = 'Tambah Kamar Baru';
                    document.getElementById('roomForm').reset();
                    document.getElementById('roomId').value = '';
                    document.getElementById('roomForm').action = '{{ route("rooms.store") }}';
                    
                    // Remove method spoofing for POST
                    const form = document.getElementById('roomForm');
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) {
                        methodInput.remove();
                    }
                }
            });
        });

        // Auto-hide popup messages
        setTimeout(function() {
            document.querySelectorAll('.popup').forEach(function(popup) {
                if (popup.classList.contains('show')) {
                    popup.classList.remove('show');
                }
            });
        }, 3000);
    </script>
</body>
</html>
