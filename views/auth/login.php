<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="login-container">
    <div class="login-wrapper">
        <!-- Decoration Circles -->
        <div class="login-decoration login-decoration-1"></div>
        <div class="login-decoration login-decoration-2"></div>
        <div class="login-decoration login-decoration-3"></div>
        
        <!-- Login Card -->
        <div class="login-card">
            <!-- Logo Section -->
            <div class="login-header">
                <div class="login-logo">
                    <div class="login-logo-icon">🎮</div>
                    <h1 class="login-logo-text">CS2 Sorteios</h1>
                </div>
                <p class="login-subtitle">Faça login para participar dos sorteios</p>
            </div>
            
            <!-- Flash Messages -->
            <div class="login-messages">
                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div class="login-alert login-alert-success">
                        <div class="login-alert-icon">✓</div>
                        <div class="login-alert-text">
                            <?= $_SESSION['flash_success'] ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['flash_error'])): ?>
                    <div class="login-alert login-alert-error">
                        <div class="login-alert-icon">✕</div>
                        <div class="login-alert-text">
                            <?= $_SESSION['flash_error'] ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>
            </div>
            
            <!-- Login Button -->
            <div class="login-body">
                <a href="<?= BASE_URL ?>/auth/callback" class="login-btn-google">
                    <span class="login-btn-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.7091 15.7682 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                            <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2 4.52727 11.8H1.12727V14.3909C2.68182 17.4909 6.20455 20 10.2 20Z" fill="#34A853"/>
                            <path d="M4.52727 11.8C4.30909 11.2 4.18182 10.55 4.18182 9.88636C4.18182 9.22273 4.30909 8.57273 4.52727 7.97273V5.38182H1.12727C0.409091 6.81818 0 8.40909 0 10C0 11.5909 0.409091 13.1818 1.12727 14.6182L4.52727 11.8Z" fill="#FBBC04"/>
                            <path d="M10.2 3.97727C11.3636 3.97727 12.3909 4.35909 13.1909 5.11818L16.1091 2.2C14.1727 0.4 11.9 0 10.2 0C6.20455 0 2.68182 2.50909 1.12727 5.60909L4.52727 8.2C5.37273 5.8 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
                        </svg>
                    </span>
                    <span class="login-btn-text">Entrar com Google</span>
                </a>
                
                <!-- Development Info -->
                <?php if (defined('APP_DEBUG') && APP_DEBUG && defined('GOOGLE_MOCK_MODE') && GOOGLE_MOCK_MODE): ?>
                    <div class="login-dev-info">
                        <div class="login-dev-title">🔧 Modo Desenvolvimento</div>
                        <p class="login-dev-text">Login simulado ativo. Escolha um usuário:</p>
                        
                        <div class="login-dev-users">
                            <a href="<?= BASE_URL ?>/auth/callback?user=0" class="login-dev-user">
                                <div class="login-dev-user-icon">👤</div>
                                <div class="login-dev-user-info">
                                    <div class="login-dev-user-name">Usuário Teste</div>
                                    <div class="login-dev-user-role">user</div>
                                </div>
                            </a>
                            
                            <a href="<?= BASE_URL ?>/auth/callback?user=1" class="login-dev-user">
                                <div class="login-dev-user-icon">👑</div>
                                <div class="login-dev-user-info">
                                    <div class="login-dev-user-name">Admin Teste</div>
                                    <div class="login-dev-user-role">admin</div>
                                </div>
                            </a>
                            
                            <a href="<?= BASE_URL ?>/auth/callback?user=2" class="login-dev-user">
                                <div class="login-dev-user-icon">👑</div>
                                <div class="login-dev-user-info">
                                    <div class="login-dev-user-name">Administrador</div>
                                    <div class="login-dev-user-role">admin</div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer -->
            <div class="login-footer">
                <p class="login-footer-text">
                    Ao fazer login, você concorda com nossos termos de uso
                </p>
                <a href="<?= BASE_URL ?>/privacy" class="login-footer-link">
                    Política de Privacidade
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* ========== LOGIN PAGE STYLES ========== */

.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    position: relative;
    overflow: hidden;
}

/* Decorative Elements */
.login-decoration {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
    pointer-events: none;
}

.login-decoration-1 {
    width: 600px;
    height: 600px;
    background: linear-gradient(135deg, #ff1647, #cc1138);
    top: -300px;
    right: -200px;
    animation: float 20s ease-in-out infinite;
}

.login-decoration-2 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    bottom: -200px;
    left: -100px;
    animation: float 25s ease-in-out infinite reverse;
}

