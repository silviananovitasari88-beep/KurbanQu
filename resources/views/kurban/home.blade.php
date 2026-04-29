<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KurbanQu</title>
    <link rel="stylesheet" href="{{ asset('css/kosan.css') }}">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1>KurbanQu</h1>
            </div>
            <nav class="nav">
                <a href="#home">Beranda</a>
                <a href="#rooms">Kamar</a>
                <a href="#about">Tentang</a>
                <a href="#contact">Kontak</a>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="home" class="hero">
            <div class="hero-image">
                <img src="{{ asset('images/auliakost.jpeg') }}" alt="Aulia Kost">
                <div class="hero-overlay"></div>
            </div>
            <div class="hero-content">
                <div class="container">
                    <h1 class="hero-title">Selamat Datang di Aulia Kost</h1>
                    <p class="hero-subtitle">Kost Sarijadi Kota Bandung yang nyaman dan aman</p>
                    <a href="#rooms" class="hero-btn">Lihat Kamar Tersedia</a>
                </div>
            </div>
        </section>

        <!-- Rooms Section -->
        <section id="rooms" class="rooms-section">
            <div class="container">
                <h2 class="section-title">Daftar Kamar Tersedia</h2>
                <div class="rooms-grid" id="roomsGrid">
                    @if($rooms->count() > 0)
                        @foreach($rooms as $room)
                            <div class="room-card" data-room-id="{{ $room->id }}">
                                <div class="room-image"></div>
                                <div class="room-info">
                                    <div class="room-number">Kamar {{ $room->number }}</div>
                                    <span class="room-status {{ $room->status === 'tersedia' ? 'status-available' : 'status-occupied' }}">
                                        {{ $room->status === 'tersedia' ? 'Tersedia' : 'Terisi' }}
                                    </span>
                                    <div class="room-price">Rp {{ number_format($room->harga, 0, ',', '.') }}/bulan</div>
                                    <ul class="room-features">
                                        <li>✓ Kamar mandi luar</li>
                                        <li>✓ WiFi gratis</li>
                                        <li>✓ Kasur dan lemari</li>
                                    </ul>
                                    <button class="room-btn" onclick="openModal({{ json_encode(['id' => $room->id, 'number' => $room->number, 'price' => $room->harga, 'status' => $room->status, 'tenant' => $room->penyewa]) }})">Lihat Detail</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p style="text-align: center; grid-column: 1 / -1; padding: 40px 20px; color: #666;">Saat ini tidak ada kamar tersedia. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                    @endif
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about-section">
            <div class="container">
                <h2 class="section-title">Tentang Aulia Kost</h2>
                <div class="about-content">
                    <p>Kosan Aulia menyediakan kost dan kamar dengan kualitas yang sama, dilengkapi dengan fasilitas lengkap untuk kenyamanan penghuni. Setiap kamar memiliki standar kualitas tinggi dengan harga terjangkau.</p>
                    
                    <!-- Location Info -->
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

                    <div class="facilities">
                        <h3>Fasilitas:</h3>
                        <ul>
                            <li>✓ Kamar mandi luar</li>
                            <li>✓ WiFi gratis</li>
                            <li>✓ Kasur dan lemari</li>
                            <li>✓ Listrik dan air</li>
                            <li>✓ Dapur bersama</li>
                            <li>✓ Parkir motor</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact-section">
            <div class="container">
                <h2 class="section-title">Hubungi Kami</h2>
                <div class="contact-content">
                    <div class="contact-info">
                        <p><strong>Alamat:</strong> Aulia Kost No. 105A, Jl. Sarimanah No.105a, Sarijadi, Sukasari, Bandung City, West Java 40151</p>
                        <p><strong>Telepon:</strong> +62 812-2328-8620</p>
                        <p><strong>Email:</strong> aulia.kost@gmail.com</p>
                        <p><strong>WhatsApp:</strong> +62 812-2328-8620</p>
                        <div class="map-btn-container">
                            <a href="https://www.google.com/maps/dir//Aulia+Kost+No.+105A,+Jl.+Sarimanah+No.105a,+Sarijadi,+Sukasari,+Bandung+City,+West+Java+40151/@-6.8774452,107.5848213,14z/data=!3m1!4b1!4m8!4m7!1m0!1m5!1m1!1s0x2e68e7e33ac35099:0x9fc518b1747c9a2b!2m2!1d107.5781565!2d-6.8762275?entry=ttu&g_ep=EgoyMDI2MDEyMS4wIKXMDSoASAFQAw%3D%3D" 
                            target="_blank" class="map-btn">📍 Lihat di Google Maps</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Room Modal -->
    <div id="roomModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modalBody">
                <!-- Modal content will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Booking Form Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close-booking">&times;</span>
            <div id="bookingFormBody">
                <h2>Form Pemesanan Kamar</h2>
                <form id="bookingForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="fullName">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="fullName" name="fullName" required placeholder="Masukkan nama lengkap">
                        <span class="error-message" id="fullNameError"></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="contoh@email.com">
                        <span class="error-message" id="emailError"></span>
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor Telepon <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required placeholder="08xxxxxxxxxx">
                        <span class="error-message" id="phoneError"></span>
                    </div>
                    <input type="hidden" id="selectedRoomNumber" name="roomNumber">
                    <button type="submit" class="modal-btn">Kirim ke WhatsApp</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Aulia Kost. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('js/kosan.js') }}"></script>
    <script>
    // Shortcut untuk akses admin: Ctrl + Shift + A
    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.shiftKey && (event.key === 'A' || event.key === 'a')) {
            event.preventDefault();
            window.location.href = "{{ route('admin.login') }}";
        }
    });
</script>
</body>
</html>
