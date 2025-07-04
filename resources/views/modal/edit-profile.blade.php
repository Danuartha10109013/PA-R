<form id="profileForm" method="POST" action="{{ route('profile.update') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name </label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', auth()->user()->name) }}" required placeholder="Nama lengkap Anda">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted">Contoh: John Doe</small>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Alamat Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', auth()->user()->email) }}" required placeholder="alamat@email.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="phone_number" class="form-label">No WhatsApp</label>
        <div class="input-group">
            <span class="input-group-text">+62</span>
            <input type="tel" name="phone_number" id="phone_number"
                class="form-control @error('phone_number') is-invalid @enderror" placeholder="8123456789"
                pattern="^8[1-9][0-9]{7,10}$" value="{{ old('phone_number', substr(auth()->user()->phone_number, 1)) }}"
                required>
            @error('phone_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted">Contoh: 8123456789 (tanpa 0 dan +62)</small>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password Baru</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter"
                minlength="8">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye-slash"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted">Harus mengandung huruf besar, kecil, angka, dan simbol</small>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmation New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="Ketik ulang password">
            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                <i class="bi bi-eye-slash"></i>
            </button>
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle me-2"></i>Done
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Close 
        </a>
    </div>
</form>

@push('scripts')
    <script>
        // Password toggle functionality
        function setupPasswordToggle(inputId, toggleId) {
            const toggleBtn = document.getElementById(toggleId);
            const input = document.getElementById(inputId);

            if (toggleBtn && input) {
                toggleBtn.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('bi-eye');
                    this.querySelector('i').classList.toggle('bi-eye-slash');
                });
            }
        }

        // Initialize toggles
        setupPasswordToggle('password', 'togglePassword');
        setupPasswordToggle('password_confirmation', 'toggleConfirmPassword');

        // Phone number validation
        const phoneInput = document.getElementById('phone_number');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('0')) {
                    value = value.substring(1);
                }
                if (value.length > 12) {
                    value = value.substring(0, 12);
                }
                e.target.value = value;

                // Real-time validation feedback
                const isValid = /^8[1-9][0-9]{7,10}$/.test(value);
                if (value.length > 0) {
                    e.target.classList.toggle('is-valid', isValid);
                    e.target.classList.toggle('is-invalid', !isValid);
                } else {
                    e.target.classList.remove('is-valid', 'is-invalid');
                }
            });
        }

        // Real-time password strength indicator
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function(e) {
                const value = e.target.value;
                const strength = {
                    length: value.length >= 8,
                    upper: /[A-Z]/.test(value),
                    lower: /[a-z]/.test(value),
                    number: /[0-9]/.test(value),
                    special: /[^A-Za-z0-9]/.test(value)
                };

                // Update UI based on strength
                const strengthMeter = document.getElementById('password-strength');
                if (strengthMeter) {
                    const strengthLevel = Object.values(strength).filter(Boolean).length;
                    strengthMeter.style.width = `${strengthLevel * 25}%`;
                    strengthMeter.classList.remove('bg-danger', 'bg-warning', 'bg-success');
                    strengthMeter.classList.add(
                        strengthLevel < 2 ? 'bg-danger' :
                        strengthLevel < 4 ? 'bg-warning' : 'bg-success'
                    );
                }
            });
        }

        // Enhanced form validation
        (function() {
            'use strict';
            const form = document.getElementById('profileForm');

            if (form) {
                // Real-time validation for all inputs
                Array.from(form.elements).forEach(element => {
                    if (element.tagName === 'INPUT') {
                        element.addEventListener('input', function() {
                            this.classList.remove('is-invalid');
                            this.classList.remove('is-valid');

                            if (this.checkValidity()) {
                                this.classList.add('is-valid');
                            } else if (this.value.length > 0) {
                                this.classList.add('is-invalid');
                            }
                        });
                    }
                });

                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();

                        // Focus on first invalid field
                        const firstInvalid = form.querySelector('.is-invalid');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            firstInvalid.focus();
                        }
                    }

                    form.classList.add('was-validated');
                }, false);
            }
        })();
    </script>
@endpush
