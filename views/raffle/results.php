<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <!-- Header da Página -->
    <div class="page-header mb-5">
        <h1 class="page-title">Resultados dos Sorteios</h1>
        <p class="page-subtitle">Confira os vencedores e a transparência de cada sorteio realizado</p>
    </div>
    
    <?php if (empty($raffles)): ?>
        <div class="empty-state-card">
            <div class="empty-state-icon">🏆</div>
            <h4 class="empty-state-title">Nenhum sorteio encerrado ainda</h4>
            <p class="empty-state-text">Os resultados dos sorteios aparecerão aqui assim que forem realizados. Participe dos sorteios ativos para ter chances de ganhar!</p>
            <a href="<?= BASE_URL ?>/user/home" class="btn-primary-custom">
                Ver Sorteios Ativos
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($raffles as $raffle): ?>
                <div class="col-lg-6 col-12">
                    <div class="result-card">
                        <!-- Card Header -->
                        <div class="result-card-header">
                            <div class="result-info">
                                <h5 class="result-title">
                                    <?= ValidationHelper::escapeHtml($raffle['title']) ?>
                                </h5>
                                <div class="result-meta">
                                    <span class="result-meta-item">
                                        <span class="result-meta-icon">👥</span>
                                        <?= $raffle['entries_count'] ?> participações
                                    </span>
                                    <span class="result-meta-item">
                                        <span class="result-meta-icon">📅</span>
                                        <?= date('d/m/Y', strtotime($raffle['updated_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            <span class="badge badge-closed">Encerrado</span>
                        </div>
                        
                        <!-- Card Body -->
                        <?php if ($raffle['winner']): ?>
                            <div class="result-card-body">
                                <!-- Winner Section -->
                                <div class="winner-section">
                                    <div class="winner-label">Vencedor</div>
                                    <div class="winner-info">
                                        <img src="<?= ValidationHelper::escapeHtml($raffle['winner']['winner_avatar']) ?>" 
                                             class="winner-avatar" 
                                             alt="Avatar do vencedor">
                                        <div class="winner-details">
                                            <div class="winner-name">
                                                <?= ValidationHelper::escapeHtml($raffle['winner']['winner_name']) ?>
                                            </div>
                                            <div class="winner-date">
                                                Sorteado em <?= date('d/m/Y', strtotime($raffle['winner']['selected_at'])) ?> às <?= date('H:i', strtotime($raffle['winner']['selected_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Transparency Log Button -->
                                <?php if ($raffle['winner']['log_info']): ?>
                                    <button type="button" 
                                            class="btn-transparency" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#logModal<?= $raffle['id'] ?>">
                                        <span class="btn-transparency-icon">🔍</span>
                                        <span>Ver Log de Transparência</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Modal Log de Transparência -->
                            <?php if ($raffle['winner']['log_info']): ?>
                                <div class="modal fade" id="logModal<?= $raffle['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content custom-modal">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Log de Transparência</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="transparency-notice">
                                                    <div class="transparency-notice-icon">ℹ️</div>
                                                    <div class="transparency-notice-text">
                                                        <strong>Sobre a Transparência:</strong><br>
                                                        Este log garante que o sorteio foi realizado de forma justa e aleatória. 
                                                        Todos os IDs das participações aprovadas e o hash de verificação estão registrados.
                                                    </div>
                                                </div>
                                                
                                                <?php 
                                                $logData = json_decode($raffle['winner']['log_info'], true);
                                                ?>
                                                
                                                <div class="log-data-grid">
                                                    <div class="log-data-item">
                                                        <div class="log-data-label">Total de Participações</div>
                                                        <div class="log-data-value"><?= $logData['total_entries'] ?? 0 ?></div>
                                                    </div>
                                                    
                                                    <div class="log-data-item">
                                                        <div class="log-data-label">Índice Sorteado</div>
                                                        <div class="log-data-value"><?= $logData['winner_index'] ?? 0 ?></div>
                                                    </div>
                                                    
                                                    <div class="log-data-item">
                                                        <div class="log-data-label">Seed (Semente)</div>
                                                        <div class="log-data-value log-data-code"><?= $logData['seed'] ?? 'N/A' ?></div>
                                                    </div>
                                                    
                                                    <div class="log-data-item">
                                                        <div class="log-data-label">Data/Hora</div>
                                                        <div class="log-data-value log-data-code"><?= $logData['timestamp'] ?? 'N/A' ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="log-hash-section">
                                                    <div class="log-hash-label">Hash de Verificação (SHA-256)</div>
                                                    <div class="log-hash-value">
                                                        <?= $logData['hash'] ?? 'N/A' ?>
                                                    </div>
                                                </div>
                                                
                                                <details class="log-details">
                                                    <summary class="log-details-summary">
                                                        Ver todos os IDs das participações
                                                    </summary>
                                                    <div class="log-details-content">
                                                        <pre><?= json_encode($logData['all_entry_ids'] ?? [], JSON_PRETTY_PRINT) ?></pre>
                                                    </div>
                                                </details>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="result-card-body">
                                <div class="no-winner-state">
                                    <div class="no-winner-icon">⏳</div>
                                    <div class="no-winner-text">Aguardando realização do sorteio</div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Card Footer -->
                        <div class="result-card-footer">
                            <a href="<?= BASE_URL ?>/raffle/view/<?= $raffle['id'] ?>" 
                               class="btn-view-details">
                                Ver Detalhes Completos
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginação -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <nav class="pagination-wrapper">
                <ul class="pagination-custom">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="pagination-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
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
/* ========== RESULTS PAGE STYLES ========== */

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

.btn-primary-custom {
    display: inline-block;
    padding: 14px 32px;
    background: linear-gradient(135deg, #ff1647, #cc1138);
    color: #ffffff;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    background: linear-gradient(135deg, #ff2d5c, #d91f47);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255, 22, 71, 0.4);
    color: #ffffff;
}

/* Result Cards */
.result-card {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border-radius: 16px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.result-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 22, 71, 0.3);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
}

.result-card-header {
    padding: 24px;
    border-bottom: 1px solid rgba(255, 22, 71, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 15px;
}

.result-info {
    flex: 1;
    min-width: 0;
}

.result-title {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 12px 0;
    line-height: 1.4;
}

.result-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.result-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #8b93a7;
    font-size: 0.9rem;
}

.result-meta-icon {
    font-size: 1rem;
}

/* Badges */
.badge-closed {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex-shrink: 0;
}

/* Card Body */
.result-card-body {
    padding: 24px;
    flex: 1;
}

/* Winner Section */
.winner-section {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
}

.winner-label {
    color: #8b93a7;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 16px;
}

.winner-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.winner-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid #ff1647;
    box-shadow: 0 0 20px rgba(255, 22, 71, 0.4);
    flex-shrink: 0;
}

.winner-details {
    flex: 1;
    min-width: 0;
}

.winner-name {
    color: #ff1647;
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.winner-date {
    color: #8b93a7;
    font-size: 0.9rem;
}

/* Transparency Button */
.btn-transparency {
    width: 100%;
    padding: 12px 20px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.2);
    border-radius: 10px;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-transparency:hover {
    border-color: rgba(255, 22, 71, 0.4);
    background: rgba(255, 22, 71, 0.1);
    transform: translateY(-2px);
}

.btn-transparency-icon {
    font-size: 1.2rem;
}

/* No Winner State */
.no-winner-state {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 12px;
    padding: 32px 20px;
    text-align: center;
}

.no-winner-icon {
    font-size: 3rem;
    margin-bottom: 12px;
    opacity: 0.6;
}

.no-winner-text {
    color: #fbbf24;
    font-size: 1rem;
    font-weight: 600;
}

/* Card Footer */
.result-card-footer {
    padding: 20px 24px;
    border-top: 1px solid rgba(255, 22, 71, 0.15);
    background: rgba(0, 0, 0, 0.2);
}

.btn-view-details {
    display: block;
    width: 100%;
    padding: 12px 24px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.2);
    color: #ffffff;
    text-decoration: none;
    text-align: center;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.btn-view-details:hover {
    border-color: rgba(255, 22, 71, 0.4);
    background: rgba(255, 22, 71, 0.1);
    color: #ff1647;
    transform: translateY(-2px);
}

/* Custom Modal */
.custom-modal {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.2);
    border-radius: 16px;
}

.custom-modal .modal-header {
    border-bottom: 1px solid rgba(255, 22, 71, 0.15);
    padding: 24px;
}

.custom-modal .modal-title {
    color: #ffffff;
    font-weight: 700;
}

.custom-modal .modal-body {
    padding: 24px;
}

/* Transparency Notice */
.transparency-notice {
    background: rgba(59, 130, 246, 0.1);
    border-left: 4px solid #3b82f6;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 24px;
    display: flex;
    gap: 12px;
}

.transparency-notice-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.transparency-notice-text {
    color: #60a5fa;
    font-size: 0.9rem;
    line-height: 1.6;
}

/* Log Data Grid */
.log-data-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.log-data-item {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    padding: 16px;
}

.log-data-label {
    color: #8b93a7;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.log-data-value {
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 700;
}

.log-data-code {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    font-weight: 400;
}

/* Log Hash */
.log-hash-section {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 16px;
}

.log-hash-label {
    color: #8b93a7;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.log-hash-value {
    font-family: 'Courier New', monospace;
    color: #22c55e;
    font-size: 0.75rem;
    word-break: break-all;
    padding: 8px;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 6px;
}

/* Log Details */
.log-details {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    padding: 16px;
}

.log-details-summary {
    color: #8b93a7;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
    transition: color 0.2s ease;
}

.log-details-summary:hover {
    color: #ffffff;
}

.log-details-content {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 22, 71, 0.15);
}

.log-details-content pre {
    background: rgba(0, 0, 0, 0.4);
    border-radius: 6px;
    padding: 12px;
    color: #22c55e;
    font-size: 0.75rem;
    overflow-x: auto;
    max-height: 200px;
    margin: 0;
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
    
    .result-card-header {
        flex-direction: column;
    }
    
    .winner-info {
        flex-direction: column;
        text-align: center;
    }
    
    .log-data-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>