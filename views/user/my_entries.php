<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <!-- Header da Página -->
    <div class="page-header mb-5">
        <h1 class="page-title">Minhas Participações</h1>
        <p class="page-subtitle">Gerencie suas participações nos sorteios</p>
    </div>
    
    <?php if (empty($entries)): ?>
        <div class="empty-state-card">
            <div class="empty-state-icon">🎮</div>
            <h4 class="empty-state-title">Você ainda não participou de nenhum sorteio</h4>
            <p class="empty-state-text">Comece agora e tenha a chance de ganhar prêmios incríveis!</p>
            <a href="<?= BASE_URL ?>/user/home" class="btn-primary-custom">
                Ver Sorteios Disponíveis
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($entries as $entry): ?>
                <div class="col-lg-6 col-12">
                    <div class="entry-card">
                        <!-- Card Header -->
                        <div class="entry-card-header">
                            <div class="entry-info">
                                <h5 class="entry-title">
                                    <?= ValidationHelper::escapeHtml($entry['raffle_title']) ?>
                                </h5>
                                <div class="entry-date">
                                    Participação em <?= date('d/m/Y', strtotime($entry['created_at'])) ?> às <?= date('H:i', strtotime($entry['created_at'])) ?>
                                </div>
                            </div>
                            <span class="badge badge-approved">
                                Aprovado
                            </span>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="entry-card-body">
                            <div class="entry-details">
                                <div class="entry-detail-item">
                                    <span class="entry-detail-label">ID da Participação:</span>
                                    <span class="entry-detail-value">#<?= $entry['id'] ?></span>
                                </div>
                                
                                <?php if ($entry['raffle_type'] === 'paid'): ?>
                                    <div class="entry-detail-item">
                                        <span class="entry-detail-label">Valor Depositado:</span>
                                        <span class="entry-detail-value entry-detail-value-money">
                                            R$ <?= number_format($entry['deposit_amount'], 2, ',', '.') ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($entry['deposit_proof']): ?>
                                        <div class="entry-detail-item">
                                            <span class="entry-detail-label">Comprovante:</span>
                                            <button type="button" 
                                                    class="btn-view-proof" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#proofModal<?= $entry['id'] ?>">
                                                Ver Comprovante
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <div class="entry-detail-item">
                                    <span class="entry-detail-label">Tipo de Sorteio:</span>
                                    <span class="entry-detail-value">
                                        <?= $entry['raffle_type'] === 'paid' ? 'Pago' : 'Gratuito' ?>
                                    </span>
                                </div>
                                
                                <div class="entry-detail-item">
                                    <span class="entry-detail-label">Data de Encerramento:</span>
                                    <span class="entry-detail-value">
                                        <?= date('d/m/Y', strtotime($entry['raffle_end_date'])) ?> às <?= date('H:i', strtotime($entry['raffle_end_date'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="entry-card-footer">
                            <?php
                            $canDelete = strtotime($entry['raffle_end_date']) > time();
                            $raffleEnded = strtotime($entry['raffle_end_date']) <= time();
                            ?>
                            
                            <div class="entry-actions">
                                <a href="<?= BASE_URL ?>/raffle/view/<?= $entry['raffle_id'] ?>" 
                                   class="btn-entry-view">
                                    Ver Sorteio
                                </a>
                                
                                <?php if ($canDelete): ?>
                                    <button type="button" 
                                            class="btn-entry-delete" 
                                            data-entry-id="<?= $entry['id'] ?>"
                                            data-raffle-title="<?= ValidationHelper::escapeHtml($entry['raffle_title']) ?>"
                                            onclick="confirmDelete(this)">
                                        Deletar Participação
                                    </button>
                                <?php else: ?>
                                    <div class="entry-locked">
                                        <span class="entry-locked-icon">🔒</span>
                                        <span class="entry-locked-text">Sorteio encerrado</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($raffleEnded): ?>
                                <div class="entry-notice entry-notice-info">
                                    Este sorteio já foi encerrado. Aguarde a divulgação do resultado.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Modal Comprovante -->
                        <?php if ($entry['raffle_type'] === 'paid' && $entry['deposit_proof']): ?>
                            <div class="modal fade" id="proofModal<?= $entry['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content custom-modal">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Comprovante de Depósito</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="<?= BASE_URL ?>/<?= ValidationHelper::escapeHtml($entry['deposit_proof']) ?>" 
                                                 class="proof-image" 
                                                 alt="Comprovante">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="delete-warning">
                    <div class="delete-warning-icon">⚠️</div>
                    <div class="delete-warning-text">
                        <strong>Atenção!</strong><br>
                        Você está prestes a deletar sua participação no sorteio:<br>
                        <span id="deleteRaffleTitle" class="delete-raffle-title"></span>
                    </div>
                </div>
                <p class="delete-info">Esta ação não pode ser desfeita. Tem certeza que deseja continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn-modal-delete" id="confirmDeleteBtn">
                    Sim, Deletar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ========== MY ENTRIES PAGE STYLES ========== */

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

/* Entry Cards */
.entry-card {
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

.entry-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 22, 71, 0.3);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
}

.entry-card-header {
    padding: 24px;
    border-bottom: 1px solid rgba(255, 22, 71, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 15px;
}

.entry-info {
    flex: 1;
    min-width: 0;
}

.entry-title {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.entry-date {
    color: #8b93a7;
    font-size: 0.9rem;
}

.badge-approved {
    background: linear-gradient(135deg, #22c55e, #16a34a);
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
.entry-card-body {
    padding: 24px;
    flex: 1;
}

.entry-details {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.entry-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 8px;
}

.entry-detail-label {
    color: #8b93a7;
    font-size: 0.9rem;
    font-weight: 600;
}

.entry-detail-value {
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 600;
}

.entry-detail-value-money {
    color: #22c55e;
    font-size: 1.1rem;
    font-weight: 700;
}

.btn-view-proof {
    padding: 6px 16px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #ffffff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-view-proof:hover {
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    transform: translateY(-1px);
}

/* Card Footer */
.entry-card-footer {
    padding: 20px 24px;
    border-top: 1px solid rgba(255, 22, 71, 0.15);
    background: rgba(0, 0, 0, 0.2);
}

.entry-actions {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
}

.btn-entry-view {
    flex: 1;
    padding: 12px 20px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.2);
    color: #ffffff;
    text-decoration: none;
    text-align: center;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: block;
}

.btn-entry-view:hover {
    border-color: rgba(255, 22, 71, 0.4);
    background: rgba(255, 22, 71, 0.1);
    color: #ff1647;
    transform: translateY(-2px);
}

.btn-entry-delete {
    flex: 1;
    padding: 12px 20px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-entry-delete:hover {
    border-color: rgba(239, 68, 68, 0.5);
    background: rgba(239, 68, 68, 0.1);
    color: #f87171;
    transform: translateY(-2px);
}

.entry-locked {
    flex: 1;
    padding: 12px 20px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(139, 147, 167, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.entry-locked-icon {
    font-size: 1.2rem;
    opacity: 0.6;
}

.entry-locked-text {
    color: #8b93a7;
    font-size: 0.9rem;
    font-weight: 600;
}

.entry-notice {
    padding: 12px;
    border-radius: 8px;
    font-size: 0.9rem;
    margin-top: 12px;
}

.entry-notice-info {
    background: rgba(59, 130, 246, 0.1);
    border-left: 4px solid #3b82f6;
    color: #60a5fa;
}

/* Delete Modal */
.delete-warning {
    background: rgba(239, 68, 68, 0.1);
    border-left: 4px solid #ef4444;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
}

.delete-warning-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.delete-warning-text {
    color: #f87171;
    font-size: 0.95rem;
    line-height: 1.6;
}

.delete-raffle-title {
    display: block;
    color: #ffffff;
    font-weight: 700;
    margin-top: 8px;
}

.delete-info {
    color: #8b93a7;
    font-size: 0.95rem;
    margin: 0;
}

.btn-modal-cancel {
    padding: 10px 24px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(139, 147, 167, 0.2);
    color: #8b93a7;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-modal-cancel:hover {
    border-color: rgba(139, 147, 167, 0.4);
    color: #ffffff;
}

.btn-modal-delete {
    padding: 10px 24px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
    color: #ffffff;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-modal-delete:hover {
    background: linear-gradient(135deg, #f87171, #ef4444);
    transform: translateY(-1px);
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

.custom-modal .modal-footer {
    border-top: 1px solid rgba(255, 22, 71, 0.15);
    padding: 20px 24px;
}

.proof-image {
    width: 100%;
    height: auto;
    border-radius: 12px;
    border: 1px solid rgba(255, 22, 71, 0.2);
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
    
    .entry-card-header {
        flex-direction: column;
    }
    
    .entry-actions {
        flex-direction: column;
    }
    
    .entry-detail-item {
        flex-direction: column;
        align-items: start;
        gap: 8px;
    }
}
</style>

<script>
// Variáveis globais
let currentEntryId = null;

// Função para confirmar exclusão
function confirmDelete(button) {
    currentEntryId = button.getAttribute('data-entry-id');
    const raffleTitle = button.getAttribute('data-raffle-title');
    
    document.getElementById('deleteRaffleTitle').textContent = raffleTitle;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Confirmar exclusão
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!currentEntryId) return;
    
    // Mostrar loading
    this.disabled = true;
    this.textContent = 'Deletando...';
    
    // Fazer requisição AJAX
    fetch('<?= BASE_URL ?>/user/delete-entry', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            entry_id: currentEntryId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fechar modal
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            
            // Recarregar página
            window.location.reload();
        } else {
            alert('Erro: ' + data.message);
            this.disabled = false;
            this.textContent = 'Sim, Deletar';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao deletar participação. Tente novamente.');
        this.disabled = false;
        this.textContent = 'Sim, Deletar';
    });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>