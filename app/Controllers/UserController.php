<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Models\Raffle;
use App\Models\RaffleEntry;
use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;

class UserController
{
    private UserService $userService;
    
    public function __construct()
    {
        $this->userService = new UserService();
    }
    
    public function home(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $raffles = Raffle::findActive($limit, $offset);
        $totalRaffles = Raffle::countActive();
        $totalPages = ceil($totalRaffles / $limit);
        
        $pageTitle = 'Sorteios Ativos';
        include __DIR__ . '/../../views/user/home.php';
    }
    
    public function completeProfile(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        if ($user['profile_completed'] == 1) {
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfHelper::validateOrDie();
            
            $result = $this->userService->completeProfile($user['id'], $_POST);
            
            if ($result['success']) {
                $_SESSION['flash_success'] = $result['message'];
                header('Location: ' . BASE_URL . '/user/home');
                exit;
            } else {
                $errors = $result['errors'];
            }
        }
        
        $pageTitle = 'Completar Perfil';
        include __DIR__ . '/../../views/user/complete_profile.php';
    }
    
    public function profile(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        $pageTitle = 'Meu Perfil';
        include __DIR__ . '/../../views/user/profile.php';
    }
    
    public function updateProfile(): void
    {
        CsrfHelper::validateOrDie();
        AuthHelper::requireLogin();
        
        $user = AuthHelper::getUser();
        $result = $this->userService->updateProfile($user['id'], $_POST);
        
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
            if (!empty($result['errors'])) {
                $_SESSION['profile_errors'] = $result['errors'];
            }
        }
        
        header('Location: ' . BASE_URL . '/user/profile');
        exit;
    }
    
    public function myEntries(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = ENTRIES_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $entries = RaffleEntry::findByUserId($user['id'], $limit, $offset);
        $totalEntries = RaffleEntry::countByUserId($user['id']);
        $totalPages = ceil($totalEntries / $limit);
        
        $pageTitle = 'Minhas Participações';
        include __DIR__ . '/../../views/user/my_entries.php';
    }
}