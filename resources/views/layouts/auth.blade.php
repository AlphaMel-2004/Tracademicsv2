<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TracAdemics') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/tracademics-logo.png') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: white;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .auth-left {
            background: url('/images/login_bg-image.jpg') center center/cover no-repeat;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: #fff;
            position: relative;
            min-height: 100vh;
            z-index: 1;
        }
        
        .auth-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(34, 49, 63, 0.55); /* dark overlay for contrast */
            z-index: 2;
        }
        
        .auth-left > * {
            position: relative;
            z-index: 3;
        }
        
        .auth-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            background: white;
        }
        
        .brand-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            font-size: 2.1rem;
            font-weight: bold;
            text-align: center;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.25), 0 1px 0 #222;
        }
        
        .brand-logo img {
            background: rgba(255,255,255,0.85);
            color: #222;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin-bottom: 12px;
            margin-right: 0;
            padding: 8px;
            box-sizing: border-box;
            box-shadow: 0 2px 12px 0 rgba(0,0,0,0.10);
        }
        
        .brand-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .brand-subtitle {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            opacity: 0.95;
        }
        
        .brand-description {
            font-size: 0.95rem;
            opacity: 0.85;
            line-height: 1.5;
        }
        
        .form-container {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 32px 0 rgba(40,167,69,0.10), 0 1.5px 6px 0 rgba(127,255,212,0.10);
            padding: 2.5rem 2rem 2rem 2rem;
            margin-top: 2rem;
        }
        
        .form-header {
            text-align: left;
            margin-bottom: 2rem;
        }
        
        .form-header h2 {
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-header p {
            color: #666;
            margin: 0;
        }
        
        .user-avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #7fffd4 0%, #28a745 100%);
            box-shadow: 0 2px 8px 0 rgba(40,167,69,0.10);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
        }
        
        .user-avatar i {
            color: #fff;
            font-size: 1.7rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #4285f4;
            box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
            outline: none;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-control {
            padding-left: 45px;
        }
        
        .input-group-text {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            background: none;
            border: none;
            z-index: 3;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .form-check-input {
            margin-right: 8px;
        }
        
        .btn-login {
            background: linear-gradient(90deg, #7fffd4 0%, #28a745 100%);
            color: #fff;
            border: none;
            border-radius: 24px;
            padding: 14px 0;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            box-shadow: 0 2px 8px 0 rgba(40,167,69,0.10);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            background: linear-gradient(90deg, #28a745 0%, #7fffd4 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 4px 16px 0 rgba(40,167,69,0.15);
        }
        
        .forgot-link {
            color: #4285f4;
            text-decoration: none;
            font-size: 0.9rem;
            float: right;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .email-notice {
            text-align: center;
            color: #666;
            font-size: 0.85rem;
            margin-top: 1.5rem;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 12px 16px;
        }

        .global-toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1085;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }

        .global-toast {
            min-width: 240px;
            max-width: 320px;
            padding: 0.7rem 0.95rem;
            border-radius: 0.75rem;
            color: #fff;
            font-size: 0.9rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            opacity: 0;
            transform: translateY(-8px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: auto;
        }

        .global-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .global-toast-icon {
            font-size: 1rem;
            line-height: 1;
            margin-top: 0.1rem;
        }

        .global-toast-success { background: linear-gradient(135deg, #198754, #0f5132); }
        .global-toast-error { background: linear-gradient(135deg, #dc3545, #a71d2a); }
        .global-toast-warning { background: linear-gradient(135deg, #ffc107, #fd7e14); color: #212529 !important; }
        .global-toast-info { background: linear-gradient(135deg, #0dcaf0, #0b7285); }
        
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            
            .auth-left {
                padding: 2rem;
                text-align: center;
            }
            
            .auth-right {
                padding: 2rem;
            }
        }
    </style>

    <script>
        (function() {
            const typeClassMap = {
                success: 'global-toast-success',
                error: 'global-toast-error',
                warning: 'global-toast-warning',
                info: 'global-toast-info'
            };

            const typeIconMap = {
                success: 'fas fa-check-circle',
                error: 'fas fa-times-circle',
                warning: 'fas fa-exclamation-circle',
                info: 'fas fa-info-circle'
            };

            function resolveContainer() {
                let container = document.querySelector('.global-toast-container');
                if (!container) {
                    if (!document.body) {
                        return null;
                    }
                    container = document.createElement('div');
                    container.className = 'global-toast-container';
                    document.body.appendChild(container);
                }
                return container;
            }

            window.showToast = function(message, type = 'info', options = {}) {
                if (!message) {
                    return;
                }

                if (!document.body) {
                    document.addEventListener('DOMContentLoaded', function() {
                        window.showToast(message, type, options);
                    }, { once: true });
                    return;
                }

                const duration = typeof options.duration === 'number' ? options.duration : 3000;
                const normalizedType = typeClassMap[type] ? type : 'info';

                const container = resolveContainer();
                if (!container) {
                    return;
                }

                const toastEl = document.createElement('div');
                toastEl.className = `global-toast ${typeClassMap[normalizedType]}`;

                const iconEl = document.createElement('span');
                iconEl.className = `global-toast-icon ${typeIconMap[normalizedType] ?? typeIconMap.info}`;

                const messageEl = document.createElement('div');
                messageEl.className = 'global-toast-message flex-grow-1';
                messageEl.textContent = typeof message === 'string' ? message : String(message);

                toastEl.appendChild(iconEl);
                toastEl.appendChild(messageEl);

                container.appendChild(toastEl);

                requestAnimationFrame(() => {
                    toastEl.classList.add('show');
                });

                const autoHide = setTimeout(() => {
                    toastEl.classList.remove('show');
                    toastEl.addEventListener('transitionend', () => {
                        toastEl.remove();
                    }, { once: true });
                }, duration);

                toastEl.addEventListener('click', () => {
                    clearTimeout(autoHide);
                    toastEl.classList.remove('show');
                    toastEl.addEventListener('transitionend', () => {
                        toastEl.remove();
                    }, { once: true });
                });
            };
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    @php
        $authFlashMessages = [
            'success' => session('success'),
            'status' => session('status'),
            'error' => session('error'),
            'warning' => session('warning'),
            'info' => session('info'),
        ];
    @endphp
    <script type="application/json" id="auth-flash-messages-data">
        {!! json_encode($authFlashMessages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flashDataEl = document.getElementById('auth-flash-messages-data');
            let flashMessages = {};

            if (flashDataEl) {
                try {
                    flashMessages = JSON.parse(flashDataEl.textContent || '{}');
                } catch (error) {
                    flashMessages = {};
                }
            }

            Object.entries(flashMessages).forEach(function([type, message]) {
                if (!message) {
                    return;
                }

                const toastType = type === 'status' ? 'success' : type;
                showToast(message, toastType);
            });
        });
    </script>
</body>
</html>
