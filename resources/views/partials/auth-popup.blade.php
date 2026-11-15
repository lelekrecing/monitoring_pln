@if (session('auth_popup'))
    @php
        $popup = session('auth_popup');
        $isLogout = ($popup['type'] ?? 'login') === 'logout';
    @endphp
    <style>
        .auth-popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 16px;
            opacity: 0;
            animation: authPopupFadeIn 0.25s ease forwards;
        }
        .auth-popup-card {
            width: 100%;
            max-width: 390px;
            background: #ffffff;
            color: #1e293b;
            border-radius: 16px;
            padding: 28px 24px 22px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
            text-align: center;
            position: relative;
            overflow: hidden;
            transform: scale(0.9) translateY(10px);
            animation: authPopupSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .auth-popup-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: {{ $isLogout ? 'rgba(14, 165, 233, 0.12)' : 'rgba(16, 185, 129, 0.12)' }};
            color: {{ $isLogout ? '#0284c7' : '#10b981' }};
        }
        .auth-popup-icon svg {
            width: 32px;
            height: 32px;
        }
        .auth-popup-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px;
        }
        .auth-popup-message {
            font-size: 14px;
            line-height: 1.5;
            color: #475569;
            margin: 0 0 22px;
        }
        .auth-popup-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            padding: 10px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            color: #ffffff;
            background: {{ $isLogout ? '#0B5EA8' : '#10b981' }};
            transition: transform 0.15s ease, opacity 0.15s ease;
        }
        .auth-popup-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }
        .auth-popup-progress {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 4px;
            width: 100%;
            background: {{ $isLogout ? '#0B5EA8' : '#10b981' }};
            transform-origin: left;
            animation: authPopupTimer 3.5s linear forwards;
        }
        @keyframes authPopupFadeIn {
            to { opacity: 1; }
        }
        @keyframes authPopupSlideUp {
            to { transform: scale(1) translateY(0); }
        }
        @keyframes authPopupTimer {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }
    </style>

    <div class="auth-popup-overlay" id="authPopupOverlay" role="dialog" aria-modal="true" aria-labelledby="authPopupTitle">
        <div class="auth-popup-card">
            <div class="auth-popup-icon">
                @if ($isLogout)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                @endif
            </div>
            <h3 class="auth-popup-title" id="authPopupTitle">{{ $popup['title'] ?? 'Berhasil' }}</h3>
            <p class="auth-popup-message">{{ $popup['message'] ?? '' }}</p>
            <button type="button" class="auth-popup-btn" id="authPopupCloseBtn">Oke</button>
            <div class="auth-popup-progress"></div>
        </div>
    </div>

    <script>
        (function () {
            const overlay = document.getElementById('authPopupOverlay');
            const closeBtn = document.getElementById('authPopupCloseBtn');
            if (!overlay) return;

            function closeAuthPopup() {
                if (!overlay.parentNode) return;
                overlay.style.transition = 'opacity 0.2s ease';
                overlay.style.opacity = '0';
                setTimeout(function () {
                    if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
                }, 200);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeAuthPopup);
            }
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) closeAuthPopup();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeAuthPopup();
            });

            setTimeout(closeAuthPopup, 3500);
        })();
    </script>
@endif
