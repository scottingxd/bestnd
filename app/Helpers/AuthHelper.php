<?php
namespace App\Helpers;

class AuthHelper
{
    public static function loginUser(array $userData): void
    {
        $_SESSION['user'] = [
            'id' => $userData['id'],
            'google_id' => $userData['google_id'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'avatar_url' => $userData['avatar_url'],
            'role' => $userData['role'],
            'profile_completed' => $userData['profile_completed'],
            'steam_tradelink' => $userData['steam_tradelink'] ?? null,
            'phone' => $userData['phone'] ?? null
        ];
        
        $_SESSION['login_time'] = time();
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    public static function logoutUser(): void
    {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }
    
    public static function getUser(): ?array
    {
        if (isset($_SESSION['user'], $_SESSION['user_agent'])) {
            $currentAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if ($_SESSION['user_agent'] !== $currentAgent) {
                self::logoutUser();
                return null;
            }
            return $_SESSION['user'];
        }
        return null;
    }
    
    public static function isLogged(): bool
    {
        return self::getUser() !== null;
    }
    
    public static function isAdmin(): bool
    {
        $user = self::getUser();
        return $user && $user['role'] === 'admin';
    }
    
    public static function requireLogin(): void
    {
        if (!self::isLogged()) {
            $_SESSION['flash_error'] = 'Você precisa estar logado.';
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        $user = self::getUser();
        $currentUri = $_SERVER['REQUEST_URI'];
        
        if ($user['profile_completed'] == 0 && 
            strpos($currentUri, 'complete-profile') === false &&
            strpos($currentUri, 'update-profile') === false &&
            strpos($currentUri, 'logout') === false) {
            header('Location: ' . BASE_URL . '/user/complete-profile');
            exit;
        }
    }
    
    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            $_SESSION['flash_error'] = 'Acesso negado.';
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
    }
    
    public static function updateUser(array $userData): void
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user'] = array_merge($_SESSION['user'], $userData);
        }
    }
}