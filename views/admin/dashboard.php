<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <h1 class="mb-4">📊 Dashboard Administrativo</h1>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary"><?= $totalRaffles ?></h3>
                    <p class="text-muted mb-0">Total de Sorteios</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success"><?= $activeRaffles ?></h3>
                    <p class="text-muted mb-0">Sorteios Ativos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning"><?= $pendingEntries ?></h3>
                    <p class="text-muted mb-0">Participações Pendentes</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚡ Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <a href="<?= BASE_URL ?>/admin/create-raffle" class="btn btn-primary me-2">➕ Criar Sorteio</a>
                    <a href="<?= BASE_URL ?>/admin/raffles" class="btn btn-outline-primary me-2">📋 Ver Sorteios</a>
                    <a href="<?= BASE_URL ?>/admin/entries" class="btn btn-outline-warning me-2">✅ Aprovar Participações</a>
                    <a href="<?= BASE_URL ?>/admin/logs" class="btn btn-outline-secondary">📜 Ver Logs</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>