<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#4f46e5">
    <title>Presensi Pegawai BVS</title>
    <meta name="description" content="Sistem Presensi Pegawai BVS">
    <meta name="keywords" content="presensi, pegawai, bvs, login" />
    <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏢</text></svg>" sizes="32x32">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #3730a3;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --background-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --input-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        /* Loader */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(10px);
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        #loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border: 0.3em solid rgba(79, 70, 229, 0.2);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Main Container */
        #appCapsule {
            width: 100%;
            max-width: 420px;
            z-index: 1;
        }

        .login-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            padding: 40px 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s ease-out 0.3s forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-image {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: var(--input-shadow);
            transform: scale(0.8);
            opacity: 0;
            animation: logoAppear 0.6s ease-out 0.6s forwards;
        }

        @keyframes logoAppear {
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .form-image::before {
            content: '🏢';
            font-size: 40px;
            color: white;
        }

        /* Title Section */
        .title-section h1 {
            color: var(--primary-dark);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .title-section h4 {
            color: #6b7280;
            font-size: 16px;
            font-weight: 400;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background: white;
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        /* Input Icons */
        .input-wrapper::before {
            content: '';
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: var(--primary-color);
            mask-size: contain;
            mask-repeat: no-repeat;
            mask-position: center;
            z-index: 2;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .input-wrapper:has(input[name="nama_lengkap"])::before {
            mask-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>');
        }

        .input-wrapper:has(input[name="password"])::before {
            mask-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>');
        }

        .form-control:focus + .input-wrapper::before {
            opacity: 1;
        }

        /* Clear Input Button */
        .clear-input {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 20px;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-control:not(:placeholder-shown) ~ .clear-input {
            opacity: 1;
        }

        .clear-input:hover {
            color: var(--danger-color);
            transform: translateY(-50%) scale(1.1);
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 10px;
            font-size: 14px;
            border: none;
            animation: alertSlide 0.3s ease-out;
        }

        @keyframes alertSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-outline-warning {
            background: rgba(217, 119, 6, 0.1);
            color: var(--warning-color);
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        /* Login Button */
        .form-button-group {
            margin-top: 30px;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #10b981);
            color: white;
            box-shadow: var(--input-shadow);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.4);
        }

        .btn-success:active {
            transform: translateY(0);
        }

        /* Button Loading State */
        .btn-success:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-success:hover::before {
            left: 100%;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .login-form {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .title-section h1 {
                font-size: 24px;
            }
            
            .form-control {
                padding: 14px 18px 14px 45px;
                font-size: 16px;
            }
        }

        /* Micro Animations */
        .form-group {
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Focus Indicators */
        .form-control:focus {
            animation: focusPulse 0.3s ease-out;
        }

        @keyframes focusPulse {
            0% { transform: translateY(-2px) scale(1); }
            50% { transform: translateY(-2px) scale(1.02); }
            100% { transform: translateY(-2px) scale(1); }
        }
    </style>
</head>

<body>
    <!-- Loader -->
    <div id="loader">
        <div class="spinner-border" role="status"></div>
    </div>

    <!-- App Capsule -->
    <div id="appCapsule">
        <div class="login-form">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="form-image animate-logo"></div>
            </div>
            
            <!-- Title Section -->
            <div class="title-section">
                <h1>Presensi BVS</h1>
                <h4>Silahkan Login Terlebih Dahulu</h4>
            </div>
            
            <!-- Form Section -->
            <form action="/proseslogin" method="POST" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" name="nama_lengkap" class="form-control" id="nama_lengkap" 
                               placeholder="Username" required autocomplete="username">
                        <i class="clear-input" onclick="clearInput('nama_lengkap')">×</i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" id="password1" name="password" 
                               placeholder="Password" required autocomplete="current-password">
                        <i class="clear-input" onclick="clearInput('password1')">×</i>
                    </div>
                    
                    <!-- Warning Alert -->
                    @php
                        $messagewarning = Session::get('warning');
                    @endphp
                    @if (Session::get('warning'))
                        <div class="alert alert-outline-warning">
                            {{ $messagewarning }}
                        </div>
                    @endif
                </div>

                <div class="form-button-group">
                    <button type="submit" class="btn btn-success" id="loginBtn">
                        <span id="btnText">Login</span>
                        <span id="btnSpinner" style="display: none;">
                            <div class="spinner-border" style="width: 20px; height: 20px;"></div>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // DOM Content Loaded
        document.addEventListener("DOMContentLoaded", function() {
            // Hide loader after page load
            setTimeout(() => {
                document.getElementById('loader').classList.add('hidden');
            }, 800);

            // Logo animation
            const logo = document.querySelector(".animate-logo");
            if (logo) {
                setTimeout(() => {
                    logo.style.transform = 'scale(1)';
                    logo.style.opacity = '1';
                }, 600);
            }

            // Form validation and submission
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            form.addEventListener('submit', function(e) {
                // Get form values
                const username = document.getElementById('nama_lengkap').value.trim();
                const password = document.getElementById('password1').value.trim();
                
                // Basic validation - only prevent if fields are empty
                if (!username || !password) {
                    e.preventDefault();
                    showAlert('Silahkan isi username dan password!');
                    return;
                }

                // Show loading state when submitting
                loginBtn.disabled = true;
                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                
                // Let the form submit normally to Laravel
                // No preventDefault() here so it submits to /proseslogin
            });

            // Auto-hide alerts
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert.style.display !== 'none') {
                        alert.style.display = 'none';
                    }
                });
            }, 5000);
        });

        // Clear input function
        function clearInput(inputId) {
            const input = document.getElementById(inputId);
            input.value = '';
            input.focus();
        }

        // Show alert function
        function showAlert(message) {
            const alert = document.getElementById('warningAlert');
            alert.textContent = message;
            alert.style.display = 'block';
            
            // Auto-hide after 4 seconds
            setTimeout(() => {
                alert.style.display = 'none';
            }, 4000);
        }

        // Add input event listeners for real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            
            inputs.forEach(input => {
                // Show/hide clear button
                input.addEventListener('input', function() {
                    const clearBtn = this.parentNode.querySelector('.clear-input');
                    if (this.value.length > 0) {
                        clearBtn.style.opacity = '1';
                    } else {
                        clearBtn.style.opacity = '0';
                    }
                });

                // Enhanced focus effects
                input.addEventListener('focus', function() {
                    this.parentNode.style.transform = 'translateY(-1px)';
                });

                input.addEventListener('blur', function() {
                    this.parentNode.style.transform = 'translateY(0)';
                });
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape key clears all inputs
            if (e.key === 'Escape') {
                document.querySelectorAll('.form-control').forEach(input => {
                    input.value = '';
                });
            }
        });
    </script>
</body>

</html>