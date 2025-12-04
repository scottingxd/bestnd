<?php
namespace App\Controllers;

use App\Services\RaffleService;
use App\Models\Raffle;
use App\Models\RaffleWinner;
use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\ValidationHelper;

class RaffleController
{
    private RaffleService $raffleService;
    
    public function __construct()
    {
        $this->raffleService = new RaffleService();
    }
    
    public function view(?int $id): void
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
        
        $raffle = Raffle::findById($id);
        
        if (!$raffle) {
            $_SESSION['flash_error'] = 'Sorteio não encontrado.';
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
        
        $userEntriesCount = 0;
        $canParticipate = true;
        
        if (AuthHelper::isLogged()) {
            $user = AuthHelper::getUser();
            $userEntriesCount = \App\Models\RaffleEntry::countByRaffleAndUser($id, $user['id']);
            $canParticipate = $this->raffleService->canUserParticipate($id, $user['id']);
        }
        
        $winner = null;
        if ($raffle['status'] === 'closed') {
            $winner = RaffleWinner::findByRaffleId($id);
        }
        
        $pageTitle = $raffle['title'];
        include __DIR__ . '/../../views/raffle/view.php';
    }
    
    public function participate(): void
    {
        CsrfHelper::validateOrDie();
        AuthHelper::requireLogin();
        
        $user = AuthHelper::getUser();
        $raffleId = ValidationHelper::sanitizeInt($_POST['raffle_id'] ?? 0);
        
        if (!$raffleId) {
            $_SESSION['flash_error'] = 'Sorteio inválido.';
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
        
        $raffle = Raffle::findById($raffleId);
        
        if (!$raffle) {
            $_SESSION['flash_error'] = 'Sorteio não encontrado.';
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
        
        if ($raffle['is_paid'] == 0) {
            $result = $this->raffleService->participateFree($raffleId, $user['id']);
        } else {
            $data = [
                'amount' => ValidationHelper::sanitizeFloat($_POST['amount'] ?? 0),
                'deposit_date' => ValidationHelper::sanitizeString($_POST['deposit_date'] ?? ''),
                'proof_file' => $_FILES['proof_image'] ?? null
            ];
            
            $result = $this->raffleService->participateWithProof($raffleId, $user['id'], $data);
        }
        
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        
        header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
        exit;
    }
    
    public function results(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $raffles = Raffle::findClosed($limit, $offset);
        
        foreach ($raffles as &$raffle) {
            $raffle['winner'] = RaffleWinner::findByRaffleId($raffle['id']);
        }
        
        $pageTitle = 'Resultados dos Sorteios';
        include __DIR__ . '/../../views/raffle/results.php';
    }
}