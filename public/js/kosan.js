// Kosan Website JavaScript
// Global variables - Sekarang menggunakan data dari blade template
let roomsData = [];
const facilities = [
    'Kamar mandi luar',
    'WiFi gratis',
    'Kasur dan lemari',
    'Listrik dan air',
    'Dapur bersama',
    'Parkir motor'
];

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const roomModal = document.getElementById('roomModal');
    const modalBody = document.getElementById('modalBody');
    const closeModal = document.querySelector('.close');
    const bookingModal = document.getElementById('bookingModal');
    const bookingForm = document.getElementById('bookingForm');
    const closeBookingModal = document.querySelector('.close-booking');
    const selectedRoomNumberInput = document.getElementById('selectedRoomNumber');
    
    // WhatsApp number - Format: country code + number tanpa + dan spasi
    // Contoh: 6281223288620 (untuk +62 812-2328-8620)
    const whatsappNumber = '6281223288620';

    // Define close modal function at function scope
    const closeModalFunc = function() {
        roomModal.classList.remove('show');
        document.body.style.overflow = 'auto';
    };

    // Event listeners for room modal
    closeModal.addEventListener('click', closeModalFunc);

    window.addEventListener('click', function(event) {
        if (event.target === roomModal) {
            closeModalFunc();
        }
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Format price to Indonesian Rupiah
    window.formatPrice = function(price) {
        return new Intl.NumberFormat('id-ID').format(price);
    };

    // Open modal with room details
    window.openModal = function(room) {
        const statusClass = room.status === 'tersedia' ? 'status-available' : 'status-occupied';
        const statusText = room.status === 'tersedia' ? 'Tersedia' : 'Terisi';

        modalBody.innerHTML = `
            <h2>Detail Kamar ${room.number}</h2>
            <p><strong>Status:</strong> <span class="room-status ${statusClass}">${statusText}</span></p>
            <p><strong>Harga:</strong> Rp ${formatPrice(room.harga)} per bulan</p>
            <p><strong>Ukuran:</strong> 3x4 meter</p>
            <h3>Fasilitas:</h3>
            <ul class="modal-features">
                ${facilities.map(facility => `<li>✓ ${facility}</li>`).join('')}
            </ul>
            <p><strong>Deskripsi:</strong> Kamar nyaman dengan kualitas terjamin, dilengkapi dengan semua fasilitas yang dibutuhkan untuk kenyamanan Anda. Semua kamar memiliki standar kualitas yang sama.</p>
            ${room.status === 'tersedia' ? `
                <button class="modal-btn" onclick="handleBooking('${room.number}')">Pesan Sekarang</button>
            ` : `
                <button class="modal-btn" style="background-color: #9ca3af; cursor: not-allowed;" disabled>Kamar Tidak Tersedia</button>
            `}
        `;

        roomModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    // Handle booking - show booking form
    window.handleBooking = function(roomNumber) {
        closeModalFunc();
        selectedRoomNumberInput.value = roomNumber;
        openBookingModal();
    };

    // Open booking form modal
    function openBookingModal() {
        bookingModal.classList.add('show');
        document.body.style.overflow = 'hidden';
        // Reset form
        bookingForm.reset();
        clearErrors();
    }

    // Close booking modal
    function closeBookingModalFunc() {
        bookingModal.classList.remove('show');
        document.body.style.overflow = 'auto';
        bookingForm.reset();
        clearErrors();
    }

    // Clear error messages
    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        document.querySelectorAll('.form-group input').forEach(el => {
            el.classList.remove('error');
        });
    }

    // Show error message
    function showError(fieldId, message) {
        const errorElement = document.getElementById(fieldId + 'Error');
        const inputElement = document.getElementById(fieldId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
        if (inputElement) {
            inputElement.classList.add('error');
        }
    }

    // Validate form
    function validateForm(formData) {
        let isValid = true;
        clearErrors();

        // Validate full name
        if (!formData.fullName || formData.fullName.trim().length < 3) {
            showError('fullName', 'Nama lengkap minimal 3 karakter');
            isValid = false;
        }

        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!formData.email || !emailRegex.test(formData.email)) {
            showError('email', 'Format email tidak valid');
            isValid = false;
        }

        // Validate phone
        const phoneRegex = /^[0-9]{10,13}$/;
        const cleanPhone = formData.phone.replace(/\D/g, '');
        if (!cleanPhone || !phoneRegex.test(cleanPhone)) {
            showError('phone', 'Nomor telepon harus 10-13 digit angka');
            isValid = false;
        }

        return isValid;
    }

    // Handle booking form submit - simple form submission
    bookingForm.addEventListener('submit', function(e) {
        const formData = {
            fullName: document.getElementById('fullName').value.trim(),
            email: document.getElementById('email').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            roomNumber: selectedRoomNumberInput.value
        };

        // Validate before submit
        if (!validateForm(formData)) {
            e.preventDefault();
            return false;
        }

        // Disable submit button to prevent double submission
        const submitBtn = bookingForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        // Set form action and method for normal form submission
        const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
        bookingForm.action = basePath + '/bookings/create';
        bookingForm.method = 'POST';
        
        // Let form submit normally - controller will handle redirect to WhatsApp
        // Form will proceed with default submission
    });
    // Event listeners for booking modal
    closeBookingModal.addEventListener('click', closeBookingModalFunc);

    window.addEventListener('click', function(event) {
        if (event.target === bookingModal) {
            closeBookingModalFunc();
        }
    });

    // Remove error styling on input
    document.addEventListener('input', function(e) {
        if (e.target.matches('#bookingForm input')) {
            e.target.classList.remove('error');
            const errorId = e.target.id + 'Error';
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
        }
    });
});
