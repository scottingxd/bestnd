<?php
namespace App\Services;

use App\Models\Raffle;
use App\Models\RaffleEntry;
use App\Models\RaffleWinner;
use App\Models\AuditLog;
use App\Helpers\UploadHelper;

class RaffleService
{
    public function canUserParticipate(int $raffleId, int $userId): bool
    {
        $count = RaffleEntry::countByRaffleAndUser($raffleId, $userId);
        return ($count < MAX_ENTRIES_PER_RAFFLE);
    }
    
    public function participateFree(int $raffleId, int $userId): array
    {
        if (!Raffle::isActive($raffleId)) {
            return [
                'success' => false,
                'message' => 'Este sorteio não está ativo ou não existe.',
                'entry_id' => null
            ];
        }
        
        if (!$this->canUserParticipate($raffleId, $userId)) {
            return [
                'success' => false,
                'message' => 'Você atingiu o limite de ' . MAX_ENTRIES_PER_RAFFLE . ' participações neste sorteio.',
                'entry_id' => null
            ];
        }
        
        $entryId = RaffleEntry::create([
            'raffle_id' => $raffleId,
            'user_id' => $userId,
            'status' => 'approved'
        ]);
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'raffle_entry_created',
            'details' => json_encode([
                'raffle_id' => $raffleId,
                'entry_id' => $entryId,
                'type' => 'free'
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Participação registrada com sucesso!',
            'entry_id' => $entryId
        ];
    }
    
    public function participateWithProof(int $raffleId, int $userId, array $data): array
    {
        if (!Raffle::isActive($raffleId)) {
            return [
                'success' => false,
                'message' => 'Este sorteio não está ativo ou não existe.'
            ];
        }
        
        $raffle = Raffle::findById($raffleId);
        if (!$raffle || $raffle['is_paid'] != 1) {
            return [
                'success' => false,
                'message' => 'Este sorteio não aceita comprovantes.'
            ];
        }
        
        if (!$this->canUserParticipate($raffleId, $userId)) {
            return [
                'success' => false,
                'message' => 'Você atingiu o limite de ' . MAX_ENTRIES_PER_RAFFLE . ' participações neste sorteio.'
            ];
        }
        
        if (empty($data['amount']) || empty($data['deposit_date']) || empty($data['proof_file'])) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos obrigatórios.'
            ];
        }
        
        if ($raffle['min_value'] && $data['amount'] < $raffle['min_value']) {
            return [
                'success' => false,
                'message' => 'O valor mínimo para este sorteio é R$ ' . number_format($raffle['min_value'], 2, ',', '.')
            ];
        }
        
        $uploadResult = UploadHelper::uploadProof($data['proof_file'], $raffleId);
        
        if (!$uploadResult['success']) {
            return [
                'success' => false,
                'message' => $uploadResult['message']
            ];
        }
        
        $entryId = RaffleEntry::create([
            'raffle_id' => $raffleId,
            'user_id' => $userId,
            'amount' => $data['amount'],
            'deposit_date' => $data['deposit_date'],
            'proof_image_path' => $uploadResult['path'],
            'status' => 'pending'
        ]);
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'raffle_entry_created',
            'details' => json_encode([
                'raffle_id' => $raffleId,
                'entry_id' => $entryId,
                'type' => 'with_proof',
                'amount' => $data['amount']
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Participação enviada com sucesso! Aguarde aprovação do administrador.',
            'entry_id' => $entryId
        ];
    }
    
