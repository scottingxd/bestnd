<?php 
use App\Helpers\ValidationHelper;
use App\Helpers\CsrfHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <h1 class="mb-4">✅ Participações Pendentes</h1>
    
    <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
    
    <?php if (empty($entries)): ?>
        <div class="alert alert-info">Nenhuma participação pendente de aprovação.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Sorteio</th>
                        <th>Valor</th>
                        <th>Data Depósito</th>
                        <th>Comprovante</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= ValidationHelper::escapeHtml($entry['user_avatar']) ?>" class="rounded-circle me-2" width="32" height="32">
                                    <div>
                                        <div><?= ValidationHelper::escapeHtml($entry['user_name']) ?></div>
                                        <small class="text-muted"><?= ValidationHelper::escapeHtml($entry['user_email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= ValidationHelper::escapeHtml($entry['raffle_title']) ?></td>
                            <td>R$ <?= number_format($entry['amount'], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y', strtotime($entry['deposit_date'])) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#proofModal<?= $entry['id'] ?>">
                                    Ver Imagem
                                </button>
                                
                                <div class="modal fade" id="proofModal<?= $entry['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Comprovante</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="<?= UPLOAD_URL ?>/<?= ValidationHelper::escapeHtml($entry['proof_image_path']) ?>" class="img-fluid" alt="Comprovante">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="<?= BASE_URL ?>/admin/approve-entry" class="d-inline">
                                    <?= CsrfHelper::getTokenField() ?>
                                    <input type="hidden" name="entry_id" value="<?= $entry['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Aprovar esta participação?')">✓ Aprovar</button>
                                </form>
                                
                                <form method="POST" action="<?= BASE_URL ?>/admin/reject-entry" class="d-inline">
                                    <?= CsrfHelper::getTokenField() ?>
                                    <input type="hidden" name="entry_id" value="<?= $entry['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Rejeitar esta participação?')">✗ Rejeitar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <nav>
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

<?php include __DIR__ . '/../partials/footer.php'; ?>