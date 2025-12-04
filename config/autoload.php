<?php
spl_autoload_register(function ($className) {
    $namespacePrefix = 'App\\';
    $baseDirectory = __DIR__ . '/../app/';
    
    $prefixLength = strlen($namespacePrefix);
    if (strncmp($namespacePrefix, $className, $prefixLength) !== 0) {
        return;
    }
    
    $relativeClassName = substr($className, $prefixLength);
    $filePath = $baseDirectory . str_replace('\\', '/', $relativeClassName) . '.php';
    
    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        if (defined('APP_DEBUG') && APP_DEBUG) {
            trigger_error("Autoload: Arquivo não encontrado para '{$className}' em: {$filePath}", E_USER_WARNING);
        }
    }
});

if (defined('APP_DEBUG') && APP_DEBUG) {
    if (function_exists('error_log')) {
        error_log('[Autoload] Sistema de autoload PSR-4 registrado.');
    }
}