<?php
namespace App\Helpers;

class CsrfHelper
{
    public static function generateToken(): string
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        } else {
            if (time() - $_SESSION['csrf_token_time'] > 3600) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['csrf_token_time'] = time();
            }
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public static function validateToken(?string $token): bool
    {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function getTokenField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    public static function validateOrDie(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!self::validateToken($token)) {
            header('HTTP/1.0 403 Forbidden');
            die('Token CSRF inválido.');
        }
    }
}