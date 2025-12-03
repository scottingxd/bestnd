<?php
namespace App\Models;

use App\Helpers\DatabaseHelper;

/**
 * Model User
 * 
 * Representa a tabela 'users' e gerencia operações relacionadas a usuários.
 * 
 * @package App\Models
 */
class User
{
    /**
     * Busca usuário por ID
     * 
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $result = DatabaseHelper::fetchOne($sql, [$id]);
        
        // Debug temporário (remover depois)
        if ($result && !isset($result['created_at'])) {
            error_log("AVISO: User #$id sem created_at no banco!");
        }
        
        return $result;
    }
    
    /**
     * Busca usuário por Google ID
     * 
     * @param string $googleId
     * @return array|null
     */
    public static function findByGoogleId(string $googleId): ?array
    {
        $sql = "SELECT * FROM users WHERE google_id = ?";
        return DatabaseHelper::fetchOne($sql, [$googleId]);
    }
    
    /**
     * Busca usuário por email
     * 
     * @param string $email
     * @return array|null
     */
    public static function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return DatabaseHelper::fetchOne($sql, [$email]);
    }
    
    /**
     * Cria novo usuário
     * 
     * @param array $data Dados do usuário
     * @return int ID do usuário criado
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO users (
                    google_id, 
                    name, 
                    email, 
                    avatar_url, 
                    steam_tradelink,
                    phone,
                    role, 
                    profile_completed, 
                    created_at, 
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        return DatabaseHelper::insert($sql, [
            $data['google_id'],
            $data['name'],
            $data['email'],
            $data['avatar_url'] ?? null,
            $data['steam_tradelink'] ?? null,
            $data['phone'] ?? null,
            $data['role'] ?? 'user',
            $data['profile_completed'] ?? 0
        ]);
    }
    
    /**
     * Atualiza dados do usuário
     * 
     * @param int $id
     * @param array $data Dados a atualizar
     * @return int Número de linhas afetadas
     */
    public static function update(int $id, array $data): int
    {
        $fields = [];
        $params = [];
        
        // Lista de campos permitidos para atualização
        $allowedFields = [
            'name', 'email', 'avatar_url', 'steam_tradelink', 
            'phone', 'profile_completed', 'role'
        ];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields, true)) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            return 0;
        }
        
        $fields[] = "updated_at = NOW()";
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return DatabaseHelper::update($sql, $params);
    }
    
    /**
     * Lista todos usuários (com paginação)
     * 
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @param string $orderDir
     * @return array
     */
    public static function findAll(
        int $limit = 20, 
        int $offset = 0,
        string $orderBy = 'created_at',
        string $orderDir = 'DESC'
    ): array {
        // Validação de ordenação (previne SQL injection)
        $allowedColumns = ['id', 'name', 'email', 'created_at', 'role'];
        if (!in_array($orderBy, $allowedColumns, true)) {
            $orderBy = 'created_at';
        }
        
        if (!in_array(strtoupper($orderDir), ['ASC', 'DESC'], true)) {
            $orderDir = 'DESC';
        }
        
        $sql = "SELECT * FROM users 
                ORDER BY $orderBy $orderDir 
                LIMIT ? OFFSET ?";
        
        return DatabaseHelper::fetchAll($sql, [$limit, $offset]);
    }
    
    /**
     * Conta total de usuários
     * 
     * @return int
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = DatabaseHelper::fetchOne($sql);
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Deleta usuário (cuidado: use com moderação)
     * 
     * @param int $id
     * @return int Número de linhas afetadas
     */
    public static function delete(int $id): int
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return DatabaseHelper::delete($sql, [$id]);
    }
}