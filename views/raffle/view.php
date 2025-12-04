<?php 
use App\Helpers\ValidationHelper;
use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8">
            <!-- Imagem do Sorteio -->
            <?php if ($raffle['image_url']): ?>
                <div class="neomorph-card mb-4 p-0 overflow-hidden">
                    <img src="<?= ValidationHelper::escapeHtml($raffle['image_url']) ?>" 
                         class="w-100" 
                         style="max-height: 400px; object-fit: cover;"
                         alt="<?= ValidationHelper::escapeHtml($raffle['title']) ?>">
                </div>
            <?php endif; ?>
            
            <!-- Título e Badge -->
            <div class="neomorph-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="mb-0"><?= ValidationHelper::escapeHtml($raffle['title']) ?></h1>
                    <span class="badge bg-<?= $raffle['is_paid'] ? 'danger' : 'success' ?> fs-6">
                        <?= $raffle['is_paid'] ? '💳 COM COMPROVANTE' : '🎁 GRATUITO' ?>
                    </span>
                </div>
                
                <div class="d-flex gap-3 text-muted small">
                    <span>📊 Status: <strong class="text-<?= 
                        $raffle['status'] === 'active' ? 'success' : 
                        ($raffle['status'] === 'draft' ? 'secondary' : 'danger') 
                    ?>"><?= strtoupper($raffle['status']) ?></strong></span>
                    
                    <?php if ($raffle['start_at']): ?>
                        <span>📅 Início: <strong><?= date('d/m/Y H:i', strtotime($raffle['start_at'])) ?></strong></span>
                    <?php endif; ?>
                    
                    <?php if ($raffle['end_at']): ?>
                        <span>⏰ Fim: <strong><?= date('d/m/Y H:i', strtotime($raffle['end_at'])) ?></strong></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Descrição -->
            <div class="neomorph-card mb-4">
                <h5 class="mb-3">📝 Descrição</h5>
                <p class="mb-0" style="white-space: pre-wrap;"><?= ValidationHelper::escapeHtml($raffle['description']) ?></p>
            </div>
            
            <!-- Regras -->
            <div class="neomorph-card">
                <h5 class="mb-3">📋 Regras de Participação</h5>
                <div class="neomorph-inset p-3">
                    <ul class="mb-0">
                        <li>Você pode participar <strong>até 30 vezes</strong> neste sorteio</li>
                        <?php if ($raffle['is_paid']): ?>
                            <li>É necessário enviar comprovante de depósito</li>
                            <?php if ($raffle['min_value']): ?>
                                <li>Valor mínimo: <strong class="text-danger">R$ <?= number_format($raffle['min_value'], 2, ',', '.') ?></strong></li>
                            <?php endif; ?>
                            <li>Sua participação será <strong class="text-warning">analisada</strong> antes de ser aprovada</li>
                        <?php else: ?>
                            <li>Participação <strong class="text-success">gratuita</strong> e imediata</li>
                        <?php endif; ?>
                        <li>O sorteio será realizado de forma transparente pelo administrador</li>
                        <li>O vencedor será notificado e exibido publicamente</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Sidebar de Participação -->
        <div class="col-lg-4">
            <div class="neomorph-card sticky-top" style="top: 20px;">
                <h5 class="mb-4">🎯 Participar do Sorteio</h5>
                
                <?php if (!AuthHelper::isLogged()): ?>
                    <!-- Não Logado -->
                    <div class="text-center py-4">
                        <div class="mb-3" style="font-size: 3rem; opacity: 0.3;">🔒</div>
                        <p class="text-muted mb-3">Faça login para participar</p>
                        <a href="<?= BASE_URL ?>/auth/login" class="neomorph-button-primary w-100">
                            Fazer Login
                        </a>
                    </div>
                    
                <?php elseif ($raffle['status'] !== 'active'): ?>
                    <!-- Sorteio Inativo -->
                    <div class="alert alert-warning mb-0">
                        ⚠️ Este sorteio não está mais ativo.
                    </div>
                    
                <?php elseif (!$canParticipate): ?>
                    <!-- Limite Atingido -->
                    <div class="alert alert-info mb-3">
                        <strong>🎯 Limite Atingido!</strong><br>
                        Você já atingiu o limite de <strong>30 participações</strong> neste sorteio.
                    </div>
                    <div class="neomorph-inset p-3 text-center">
                        <div class="display-6 text-danger mb-2"><?= $userEntriesCount ?> / 30</div>
                        <small class="text-muted">Suas participações</small>
                    </div>
                    
                <?php else: ?>
                    <!-- Pode Participar -->
                    <div class="neomorph-inset p-3 mb-4 text-center">
                        <div class="text-muted small mb-1">Suas participações</div>
                        <div class="h4 mb-0">
                            <span class="text-danger"><?= $userEntriesCount ?></span>
                            <span class="text-muted"> / 30</span>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-danger" 
                                 role="progressbar" 
                                 style="width: <?= ($userEntriesCount / 30) * 100 ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($raffle['is_paid'] == 0): ?>
                        <!-- Sorteio Gratuito -->
                        <form method="POST" action="<?= BASE_URL ?>/raffle/participate">
                            <?= CsrfHelper::getTokenField() ?>
                            <input type="hidden" name="raffle_id" value="<?= $raffle['id'] ?>">
                            
                            <button type="submit" class="neomorph-button-primary w-100 btn-lg mb-3">
                                🎁 Participar Gratuitamente
                            </button>
                            
                            <div class="alert alert-success mb-0">
                                <small>✅ Participação aprovada automaticamente!</small>
                            </div>
                        </form>
                        
                    <?php else: ?>
                        <!-- Sorteio Com Comprovante -->
                        <form method="POST" action="<?= BASE_URL ?>/raffle/participate" enctype="multipart/form-data">
                            <?= CsrfHelper::getTokenField() ?>
                            <input type="hidden" name="raffle_id" value="<?= $raffle['id'] ?>">
                            
                            <div class="mb-3">
                                <label for="amount" class="form-label">
                                    💰 Valor (R$) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control" 
                                       id="amount" 
                                       name="amount" 
                                       required
                                       <?= $raffle['min_value'] ? 'min="' . $raffle['min_value'] . '"' : '' ?>
                                       placeholder="<?= $raffle['min_value'] ? 'Mín: R$ ' . number_format($raffle['min_value'], 2, ',', '.') : 'Ex: 50.00' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="deposit_date" class="form-label">
                                    📅 Data do Depósito <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="deposit_date" 
                                       name="deposit_date" 
                                       required
                                       max="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="proof_image" class="form-label">
                                    📄 Comprovante <span class="text-danger">*</span>
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="proof_image" 
                                       name="proof_image" 
                                       accept="image/jpeg,image/jpg,image/png,image/webp" 
                                       required>
                                <div class="form-text">
                                    JPG, PNG ou WEBP (máx. 2MB)
                                </div>
                            </div>
                            
                            <button type="submit" class="neomorph-button-primary w-100 btn-lg mb-3">
                                💳 Enviar Participação
                            </button>
                            
                            <div class="alert alert-warning mb-0">
                                <small>⏳ Participação será analisada pelo admin</small>
                            </div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Vencedor -->
            <?php if ($raffle['status'] === 'closed' && $winner): ?>
                <div class="neomorph-card mt-4 text-center">
                    <h5 class="mb-3">🏆 Vencedor</h5>
                    <div class="mb-3">
                        <img src="<?= ValidationHelper::escapeHtml($winner['winner_avatar']) ?>" 
                             class="rounded-circle glow-animation" 
                             width="80" 
                             height="80"
                             style="border: 3px solid #ff1647;">
                    </div>
                    <h5 class="text-danger mb-2">
                        <?= ValidationHelper::escapeHtml($winner['winner_name']) ?>
                    </h5>
                    <p class="text-muted small mb-0">
                        Sorteado em<br>
                        <strong><?= date('d/m/Y H:i', strtotime($winner['selected_at'])) ?></strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>