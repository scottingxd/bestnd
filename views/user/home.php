<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Sorteios Ativos</h1>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <?php if (empty($raffles)): ?>
        <div class="alert alert-info text-center">
            Nenhum sorteio ativo no momento.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($raffles as $raffle): ?>
                <div class="col-md-4 mb-4">
                    <div class="card raffle-card h-100">
                        <?php if ($raffle['image_url']): ?>
                            <img src="<?= ValidationHelper::escapeHtml($raffle['image_url']) ?>" class="raffle-card-img" alt="<?= ValidationHelper::escapeHtml($raffle['title']) ?>">
                        <?php endif; ?>
                        
                        <span class="raffle-badge <?= $raffle['is_paid'] ? 'paid' : 'free' ?>">
                            <?= $raffle['is_paid'] ? 'COM COMPROVANTE' : 'GRATUITO' ?>
                        </span>
                        
                        <div class="card-body">
                            <h5 class="raffle-title"><?= ValidationHelper::escapeHtml($raffle['title']) ?></h5>
                            <p class="raffle-description"><?= ValidationHelper::escapeHtml(substr($raffle['description'], 0, 100)) ?>...</p>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    📊 <?= $raffle['entries_count'] ?? 0 ?> participações
                                </small>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <a href="<?= BASE_URL ?>/raffle/view/<?= $raffle['id'] ?>" class="btn btn-primary w-100">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Paginação">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>