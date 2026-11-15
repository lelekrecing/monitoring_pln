<style>
    .login-loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(13, 33, 55, 0.68);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .login-loading-overlay.active {
        display: flex;
        opacity: 1;
    }
    .login-loading-card {
        width: 100%;
        max-width: 370px;
        background: #ffffff;
        border-radius: 18px;
        padding: 32px 28px 28px;
        text-align: center;
        box-shadow: 0 24px 48px rgba(10, 25, 47, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-family: 'Segoe UI', Arial, sans-serif;
        transform: scale(0.92);
        animation: loginCardPop 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes loginCardPop {
        to { transform: scale(1); }
    }
    .login-spinner-wrap {
        position: relative;
        width: 72px;
        height: 72px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-spinner-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3.5px solid #e2e8f0;
        border-top-color: #0d2137;
        border-right-color: #22a7ea;
        animation: loginSpin 0.85s linear infinite;
    }
    .login-spinner-ring-inner {
        position: absolute;
        inset: 9px;
        border-radius: 50%;
        border: 2.5px solid transparent;
        border-bottom-color: #22a7ea;
        animation: loginSpinReverse 1.1s linear infinite;
    }
    .login-spinner-core {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0d2137, #1d4ed8);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(34, 167, 234, 0.35);
        animation: loginPulse 1.4s ease-in-out infinite;
    }
    .login-spinner-core svg {
        width: 17px;
        height: 17px;
    }
    @keyframes loginSpin {
        to { transform: rotate(360deg); }
    }
    @keyframes loginSpinReverse {
        to { transform: rotate(-360deg); }
    }
    @keyframes loginPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }
    .login-loading-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2px;
    }
    .login-loading-dots span {
        display: inline-block;
        animation: loginDotBounce 1.4s infinite ease-in-out both;
    }
    .login-loading-dots span:nth-child(1) { animation-delay: 0s; }
    .login-loading-dots span:nth-child(2) { animation-delay: 0.2s; }
    .login-loading-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes loginDotBounce {
        0%, 80%, 100% { opacity: 0.25; transform: translateY(0); }
        40% { opacity: 1; transform: translateY(-2px); }
    }
    .login-loading-sub {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 18px;
        line-height: 1.5;
    }
    .login-loading-bar-track {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        position: relative;
    }
    .login-loading-bar-fill {
        position: absolute;
        top: 0;
        left: -40%;
        width: 45%;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #0d2137, #22a7ea, #0d2137);
        animation: loginBarSlide 1.2s ease-in-out infinite;
    }
    @keyframes loginBarSlide {
        0% { left: -45%; }
        100% { left: 105%; }
    }
    .btn-login.is-loading {
        opacity: 0.85;
        cursor: wait;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .btn-login-spinner {
        width: 18px;
        height: 18px;
        border: 2.5px solid rgba(255, 255, 255, 0.35);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: loginSpin 0.75s linear infinite;
        flex-shrink: 0;
    }
</style>

<div class="login-loading-overlay" id="loginLoadingOverlay" role="status" aria-live="polite" aria-label="Sedang memproses data login">
    <div class="login-loading-card">
        <div class="login-spinner-wrap">
            <div class="login-spinner-ring"></div>
            <div class="login-spinner-ring-inner"></div>
            <div class="login-spinner-core">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>
        </div>
        <h3 class="login-loading-title">
            Memproses Data
            <span class="login-loading-dots"><span>.</span><span>.</span><span>.</span></span>
        </h3>
        <p class="login-loading-sub">Sedang memverifikasi akun dan menyiapkan ruang kerja Anda.</p>
        <div class="login-loading-bar-track">
            <div class="login-loading-bar-fill"></div>
        </div>
    </div>
</div>

<script>
    (function () {
        const forms = document.querySelectorAll('.login-box form');
        const overlay = document.getElementById('loginLoadingOverlay');
        if (!forms.length || !overlay) return;

        function resetLoadingState() {
            overlay.classList.remove('active');
            forms.forEach(function (form) {
                form.dataset.submitting = 'false';
                const btn = form.querySelector('.btn-login');
                if (btn && btn.dataset.originalHtml) {
                    btn.disabled = false;
                    btn.classList.remove('is-loading');
                    btn.innerHTML = btn.dataset.originalHtml;
                }
            });
        }

        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) return;
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }
                form.dataset.submitting = 'true';

                const btn = form.querySelector('.btn-login');
                if (btn) {
                    if (!btn.dataset.originalHtml) {
                        btn.dataset.originalHtml = btn.innerHTML;
                    }
                    btn.disabled = true;
                    btn.classList.add('is-loading');
                    btn.innerHTML = '<span class="btn-login-spinner"></span><span>Memproses...</span>';
                }

                overlay.classList.add('active');
            });
        });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                resetLoadingState();
            }
        });
    })();
</script>
