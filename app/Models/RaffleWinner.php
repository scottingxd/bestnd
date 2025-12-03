<?php
namespace App\Models;

use App\Helpers\DatabaseHelper;

/**
 * Model RaffleWinner
 * 
 * Representa a tabela 'raffle_winners' e gerencia vencedores de sorteios.
 * 
 * @package App\Models
 */
class RaffleWinner
{
    /**
     * Busca vencedor por ID
     * 
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array
    {
        $sql = "SELECT w.*, e.user_id, u.name as winner_name, 
                u.email as winner_email, u.avatar_url as winner_avatar,
                r.title as raffle_title
                FROM raffle_winners w
                JOIN raffle_entries e ON w.raffle_entry_id = e.id
                JOIN users u ON e.user_id = u.id
                JOIN raffles r ON w.raffle_id = r.id
                WHERE w.id = ?";
        
        return DatabaseHelper::fetchOne($sql, [$id]);
    }
    
    /**
     * Busca vencedor de um sorteio
     * 
     * @param int $raffleId
     * @return array|null
     */
    public static function findByRaffleId(int $raffleId): ?array
    {
        $sql = "SELECT w.*, e.user_id, u.name as winner_name, 
                u.email as winner_email, u.avatar_url as winner_avatar,
                u.steam_tradelink as winner_tradelink
                FROM raffle_winners w
                JOIN raffle_entries e ON w.raffle_entry_id = e.id
                JOIN users u ON e.user_id = u.id
                WHERE w.raffle_id = ?";
        
        return DatabaseHelper::fetchOne($sql, [$raffleId]);
    }
    
    /**
     * Cria registro de vencedor
     * 
     * @param array $data
     * @return int ID do registro criado
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO raffle_winners (
                    raffle_id, 
                    raffle_entry_id, 
                    selected_at,
                    selected_by, 
                    log_info
                ) VALUES (?, ?, NOW(), ?, ?)";
        
        return DatabaseHelper::insert($sql, [
            $data['raffle_id'],
            $data['raffle_entry_id'],
            $data['selected_by'],
            $data['log_info'] ?? null
        ]);
    }
}