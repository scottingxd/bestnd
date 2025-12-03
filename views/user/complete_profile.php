<?php 
use App\Helpers\ValidationHelper;
use App\Helpers\CsrfHelper;
include __DIR__ . '/../partials/header.php'; 
?>

<div class="modal fade show d-block" style="background-color: rgba(0,0,0,0.8);" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete seu Perfil</h5>
            </div>
            <div class="modal-body">
                <p class="text-secondary mb-4">
                    Antes de participar dos sorteios, precisamos que você complete seu perfil.
                </p>
                
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= ValidationHelper::escapeHtml($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>/user/complete-profile">
                    <?= CsrfHelper::getTokenField() ?>
                    
                    <div class="mb-3">
                        <label for="steam_tradelink" class="form-label">
                            Steam Trade Link <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="url" 
                            class="form-control" 
                            id="steam_tradelink" 
                            name="steam_tradelink"
                            placeholder="https://steamcommunity.com/tradeoffer/new/?partner=..."
                            required
                            value="<?= ValidationHelper::escapeHtml($_POST['steam_tradelink'] ?? '') ?>">
                        <div class="form-text">
                            Onde encontrar: Steam → Inventário → Trade Offers → Trade URL
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            Telefone (com DDI) <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="tel" 
                            class="form-control" 
                            id="phone" 
                            name="phone"
                            placeholder="+55 41 99999-9999"
                            required
                            value="<?= ValidationHelper::escapeHtml($_POST['phone'] ?? '') ?>">
                        <div class="form-text">
                            Formato: +DDI DDD Número (Ex: +55 41 99999-9999)
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        Salvar e Continuar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>