<?php 
use App\Helpers\ValidationHelper;
use App\Helpers\CsrfHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">📋 Gerenciar Sorteios</h1>
        <a href="<?= BASE_URL ?>/admin/create-raffle" class="btn btn-primary">
            ➕ Novo Sorteio
        </a>
    </div>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <!-- Filtros -->
    <div class="neomorph-card mb-4">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filtrar por Status</label>
                <select class="form-control" id="statusFilter" onchange="filterByStatus(this.value)">
                    <option value="">Todos</option>
                    <option value="draft" <?= (isset($_GET['status']) && $_GET['status'] === 'draft') ? 'selected' : '' ?>>Rascunho</option>
                    <option value="active" <?= (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : '' ?>>Ativo</option>
                    <option value="closed" <?= (isset($_GET['status']) && $_GET['status'] === 'closed') ? 'selected' : '' ?>>Encerrado</option>
                </select>
            </div>
        </div>
    </div>
    
    <?php if (empty($raffles)): ?>
        <div class="alert alert-info">
            Nenhum sorteio encontrado. Crie o primeiro!
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Participações</th>
                        <th>Pendentes</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($raffles as $raffle): ?>
                        <tr>
                            <td><?= $raffle['id'] ?></td>
                            <td>
                                <strong><?= ValidationHelper::escapeHtml($raffle['title']) ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $raffle['is_paid'] ? 'bg-danger' : 'bg-success' ?>">
                                    <?= $raffle['is_paid'] ? 'Com Comprovante' : 'Gratuito' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $raffle['status'] === 'active' ? 'success' : ($raffle['status'] === 'draft' ? 'secondary' : 'danger') ?>">
                                    <?= strtoupper($raffle['status']) ?>
                                </span>
                            </td>
                            <td><?= $raffle['entries_count'] ?? 0 ?></td>
                            <td>
                                <?php if ($raffle['pending_count'] > 0): ?>
                                    <span class="badge bg-warning"><?= $raffle['pending_count'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($raffle['created_at'])) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/edit-raffle/<?= $raffle['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    ✏️ Editar
                                </a>
                                <a href="<?= BASE_URL ?>/raffle/view/<?= $raffle['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    👁️ Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function filterByStatus(status) {
    const url = new URL(window.location.href);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>