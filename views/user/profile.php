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
    <!-- Header da Página -->
    <div class="profile-header mb-5">
        <h1 class="profile-title">Meu Perfil</h1>
        <p class="profile-subtitle">Gerencie suas informações e acompanhe suas estatísticas</p>
    </div>
    
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
        <!-- Coluna Esquerda: Informações da Conta -->
        <div class="col-lg-4">
            <!-- Card: Informações do Usuário -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h6 class="profile-card-title">Informações da Conta</h6>
                </div>
                
                <div class="text-center mb-4">
                    <div class="profile-avatar-wrapper">
                        <img src="<?= ValidationHelper::escapeHtml($user['avatar_url']) ?>" 
                             class="profile-avatar" 
                             alt="Avatar">
                    </div>
                    <h4 class="profile-name"><?= ValidationHelper::escapeHtml($user['name']) ?></h4>
                    <p class="profile-email"><?= ValidationHelper::escapeHtml($user['email']) ?></p>
                </div>
                
                <div class="profile-info-list">
                    <div class="profile-info-item">
                        <span class="profile-info-label">ID do Usuário</span>
                        <span class="profile-info-value profile-id">#<?= $user['id'] ?></span>
                    </div>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Tipo de Conta</span>
                        <span class="badge profile-badge-<?= $user['role'] === 'admin' ? 'admin' : 'user' ?>">
                            <?= $user['role'] === 'admin' ? 'Administrador' : 'Usuário' ?>
                        </span>
                    </div>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Membro desde</span>
                        <span class="profile-info-value">
                            <?php 
                            $createdDate = $user['created_at'] ?? date('Y-m-d H:i:s');
                            echo date('d/m/Y', strtotime($createdDate));
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="profile-notice">
                    <div class="profile-notice-icon">i</div>
                    <div class="profile-notice-text">
                        <strong>Informação:</strong> Dados da conta Google não podem ser alterados aqui.
                    </div>
                </div>
            </div>
            
            <!-- Card: Estatísticas Rápidas -->
            <div class="profile-card mt-4">
                <div class="profile-card-header">
                    <h6 class="profile-card-title">Suas Estatísticas</h6>
                </div>
                
                <div class="profile-stats">
                    <div class="profile-stat-item">
                        <div class="profile-stat-value profile-stat-primary">
                            <?php
                            $totalEntries = \App\Models\RaffleEntry::countByUserId($user['id']);
                            echo $totalEntries;
                            ?>
                        </div>
                        <div class="profile-stat-label">Total de Participações</div>
                    </div>
                    
                    <div class="profile-stat-item">
                        <div class="profile-stat-value profile-stat-success">
                            <?php
                            $sql = "SELECT COUNT(*) as count FROM raffle_winners w 
                                    JOIN raffle_entries e ON w.raffle_entry_id = e.id 
                                    WHERE e.user_id = ?";
                            $result = \App\Helpers\DatabaseHelper::fetchOne($sql, [$user['id']]);
                            echo $result['count'] ?? 0;
                            ?>
                        </div>
                        <div class="profile-stat-label">Sorteios Vencidos</div>
                    </div>
                    
                    <div class="profile-stat-item">
                        <div class="profile-stat-value profile-stat-warning">
                            <?php
                            $sql = "SELECT COUNT(*) as count FROM raffle_entries 
                                    WHERE user_id = ? AND status = 'pending'";
                            $result = \App\Helpers\DatabaseHelper::fetchOne($sql, [$user['id']]);
                            echo $result['count'] ?? 0;
                            ?>
                        </div>
                        <div class="profile-stat-label">Aguardando Aprovação</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Coluna Direita: Edição de Perfil -->
        <div class="col-lg-8">
            <!-- Card: Editar Informações -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h6 class="profile-card-title">Editar Informações</h6>
                </div>
                
                <form method="POST" action="<?= BASE_URL ?>/user/update-profile">
                    <?= CsrfHelper::getTokenField() ?>
                    
                    <div class="profile-form-group">
                        <label for="steam_tradelink" class="profile-form-label">
                            Steam Trade Link
                            <span class="profile-required">*</span>
                        </label>
                        <input 
                            type="url" 
                            class="profile-form-control" 
                            id="steam_tradelink" 
                            name="steam_tradelink"
                            value="<?= ValidationHelper::escapeHtml($user['steam_tradelink'] ?? '') ?>"
                            placeholder="https://steamcommunity.com/tradeoffer/new/?partner=..."
                            required>
                        <div class="profile-form-help">
                            Onde encontrar: Steam → Inventário → Trade Offers → Trade URL
                        </div>
                    </div>
                    
                    <div class="profile-form-group">
                        <label for="phone" class="profile-form-label">
                            Telefone (com DDI)
                            <span class="profile-required">*</span>
                        </label>
                        <input 
                            type="tel" 
                            class="profile-form-control" 
                            id="phone" 
                            name="phone"
                            value="<?= ValidationHelper::escapeHtml($user['phone'] ?? '') ?>"
                            placeholder="+55 41 99999-9999"
                            required>
                        <div class="profile-form-help">
                            Formato: +DDI DDD Número (Ex: +55 41 99999-9999)
                        </div>
                    </div>
                    
                    <div class="profile-form-actions">
                        <button type="submit" class="profile-btn profile-btn-primary">
                            Salvar Alterações
                        </button>
                        <a href="<?= BASE_URL ?>/user/home" class="profile-btn profile-btn-secondary">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Card: Atividades Recentes -->
            <div class="profile-card mt-4">
                <div class="profile-card-header">
                    <h6 class="profile-card-title">Atividade Recente</h6>
                </div>
                
                <?php
                // Buscar últimas 5 participações
                $sql = "SELECT e.*, r.title as raffle_title, r.status as raffle_status
                        FROM raffle_entries e
                        JOIN raffles r ON e.raffle_id = r.id
                        WHERE e.user_id = ?
                        ORDER BY e.created_at DESC
                        LIMIT 5";
                $recentEntries = \App\Helpers\DatabaseHelper::fetchAll($sql, [$user['id']]);
                ?>
                
                <?php if (empty($recentEntries)): ?>
                    <div class="profile-empty-state">
                        <div class="profile-empty-icon">📋</div>
                        <p class="profile-empty-text">Nenhuma atividade recente</p>
                        <a href="<?= BASE_URL ?>/user/home" class="profile-btn profile-btn-primary profile-btn-sm">
                            Ver Sorteios Ativos
                        </a>
                    </div>
                <?php else: ?>
                    <div class="profile-activity-list">
                        <?php foreach ($recentEntries as $entry): ?>
                            <div class="profile-activity-item">
                                <div class="profile-activity-content">
                                    <div class="profile-activity-title">
                                        <?= ValidationHelper::escapeHtml($entry['raffle_title']) ?>
                                    </div>
                                    <div class="profile-activity-meta">
                                        <?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?>
                                    </div>
                                </div>
                                <div class="profile-activity-badge">
                                    <span class="badge profile-badge-<?= $entry['status'] ?>">
                                        <?php
                                        $statusText = [
                                            'approved' => 'Aprovado',
                                            'pending' => 'Pendente',
                                            'rejected' => 'Rejeitado'
                                        ];
                                        echo $statusText[$entry['status']] ?? $entry['status'];
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="profile-card-footer">
                        <a href="<?= BASE_URL ?>/user/my-entries" class="profile-link">
                            Ver todas as participações →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* ========== PROFILE PAGE STYLES ========== */

