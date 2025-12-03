<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Helpers\AuthHelper;
use App\Helpers\GoogleMockHelper;

class AuthController
{
    private UserService $userService;
    
    public function __construct()
    {
        $this->userService = new UserService();
    }
    
    public function login(): void
    {
        if (AuthHelper::isLogged()) {
            $user = AuthHelper::getUser();
            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/dashboard');
            } else {
                header('Location: ' . BASE_URL . '/user/home');
            }
            exit;
        }
        
        $pageTitle = 'Login - Sistema de Sorteios CS2';
        include __DIR__ . '/../../views/auth/login.php';
    }
    
    public function callback(): void
    {
        $googleData = GoogleMockHelper::getMockUser();
        $user = $this->userService->createOrUpdateFromGoogle($googleData);
        
        AuthHelper::loginUser($user);
        session_regenerate_id(true);
        
        if ($user['profile_completed'] == 0) {
            header('Location: ' . BASE_URL . '/user/complete-profile');
            exit;
        }
        
        if ($user['role'] === 'admin') {
            header('Location: ' . BASE_URL . '/admin/dashboard');
        } else {
            header('Location: ' . BASE_URL . '/user/home');
        }
        exit;
    }
    
    public function logout(): void
    {
        AuthHelper::logoutUser();
        $_SESSION['flash_success'] = 'Logout realizado com sucesso.';
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}