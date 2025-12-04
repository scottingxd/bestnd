<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\DatabaseHelper;
use App\Helpers\ValidationHelper;
use Exception;

class UserController
{
    public function home(): void
    {
        AuthHelper::requireLogin();
        
        $sql = "SELECT * FROM raffles 
                WHERE status = 'active' 
                AND end_at > NOW() 
                ORDER BY created_at DESC";
        
        $raffles = DatabaseHelper::fetchAll($sql);
        
        include __DIR__ . '/../../views/user/home.php';
    }
    
    public function profile(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        $sql = "SELECT * FROM users WHERE id = ?";
        $userData = DatabaseHelper::fetchOne($sql, [$user['id']]);
        
        $sqlStats = "SELECT 
                        COUNT(*) as total_entries,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_entries,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_entries
                     FROM raffle_entries 
                     WHERE user_id = ? AND deleted_at IS NULL";
        
        $stats = DatabaseHelper::fetchOne($sqlStats, [$user['id']]);
        
        $sqlRecent = "SELECT re.*, r.title as raffle_title 
                      FROM raffle_entries re
                      INNER JOIN raffles r ON re.raffle_id = r.id
                      WHERE re.user_id = ? AND re.deleted_at IS NULL
                      ORDER BY re.created_at DESC
                      LIMIT 5";
        
        $recentEntries = DatabaseHelper::fetchAll($sqlRecent, [$user['id']]);
        
        include __DIR__ . '/../../views/user/profile.php';
    }
    
    public function updateProfile(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Método inválido';
            header('Location: ' . BASE_URL . '/user/profile');
            exit;
        }
        
        $steamTradelink = $_POST['steam_tradelink'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        if (empty($steamTradelink) || empty($phone)) {
            $_SESSION['flash_error'] = 'Preencha todos os campos';
            header('Location: ' . BASE_URL . '/user/profile');
            exit;
        }
        
        $sql = "UPDATE users 
                SET steam_tradelink = ?, 
                    phone = ?, 
                    profile_completed = 1,
                    updated_at = NOW() 
                WHERE id = ?";
        
        DatabaseHelper::query($sql, [$steamTradelink, $phone, $user['id']]);
        
        $user['steam_tradelink'] = $steamTradelink;
        $user['phone'] = $phone;
        $user['profile_completed'] = 1;
        $_SESSION['user'] = $user;
        
        $_SESSION['flash_success'] = 'Perfil atualizado com sucesso!';
        header('Location: ' . BASE_URL . '/user/profile');
        exit;
    }
    
    public function myEntries(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT re.*, 
                       r.title as raffle_title, 
                       r.end_at as raffle_end_date,
                       r.is_paid as raffle_is_paid
                FROM raffle_entries re
                INNER JOIN raffles r ON re.raffle_id = r.id
                WHERE re.user_id = ? AND re.deleted_at IS NULL
                ORDER BY re.created_at DESC
                LIMIT ? OFFSET ?";
        
        $entries = DatabaseHelper::fetchAll($sql, [$user['id'], $perPage, $offset]);
        
        $sqlCount = "SELECT COUNT(*) as total 
                     FROM raffle_entries 
                     WHERE user_id = ? AND deleted_at IS NULL";
        
        $totalResult = DatabaseHelper::fetchOne($sqlCount, [$user['id']]);
        $total = $totalResult['total'];
        $totalPages = ceil($total / $perPage);
        
        include __DIR__ . '/../../views/user/my_entries.php';
    }
    
