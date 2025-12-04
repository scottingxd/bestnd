<?php use App\Helpers\ValidationHelper; use App\Helpers\AuthHelper; ?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/user/home">
            CS2 SORTEIOS
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php if (AuthHelper::isLogged()): 
                    $user = AuthHelper::getUser(); ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/user/home">
                            <span class="nav-link-icon">🎮</span>
                            <span>Sorteios</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/user/my-entries">
                            <span class="nav-link-icon">🎟️</span>
                            <span>Minhas Participações</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/raffle/results">
                            <span class="nav-link-icon">🏆</span>
                            <span>Resultados</span>
                        </a>
                    </li>
                    
                    <?php if ($user['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link nav-link-admin" href="<?= BASE_URL ?>/admin/dashboard">
                            <span class="nav-link-icon">⚙️</span>
                            <span>Admin</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle user-dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= ValidationHelper::escapeHtml($user['avatar_url']) ?>" 
                                 class="user-avatar" 
                                 alt="Avatar">
                            <span class="user-name"><?= ValidationHelper::escapeHtml($user['name']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                            <li class="dropdown-header">
                                <div class="dropdown-user-info">
                                    <div class="dropdown-user-name"><?= ValidationHelper::escapeHtml($user['name']) ?></div>
                                    <div class="dropdown-user-email"><?= ValidationHelper::escapeHtml($user['email']) ?></div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/user/profile">
                                    <span class="dropdown-item-icon">👤</span>
                                    <span>Meu Perfil</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/user/my-entries">
                                    <span class="dropdown-item-icon">📋</span>
                                    <span>Minhas Participações</span>
                                </a>
                            </li>
                            <?php if ($user['role'] === 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item dropdown-item-admin" href="<?= BASE_URL ?>/admin/dashboard">
                                    <span class="dropdown-item-icon">⚙️</span>
                                    <span>Painel Admin</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item dropdown-item-logout" href="<?= BASE_URL ?>/auth/logout">
                                    <span class="dropdown-item-icon">🚪</span>
                                    <span>Sair</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-login" href="<?= BASE_URL ?>/auth/login">
                            Entrar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
/* ========== NAVBAR STYLES ========== */
.navbar {
    background: linear-gradient(180deg, #1a1f28 0%, #131822 100%) !important;
    backdrop-filter: blur(16px) saturate(180%);
    border-bottom: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
    padding: 1rem 0 !important;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-brand {
    font-size: 1.5rem !important;
    font-weight: 800 !important;
    background: linear-gradient(135deg, #ff1647, #cc1138);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 0 12px rgba(255, 22, 71, 0.3));
    transition: all 0.3s ease;
    padding: 8px 0 !important;
}

.navbar-brand:hover {
    filter: drop-shadow(0 0 20px rgba(255, 22, 71, 0.5));
    transform: scale(1.03);
}

/* Nav Links */
.nav-link {
    color: #8b93a7 !important;
    font-weight: 600 !important;
    font-size: 0.95rem !important;
    transition: all 0.3s ease !important;
    position: relative;
    padding: 10px 16px !important;
    margin: 0 4px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-link-icon {
    font-size: 1.1rem;
    opacity: 0.8;
}

.nav-link:hover {
    color: #ffffff !important;
    background: rgba(255, 22, 71, 0.1);
}

.nav-link:hover .nav-link-icon {
    opacity: 1;
    transform: scale(1.1);
}

/* Admin Link Highlight */
.nav-link-admin {
    color: #ff1647 !important;
    background: rgba(255, 22, 71, 0.1);
    font-weight: 700 !important;
}

.nav-link-admin:hover {
    background: rgba(255, 22, 71, 0.2);
    color: #ff2d5c !important;
}

/* User Dropdown Toggle */
.user-dropdown-toggle {
    display: flex !important;
    align-items: center;
    gap: 10px;
    padding: 8px 16px !important;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    transition: all 0.3s ease;
}

.user-dropdown-toggle:hover {
    background: rgba(255, 22, 71, 0.1);
    border-color: rgba(255, 22, 71, 0.3);
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid rgba(255, 22, 71, 0.3);
    transition: all 0.3s ease;
}

.user-dropdown-toggle:hover .user-avatar {
    border-color: rgba(255, 22, 71, 0.6);
    transform: scale(1.05);
}

.user-name {
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.95rem;
}

/* Dropdown Menu */
.user-dropdown-menu {
    background: linear-gradient(145deg, #1e242e, #181d26) !important;
    border: 1px solid rgba(255, 22, 71, 0.2) !important;
    border-radius: 12px !important;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5) !important;
    padding: 8px !important;
    min-width: 280px !important;
    margin-top: 8px !important;
}

.dropdown-header {
    padding: 16px !important;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 8px;
    margin-bottom: 8px;
}

.dropdown-user-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.dropdown-user-name {
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
}

.dropdown-user-email {
    color: #8b93a7;
    font-size: 0.85rem;
}

.dropdown-divider {
    border-color: rgba(255, 22, 71, 0.15) !important;
    margin: 8px 0 !important;
}

.dropdown-item {
    color: #8b93a7 !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
    transition: all 0.2s ease !important;
    display: flex !important;
    align-items: center;
    gap: 12px;
}

.dropdown-item-icon {
    font-size: 1.1rem;
    opacity: 0.8;
    width: 20px;
    text-align: center;
}

.dropdown-item:hover {
    background: rgba(255, 22, 71, 0.12) !important;
    color: #ffffff !important;
}

.dropdown-item:hover .dropdown-item-icon {
    opacity: 1;
    transform: scale(1.1);
}

/* Admin Dropdown Item */
.dropdown-item-admin {
    color: #ff1647 !important;
    font-weight: 700 !important;
}

.dropdown-item-admin:hover {
    background: rgba(255, 22, 71, 0.2) !important;
    color: #ff2d5c !important;
}

/* Logout Item */
.dropdown-item-logout {
    color: #ef4444 !important;
}

.dropdown-item-logout:hover {
    background: rgba(239, 68, 68, 0.15) !important;
    color: #f87171 !important;
}

/* Login Button */
.btn-login {
    padding: 10px 24px !important;
    background: linear-gradient(135deg, #ff1647, #cc1138) !important;
    border: none !important;
    border-radius: 10px !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 16px rgba(255, 22, 71, 0.25);
}

.btn-login:hover {
    background: linear-gradient(135deg, #ff2d5c, #d91f47) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(255, 22, 71, 0.35);
}

/* Navbar Toggler */
.navbar-toggler {
    border: 1px solid rgba(255, 22, 71, 0.3) !important;
    padding: 8px 12px;
}

.navbar-toggler:focus {
    box-shadow: 0 0 0 3px rgba(255, 22, 71, 0.2) !important;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 22, 71, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
}

/* Mobile Adjustments */
@media (max-width: 991px) {
    .navbar-nav {
        padding-top: 16px;
    }
    
    .nav-link {
        margin: 4px 0;
        justify-content: flex-start;
    }
    
    .user-dropdown-toggle {
        justify-content: flex-start;
        width: 100%;
    }
    
    .user-dropdown-menu {
        width: 100% !important;
        margin-top: 8px !important;
    }
    
    .nav-item.dropdown {
        margin-top: 8px;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .navbar-brand {
        font-size: 1.3rem !important;
    }
    
    .user-name {
        font-size: 0.9rem;
    }
}
</style>