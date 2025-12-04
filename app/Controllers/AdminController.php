<?php
namespace App\Controllers;

use App\Services\RaffleService;
use App\Models\Raffle;
use App\Models\RaffleEntry;
use App\Models\AuditLog;
use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\ValidationHelper;

class AdminController
{
    private RaffleService $raffleService;
    
    public function __construct()
    {
        $this->raffleService = new RaffleService();
        AuthHelper::requireAdmin();
    }
    
    public function dashboard(): void
    {
        $totalRaffles = Raffle::count();
        $activeRaffles = Raffle::countActive();
        $pendingEntries = RaffleEntry::countPending();
        
        $recentRaffles = Raffle::findAll(5, 0);
        $recentPending = RaffleEntry::findPending(5, 0);
        
        $pageTitle = 'Dashboard Administrativo';
        include __DIR__ . '/../../views/admin/dashboard.php';
    }
    
    public function raffles(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $statusFilter = $_GET['status'] ?? null;
        if ($statusFilter && !in_array($statusFilter, ['draft', 'active', 'closed'])) {
            $statusFilter = null;
        }
        
        $raffles = Raffle::findAll($limit, $offset, $statusFilter);
        $totalRaffles = Raffle::count();
        $totalPages = ceil($totalRaffles / $limit);
        
        $pageTitle = 'Gerenciar Sorteios';
        include __DIR__ . '/../../views/admin/raffles.php';
    }
    
    public function createRaffle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfHelper::validateOrDie();
            
            $user = AuthHelper::getUser();
            
            $data = [
                'title' => ValidationHelper::sanitizeString($_POST['title'] ?? ''),
                'description' => ValidationHelper::sanitizeString($_POST['description'] ?? ''),
                'image_url' => ValidationHelper::sanitizeString($_POST['image_url'] ?? ''),
                'is_paid' => isset($_POST['is_paid']) ? 1 : 0,
                'min_value' => ValidationHelper::sanitizeFloat($_POST['min_value'] ?? 0),
                'max_participants' => ValidationHelper::sanitizeInt($_POST['max_participants'] ?? 0),
                'status' => ValidationHelper::sanitizeString($_POST['status'] ?? 'draft'),
                'start_at' => ValidationHelper::sanitizeString($_POST['start_at'] ?? ''),
                'end_at' => ValidationHelper::sanitizeString($_POST['end_at'] ?? ''),
                'created_by' => $user['id']
            ];
            
            $errors = [];
            if (empty($data['title'])) {
                $errors[] = 'Título é obrigatório.';
            }
            if (empty($data['description'])) {
                $errors[] = 'Descrição é obrigatória.';
            }
            
            if (empty($errors)) {
                $raffleId = Raffle::create($data);
                
                $_SESSION['flash_success'] = 'Sorteio criado com sucesso!';
                header('Location: ' . BASE_URL . '/admin/edit-raffle/' . $raffleId);
                exit;
            } else {
                $_SESSION['flash_error'] = 'Corrija os erros abaixo.';
                $_SESSION['raffle_errors'] = $errors;
            }
        }
        
        $pageTitle = 'Criar Sorteio';
        include __DIR__ . '/../../views/admin/create_raffle.php';
    }
    
    public function editRaffle(?int $id): void
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/raffles');
            exit;
        }
        
        $raffle = Raffle::findById($id);
        
        if (!$raffle) {
            $_SESSION['flash_error'] = 'Sorteio não encontrado.';
            header('Location: ' . BASE_URL . '/admin/raffles');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfHelper::validateOrDie();
            
            $data = [
                'title' => ValidationHelper::sanitizeString($_POST['title'] ?? ''),
                'description' => ValidationHelper::sanitizeString($_POST['description'] ?? ''),
                'image_url' => ValidationHelper::sanitizeString($_POST['image_url'] ?? ''),
                'is_paid' => isset($_POST['is_paid']) ? 1 : 0,
                'min_value' => ValidationHelper::sanitizeFloat($_POST['min_value'] ?? 0),
                'max_participants' => ValidationHelper::sanitizeInt($_POST['max_participants'] ?? 0),
                'status' => ValidationHelper::sanitizeString($_POST['status'] ?? 'draft'),
                'start_at' => ValidationHelper::sanitizeString($_POST['start_at'] ?? ''),
                'end_at' => ValidationHelper::sanitizeString($_POST['end_at'] ?? '')
            ];
            
            Raffle::update($id, $data);
            
            $_SESSION['flash_success'] = 'Sorteio atualizado com sucesso!';
            header('Location: ' . BASE_URL . '/admin/edit-raffle/' . $id);
            exit;
        }
        
        $entries = RaffleEntry::findByRaffleId($id);
        
        $pageTitle = 'Editar Sorteio';
        include __DIR__ . '/../../views/admin/edit_raffle.php';
    }
    public function deleteRaffle(?int $id): void
    {
        CsrfHelper::validateOrDie();
        
        if (!$id) {
            $_SESSION['flash_error'] = 'ID inválido.';
            header('Location: ' . BASE_URL . '/admin/raffles');
            exit;
        }
        
        $user = AuthHelper::getUser();
        $deleteEntries = isset($_POST['delete_entries']) && $_POST['delete_entries'] === '1';
        
        $result = $this->raffleService->deleteRaffle($id, $user['id'], $deleteEntries);
        
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        
        header('Location: ' . BASE_URL . '/admin/raffles');
        exit;
    }
    
    public function entries(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = ENTRIES_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $entries = RaffleEntry::findPending($limit, $offset);
        $totalEntries = RaffleEntry::countPending();
        $totalPages = ceil($totalEntries / $limit);
        
        $pageTitle = 'Participações Pendentes';
        include __DIR__ . '/../../views/admin/entries.php';
    }
    
    public function approveEntry(): void
    {
        CsrfHelper::validateOrDie();
        
        $entryId = ValidationHelper::sanitizeInt($_POST['entry_id'] ?? 0);
        $user = AuthHelper::getUser();
        
        $result = $this->raffleService->approveEntry($entryId, $user['id']);
        
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        
        header('Location: ' . BASE_URL . '/admin/entries');
        exit;
    }
    
    public function rejectEntry(): void
    {
        CsrfHelper::validateOrDie();
        
        $entryId = ValidationHelper::sanitizeInt($_POST['entry_id'] ?? 0);
        $user = AuthHelper::getUser();
        
        $result = $this->raffleService->rejectEntry($entryId, $user['id']);
        
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        
        header('Location: ' . BASE_URL . '/admin/entries');
        exit;
    }
    
    public function drawWinner(?int $raffleId): void
    {
        CsrfHelper::validateOrDie();
        
        if (!$raffleId) {
            $_SESSION['flash_error'] = 'ID inválido.';
            header('Location: ' . BASE_URL . '/admin/raffles');
            exit;
        }
        
        $user = AuthHelper::getUser();
        
        $result = $this->raffleService->drawWinner($raffleId, $user['id']);
        
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        
        header('Location: ' . BASE_URL . '/admin/edit-raffle/' . $raffleId);
        exit;
    }
    
    public function logs(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = LOGS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $logs = AuditLog::findAll($limit, $offset);
        $totalLogs = AuditLog::count();
        $totalPages = ceil($totalLogs / $limit);
        
        $pageTitle = 'Logs de Auditoria';
        include __DIR__ . '/../../views/admin/logs.php';
    }
}