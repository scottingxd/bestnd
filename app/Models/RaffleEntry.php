<?php
namespace App\Models;

use App\Helpers\DatabaseHelper;

/**
 * Model RaffleEntry
 * 
 * Representa a tabela 'raffle_entries' e gerencia participações em sorteios.
 * 
 * @package App\Models
 */
class RaffleEntry
{
    /**
     * Busca entrada por ID
     * 
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array
    {
        $sql = "SELECT e.*, u.name as user_name, u.email as user_email, 
                u.avatar_url as user_avatar, r.title as raffle_title
                FROM raffle_entries e
                JOIN users u ON e.user_id = u.id
                JOIN raffles r ON e.raffle_id = r.id
                WHERE e.id = ?";
        
        return DatabaseHelper::fetchOne($sql, [$id]);
    }
    
    /**
     * Conta participações de um usuário em um sorteio específico
     * CRÍTICO: Usado para validar limite de 30 participações
     * 
     * @param int $raffleId
     * @param int $userId
     * @return int
     */
    public static function countByRaffleAndUser(int $raffleId, int $userId): int
    {
        $sql = "SELECT COUNT(*) as count FROM raffle_entries 
                WHERE raffle_id = ? AND user_id = ?";
        
        $result = DatabaseHelper::fetchOne($sql, [$raffleId, $userId]);
        return (int) $result['count'];
    }
    
    /**
     * Lista participações de um sorteio
     * 
     * @param int $raffleId
     * @param string|null $status Filtro por status (pending/approved/rejected)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function findByRaffleId(
        int $raffleId, 
        ?string $status = null,
        int $limit = 100,
        int $offset = 0
    ): array {
        $sql = "SELECT e.*, u.name as user_name, u.email as user_email,
                u.avatar_url as user_avatar
                FROM raffle_entries e 
                JOIN users u ON e.user_id = u.id
                WHERE e.raffle_id = ?";
        
        $params = [$raffleId];
        
        if ($status !== null) {
            $sql .= " AND e.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY e.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return DatabaseHelper::fetchAll($sql, $params);
    }
    
    /**
     * Lista participações de um usuário
     * 
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function findByUserId(
        int $userId, 
        int $limit = 20, 
        int $offset = 0
    ): array {
        $sql = "SELECT e.*, r.title as raffle_title, r.image_url as raffle_image,
                r.status as raffle_status,
                (SELECT COUNT(*) FROM raffle_winners rw 
                 WHERE rw.raffle_entry_id = e.id) as is_winner
                FROM raffle_entries e
                JOIN raffles r ON e.raffle_id = r.id
                WHERE e.user_id = ?
                ORDER BY e.created_at DESC
                LIMIT ? OFFSET ?";
        
        return DatabaseHelper::fetchAll($sql, [$userId, $limit, $offset]);
    }
    
    /**
     * Conta participações de um usuário
     * 
     * @param int $userId
     * @return int
     */
    public static function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) as count FROM raffle_entries WHERE user_id = ?";
        $result = DatabaseHelper::fetchOne($sql, [$userId]);
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Lista participações pendentes (admin)
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function findPending(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT e.*, u.name as user_name, u.email as user_email,
                u.avatar_url as user_avatar, r.title as raffle_title
                FROM raffle_entries e
                JOIN users u ON e.user_id = u.id
                JOIN raffles r ON e.raffle_id = r.id
                WHERE e.status = 'pending'
                ORDER BY e.created_at ASC
                LIMIT ? OFFSET ?";
        
        return DatabaseHelper::fetchAll($sql, [$limit, $offset]);
    }
    
    /**
     * Conta participações pendentes
     * 
     * @return int
     */
    public static function countPending(): int
    {
        $sql = "SELECT COUNT(*) as count FROM raffle_entries 
                WHERE status = 'pending'";
        
        $result = DatabaseHelper::fetchOne($sql);
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Cria nova participação
     * 
     * @param array $data
     * @return int ID da entrada criada
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO raffle_entries (
                    raffle_id, 
                    user_id, 
                    amount, 
                    proof_image_path,
                    deposit_date, 
                    status, 
                    created_at, 
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        return DatabaseHelper::insert($sql, [
            $data['raffle_id'],
            $data['user_id'],
            $data['amount'] ?? null,
            $data['proof_image_path'] ?? null,
            $data['deposit_date'] ?? null,
            $data['status'] ?? 'pending'
        ]);
    }
    
    /**
     * Atualiza status de uma participação
     * 
     * @param int $id
     * @param string $status
     * @return int Número de linhas afetadas
     */
    public static function updateStatus(int $id, string $status): int
    {
        $sql = "UPDATE raffle_entries 
                SET status = ?, updated_at = NOW() 
                WHERE id = ?";
        
        return DatabaseHelper::update($sql, [$status, $id]);
    }
    
    /**
     * Deleta participação
     * 
     * @param int $id
     * @return int Número de linhas afetadas
     */
    public static function delete(int $id): int
    {
        $sql = "DELETE FROM raffle_entries WHERE id = ?";
        return DatabaseHelper::delete($sql, [$id]);
    }
    
    /**
     * Deleta todas participações de um sorteio
     * 
     * @param int $raffleId
     * @return int Número de linhas afetadas
     */
    public static function deleteByRaffleId(int $raffleId): int
    {
        $sql = "DELETE FROM raffle_entries WHERE raffle_id = ?";
        return DatabaseHelper::delete($sql, [$raffleId]);
    }
}