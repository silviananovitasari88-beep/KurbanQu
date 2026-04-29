// Minimal admin.js - hanya untuk UI interactions
// Database logic sudah dipindahkan ke Controllers

// Handle modal toggle
function openAddRoomModal() {
    document.getElementById('roomForm').action = "{{ route('rooms.store') }}";
    document.getElementById('roomForm').reset();
    document.getElementById('roomModalTitle').textContent = 'Tambah Kamar Baru';
    document.getElementById('roomModal').classList.remove('hidden');
}

function closeRoomModal() {
    document.getElementById('roomModal').classList.add('hidden');
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

// Confirm delete
function confirmDelete(type, id) {
    const typeName = type === 'room' ? 'Kamar' : 'Booking';
    if (confirm(`Apakah Anda yakin ingin menghapus ${typeName} ini?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('Error: CSRF token tidak ditemukan. Silakan refresh halaman.');
                console.error('CSRF token not found');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            // Build action URL
            const action = type === 'room' ? `/admin/rooms/${id}` : `/admin/bookings/${id}`;
            form.action = action;
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.content;
            form.appendChild(csrfInput);
            
            // Add method override for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Add to body and submit
            document.body.appendChild(form);
            console.log(`Menghapus ${typeName} dengan ID ${id} ke ${action}`);
            form.submit();
        } catch (error) {
            alert('Error: Gagal menghapus. Silakan cek console untuk detail error.');
            console.error('Delete error:', error);
        }
    }
}

// Section navigation
document.querySelectorAll('.admin-sidebar a').forEach(link => {
    link.addEventListener('click', function(e) {
        // Let links work naturally for server-side routing
        document.querySelectorAll('.admin-sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

console.log('Admin dashboard loaded - Database connected');
