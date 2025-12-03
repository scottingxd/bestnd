<?php
namespace App\Helpers;

class GoogleMockHelper
{
    public static function getMockUser(): array
    {
        $mockUsers = [
            [
                'google_id' => 'mock-user-123456',
                'name' => 'Usuário Teste',
                'email' => 'usuario@teste.com',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Usuario+Teste&size=200&background=E50914&color=fff'
            ],
            [
                'google_id' => 'mock-admin-789012',
                'name' => 'Admin Teste',
                'email' => 'admin@teste.com',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Admin+Teste&size=200&background=E50914&color=fff'
            ]
        ];
        
        $userIndex = $_GET['user'] ?? 0;
        $userIndex = (int) $userIndex;
        
        if (!isset($mockUsers[$userIndex])) {
            $userIndex = 0;
        }
        
        return $mockUsers[$userIndex];
    }
}