.profile-header {
    text-align: center;
    margin-bottom: 40px;
}

.profile-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ff1647, #cc1138);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 10px;
}

.profile-subtitle {
    color: #8b93a7;
    font-size: 1.1rem;
    margin: 0;
}

/* Cards */
.profile-card {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border-radius: 16px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.profile-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255, 22, 71, 0.15);
}

.profile-card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
}

.profile-card-footer {
    padding: 16px 24px;
    border-top: 1px solid rgba(255, 22, 71, 0.15);
    background: rgba(0, 0, 0, 0.2);
}

/* Avatar */
.profile-avatar-wrapper {
    margin: 30px auto 20px;
    position: relative;
    width: 120px;
    height: 120px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid rgba(255, 22, 71, 0.3);
    box-shadow: 0 0 30px rgba(255, 22, 71, 0.4);
    transition: all 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
    border-color: rgba(255, 22, 71, 0.6);
}

.profile-name {
    color: #ffffff;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 15px 0 5px;
}

.profile-email {
    color: #8b93a7;
    font-size: 1rem;
    margin: 0;
}

/* Info List */
.profile-info-list {
    padding: 24px;
}

.profile-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 22, 71, 0.1);
}

.profile-info-item:last-child {
    border-bottom: none;
}

.profile-info-label {
    color: #8b93a7;
    font-size: 0.95rem;
    font-weight: 500;
}

.profile-info-value {
    color: #ffffff;
    font-weight: 600;
}

