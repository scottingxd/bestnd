<?php
namespace App\Services;

use App\Models\User;
use App\Models\AuditLog;
use App\Helpers\ValidationHelper;
use App\Helpers\AuthHelper;

class UserService
{
    public function createOrUpdateFromGoogle(array $googleData): array
    {
        $user = User::findByGoogleId($googleData['google_id']);
        
        if ($user) {
            User::update($user['id'], [
                'name' => $googleData['name'],
                'email' => $googleData['email'],
                'avatar_url' => $googleData['avatar_url']
            ]);
            
            $user = User::findById($user['id']);
            
        } else {
            $userId = User::create([
                'google_id' => $googleData['google_id'],
                'name' => $googleData['name'],
                'email' => $googleData['email'],
                'avatar_url' => $googleData['avatar_url'],
                'role' => 'user',
                'profile_completed' => 0
            ]);
            
            $user = User::findById($userId);
            
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'user_created',
                'details' => json_encode([
                    'google_id' => $googleData['google_id'],
                    'email' => $googleData['email']
                ])
            ]);
        }
        
        return $user;
    }
    
    public function completeProfile(int $userId, array $data): array
    {
        $errors = [];
        
        $tradelink = ValidationHelper::sanitizeString($data['steam_tradelink'] ?? '');
        if (empty($tradelink)) {
            $errors[] = 'Steam Trade Link é obrigatório.';
        } elseif (!ValidationHelper::validateSteamTradeLink($tradelink)) {
            $errors[] = 'Steam Trade Link inválido. Use o formato correto do Steam.';
        }
        
        $phone = ValidationHelper::sanitizeString($data['phone'] ?? '');
        if (empty($phone)) {
            $errors[] = 'Telefone é obrigatório.';
        } elseif (!ValidationHelper::validatePhone($phone)) {
            $errors[] = 'Telefone inválido. Use o formato internacional (+55 41 99999-9999).';
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Corrija os erros abaixo.',
                'errors' => $errors
            ];
        }
        
        User::update($userId, [
            'steam_tradelink' => $tradelink,
            'phone' => $phone,
            'profile_completed' => 1
        ]);
        
        AuthHelper::updateUser([
            'steam_tradelink' => $tradelink,
            'phone' => $phone,
            'profile_completed' => 1
        ]);
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'profile_completed',
            'details' => json_encode([
                'completed_at' => date('Y-m-d H:i:s')
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Perfil completado com sucesso!',
            'errors' => []
        ];
    }
    
    public function updateProfile(int $userId, array $data): array
    {
        $errors = [];
        $updateData = [];
        
        if (isset($data['steam_tradelink'])) {
            $tradelink = ValidationHelper::sanitizeString($data['steam_tradelink']);
            if (!empty($tradelink)) {
                if (!ValidationHelper::validateSteamTradeLink($tradelink)) {
                    $errors[] = 'Steam Trade Link inválido.';
                } else {
                    $updateData['steam_tradelink'] = $tradelink;
                }
            }
        }
        
        if (isset($data['phone'])) {
            $phone = ValidationHelper::sanitizeString($data['phone']);
            if (!empty($phone)) {
                if (!ValidationHelper::validatePhone($phone)) {
                    $errors[] = 'Telefone inválido.';
                } else {
                    $updateData['phone'] = $phone;
                }
            }
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Corrija os erros abaixo.',
                'errors' => $errors
            ];
        }
        
        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'Nenhum dado para atualizar.',
                'errors' => []
            ];
        }
        
        User::update($userId, $updateData);
        AuthHelper::updateUser($updateData);
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'profile_updated',
            'details' => json_encode([
                'fields_updated' => array_keys($updateData)
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Perfil atualizado com sucesso!',
            'errors' => []
        ];
    }
}