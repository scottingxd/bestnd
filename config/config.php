<?php
define('APP_DEBUG', true);
define('APP_ENV', 'development');

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ⚠️ EDITE AQUI
define('DB_HOST', 'localhost');
define('DB_NAME', 'u381533463_raffle_system');
define('DB_USER', 'u381533463_raffle_system');
define('DB_PASS', '#NWfEe!diRg9');
define('DB_CHARSET', 'utf8mb4');

// ⚠️ EDITE AQUI
define('BASE_URL', 'https://cs2bestnades.com.br/sorteios');

define('ROOT_PATH', __DIR__ . '/..');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

define('SESSION_NAME', 'raffle_session');
define('SESSION_LIFETIME', 1800);
define('SESSION_TIMEOUT', 900);

define('CSRF_SECRET', 'change-this-' . md5(__FILE__));
define('SECURITY_SALT', 'another-random-' . md5(__DIR__));

define('MAX_UPLOAD_SIZE', 2097152);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);

define('ITEMS_PER_PAGE', 12);
define('ENTRIES_PER_PAGE', 20);
define('LOGS_PER_PAGE', 50);

define('MAX_ENTRIES_PER_RAFFLE', 30);
define('AUTO_CLEANUP_DAYS', 180);

define('GOOGLE_MOCK_MODE', true);
define('GOOGLE_MOCK_ID', 'mock-user-' . md5('default-user'));

date_default_timezone_set('America/Sao_Paulo');

define('RATE_LIMIT_REQUESTS', 60);
define('RATE_LIMIT_BLOCK_TIME', 300);

define('ADMIN_EMAIL', 'admin@sistema.com');
define('SYSTEM_NAME', 'Sistema de Sorteios CS2');