    public function approveEntry(int $entryId, int $adminId): array
    {
        $entry = RaffleEntry::findById($entryId);
        
        if (!$entry) {
            return [
                'success' => false,
                'message' => 'Participação não encontrada.'
            ];
        }
        
        if ($entry['status'] === 'approved') {
            return [
                'success' => false,
                'message' => 'Esta participação já foi aprovada.'
            ];
        }
        
        RaffleEntry::updateStatus($entryId, 'approved');
        
        AuditLog::create([
            'user_id' => $adminId,
            'action' => 'entry_approved',
            'details' => json_encode([
                'entry_id' => $entryId,
                'raffle_id' => $entry['raffle_id'],
                'participant_user_id' => $entry['user_id']
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Participação aprovada com sucesso!'
        ];
    }
    
    public function rejectEntry(int $entryId, int $adminId, ?string $reason = null): array
    {
        $entry = RaffleEntry::findById($entryId);
        
        if (!$entry) {
            return [
                'success' => false,
                'message' => 'Participação não encontrada.'
            ];
        }
        
        if ($entry['status'] === 'rejected') {
            return [
                'success' => false,
                'message' => 'Esta participação já foi rejeitada.'
            ];
        }
        
        RaffleEntry::updateStatus($entryId, 'rejected');
        
        AuditLog::create([
            'user_id' => $adminId,
            'action' => 'entry_rejected',
            'details' => json_encode([
                'entry_id' => $entryId,
                'raffle_id' => $entry['raffle_id'],
                'participant_user_id' => $entry['user_id'],
                'reason' => $reason
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Participação rejeitada.'
        ];
    }
    
    public function drawWinner(int $raffleId, int $adminId): array
    {
        $raffle = Raffle::findById($raffleId);
        if (!$raffle) {
            return [
                'success' => false,
                'message' => 'Sorteio não encontrado.'
            ];
        }
        
        $existingWinner = RaffleWinner::findByRaffleId($raffleId);
        if ($existingWinner) {
            return [
                'success' => false,
                'message' => 'Este sorteio já tem um vencedor.'
            ];
        }
        
        $entries = RaffleEntry::findByRaffleId($raffleId, 'approved');
        
        if (empty($entries)) {
            return [
                'success' => false,
                'message' => 'Nenhuma participação aprovada para sortear.'
            ];
        }
        
        $winnerIndex = random_int(0, count($entries) - 1);
        $winnerEntry = $entries[$winnerIndex];
        
        $seed = time();
        $logInfo = [
            'total_entries' => count($entries),
            'winner_index' => $winnerIndex,
            'seed' => $seed,
            'timestamp' => date('Y-m-d H:i:s'),
            'all_entry_ids' => array_column($entries, 'id'),
            'winner_entry_id' => $winnerEntry['id'],
            'hash' => hash('sha256', json_encode($entries) . $seed)
        ];
        
        RaffleWinner::create([
            'raffle_id' => $raffleId,
            'raffle_entry_id' => $winnerEntry['id'],
            'selected_by' => $adminId,
            'log_info' => json_encode($logInfo)
        ]);
        
        Raffle::update($raffleId, ['status' => 'closed']);
        
        AuditLog::create([
            'user_id' => $adminId,
            'action' => 'winner_drawn',
            'details' => json_encode([
                'raffle_id' => $raffleId,
                'winner_entry_id' => $winnerEntry['id'],
                'winner_user_id' => $winnerEntry['user_id'],
                'total_entries' => count($entries)
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Vencedor sorteado com sucesso!',
            'winner' => $winnerEntry
        ];
    }
    
    public function deleteRaffle(int $raffleId, int $adminId, bool $deleteEntries = true): array
    {
        $raffle = Raffle::findById($raffleId);
        
        if (!$raffle) {
            return [
                'success' => false,
                'message' => 'Sorteio não encontrado.'
            ];
        }
        
        if ($deleteEntries) {
            UploadHelper::deleteRaffleProofs($raffleId);
            RaffleEntry::deleteByRaffleId($raffleId);
        }
        
        Raffle::delete($raffleId);
        
        AuditLog::create([
            'user_id' => $adminId,
            'action' => 'raffle_deleted',
            'details' => json_encode([
                'raffle_id' => $raffleId,
                'raffle_title' => $raffle['title'],
                'deleted_entries' => $deleteEntries
            ])
        ]);
        
        return [
            'success' => true,
            'message' => 'Sorteio excluído com sucesso.'
        ];
    }
}