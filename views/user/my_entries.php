<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <h1 class="mb-4" style="color: #ffffff; text-shadow: 0 0 20px rgba(255, 22, 71, 0.3);">
        🎟️ Minhas Participações
    </h1>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <?php if (empty($entries)): ?>
        <div class="neomorph-card text-center py-5">
            <div class="mb-4" style="font-size: 4rem; opacity: 0.5;">🎮</div>
            <h4 class="mb-3" style="color: #ffffff;">Você ainda não participou de nenhum sorteio</h4>
            <p class="mb-4" style="color: #b0b0b0;">Navegue pelos sorteios ativos e comece a participar!</p>
            <a href="<?= BASE_URL ?>/user/home" class="neomorph-button-primary">
                🎲 Ver Sorteios Ativos
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($entries as $entry): ?>
                <div class="col-lg-6 col-12">
                    <div class="neomorph-card h-100">
                        <!-- Header do Card -->
                        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="mb-2" style="color: #ffffff; font-weight: 600;">
                                    <?= ValidationHelper::escapeHtml($entry['raffle_title']) ?>
                                </h5>
                                <small style="color: #b0b0b0; display: block;">
                                    📅 <?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?>
                                </small>
                            </div>
                            
                            <div class="text-end">
                                <span class="badge bg-<?= 
                                    $entry['status'] === 'approved' ? 'success' : 
                                    ($entry['status'] === 'pending' ? 'warning' : 'danger') 
                                ?>" style="font-size: 0.8rem; padding: 6px 12px; font-weight: 600;">
                                    <?php
                                    $statusText = [
                                        'approved' => '✓ APROVADO',
                                        'pending' => '⏳ PENDENTE',
                                        'rejected' => '✗ REJEITADO'
                                    ];
                                    echo $statusText[$entry['status']] ?? strtoupper($entry['status']);
                                    ?>
                                </span>
                                
                                <?php if ($entry['is_winner'] > 0): ?>
                                    <div class="mt-2">
                                        <span class="badge bg-success glow-animation" style="font-size: 0.85rem; padding: 6px 14px; font-weight: 700;">
                                            🏆 VENCEDOR
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Informações -->
                        <div class="neomorph-inset p-3 mb-3">
                            <div class="row g-3">
                                <div class="col-6">
                                    <small style="color: #b0b0b0; display: block; margin-bottom: 4px; font-weight: 500;">
                                        Status do Sorteio:
                                    </small>
                                    <span class="badge bg-<?= 
                                        $entry['raffle_status'] === 'active' ? 'success' : 
                                        ($entry['raffle_status'] === 'draft' ? 'secondary' : 'danger') 
                                    ?>" style="font-size: 0.75rem; padding: 4px 10px;">
                                        <?= strtoupper($entry['raffle_status']) ?>
                                    </span>
                                </div>
                                
                                <?php if ($entry['amount']): ?>
                                <div class="col-6 text-end">
                                    <small style="color: #b0b0b0; display: block; margin-bottom: 4px; font-weight: 500;">
                                        Valor:
                                    </small>
                                    <strong style="color: #22c55e; font-size: 1.1rem; text-shadow: 0 0 10px rgba(34, 197, 94, 0.3);">
                                        R$ <?= number_format($entry['amount'], 2, ',', '.') ?>
                                    </strong>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Ações -->
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= BASE_URL ?>/raffle/view/<?= $entry['raffle_id'] ?>" 
                               class="neomorph-button flex-fill text-center"
                               style="padding: 10px 20px; font-weight: 500; color: #ffffff; text-decoration: none;">
                                👁️ Ver Sorteio
                            </a>
                            
                            <?php if ($entry['proof_image_path']): ?>
                                <button type="button" 
                                        class="neomorph-button"
                                        style="padding: 10px 20px; font-weight: 500; color: #ffffff;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#proofModal<?= $entry['id'] ?>">
                                    📄 Comprovante
                                </button>
                                
                                <!-- Modal Comprovante -->
                                <div class="modal fade" id="proofModal<?= $entry['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="color: #ffffff;">📄 Meu Comprovante</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="<?= UPLOAD_URL ?>/<?= ValidationHelper::escapeHtml($entry['proof_image_path']) ?>" 
                                                     class="img-fluid rounded" 
                                                     alt="Comprovante"
                                                     style="max-height: 600px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Alertas de Status -->
                        <?php if ($entry['status'] === 'pending'): ?>
                            <div class="alert alert-warning mt-3 mb-0" style="background: rgba(245, 158, 11, 0.15); border-left: 4px solid #f59e0b;">
                                <small style="color: #fbbf24; font-weight: 500;">
                                    ⏳ Aguardando aprovação do administrador
                                </small>
                            </div>
                        <?php elseif ($entry['status'] === 'rejected'): ?>
                            <div class="alert alert-danger mt-3 mb-0" style="background: rgba(239, 68, 68, 0.15); border-left: 4px solid #ef4444;">
                                <small style="color: #f87171; font-weight: 500;">
                                    ❌ Participação rejeitada
                                </small>
                            </div>
                        <?php elseif ($entry['status'] === 'approved' && $entry['raffle_status'] === 'active'): ?>
                            <div class="alert alert-success mt-3 mb-0" style="background: rgba(34, 197, 94, 0.15); border-left: 4px solid #22c55e;">
                                <small style="color: #4ade80; font-weight: 500;">
                                    ✓ Participação aprovada • Aguardando sorteio
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>" style="color: <?= $i === $page ? '#ffffff' : '#b0b0b0' ?>;">
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
/* Estilos específicos para esta página */
.neomorph-card h5 {
    color: #ffffff !important;
}

.neomorph-card small {
    color: #b0b0b0 !important;
}

.neomorph-button {
    color: #ffffff !important;
}

.badge {
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* Melhorar legibilidade dos alertas */
.alert small {
    font-size: 0.9rem;
}

/* Responsivo */
@media (max-width: 768px) {
    h1 {
        font-size: 1.75rem;
    }
    
    .neomorph-card {
        padding: 20px;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 5px 10px;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .neomorph-button {
        width: 100%;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>