<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <!-- Header da Página -->
    <div class="page-header mb-5">
        <h1 class="page-title">Sorteios Ativos</h1>
        <p class="page-subtitle">Participe dos sorteios disponíveis e concorra a prêmios incríveis</p>
    </div>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <?php if (empty($raffles)): ?>
        <div class="empty-state-card">
            <div class="empty-state-icon">🎲</div>
            <h4 class="empty-state-title">Nenhum sorteio ativo no momento</h4>
            <p class="empty-state-text">Novos sorteios em breve! Fique de olho para não perder as próximas oportunidades.</p>
            <a href="<?= BASE_URL ?>/raffle/results" class="btn-secondary-custom">
                Ver Resultados Anteriores
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($raffles as $raffle): ?>
                <div class="col-xl-4 col-lg-6 col-12">
                    <div class="raffle-card">
                        <!-- Imagem do Sorteio -->
                        <?php if ($raffle['image_url']): ?>
                            <div class="raffle-card-image-wrapper">
                                <img src="<?= ValidationHelper::escapeHtml($raffle['image_url']) ?>" 
                                     class="raffle-card-image" 
                                     alt="<?= ValidationHelper::escapeHtml($raffle['title']) ?>">
                                <div class="raffle-card-overlay"></div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de Tipo -->
                        <div class="raffle-type-badge raffle-type-<?= $raffle['is_paid'] ? 'paid' : 'free' ?>">
                            <?= $raffle['is_paid'] ? 'Com Comprovante' : 'Gratuito' ?>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="raffle-card-body">
                            <h5 class="raffle-card-title">
                                <?= ValidationHelper::escapeHtml($raffle['title']) ?>
                            </h5>
                            <p class="raffle-card-description">
                                <?= ValidationHelper::escapeHtml(mb_substr($raffle['description'], 0, 120)) ?>...
                            </p>
                            
                            <!-- Estatísticas -->
                            <div class="raffle-stats">
                                <div class="raffle-stat-item">
                                    <span class="raffle-stat-icon">👥</span>
                                    <span class="raffle-stat-value"><?= $raffle['entries_count'] ?? 0 ?></span>
                                    <span class="raffle-stat-label">participações</span>
                                </div>
                                
                                <?php if ($raffle['max_participants']): ?>
                                <div class="raffle-stat-item">
                                    <span class="raffle-stat-icon">🎯</span>
                                    <span class="raffle-stat-value"><?= $raffle['max_participants'] ?></span>
                                    <span class="raffle-stat-label">limite</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="raffle-card-footer">
                            <a href="<?= BASE_URL ?>/raffle/view/<?= $raffle['id'] ?>" 
                               class="btn-raffle-view">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
            <nav class="pagination-wrapper">
                <ul class="pagination-custom">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="pagination-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="pagination-link" href="?page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
/* ========== HOME PAGE STYLES ========== */

.page-header {
    text-align: center;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ff1647, #cc1138);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 10px;
}

.page-subtitle {
    color: #8b93a7;
    font-size: 1.1rem;
    margin: 0;
}

/* Empty State */
.empty-state-card {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border-radius: 16px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    padding: 80px 40px;
    text-align: center;
}

.empty-state-icon {
    font-size: 5rem;
    opacity: 0.4;
    margin-bottom: 30px;
}

.empty-state-title {
    color: #ffffff;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.empty-state-text {
    color: #8b93a7;
    font-size: 1rem;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* Raffle Cards */
.raffle-card {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border-radius: 16px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.raffle-card:hover {
    transform: translateY(-8px);
    border-color: rgba(255, 22, 71, 0.3);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.6);
}

.raffle-card-image-wrapper {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
    background: linear-gradient(135deg, #1e242e, #252b36);
}

.raffle-card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.raffle-card:hover .raffle-card-image {
    transform: scale(1.1);
}

.raffle-card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(to bottom, transparent 0%, rgba(15, 20, 25, 0.8) 100%);
}

/* Type Badge */
.raffle-type-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    padding: 8px 18px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(12px);
    z-index: 10;
}

.raffle-type-free {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9));
    color: #ffffff;
    box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3);
}

.raffle-type-paid {
    background: linear-gradient(135deg, rgba(255, 22, 71, 0.9), rgba(204, 17, 56, 0.9));
    color: #ffffff;
    box-shadow: 0 4px 16px rgba(255, 22, 71, 0.4);
}

/* Card Body */
.raffle-card-body {
    padding: 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.raffle-card-title {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 12px 0;
    line-height: 1.4;
    min-height: 2.8em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.raffle-card-description {
    color: #8b93a7;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0 0 20px 0;
    flex: 1;
}

/* Stats */
.raffle-stats {
    display: flex;
    gap: 20px;
    padding: 16px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    margin-top: auto;
}

.raffle-stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.raffle-stat-icon {
    font-size: 1.2rem;
}

.raffle-stat-value {
    color: #ff1647;
    font-weight: 700;
    font-size: 1.1rem;
}

.raffle-stat-label {
    color: #8b93a7;
    font-size: 0.85rem;
}

/* Card Footer */
.raffle-card-footer {
    padding: 20px 24px;
    border-top: 1px solid rgba(255, 22, 71, 0.15);
    background: rgba(0, 0, 0, 0.2);
}

.btn-raffle-view {
    display: block;
    width: 100%;
    padding: 12px 24px;
    background: linear-gradient(135deg, #ff1647, #cc1138);
    color: #ffffff;
    text-decoration: none;
    text-align: center;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-raffle-view:hover {
    background: linear-gradient(135deg, #ff2d5c, #d91f47);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255, 22, 71, 0.4);
    color: #ffffff;
}

/* Buttons */
.btn-secondary-custom {
    display: inline-block;
    padding: 14px 32px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.2);
    color: #ffffff;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary-custom:hover {
    border-color: rgba(255, 22, 71, 0.4);
    transform: translateY(-2px);
    color: #ffffff;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 50px;
}

.pagination-custom {
    display: flex;
    justify-content: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination-item {
    display: inline-block;
}

.pagination-link {
    display: block;
    padding: 10px 16px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.15);
    border-radius: 10px;
    color: #8b93a7;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination-link:hover {
    border-color: rgba(255, 22, 71, 0.3);
    color: #ff1647;
    transform: translateY(-2px);
}

.pagination-item.active .pagination-link {
    background: linear-gradient(135deg, #ff1647, #cc1138);
    border-color: #ff1647;
    color: #ffffff;
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.8rem;
    }
    
    .page-subtitle {
        font-size: 1rem;
    }
    
    .empty-state-card {
        padding: 60px 30px;
    }
    
    .empty-state-icon {
        font-size: 4rem;
    }
    
    .raffle-card-image-wrapper {
        height: 180px;
    }
    
    .raffle-stats {
        flex-direction: column;
        gap: 12px;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>