.profile-id {
    background: rgba(255, 22, 71, 0.15);
    padding: 4px 12px;
    border-radius: 8px;
    color: #ff1647;
    font-family: 'Courier New', monospace;
}

/* Badges */
.profile-badge-admin {
    background: linear-gradient(135deg, #ff1647, #cc1138);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}

.profile-badge-user {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}

.profile-badge-approved {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
}

.profile-badge-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
}

.profile-badge-rejected {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Notice Box */
.profile-notice {
    margin: 24px;
    padding: 16px;
    background: rgba(59, 130, 246, 0.1);
    border-left: 4px solid #3b82f6;
    border-radius: 8px;
    display: flex;
    gap: 12px;
    align-items: start;
}

.profile-notice-icon {
    width: 24px;
    height: 24px;
    background: #3b82f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-weight: 700;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.profile-notice-text {
    color: #60a5fa;
    font-size: 0.9rem;
    line-height: 1.5;
}

/* Stats */
.profile-stats {
    padding: 24px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.profile-stat-item {
    text-align: center;
    padding: 20px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    border: 1px solid rgba(255, 22, 71, 0.1);
    transition: all 0.3s ease;
}

.profile-stat-item:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 22, 71, 0.3);
}

.profile-stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.profile-stat-primary {
    color: #ff1647;
    text-shadow: 0 0 20px rgba(255, 22, 71, 0.4);
}

.profile-stat-success {
    color: #22c55e;
    text-shadow: 0 0 20px rgba(34, 197, 94, 0.4);
}

.profile-stat-warning {
    color: #f59e0b;
    text-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
}

.profile-stat-label {
    color: #8b93a7;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

/* Forms */
.profile-form-group {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255, 22, 71, 0.1);
}

.profile-form-group:last-of-type {
    border-bottom: none;
}

.profile-form-label {
    display: block;
    color: #ffffff;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.profile-required {
    color: #ff1647;
    margin-left: 4px;
}

.profile-form-control {
    width: 100%;
    padding: 12px 16px;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 22, 71, 0.2);
    border-radius: 10px;
    color: #ffffff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.profile-form-control:focus {
    outline: none;
    border-color: #ff1647;
    box-shadow: 0 0 0 3px rgba(255, 22, 71, 0.2);
    background: rgba(0, 0, 0, 0.5);
}

.profile-form-control::placeholder {
    color: #5a6275;
}

.profile-form-help {
    margin-top: 8px;
    color: #8b93a7;
    font-size: 0.85rem;
}

.profile-form-actions {
    padding: 24px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Buttons */
.profile-btn {
    padding: 12px 28px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.profile-btn-primary {
    background: linear-gradient(135deg, #ff1647, #cc1138);
    color: #ffffff;
    flex: 1;
}

.profile-btn-primary:hover {
    background: linear-gradient(135deg, #ff2d5c, #d91f47);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255, 22, 71, 0.4);
}

.profile-btn-secondary {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.2);
    color: #ffffff;
}

.profile-btn-secondary:hover {
    border-color: rgba(255, 22, 71, 0.4);
    transform: translateY(-2px);
}

.profile-btn-sm {
    padding: 8px 20px;
    font-size: 0.9rem;
}

/* Activity List */
.profile-activity-list {
    padding: 0;
}

.profile-activity-item {
    padding: 16px 24px;
    border-bottom: 1px solid rgba(255, 22, 71, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s ease;
}

.profile-activity-item:hover {
    background: rgba(255, 22, 71, 0.05);
}

.profile-activity-item:last-child {
    border-bottom: none;
}

.profile-activity-content {
    flex: 1;
}

.profile-activity-title {
    color: #ffffff;
    font-weight: 600;
    margin-bottom: 4px;
}

.profile-activity-meta {
    color: #8b93a7;
    font-size: 0.85rem;
}

/* Empty State */
.profile-empty-state {
    padding: 60px 24px;
    text-align: center;
}

.profile-empty-icon {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 20px;
}

.profile-empty-text {
    color: #8b93a7;
    margin-bottom: 20px;
}

/* Link */
.profile-link {
    color: #ff1647;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.2s ease;
}

.profile-link:hover {
    color: #ff2d5c;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-title {
        font-size: 1.8rem;
    }
    
    .profile-subtitle {
        font-size: 1rem;
    }
    
    .profile-form-actions {
        flex-direction: column;
    }
    
    .profile-btn {
        width: 100%;
    }
    
    .profile-stats {
        grid-template-columns: 1fr;
    }
    
    .profile-activity-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>