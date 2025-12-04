<?php
namespace App\Helpers;

/**
 * GoogleMockHelper
 * 
 * Helper para simular autenticação do Google em desenvolvimento.
 * 
 * USO:
 * - /auth/callback?user=0 → Loga como Usuário Teste (user comum)
 * - /auth/callback?user=1 → Loga como Admin Teste (admin)
 * - /auth/callback?user=2 → Loga como Administrador (admin principal)
 * 
 * ⚠️ IMPORTANTE: Este mock NÃO deve ser usado em produção!
 * Em produção, substitua por autenticação Google OAuth real.
 * 
 * @package App\Helpers
 */
class GoogleMockHelper
{
    /**
     * Retorna dados mockados de um usuário do Google
     * 
     * @return array Dados do usuário (google_id, name, email, avatar_url)
     */
    public static function getMockUser(): array
    {
        $mockUsers = [
            // ==========================================
            // ÍNDICE 0: Usuário Comum
            // ==========================================
            0 => [
                'google_id' => 'mock-user-123456',
                'name' => 'Usuário Teste',
                'email' => 'usuario@teste.com',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Usuario+Teste&size=200&background=3B82F6&color=fff'
            ],
            
            // ==========================================
            // ÍNDICE 1: Admin Teste
            // ==========================================
            1 => [
                'google_id' => 'mock-admin-789012',
                'name' => 'Admin Teste',
                'email' => 'admin@teste.com',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Admin+Teste&size=200&background=E50914&color=fff'
            ],
            
            // ==========================================
            // ÍNDICE 2: Administrador Principal
            // ==========================================
            2 => [
                'google_id' => 'admin-mock-12345',
                'name' => 'Administrador',
                'email' => 'admin@sistema.com',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Administrador&size=200&background=E50914&color=fff'
            ]
        ];
        
        // Pega o índice do usuário da URL (?user=0, ?user=1, ?user=2)
        $userIndex = $_GET['user'] ?? 0;
        $userIndex = (int) $userIndex;
        
        // Se índice inválido, usa o primeiro usuário (user comum)
        if (!isset($mockUsers[$userIndex])) {
            $userIndex = 0;
        }
        
        return $mockUsers[$userIndex];
    }
    
    /**
     * Retorna lista de todos os usuários mock disponíveis
     * Útil para criar página de seleção de usuário em desenvolvimento
     * 
     * @return array Lista de usuários mock
     */
    public static function getAllMockUsers(): array
    {
        return [
            [
                'index' => 0,
                'name' => 'Usuário Teste',
                'email' => 'usuario@teste.com',
                'role' => 'user',
                'description' => 'Usuário comum para testes'
            ],
            [
                'index' => 1,
                'name' => 'Admin Teste',
                'email' => 'admin@teste.com',
                'role' => 'admin',
                'description' => 'Administrador para testes'
            ],
            [
                'index' => 2,
                'name' => 'Administrador',
                'email' => 'admin@sistema.com',
                'role' => 'admin',
                'description' => 'Administrador principal do sistema'
            ]
        ];
    }
}