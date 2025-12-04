<?php 
use App\Helpers\ValidationHelper;
use App\Helpers\CsrfHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <h1 class="mb-4">➕ Criar Novo Sorteio</h1>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <?php if (isset($_SESSION['raffle_errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['raffle_errors'] as $error): ?>
                    <li><?= ValidationHelper::escapeHtml($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['raffle_errors']); ?>
    <?php endif; ?>
    
    <div class="neomorph-card">
        <form method="POST" action="<?= BASE_URL ?>/admin/create-raffle">
            <?= CsrfHelper::getTokenField() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título do Sorteio <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               value="<?= ValidationHelper::escapeHtml($_POST['title'] ?? '') ?>"
                               placeholder="Ex: Sorteio AWP Dragon Lore FN">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required 
                                  placeholder="Descreva o prêmio e as regras do sorteio..."><?= ValidationHelper::escapeHtml($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_url" class="form-label">URL da Imagem</label>
                        <input type="url" class="form-control" id="image_url" name="image_url"
                               value="<?= ValidationHelper::escapeHtml($_POST['image_url'] ?? '') ?>"
                               placeholder="https://exemplo.com/imagem.jpg">
                        <div class="form-text">Cole a URL de uma imagem do prêmio</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="neomorph-inset p-3 mb-3">
                        <h6 class="mb-3">⚙️ Configurações</h6>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="draft" <?= (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : '' ?>>Rascunho</option>
                                <option value="active" <?= (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : '' ?>>Ativo</option>
                            </select>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" value="1"
                                   <?= (isset($_POST['is_paid']) && $_POST['is_paid']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_paid">
                                Requer Comprovante
                            </label>
                        </div>
                        
                        <div class="mb-3" id="minValueGroup" style="display: none;">
                            <label for="min_value" class="form-label">Valor Mínimo (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="min_value" name="min_value"
                                   value="<?= ValidationHelper::escapeHtml($_POST['min_value'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="max_participants" class="form-label">Máx. Participantes (Total)</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants"
                                   value="<?= ValidationHelper::escapeHtml($_POST['max_participants'] ?? '') ?>"
                                   placeholder="Deixe vazio para ilimitado">
                            <div class="form-text">Limite total de todas participações</div>
                        </div>
                    </div>
                    
                    <div class="neomorph-inset p-3">
                        <h6 class="mb-3">📅 Período</h6>
                        
                        <div class="mb-3">
                            <label for="start_at" class="form-label">Data/Hora Início</label>
                            <input type="datetime-local" class="form-control" id="start_at" name="start_at"
                                   value="<?= ValidationHelper::escapeHtml($_POST['start_at'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_at" class="form-label">Data/Hora Fim</label>
                            <input type="datetime-local" class="form-control" id="end_at" name="end_at"
                                   value="<?= ValidationHelper::escapeHtml($_POST['end_at'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: rgba(255, 22, 71, 0.2);">
            
            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/admin/raffles" class="btn btn-outline-primary">
                    ← Voltar
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    💾 Criar Sorteio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('is_paid').addEventListener('change', function() {
    document.getElementById('minValueGroup').style.display = this.checked ? 'block' : 'none';
});

// Mostrar campo se já estiver marcado
if (document.getElementById('is_paid').checked) {
    document.getElementById('minValueGroup').style.display = 'block';
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>