    public function participateInRaffle(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Método inválido';
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
        
        $raffleId = $_POST['raffle_id'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $depositDate = $_POST['deposit_date'] ?? null;
        
        if (!$raffleId) {
            $_SESSION['flash_error'] = 'Sorteio inválido';
            header('Location: ' . BASE_URL . '/user/home');
            exit;
        }
        
        try {
            $sql = "SELECT * FROM raffles WHERE id = ? AND status = 'active'";
            $raffle = DatabaseHelper::fetchOne($sql, [$raffleId]);
            
            if (!$raffle) {
                $_SESSION['flash_error'] = 'Sorteio não encontrado ou inativo';
                header('Location: ' . BASE_URL . '/user/home');
                exit;
            }
            
            if (strtotime($raffle['end_at']) <= time()) {
                $_SESSION['flash_error'] = 'Sorteio já encerrado';
                header('Location: ' . BASE_URL . '/user/home');
                exit;
            }
            
            if ($raffle['max_participants'] > 0) {
                $sqlCount = "SELECT COUNT(*) as count 
                             FROM raffle_entries 
                             WHERE raffle_id = ? AND deleted_at IS NULL AND status = 'approved'";
                $countResult = DatabaseHelper::fetchOne($sqlCount, [$raffleId]);
                
                if ($countResult['count'] >= $raffle['max_participants']) {
                    $_SESSION['flash_error'] = 'Limite de participações atingido';
                    header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
                    exit;
                }
            }
            
            $sqlCheck = "SELECT COUNT(*) as count 
                         FROM raffle_entries 
                         WHERE user_id = ? AND raffle_id = ? AND deleted_at IS NULL";
            $checkResult = DatabaseHelper::fetchOne($sqlCheck, [$user['id'], $raffleId]);
            
            if ($checkResult['count'] > 0) {
                $_SESSION['flash_error'] = 'Você já participou deste sorteio';
                header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
                exit;
            }
            
            $proofPath = null;
            
            if ($raffle['is_paid'] == 1) {
                if (empty($amount) || $amount <= 0) {
                    $_SESSION['flash_error'] = 'Valor do depósito inválido';
                    header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
                    exit;
                }
                
                if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['flash_error'] = 'Comprovante de depósito é obrigatório';
                    header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
                    exit;
                }
                
                $file = $_FILES['proof_image'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!in_array($file['type'], $allowedTypes)) {
                    $_SESSION['flash_error'] = 'Formato de imagem inválido. Use JPG ou PNG';
                    header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
                    exit;
                }
                
                $uploadDir = __DIR__ . '/../../public/uploads/proofs/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'proof_' . $user['id'] . '_' . $raffleId . '_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $_SESSION['flash_error'] = 'Erro ao fazer upload do comprovante';
                    header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
                    exit;
                }
                
                $proofPath = 'uploads/proofs/' . $filename;
            }
            
            $sqlInsert = "INSERT INTO raffle_entries 
                          (user_id, raffle_id, amount, proof_image_path, deposit_date, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            
            DatabaseHelper::query($sqlInsert, [
                $user['id'],
                $raffleId,
                $amount,
                $proofPath,
                $depositDate
            ]);
            
            $_SESSION['flash_success'] = 'Participação registrada com sucesso!';
            header('Location: ' . BASE_URL . '/user/my-entries');
            exit;
            
        } catch (Exception $e) {
            error_log("Erro ao participar: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Erro ao processar participação. Tente novamente.';
            header('Location: ' . BASE_URL . '/raffle/view/' . $raffleId);
            exit;
        }
    }
    
    public function deleteEntry(): void
    {
        if (!AuthHelper::isLogged()) {
            echo json_encode([
                'success' => false,
                'message' => 'Você precisa estar logado'
            ]);
            return;
        }
        
        $user = AuthHelper::getUser();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $entryId = $input['entry_id'] ?? null;
        
        if (!$entryId) {
            echo json_encode([
                'success' => false,
                'message' => 'ID da participação não fornecido'
            ]);
            return;
        }
        
        try {
            $sql = "SELECT re.*, r.end_at as raffle_end_date 
                    FROM raffle_entries re
                    INNER JOIN raffles r ON re.raffle_id = r.id
                    WHERE re.id = ? AND re.user_id = ? AND re.deleted_at IS NULL";
            
            $entry = DatabaseHelper::fetchOne($sql, [$entryId, $user['id']]);
            
            if (!$entry) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Participação não encontrada'
                ]);
                return;
            }
            
            if (strtotime($entry['raffle_end_date']) <= time()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Não é possível deletar participação de sorteio encerrado'
                ]);
                return;
            }
            
            $sqlDelete = "UPDATE raffle_entries 
                          SET deleted_at = NOW(), updated_at = NOW() 
                          WHERE id = ?";
            
            DatabaseHelper::query($sqlDelete, [$entryId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Participação deletada com sucesso'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao deletar entry: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao deletar participação. Tente novamente.'
            ]);
        }
    }
}