document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const storedUsername = localStorage.getItem('admin_username') || 'admin';
    const storedPassword = localStorage.getItem('admin_password') || 'admin';

    if (username === storedUsername && password === storedPassword) {
        localStorage.setItem('admin_logged_in', 'true');
        window.location.href = 'admin.html';
    } else {
        document.getElementById('error').textContent = 'Username atau password salah';
        document.getElementById('error').style.display = 'block';
    }
});

// ======================== FORGOT PASSWORD LOGIC ========================

let forgotEmail = '';
let verificationCode = '';
let recoveryType = 'username';

function openForgotModal() {
    document.getElementById('forgotModal').classList.remove('hidden');
    resetForgotForm();
}

function closeForgotModal() {
    document.getElementById('forgotModal').classList.add('hidden');
    resetForgotForm();
}

function resetForgotForm() {
    showForgotStep(1);
    document.getElementById('forgotEmailForm').reset();
    document.getElementById('forgotCodeForm').reset();
    document.getElementById('forgotRecoveryForm').reset();
    forgotEmail = '';
    verificationCode = '';
}

function showForgotStep(step) {
    document.getElementById('forgotStep1').classList.toggle('active', step === 1);
    document.getElementById('forgotStep2').classList.toggle('active', step === 2);
    document.getElementById('forgotStep3').classList.toggle('active', step === 3);
}

function goBackForgotStep1() {
    showForgotStep(1);
}

// Step 1: Email input
document.getElementById('forgotEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    forgotEmail = document.getElementById('forgotEmail').value;
    
    // Simulate email verification (dalam aplikasi sebenarnya, email akan dikirim)
    verificationCode = Math.floor(100000 + Math.random() * 900000).toString();
    
    // Store verifikasi code di session storage untuk demonstrasi (tidak digunakan dalam aplikasi production)
    sessionStorage.setItem('verificationCode', verificationCode);
    sessionStorage.setItem('verificationEmail', forgotEmail);
    
    // Tampilkan pesan bahwa email telah dikirim
    document.getElementById('emailConfirmation').textContent = 'Kode telah dikirim ke: ' + forgotEmail;
    document.getElementById('emailSentModal').classList.remove('hidden');
});

function closeEmailSentModal() {
    document.getElementById('emailSentModal').classList.add('hidden');
}

function proceedToVerification() {
    closeEmailSentModal();
    showForgotStep(2);
}

// Step 2: Code verification
document.getElementById('forgotCodeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const inputCode = document.getElementById('verificationCode').value;
    const storedCode = sessionStorage.getItem('verificationCode');
    
    if (inputCode === storedCode) {
        showForgotStep(3);
    } else {
        alert('Kode verifikasi salah! Silakan coba lagi.');
        document.getElementById('verificationCode').value = '';
    }
});

// Step 3: Recovery options
document.getElementById('forgotRecoveryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    recoveryType = document.querySelector('input[name="recoveryType"]:checked').value;
    
    if (recoveryType === 'username') {
        const storedUsername = localStorage.getItem('admin_username') || 'admin';
        document.getElementById('recoveredUsername').textContent = storedUsername;
        document.getElementById('recoveryResultBox').style.display = 'block';
        document.getElementById('newPasswordField').style.display = 'none';
        document.getElementById('recoverySubmitBtn').textContent = 'Tutup';
    } else {
        document.getElementById('recoveryResultBox').style.display = 'none';
        document.getElementById('newPasswordField').style.display = 'block';
        document.getElementById('recoverySubmitBtn').textContent = 'Reset Password';
    }
});

// Handle radio button changes
document.querySelectorAll('input[name="recoveryType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'username') {
            document.getElementById('recoveryResultBox').style.display = 'none';
            document.getElementById('newPasswordField').style.display = 'none';
        } else {
            document.getElementById('recoveryResultBox').style.display = 'none';
            document.getElementById('newPasswordField').style.display = 'block';
        }
    });
});

// Password reset submission
document.getElementById('recoverySubmitBtn').addEventListener('click', function() {
    if (recoveryType === 'username') {
        closeForgotModal();
    } else {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (!newPassword || !confirmPassword) {
            alert('Silakan isi semua field password');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            alert('Password tidak cocok!');
            return;
        }
        
        // Update password
        localStorage.setItem('admin_password', newPassword);
        alert('Password berhasil direset! Silakan login dengan password baru Anda.');
        closeForgotModal();
        document.getElementById('username').focus();
    }
});
