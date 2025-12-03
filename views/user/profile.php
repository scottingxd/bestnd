<?php 
use App\Helpers\ValidationHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\AuthHelper;
use App\Helpers\DatabaseHelper;

// Buscar dados completos DIRETO do banco
$sessionUser = AuthHelper::getUser();
$userId = $sessionUser['id'];

// Query direta para garantir
$sql = "SELECT * FROM users WHERE id = ?";
$user = DatabaseHelper::fetchOne($sql, [$userId]);

// Se ainda não tiver created_at, forçar valor padrão
if (!isset($user['created_at']) || empty($user['created_at'])) {
    $user['created_at'] = date('Y-m-d H:i:s');
}

include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <h1 class="mb-4" style="color: #ffffff; text-shadow: 0 0 20px rgba(255, 22, 71, 0.3);">
        👤 Meu Perfil
    </h1>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <?php if (isset($_SESSION['profile_errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['profile_errors'] as $error): ?>
                    <li><?= ValidationHelper::escapeHtml($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['profile_errors']); ?>
    <?php endif; ?>
    
    <div class="row g-4">
        <!-- Card de Informações do Google -->
        <div class="col-lg-4">
            <div class="neomorph-card text-center">
                <img src="<?= ValidationHelper::escapeHtml($user['avatar_url']) ?>" 
                     class="rounded-circle mb-3" 
                     width="120" 
                     height="120" 
                     alt="Avatar"
                     style="border: 4px solid #ff1647; box-shadow: 0 0 20px rgba(255, 22, 71, 0.4);">
                
                <h4 class="mb-2" style="color: #ffffff;"><?= ValidationHelper::escapeHtml($user['name']) ?></h4>
                <p class="mb-4" style="color: #b0b0b0;"><?= ValidationHelper::escapeHtml($user['email']) ?></p>
                
                <div class="neomorph-inset p-3 mb-3 text-start">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span style="color: #b0b0b0; font-weight: 500;">ID:</span>
                        <code style="background: rgba(255, 22, 71, 0.1); padding: 4px 12px; border-radius: 8px; color: #ff1647; font-weight: 600;">
                            #<?= $user['id'] ?>
                        </code>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span style="color: #b0b0b0; font-weight: 500;">Role:</span>
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>" style="font-size: 0.85rem; padding: 6px 14px;">
                            <?= strtoupper($user['role']) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="color: #b0b0b0; font-weight: 500;">Membro desde:</span>
                        <small style="color: #ffffff; font-weight: 600; background: rgba(255, 22, 71, 0.1); padding: 4px 12px; border-radius: 6px;">
                            <?php 
                            $createdDate = $user['created_at'] ?? date('Y-m-d H:i:s');
                            echo date('d/m/Y', strtotime($createdDate));
                            ?>
                        </small>
                    </div>
                </div>
                
                <div class="alert alert-info mb-0" style="background: rgba(59, 130, 246, 0.15); border-left: 4px solid #3b82f6; color: #60a5fa;">
                    <small>
                        <strong>ℹ️ Info:</strong> Dados do Google não podem ser editados aqui.
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Card de Edição de Perfil -->
        <div class="col-lg-8">
            <div class="neomorph-card">
                <h5 class="mb-4" style="color: #ffffff; display: flex; align-items: center; gap: 10px;">
                    <span style="color: #ff1647;">✏️</span> Editar Informações
                </h5>
                
                <form method="POST" action="<?= BASE_URL ?>/user/update-profile">
                    <?= CsrfHelper::getTokenField() ?>
                    
                    <div class="mb-4">
                        <label for="steam_tradelink" class="form-label" style="color: #ffffff; font-weight: 500; margin-bottom: 12px;">
                            🎮 Steam Trade Link <span style="color: #ff1647;">*</span>
                        </label>
                        <input 
                            type="url" 
                            class="form-control" 
                            id="steam_tradelink" 
                            name="steam_tradelink"
                            value="<?= ValidationHelper::escapeHtml($user['steam_tradelink'] ?? '') ?>"
                            placeholder="https://steamcommunity.com/tradeoffer/new/?partner=..."
                            style="color: #ffffff !important; font-size: 0.95rem;"
                            required>
                        <div class="form-text" style="color: #b0b0b0; margin-top: 8px;">
                            📍 Onde encontrar: Steam → Inventário → Trade Offers → Trade URL
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="form-label" style="color: #ffffff; font-weight: 500; margin-bottom: 12px;">
                            📱 Telefone (com DDI) <span style="color: #ff1647;">*</span>
                        </label>
                        <input 
                            type="tel" 
                            class="form-control" 
                            id="phone" 
                            name="phone"
                            value="<?= ValidationHelper::escapeHtml($user['phone'] ?? '') ?>"
                            placeholder="+55 41 99999-9999"
                            style="color: #ffffff !important; font-size: 0.95rem;"
                            required>
                        <div class="form-text" style="color: #b0b0b0; margin-top: 8px;">
                            📍 Formato: +DDI DDD Número (Ex: +55 41 99999-9999)
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                        <button type="submit" class="neomorph-button-primary flex-grow-1" style="padding: 14px 28px; font-size: 1rem; font-weight: 600;">
                            💾 Salvar Alterações
                        </button>
                        <a href="<?= BASE_URL ?>/user/home" class="neomorph-button" style="padding: 14px 28px; font-size: 1rem; font-weight: 600; text-align: center; text-decoration: none; color: #ffffff;">
                            ← Voltar
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Estatísticas -->
            <div class="neomorph-card mt-4">
                <h5 class="mb-4" style="color: #ffffff; display: flex; align-items: center; gap: 10px;">
                    <span style="color: #ff1647;">📊</span> Suas Estatísticas
                </h5>
                
                <div class="row g-3 text-center">
                    <div class="col-md-4 col-12">
                        <div class="neomorph-inset p-4">
                            <h2 class="mb-2" style="color: #ff1647; font-weight: 700; text-shadow: 0 0 15px rgba(255, 22, 71, 0.4);">
                                <?php
                                $totalEntries = \App\Models\RaffleEntry::countByUserId($user['id']);
                                echo $totalEntries;
                                ?>
                            </h2>
                            <small style="color: #b0b0b0; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                Participações
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-12">
                        <div class="neomorph-inset p-4">
                            <h2 class="mb-2" style="color: #22c55e; font-weight: 700; text-shadow: 0 0 15px rgba(34, 197, 94, 0.4);">
                                <?php
                                $sql = "SELECT COUNT(*) as count FROM raffle_winners w 
                                        JOIN raffle_entries e ON w.raffle_entry_id = e.id 
                                        WHERE e.user_id = ?";
                                $result = \App\Helpers\DatabaseHelper::fetchOne($sql, [$user['id']]);
                                echo $result['count'] ?? 0;
                                ?>
                            </h2>
                            <small style="color: #b0b0b0; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                Vitórias
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-12">
                        <div class="neomorph-inset p-4">
                            <h2 class="mb-2" style="color: #f59e0b; font-weight: 700; text-shadow: 0 0 15px rgba(245, 158, 11, 0.4);">
                                <?php
                                $sql = "SELECT COUNT(*) as count FROM raffle_entries 
                                        WHERE user_id = ? AND status = 'pending'";
                                $result = \App\Helpers\DatabaseHelper::fetchOne($sql, [$user['id']]);
                                echo $result['count'] ?? 0;
                                ?>
                            </h2>
                            <small style="color: #b0b0b0; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                Pendentes
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Melhorias específicas para esta página */
.form-control {
    color: #ffffff !important;
    background: #000000 !important;
    border: none !important;
    transition: all 0.3s ease;
}

.form-control:focus {
    color: #ffffff !important;
    box-shadow: 
        inset 4px 4px 8px rgba(0, 0, 0, 0.9),
        inset -4px -4px 8px rgba(255, 22, 71, 0.05),
        0 0 0 3px rgba(255, 22, 71, 0.3) !important;
}

.form-control::placeholder {
    color: #666666 !important;
    opacity: 1;
}

.form-label {
    color: #ffffff;
    font-weight: 500;
}

.form-text {
    color: #b0b0b0;
}

/* Responsivo Mobile */
@media (max-width: 768px) {
    .neomorph-card {
        padding: 20px;
    }
    
    h1 {
        font-size: 1.75rem;
    }
    
    h5 {
        font-size: 1.1rem;
    }
    
    .neomorph-inset {
        font-size: 0.9rem;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>