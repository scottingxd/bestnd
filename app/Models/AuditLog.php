<?php
namespace App\Models;

use App\Helpers\DatabaseHelper;

/**
 * Model AuditLog
 * 
 * Representa a tabela 'audit_logs' e gerencia logs de auditoria.
 * 
 * @package App\Models
 */
class AuditLog
{
    /**
     * Cria novo log
     * 
     * @param array $data
     * @return int ID do log criado
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO audit_logs (
                    user_id, 
                    action, 
                    details, 
                    ip_address,
                    created_at
                ) VALUES (?, ?, ?, ?, NOW())";
        
        return DatabaseHelper::insert($sql, [
            $data['user_id'] ?? null,
            $data['action'],
            $data['details'] ?? null,
            $data['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? null)
        ]);
    }
    
    /**
     * Lista logs (admin)
     * 
     * @param int $limit
     * @param int $offset
     * @param int|null $userId Filtro por usuário
     * @return array
     */
    public static function findAll(
        int $limit = 50, 
        int $offset = 0,
        ?int $userId = null
    ): array {
        $sql = "SELECT l.*, u.name as user_name, u.email as user_email
                FROM audit_logs l
                LEFT JOIN users u ON l.user_id = u.id";
        
        $params = [];
        
        if ($userId !== null) {
            $sql .= " WHERE l.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY l.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return DatabaseHelper::fetchAll($sql, $params);
    }
    
    /**
     * Conta total de logs
     * 
     * @return int
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM audit_logs";
        $result = DatabaseHelper::fetchOne($sql);
        return (int) $result['count'];
    }
}