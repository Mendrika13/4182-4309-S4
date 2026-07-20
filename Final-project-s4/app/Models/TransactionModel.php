<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'type_operation',
        'expediteur_id',
        'destinataire_id',
        'montant',
        'frais',
        'date_transaction',
    ];
    protected $useTimestamps    = false;


    public function enregistrerDepot(int $clientId, float $montant): int
    {
        return (int) $this->insert([
            'type_operation'   => 'depot',
            'expediteur_id'    => null,
            'destinataire_id'  => $clientId,
            'montant'          => $montant,
            'frais'            => 0,
            'date_transaction' => date('Y-m-d H:i:s'),
        ], true);
    }


    public function enregistrerRetrait(int $clientId, float $montant, float $frais): int
    {
        return (int) $this->insert([
            'type_operation'   => 'retrait',
            'expediteur_id'    => $clientId,
            'destinataire_id'  => null,
            'montant'          => $montant,
            'frais'            => $frais,
            'date_transaction' => date('Y-m-d H:i:s'),
        ], true);
    }


    public function enregistrerTransfert(int $expediteurId, int $destinataireId, float $montant, float $frais): int
    {
        return (int) $this->insert([
            'type_operation'   => 'transfert',
            'expediteur_id'    => $expediteurId,
            'destinataire_id'  => $destinataireId,
            'montant'          => $montant,
            'frais'            => $frais,
            'date_transaction' => date('Y-m-d H:i:s'),
        ], true);
    }


    public function getHistoriqueClient(int $clientId): array
    {
        $db = $this->db;

        $sql = "
            SELECT
                t.id,
                t.type_operation,
                t.montant,
                t.frais,
                t.date_transaction,
                t.expediteur_id,
                t.destinataire_id,
                ce.telephone AS telephone_expediteur,
                cd.telephone AS telephone_destinataire
            FROM transactions t
            LEFT JOIN clients ce ON ce.id = t.expediteur_id
            LEFT JOIN clients cd ON cd.id = t.destinataire_id
            WHERE t.expediteur_id = ? OR t.destinataire_id = ?
            ORDER BY t.date_transaction DESC, t.id DESC
        ";

        return $db->query($sql, [$clientId, $clientId])->getResultArray();
    }


    public function getGainTotalOperateur(): float
    {
        $result = $this->selectSum('frais')->get()->getRowArray();

        return (float) ($result['frais'] ?? 0);
    }
}
