<?php use App\Helpers\ValidationHelper; use App\Helpers\AuthHelper; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-danger">
    <div class="container">
        <a class="navbar-brand text-danger fw-bold" href="<?= BASE_URL ?>/user/home">
            🎮 CS2 SORTEIOS
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (AuthHelper::isLogged()): 
                    $user = AuthHelper::getUser(); ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/user/home">Sorteios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/user/my-entries">Minhas Participações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/raffle/results">Resultados</a>
                    </li>
                    
                    <?php if ($user['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?= BASE_URL ?>/admin/dashboard">Admin</a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?= ValidationHelper::escapeHtml($user['avatar_url']) ?>" class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                            <?= ValidationHelper::escapeHtml($user['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                            <li><a class="dropdown-item text-light" href="<?= BASE_URL ?>/user/profile">Meu Perfil</a></li>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout">Sair</a></li>
                        </ul>
                    </li>
                    
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="<?= BASE_URL ?>/auth/login">Entrar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>