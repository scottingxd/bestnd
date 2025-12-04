<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Sorteios CS2 - Participe e ganhe prêmios!">
    <title><?= $pageTitle ?? 'Sorteios CS2' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/assets/img/favicon.ico">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f1419 0%, #1a1f28 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        
        /* Evitar flash de conteúdo não estilizado */
        .page-loading {
            opacity: 0;
            transition: opacity 0.3s ease-in;
        }
        
        .page-loaded {
            opacity: 1;
        }
    </style>
</head>
<body class="page-loading">
    <script>
        // Remover loading quando página carregar
        window.addEventListener('DOMContentLoaded', function() {
            document.body.classList.remove('page-loading');
            document.body.classList.add('page-loaded');
        });
    </script>