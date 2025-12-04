<?php
/**
 * DEBUG: Testar Login Mock
 * 
 * Acesse: /debug-login.php?user=0
 * 
 * Este arquivo testa todo o fluxo de login
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/autoload.php';

use App\Helpers\AuthHelper;
use App\Helpers\DatabaseHelper;
use App\Helpers\GoogleMockHelper;

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Debug Login</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1f28; color: #fff; }
        .success { color: #22c55e; }
        .error { color: #ef4444; }
        .info { color: #3b82f6; }
        .section { background: #0f1419; padding: 15px; margin: 10px 0; border-radius: 8px; }
        h2 { color: #ff1647; }
        pre { background: #000; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>🔍 Debug Login Mock</h1>
";

// Passo 1: Verificar constantes
echo "<div class='section'>";
echo "<h2>1️⃣ Constantes</h2>";
echo "BASE_URL: <span class='info'>" . BASE_URL . "</span><br>";
echo "GOOGLE_MOCK_MODE: <span class='info'>" . (defined('GOOGLE_MOCK_MODE') ? (GOOGLE_MOCK_MODE ? 'TRUE' : 'FALSE') : 'NÃO DEFINIDO') . "</span><br>";
echo "</div>";

// Passo 2: Testar GoogleMockHelper
echo "<div class='section'>";
echo "<h2>2️⃣ GoogleMockHelper</h2>";
$userIndex = $_GET['user'] ?? 0;
echo "User Index: <span class='info'>$userIndex</span><br>";

try {
    $mockData = GoogleMockHelper::getMockUser($userIndex);
    if ($mockData) {
        echo "<span class='success'>✅ Mock User encontrado</span><br>";
        echo "<pre>";
        print_r($mockData);
        echo "</pre>";
    } else {
        echo "<span class='error'>❌ Mock User não encontrado</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro: " . $e->getMessage() . "</span><br>";
}
echo "</div>";

// Passo 3: Verificar se usuário existe no banco
echo "<div class='section'>";
echo "<h2>3️⃣ Verificar Banco de Dados</h2>";
if ($mockData) {
    $sql = "SELECT * FROM users WHERE google_id = ?";
    $existingUser = DatabaseHelper::fetchOne($sql, [$mockData['google_id']]);
    
    if ($existingUser) {
        echo "<span class='success'>✅ Usuário existe no banco</span><br>";
        echo "<pre>";
        print_r($existingUser);
        echo "</pre>";
    } else {
        echo "<span class='info'>ℹ️ Usuário não existe no banco (será criado)</span><br>";
    }
}
echo "</div>";

// Passo 4: Testar criação/atualização
echo "<div class='section'>";
echo "<h2>4️⃣ Testar Login</h2>";

if ($mockData) {
    try {
        // Se usuário existe, atualizar
        if ($existingUser) {
            $sqlUpdate = "UPDATE users 
                          SET name = ?, 
                              email = ?, 
                              avatar_url = ?,
                              updated_at = NOW()
                          WHERE id = ?";
            
            $stmt = DatabaseHelper::query($sqlUpdate, [
                $mockData['name'],
                $mockData['email'],
                $mockData['avatar_url'],
                $existingUser['id']
            ]);
            
            echo "<span class='success'>✅ Usuário atualizado</span><br>";
            
            // Buscar dados atualizados
            $user = DatabaseHelper::fetchOne($sql, [$mockData['google_id']]);
        } else {
            // Criar novo usuário
            $sqlInsert = "INSERT INTO users 
                          (google_id, name, email, avatar_url, role, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, 'user', NOW(), NOW())";
            
            $stmt = DatabaseHelper::query($sqlInsert, [
                $mockData['google_id'],
                $mockData['name'],
                $mockData['email'],
                $mockData['avatar_url']
            ]);
            
            echo "<span class='success'>✅ Usuário criado</span><br>";
            
            // Buscar usuário criado
            $user = DatabaseHelper::fetchOne($sql, [$mockData['google_id']]);
        }
        
        if ($user) {
            echo "<span class='success'>✅ Usuário encontrado após operação</span><br>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
        } else {
            echo "<span class='error'>❌ Erro: Usuário não encontrado após operação</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>❌ Erro SQL: " . $e->getMessage() . "</span><br>";
    }
}
echo "</div>";

// Passo 5: Testar AuthHelper::loginUser
echo "<div class='section'>";
echo "<h2>5️⃣ Testar AuthHelper::loginUser</h2>";

if (isset($user) && $user) {
    try {
        AuthHelper::loginUser($user);
        echo "<span class='success'>✅ AuthHelper::loginUser() executado</span><br>";
        
        // Verificar sessão
        if (isset($_SESSION['user'])) {
            echo "<span class='success'>✅ Sessão criada</span><br>";
            echo "<pre>";
            print_r($_SESSION['user']);
            echo "</pre>";
        } else {
            echo "<span class='error'>❌ Sessão NÃO foi criada</span><br>";
        }
        
        // Verificar AuthHelper::isLogged
        if (AuthHelper::isLogged()) {
            echo "<span class='success'>✅ AuthHelper::isLogged() retorna TRUE</span><br>";
        } else {
            echo "<span class='error'>❌ AuthHelper::isLogged() retorna FALSE</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>❌ Erro ao fazer login: " . $e->getMessage() . "</span><br>";
    }
}
echo "</div>";

// Passo 6: Verificar redirecionamento
echo "<div class='section'>";
echo "<h2>6️⃣ Redirecionamento</h2>";

if (isset($user) && $user) {
    if ($user['profile_completed'] == 0) {
        $redirectUrl = BASE_URL . '/user/profile';
        echo "<span class='info'>ℹ️ Deve redirecionar para: <strong>$redirectUrl</strong></span><br>";
    } else {
        $redirectUrl = BASE_URL . '/user/home';
        echo "<span class='info'>ℹ️ Deve redirecionar para: <strong>$redirectUrl</strong></span><br>";
    }
    
    echo "<br><a href='$redirectUrl' style='color: #ff1647; font-weight: bold;'>👉 Clique aqui para ir manualmente</a>";
}
echo "</div>";

// Passo 7: Sessão completa
echo "<div class='section'>";
echo "<h2>7️⃣ Sessão Completa</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>✅ Teste Finalizado</h2>";
echo "<p>Se tudo estiver verde acima, o login está funcionando corretamente.</p>";
echo "<p>Se houver erros vermelhos, copie e me envie a saída completa.</p>";
echo "</div>";

echo "</body></html>";