@extends('layouts.auth')

@section('title', 'Verifikasi Kode OTP')

@section('content')
    <h2 class="auth-title">Masukkan kode OTP</h2>
    <p style="text-align: center; margin-bottom: 1.5rem; color: #555;">
        Masukkan kode 6 digit yang telah dikirim ke email Anda.
    </p>

    <!-- Tampilkan pesan error jika ada -->
    @if ($errors->any())
        <div class="error-message">
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.otp.verify') }}">
        @csrf
        
        <!-- 6 KOTAK INPUT -->
        <div class="form-group" style="display: flex; justify-content: space-around; gap: 5px;">
            <input type="text" name="otp_1" class="otp-input" maxlength="1" required>
            <input type="text" name="otp_2" class="otp-input" maxlength="1" required>
            <input type="text" name="otp_3" class="otp-input" maxlength="1" required>
            <input type="text" name="otp_4" class="otp-input" maxlength="1" required>
            <input type="text" name="otp_5" class="otp-input" maxlength="1" required>
            <!-- ▼▼▼ PERBAIKAN TYPO DI SINI ▼▼▼ -->
            <input type="text" name="otp_6" class="otp-input" maxlength="1" required>
            <!-- ▲▲▲ ----------------------- ▲▲▲ -->
        </div>
        <!-- Input tersembunyi untuk menggabungkan OTP -->
        <input type="hidden" name="otp" id="otp-full">
        
        <!-- Timer dan Kirim Ulang -->
        <div style="text-align: center; margin-top: 1.5rem; color: #555;">
            <span id="timer-text">Kirim ulang kode OTP dalam </span>
            <span id="countdown" style="font-weight: bold;">60 detik</span> 
            
            <a href="{{ route('password.resend') }}" id="resend-link" class="disabled" style="display: none; font-weight: bold;">
                Kirim ulang kode OTP
            </a>
        </div>
        
        <!-- Tombol Kirim -->
        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" class="btn-primary">
                KIRIM
            </button>
        </div>
    </form>
@endsection

@push('styles')
<style>
    /* Style untuk 6 kotak OTP */
    .otp-input {
        width: 45px;
        height: 55px;
        font-size: 1.5rem;
        text-align: center;
        border: 2px solid #BDBDBD;
        border-radius: 10px;
    }
    .otp-input:focus {
        outline: none;
        border-color: #B91C1C;
    }
    /* Style untuk link kirim ulang */
    a.disabled {
        color: #999;
        pointer-events: none;
        text-decoration: none;
    }
    /* ▼▼▼ PERBAIKAN CSS WARNA LINK ▼▼▼ */
    /* Warna ungu/biru seperti link 'Daftar' & 'Lupa kata sandi?' */
    #resend-link:not(.disabled) {
        color: #581C87; 
        text-decoration: none;
        font-weight: 600; /* Dibuat sama seperti link lain */
    }
    #resend-link:not(.disabled):hover {
        text-decoration: underline;
    }
    /* ▲▲▲ ----------------------- ▲▲▲ */
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.otp-input');
        const form = inputs[0].closest('form');
        const otpFullInput = document.getElementById('otp-full');

        // Logika Auto-Tab
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        // Gabungkan OTP sebelum form disubmit
        form.addEventListener('submit', (e) => {
            let otpValue = '';
            inputs.forEach(input => {
                otpValue += input.value;
            });
            otpFullInput.value = otpValue;
            
            if (otpValue.length !== 6) { // Cek 6 digit
                e.preventDefault(); 
                alert('OTP harus 6 digit.');
            }
        });

        // Timer
        const countdownEl = document.getElementById('countdown');
        const timerTextEl = document.getElementById('timer-text');
        const resendLinkEl = document.getElementById('resend-link');
        let timeLeft = 60; // 60 detik

        const timer = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownEl.style.display = 'none';
                timerTextEl.style.display = 'none';
                resendLinkEl.style.display = 'inline';
                resendLinkEl.classList.remove('disabled');
            } else {
                timeLeft--;
                countdownEl.textContent = timeLeft;
            }
        }, 1000);
    });
</script>
@endpush