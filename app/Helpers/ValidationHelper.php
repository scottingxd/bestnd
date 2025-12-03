<?php
namespace App\Helpers;

class ValidationHelper
{
    public static function sanitizeString(?string $value): string
    {
        if ($value === null) return '';
        return trim(strip_tags($value));
    }
    
    public static function sanitizeInt($value, int $default = 0): int
    {
        if (is_int($value)) return $value;
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false ? $filtered : $default;
    }
    
    public static function sanitizeFloat($value, float $default = 0.0): float
    {
        if (is_float($value)) return $value;
        $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
        return $filtered !== false ? $filtered : $default;
    }
    
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    public static function validateDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    public static function validatePhone(string $phone): bool
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 15;
    }
    
    public static function validateSteamTradeLink(string $tradelink): bool
    {
        if (strpos($tradelink, 'steamcommunity.com/tradeoffer/new') === false) {
            return false;
        }
        if (strpos($tradelink, 'partner=') === false || strpos($tradelink, 'token=') === false) {
            return false;
        }
        return self::validateUrl($tradelink);
    }
    
    public static function escapeHtml(?string $value): string
    {
        if ($value === null) return '';
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}