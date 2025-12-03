<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <h1 class="mb-4">📜 Logs de Auditoria</h1>
    
    <div class="neomorph-card">
        <?php if (empty($logs)): ?>
            <div class="alert alert-info">
                Nenhum log de auditoria registrado ainda.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>IP</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= $log['id'] ?></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                <td>
                                    <?php if ($log['user_name']): ?>
                                        <strong><?= ValidationHelper::escapeHtml($log['user_name']) ?></strong><br>
                                        <small class="text-muted"><?= ValidationHelper::escapeHtml($log['user_email']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Sistema</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        str_contains($log['action'], 'created') ? 'success' : 
                                        (str_contains($log['action'], 'deleted') ? 'danger' : 
                                        (str_contains($log['action'], 'winner') ? 'warning' : 'primary'))
                                    ?>">
                                        <?= ValidationHelper::escapeHtml($log['action']) ?>
                                    </span>
                                </td>
                                <td><code><?= ValidationHelper::escapeHtml($log['ip_address']) ?></code></td>
                                <td>
                                    <?php if ($log['details']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailsModal<?= $log['id'] ?>">
                                            Ver Detalhes
                                        </button>
                                        
                                        <!-- Modal -->
                                        <div class="modal fade" id="detailsModal<?= $log['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detalhes do Log #<?= $log['id'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <pre class="bg-dark p-3 rounded" style="max-height: 400px; overflow-y: auto;"><?= ValidationHelper::escapeHtml(json_encode(json_decode($log['details']), JSON_PRETTY_PRINT)) ?></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
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
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>