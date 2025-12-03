<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="card" style="max-width: 400px; width: 100%;">
        <div class="card-body text-center p-5">
            <h2 class="text-danger mb-4">🎮 CS2 Sorteios</h2>
            <p class="text-secondary mb-4">Faça login para participar dos sorteios</p>
            
            <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
            
            <a href="<?= BASE_URL ?>/auth/callback" class="btn btn-light btn-lg w-100 mb-3">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="20" class="me-2">
                Entrar com Google
            </a>
            
            <p class="text-muted mt-4 small">
                Login simulado para desenvolvimento
            </p>
            
            <a href="<?= BASE_URL ?>/auth/callback?user=1" class="text-muted small">
                (Admin Login)
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>