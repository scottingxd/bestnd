<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <!-- Header da Página -->
    <div class="page-header mb-5">
        <h1 class="page-title">Dashboard Administrativo</h1>
        <p class="page-subtitle">Visão geral do sistema e ações rápidas</p>
    </div>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <!-- Estatísticas Principais -->
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="stats-card stats-card-primary">
                <div class="stats-card-icon">📊</div>
                <div class="stats-card-content">
                    <div class="stats-card-value"><?= $totalRaffles ?></div>
                    <div class="stats-card-label">Total de Sorteios</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="stats-card stats-card-success">
                <div class="stats-card-icon">✓</div>
                <div class="stats-card-content">
                    <div class="stats-card-value"><?= $activeRaffles ?></div>
                    <div class="stats-card-label">Sorteios Ativos</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="stats-card stats-card-warning">
                <div class="stats-card-icon">⏳</div>
                <div class="stats-card-content">
                    <div class="stats-card-value"><?= $pendingEntries ?></div>
                    <div class="stats-card-label">Participações Pendentes</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ações Rápidas -->
    <div class="admin-card mb-5">
        <div class="admin-card-header">
            <h6 class="admin-card-title">Ações Rápidas</h6>
        </div>
        <div class="admin-card-body">
            <div class="quick-actions">
                <a href="<?= BASE_URL ?>/admin/create-raffle" class="quick-action-btn quick-action-primary">
                    <span class="quick-action-icon">➕</span>
                    <span class="quick-action-text">Criar Sorteio</span>
                </a>
                
                <a href="<?= BASE_URL ?>/admin/raffles" class="quick-action-btn quick-action-secondary">
                    <span class="quick-action-icon">📋</span>
                    <span class="quick-action-text">Ver Sorteios</span>
                </a>
                
                <a href="<?= BASE_URL ?>/admin/entries" class="quick-action-btn quick-action-warning">
                    <span class="quick-action-icon">✓</span>
                    <span class="quick-action-text">Aprovar Participações</span>
                </a>
                
                <a href="<?= BASE_URL ?>/admin/logs" class="quick-action-btn quick-action-info">
                    <span class="quick-action-icon">📜</span>
                    <span class="quick-action-text">Ver Logs</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Sorteios Recentes -->
    <?php if (!empty($recentRaffles)): ?>
    <div class="admin-card">
        <div class="admin-card-header">
            <h6 class="admin-card-title">Sorteios Recentes</h6>
        </div>
        <div class="admin-card-body p-0">
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Status</th>
                            <th>Tipo</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentRaffles as $raffle): ?>
                        <tr>
                            <td class="admin-table-title">
                                <?= ValidationHelper::escapeHtml($raffle['title']) ?>
                            </td>
                            <td>
                                <span class="badge badge-raffle-<?= $raffle['status'] ?>">
                                    <?= strtoupper($raffle['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-type-<?= $raffle['is_paid'] ? 'paid' : 'free' ?>">
                                    <?= $raffle['is_paid'] ? 'Pago' : 'Grátis' ?>
                                </span>
                            </td>
                            <td class="admin-table-date">
                                <?= date('d/m/Y', strtotime($raffle['created_at'])) ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/edit-raffle/<?= $raffle['id'] ?>" 
                                   class="admin-table-btn">
                                    Editar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="admin-card-footer">
            <a href="<?= BASE_URL ?>/admin/raffles" class="admin-link">
                Ver todos os sorteios →
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* ========== ADMIN DASHBOARD STYLES ========== */

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

/* Stats Cards */
.stats-card {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border-radius: 16px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    padding: 28px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
}

.stats-card-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 2rem;
    flex-shrink: 0;
}

.stats-card-primary .stats-card-icon {
    background: linear-gradient(135deg, rgba(255, 22, 71, 0.2), rgba(204, 17, 56, 0.1));
    color: #ff1647;
}

.stats-card-success .stats-card-icon {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.1));
    color: #22c55e;
}

.stats-card-warning .stats-card-icon {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.1));
    color: #f59e0b;
}

.stats-card-content {
    flex: 1;
}

.stats-card-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 5px;
}

.stats-card-primary .stats-card-value {
    color: #ff1647;
    text-shadow: 0 0 20px rgba(255, 22, 71, 0.3);
}

.stats-card-success .stats-card-value {
    color: #22c55e;
    text-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
}

.stats-card-warning .stats-card-value {
    color: #f59e0b;
    text-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
}

