<?php 
use App\Helpers\ValidationHelper;
use App\Helpers\CsrfHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">✏️ Editar Sorteio</h1>
        <div>
            <a href="<?= BASE_URL ?>/raffle/view/<?= $raffle['id'] ?>" class="btn btn-outline-primary" target="_blank">
                👁️ Visualizar
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                🗑️ Excluir
            </button>
        </div>
    </div>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="neomorph-card mb-4">
                <form method="POST" action="<?= BASE_URL ?>/admin/edit-raffle/<?= $raffle['id'] ?>">
                    <?= CsrfHelper::getTokenField() ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               value="<?= ValidationHelper::escapeHtml($raffle['title']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= ValidationHelper::escapeHtml($raffle['description']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_url" class="form-label">URL da Imagem</label>
                        <input type="url" class="form-control" id="image_url" name="image_url"
                               value="<?= ValidationHelper::escapeHtml($raffle['image_url']) ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="draft" <?= $raffle['status'] === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                                    <option value="active" <?= $raffle['status'] === 'active' ? 'selected' : '' ?>>Ativo</option>
                                    <option value="closed" <?= $raffle['status'] === 'closed' ? 'selected' : '' ?>>Encerrado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label d-block">Tipo</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" value="1"
                                           <?= $raffle['is_paid'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_paid">
                                        Requer Comprovante
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_value" class="form-label">Valor Mínimo (R$)</label>
                                <input type="number" step="0.01" class="form-control" id="min_value" name="min_value"
                                       value="<?= $raffle['min_value'] ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_participants" class="form-label">Máx. Participantes</label>
                                <input type="number" class="form-control" id="max_participants" name="max_participants"
                                       value="<?= $raffle['max_participants'] ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_at" class="form-label">Data/Hora Início</label>
                                <input type="datetime-local" class="form-control" id="start_at" name="start_at"
                                       value="<?= $raffle['start_at'] ? date('Y-m-d\TH:i', strtotime($raffle['start_at'])) : '' ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_at" class="form-label">Data/Hora Fim</label>
                                <input type="datetime-local" class="form-control" id="end_at" name="end_at"
                                       value="<?= $raffle['end_at'] ? date('Y-m-d\TH:i', strtotime($raffle['end_at'])) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        💾 Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Estatísticas -->
            <div class="neomorph-card mb-4">
                <h5 class="mb-3">📊 Estatísticas</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total de Participações:</span>
                    <strong><?= count($entries) ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Pendentes:</span>
                    <strong class="text-warning"><?= count(array_filter($entries, fn($e) => $e['status'] === 'pending')) ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Aprovadas:</span>
                    <strong class="text-success"><?= count(array_filter($entries, fn($e) => $e['status'] === 'approved')) ?></strong>
                </div>
            </div>
            
            <!-- Sortear Vencedor -->
            <?php 
            $approvedEntries = array_filter($entries, fn($e) => $e['status'] === 'approved');
            $hasWinner = \App\Models\RaffleWinner::findByRaffleId($raffle['id']);
            ?>
            
            <?php if (!$hasWinner && count($approvedEntries) > 0): ?>
                <div class="neomorph-card mb-4">
                    <h5 class="mb-3">🎲 Sortear Vencedor</h5>
                    <p class="text-muted small">
                        Há <strong><?= count($approvedEntries) ?></strong> participações aprovadas.
                    </p>
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#drawModal">
                        🏆 Sortear Agora
                    </button>
                </div>
            <?php elseif ($hasWinner): ?>
                <div class="neomorph-card mb-4">
                    <h5 class="mb-3">🏆 Vencedor</h5>
                    <div class="text-center">
                        <img src="<?= ValidationHelper::escapeHtml($hasWinner['winner_avatar']) ?>" 
                             class="rounded-circle mb-2" width="60" height="60">
                        <div><strong><?= ValidationHelper::escapeHtml($hasWinner['winner_name']) ?></strong></div>
                        <small class="text-muted">
                            Sorteado em <?= date('d/m/Y H:i', strtotime($hasWinner['selected_at'])) ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Sortear -->
<div class="modal fade" id="drawModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🎲 Confirmar Sorteio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Você está prestes a sortear o vencedor deste sorteio.</p>
                <p class="text-warning"><strong>⚠️ Esta ação não pode ser desfeita!</strong></p>
                <p class="text-muted small">
                    Serão consideradas <strong><?= count($approvedEntries) ?></strong> participações aprovadas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= BASE_URL ?>/admin/draw-winner/<?= $raffle['id'] ?>" class="d-inline">
                    <?= CsrfHelper::getTokenField() ?>
                    <button type="submit" class="btn btn-primary">
                        🎲 Confirmar Sorteio
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🗑️ Excluir Sorteio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este sorteio?</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="delete_entries" name="delete_entries" value="1" checked>
                    <label class="form-check-label" for="delete_entries">
                        Excluir também todas as participações e comprovantes
                    </label>
                </div>
                <p class="text-danger mt-3"><strong>⚠️ Esta ação não pode ser desfeita!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= BASE_URL ?>/admin/delete-raffle/<?= $raffle['id'] ?>" class="d-inline">
                    <?= CsrfHelper::getTokenField() ?>
                    <input type="hidden" name="delete_entries" id="delete_entries_input" value="1">
                    <button type="submit" class="btn btn-danger">
                        🗑️ Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('delete_entries').addEventListener('change', function() {
    document.getElementById('delete_entries_input').value = this.checked ? '1' : '0';
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>