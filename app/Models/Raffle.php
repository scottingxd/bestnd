<?php
namespace App\Models;

use App\Helpers\DatabaseHelper;

class Raffle
{
    public static function findById(int $id): ?array
    {
        $sql = "SELECT r.*, u.name as creator_name 
                FROM raffles r
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.id = ?";
        
        return DatabaseHelper::fetchOne($sql, [$id]);
    }
    
    public static function findActive(int $limit = 12, int $offset = 0): array
    {
        $sql = "SELECT r.*, u.name as creator_name,
                (SELECT COUNT(*) FROM raffle_entries re 
                 WHERE re.raffle_id = r.id AND re.status = 'approved') as entries_count
                FROM raffles r
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.status = 'active' 
                AND (r.start_at IS NULL OR r.start_at <= NOW())
                AND (r.end_at IS NULL OR r.end_at >= NOW())
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        
        return DatabaseHelper::fetchAll($sql, [$limit, $offset]);
    }
    
    public static function countActive(): int
    {
        $sql = "SELECT COUNT(*) as count FROM raffles 
                WHERE status = 'active'
                AND (start_at IS NULL OR start_at <= NOW())
                AND (end_at IS NULL OR end_at >= NOW())";
        
        $result = DatabaseHelper::fetchOne($sql);
        return (int) $result['count'];
    }
    
    public static function findClosed(int $limit = 12, int $offset = 0): array
    {
        $sql = "SELECT r.*, u.name as creator_name,
                (SELECT COUNT(*) FROM raffle_entries re 
                 WHERE re.raffle_id = r.id) as entries_count,
                (SELECT COUNT(*) FROM raffle_winners rw 
                 WHERE rw.raffle_id = r.id) as has_winner
                FROM raffles r
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.status = 'closed'
                ORDER BY r.updated_at DESC
                LIMIT ? OFFSET ?";
        
        return DatabaseHelper::fetchAll($sql, [$limit, $offset]);
    }
    
    public static function findAll(
        int $limit = 20, 
        int $offset = 0,
        ?string $status = null
    ): array {
        $sql = "SELECT r.*, u.name as creator_name,
                (SELECT COUNT(*) FROM raffle_entries re 
                 WHERE re.raffle_id = r.id) as entries_count,
                (SELECT COUNT(*) FROM raffle_entries re 
                 WHERE re.raffle_id = r.id AND re.status = 'pending') as pending_count
                FROM raffles r
                LEFT JOIN users u ON r.created_by = u.id";
        
        $params = [];
        
        if ($status !== null) {
            $sql .= " WHERE r.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return DatabaseHelper::fetchAll($sql, $params);
    }
    
    public static function create(array $data): int
    {
        $sql = "INSERT INTO raffles (
                    title, 
                    description, 
                    image_url, 
                    is_paid, 
                    min_value,
                    max_participants, 
                    status, 
                    start_at, 
                    end_at, 
                    created_by,
                    created_at, 
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        return DatabaseHelper::insert($sql, [
            $data['title'],
            $data['description'],
            $data['image_url'] ?? null,
            $data['is_paid'] ?? 0,
            $data['min_value'] ?? null,
            $data['max_participants'] ?? null,
            $data['status'] ?? 'draft',
            $data['start_at'] ?? null,
            $data['end_at'] ?? null,
            $data['created_by']
        ]);
    }
    
    public static function update(int $id, array $data): int
    {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'title', 'description', 'image_url', 'is_paid', 'min_value', 
            'max_participants', 'status', 'start_at', 'end_at'
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
        
        $sql = "UPDATE raffles SET " . implode(', ', $fields) . " WHERE id = ?";
        return DatabaseHelper::update($sql, $params);
    }
    
    public static function delete(int $id): int
    {
        $sql = "DELETE FROM raffles WHERE id = ?";
        return DatabaseHelper::delete($sql, [$id]);
    }
    
    public static function isActive(int $id): bool
    {
        $raffle = self::findById($id);
        
        if (!$raffle || $raffle['status'] !== 'active') {
            return false;
        }
        
        $now = date('Y-m-d H:i:s');
        
        if ($raffle['start_at'] && $raffle['start_at'] > $now) {
            return false;
        }
        
        if ($raffle['end_at'] && $raffle['end_at'] < $now) {
            return false;
        }
        
        return true;
    }
    
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM raffles";
        $result = DatabaseHelper::fetchOne($sql);
        return (int) $result['count'];
    }
}