.stats-card-label {
    color: #8b93a7;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Admin Cards */
.admin-card {
    background: linear-gradient(145deg, #1e242e, #181d26);
    border-radius: 16px;
    border: 1px solid rgba(255, 22, 71, 0.15);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.admin-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255, 22, 71, 0.15);
}

.admin-card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
}

.admin-card-body {
    padding: 24px;
}

.admin-card-footer {
    padding: 16px 24px;
    border-top: 1px solid rgba(255, 22, 71, 0.15);
    background: rgba(0, 0, 0, 0.2);
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 24px 20px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 22, 71, 0.15);
}

.quick-action-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 1.5rem;
}

.quick-action-text {
    font-weight: 600;
    font-size: 0.95rem;
    text-align: center;
}

.quick-action-primary {
    background: linear-gradient(135deg, rgba(255, 22, 71, 0.1), rgba(204, 17, 56, 0.05));
}

.quick-action-primary .quick-action-icon {
    background: linear-gradient(135deg, #ff1647, #cc1138);
    color: #ffffff;
}

.quick-action-primary .quick-action-text {
    color: #ff1647;
}

.quick-action-primary:hover {
    background: linear-gradient(135deg, rgba(255, 22, 71, 0.15), rgba(204, 17, 56, 0.1));
    border-color: rgba(255, 22, 71, 0.3);
    transform: translateY(-4px);
}

.quick-action-secondary {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
}

.quick-action-secondary .quick-action-icon {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #ffffff;
}

.quick-action-secondary .quick-action-text {
    color: #60a5fa;
}

.quick-action-secondary:hover {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.1));
    border-color: rgba(59, 130, 246, 0.3);
    transform: translateY(-4px);
}

.quick-action-warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05));
}

.quick-action-warning .quick-action-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff;
}

.quick-action-warning .quick-action-text {
    color: #fbbf24;
}

.quick-action-warning:hover {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.1));
    border-color: rgba(245, 158, 11, 0.3);
    transform: translateY(-4px);
}

.quick-action-info {
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.1), rgba(75, 85, 99, 0.05));
}

.quick-action-info .quick-action-icon {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: #ffffff;
}

.quick-action-info .quick-action-text {
    color: #9ca3af;
}

.quick-action-info:hover {
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.15), rgba(75, 85, 99, 0.1));
    border-color: rgba(107, 114, 128, 0.3);
    transform: translateY(-4px);
}

/* Admin Table */
.admin-table-wrapper {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead th {
    padding: 16px 24px;
    text-align: left;
    font-weight: 700;
    font-size: 0.85rem;
    color: #8b93a7;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(255, 22, 71, 0.15);
    background: rgba(0, 0, 0, 0.2);
}

.admin-table tbody tr {
    border-bottom: 1px solid rgba(255, 22, 71, 0.08);
    transition: background 0.2s ease;
}

.admin-table tbody tr:hover {
    background: rgba(255, 22, 71, 0.05);
}

.admin-table tbody td {
    padding: 16px 24px;
    color: #ffffff;
}

.admin-table-title {
    font-weight: 600;
}

.admin-table-date {
    color: #8b93a7;
    font-size: 0.9rem;
}

.admin-table-btn {
    padding: 6px 16px;
    background: linear-gradient(145deg, #1e242e, #181d26);
    border: 1px solid rgba(255, 22, 71, 0.2);
    border-radius: 8px;
    color: #ffffff;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-block;
}

.admin-table-btn:hover {
    border-color: rgba(255, 22, 71, 0.4);
    color: #ff1647;
}

/* Badges */
.badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-raffle-active {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #ffffff;
}

.badge-raffle-draft {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: #ffffff;
}

.badge-raffle-closed {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #ffffff;
}

.badge-type-free {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #ffffff;
}

.badge-type-paid {
    background: linear-gradient(135deg, #ff1647, #cc1138);
    color: #ffffff;
}

/* Link */
.admin-link {
    color: #ff1647;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.2s ease;
}

.admin-link:hover {
    color: #ff2d5c;
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.8rem;
    }
    
    .page-subtitle {
        font-size: 1rem;
    }
    
    .stats-card {
        padding: 20px;
    }
    
    .stats-card-value {
        font-size: 2rem;
    }
    
    .quick-actions {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .admin-table thead th,
    .admin-table tbody td {
        padding: 12px 16px;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>