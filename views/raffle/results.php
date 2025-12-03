<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="mb-2">🏆 Resultados dos Sorteios</h1>
        <p class="text-muted">Confira os vencedores dos sorteios já realizados</p>
    </div>
    
    <?php if (empty($raffles)): ?>
        <div class="neomorph-card text-center py-5">
            <div class="mb-4" style="font-size: 4rem; opacity: 0.3;">🎲</div>
            <h4 class="text-muted mb-3">Nenhum sorteio encerrado ainda</h4>
            <p class="text-muted mb-4">Os resultados aparecerão aqui assim que os sorteios forem realizados</p>
            <a href="<?= BASE_URL ?>/user/home" class="neomorph-button-primary">
                🎯 Ver Sorteios Ativos
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($raffles as $raffle): ?>
                <div class="col-md-6 mb-4">
                    <div class="neomorph-card h-100">
                        <!-- Header do Card -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">
                                    <?= ValidationHelper::escapeHtml($raffle['title']) ?>
                                </h5>
                                <div class="d-flex gap-3 small text-muted">
                                    <span>📊 <?= $raffle['entries_count'] ?> participações</span>
                                    <span>📅 <?= date('d/m/Y', strtotime($raffle['updated_at'])) ?></span>
                                </div>
                            </div>
                            <span class="badge bg-danger">ENCERRADO</span>
                        </div>
                        
                        <?php if ($raffle['winner']): ?>
                            <!-- Vencedor -->
                            <div class="neomorph-inset p-4 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="<?= ValidationHelper::escapeHtml($raffle['winner']['winner_avatar']) ?>" 
                                             class="rounded-circle" 
                                             width="60" 
                                             height="60"
                                             style="border: 2px solid #ff1647;">
                                    </div>
                                    <div class="col">
                                        <div class="small text-muted mb-1">🏆 Vencedor</div>
                                        <h6 class="mb-1 text-danger">
                                            <?= ValidationHelper::escapeHtml($raffle['winner']['winner_name']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            Sorteado em <?= date('d/m/Y H:i', strtotime($raffle['winner']['selected_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Transparência -->
                            <?php if ($raffle['winner']['log_info']): ?>
                                <div class="mb-3">
                                    <button type="button" 
                                            class="neomorph-button w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#logModal<?= $raffle['id'] ?>">
                                        🔍 Ver Log de Transparência
                                    </button>
                                    
                                    <!-- Modal Log -->
                                    <div class="modal fade" id="logModal<?= $raffle['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">🔍 Log de Transparência</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info mb-3">
                                                        <strong>ℹ️ Sobre a Transparência:</strong><br>
                                                        Este log garante que o sorteio foi realizado de forma justa e aleatória.
                                                        Todos os IDs das participações aprovadas e o hash de verificação estão registrados.
                                                    </div>
                                                    
                                                    <?php 
                                                    $logData = json_decode($raffle['winner']['log_info'], true);
                                                    ?>
                                                    
                                                    <div class="neomorph-inset p-3">
                                                        <div class="row mb-3">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Total de Participações</small>
                                                                <strong><?= $logData['total_entries'] ?? 0 ?></strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Índice Sorteado</small>
                                                                <strong><?= $logData['winner_index'] ?? 0 ?></strong>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row mb-3">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Seed (Semente)</small>
                                                                <code><?= $logData['seed'] ?? 'N/A' ?></code>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Data/Hora</small>
                                                                <code><?= $logData['timestamp'] ?? 'N/A' ?></code>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block mb-2">Hash de Verificação (SHA-256)</small>
                                                            <code class="d-block p-2 bg-dark rounded" style="font-size: 0.7rem; word-break: break-all;">
                                                                <?= $logData['hash'] ?? 'N/A' ?>
                                                            </code>
                                                        </div>
                                                        
                                                        <details>
                                                            <summary class="text-muted small" style="cursor: pointer;">
                                                                Ver todos os IDs das participações
                                                            </summary>
                                                            <div class="mt-2">
                                                                <pre class="bg-dark p-2 rounded" style="font-size: 0.75rem; max-height: 200px; overflow-y: auto;"><?= json_encode($logData['all_entry_ids'] ?? [], JSON_PRETTY_PRINT) ?></pre>
                                                            </div>
                                                        </details>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <!-- Sem Vencedor -->
                            <div class="alert alert-warning mb-3">
                                ⏳ Aguardando realização do sorteio
                            </div>
                        <?php endif; ?>
                        
                        <!-- Ações -->
                        <a href="<?= BASE_URL ?>/raffle/view/<?= $raffle['id'] ?>" 
                           class="neomorph-button w-100">
                            👁️ Ver Detalhes do Sorteio
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginação -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>