.login-decoration-3 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: pulse 15s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(10deg); }
}

@keyframes pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.1; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.15; }
}

/* Login Wrapper */
.login-wrapper {
    width: 100%;
    max-width: 480px;
    position: relative;
    z-index: 1;
}

/* Login Card */
.login-card {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border-radius: 24px;
    padding: 48px 40px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(255, 255, 255, 0.05);
    position: relative;
    overflow: hidden;
}

.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ff1647, transparent);
}

/* Header */
.login-header {
    text-align: center;
    margin-bottom: 40px;
}

.login-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
}

.login-logo-icon {
    font-size: 4rem;
    filter: drop-shadow(0 0 30px rgba(255, 22, 71, 0.4));
    animation: float 3s ease-in-out infinite;
}

.login-logo-text {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ff1647, #cc1138);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
    letter-spacing: -1px;
}

.login-subtitle {
    color: #8b93a7;
    font-size: 1.1rem;
    margin: 0;
}

/* Messages */
.login-messages {
    margin-bottom: 32px;
}

.login-alert {
    padding: 16px;
    border-radius: 12px;
    display: flex;
    align-items: start;
    gap: 12px;
    margin-bottom: 12px;
}

.login-alert:last-child {
    margin-bottom: 0;
}

.login-alert-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 700;
    flex-shrink: 0;
}

.login-alert-text {
    flex: 1;
    font-size: 0.95rem;
    font-weight: 500;
}

.login-alert-success {
    background: rgba(34, 197, 94, 0.1);
    border-left: 4px solid #22c55e;
}

.login-alert-success .login-alert-icon {
    background: #22c55e;
    color: #ffffff;
}

.login-alert-success .login-alert-text {
    color: #4ade80;
}

.login-alert-error {
    background: rgba(239, 68, 68, 0.1);
    border-left: 4px solid #ef4444;
}

.login-alert-error .login-alert-icon {
    background: #ef4444;
    color: #ffffff;
}

.login-alert-error .login-alert-text {
    color: #f87171;
}

/* Body */
.login-body {
    margin-bottom: 32px;
}

/* Google Button */
.login-btn-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 16px 32px;
    background: #ffffff;
    color: #1f2937;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

.login-btn-google::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
    transition: left 0.5s;
}

.login-btn-google:hover::before {
    left: 100%;
}

.login-btn-google:hover {
    background: #f9fafb;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    color: #1f2937;
}

.login-btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-btn-text {
    font-weight: 600;
}

/* Development Info */
.login-dev-info {
    margin-top: 32px;
    padding: 24px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
}

.login-dev-title {
    color: #60a5fa;
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 12px;
}

.login-dev-text {
    color: #8b93a7;
    font-size: 0.9rem;
    margin-bottom: 16px;
}

.login-dev-users {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.login-dev-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.login-dev-user:hover {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.4);
    transform: translateX(4px);
}

.login-dev-user-icon {
    font-size: 1.5rem;
}

.login-dev-user-info {
    flex: 1;
}

.login-dev-user-name {
    color: #ffffff;
    font-weight: 600;
    font-size: 0.95rem;
}

.login-dev-user-role {
    color: #60a5fa;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Footer */
.login-footer {
    text-align: center;
    padding-top: 32px;
    border-top: 1px solid rgba(255, 22, 71, 0.15);
}

.login-footer-text {
    color: #8b93a7;
    font-size: 0.9rem;
    margin: 0 0 8px 0;
}

.login-footer-link {
    color: #ff1647;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.2s ease;
}

.login-footer-link:hover {
    color: #ff2d5c;
}

/* Responsive */
@media (max-width: 768px) {
    .login-container {
        padding: 20px;
    }
    
    .login-card {
        padding: 32px 24px;
        border-radius: 20px;
    }
    
    .login-logo-icon {
        font-size: 3rem;
    }
    
    .login-logo-text {
        font-size: 2rem;
    }
    
    .login-subtitle {
        font-size: 1rem;
    }
    
    .login-btn-google {
        padding: 14px 24px;
        font-size: 1rem;
    }
    
    .login-decoration-1 {
        width: 400px;
        height: 400px;
    }
    
    .login-decoration-2 {
        width: 300px;
        height: 300px;
    }
    
    .login-decoration-3 {
        display: none;
    }
}

@media (max-width: 480px) {
    .login-card {
        padding: 28px 20px;
    }
    
    .login-logo-icon {
        font-size: 2.5rem;
    }
    
    .login-logo-text {
        font-size: 1.75rem;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>