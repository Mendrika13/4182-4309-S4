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
        'est_externe',
        'autre_operateur_id',
        'commission',
        'lot_id',
    ];
    protected $useTimestamps    = false;

    /**
     * Enregistre un dépôt. Le dépôt est gratuit (frais = 0) et crédite
     * directement le compte du client (destinataire_id).
     */
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

    /**
     * Enregistre un retrait. Le client est l'expediteur (l'argent sort de
     * son compte), il n'y a pas de destinataire.
     */
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

    /**
     * Enregistre un transfert entre deux clients.
     */
    public function enregistrerTransfert(
        int $expediteurId,
        ?int $destinataireId,
        float $montant,
        float $frais,
        bool $estExterne = false,
        ?int $autreOperateurId = null,
        float $commission = 0.0,
        ?string $lotId = null
    ): int {
        return (int) $this->insert([
            'type_operation'     => 'transfert',
            'expediteur_id'      => $expediteurId,
            'destinataire_id'    => $destinataireId,
            'montant'            => $montant,
            'frais'              => $frais,
            'date_transaction'   => date('Y-m-d H:i:s'),
            'est_externe'        => $estExterne ? 1 : 0,
            'autre_operateur_id' => $autreOperateurId,
            'commission'         => $commission,
            'lot_id'             => $lotId,
        ], true);
    }

    /**
     * Historique complet des transactions d'un client (celles où il est
     * expéditeur OU destinataire), avec le numéro de téléphone de la
     * contrepartie éventuelle, trié du plus récent au plus ancien.
     */
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
                cd.telephone AS telephone_destinataire,
                t.est_externe,
                t.autre_operateur_id,
                ao.nom AS autre_operateur_nom,
                t.commission,
                t.lot_id
            FROM transactions t
            LEFT JOIN clients ce ON ce.id = t.expediteur_id
            LEFT JOIN clients cd ON cd.id = t.destinataire_id
            LEFT JOIN autres_operateurs ao ON ao.id = t.autre_operateur_id
            WHERE t.expediteur_id = ? OR t.destinataire_id = ?
            ORDER BY t.date_transaction DESC, t.id DESC
        ";

        return $db->query($sql, [$clientId, $clientId])->getResultArray();
    }

    /**
     * Gain global cumulé de l'opérateur : somme de tous les frais perçus
     * (retraits + transferts) sur l'ensemble des transactions.
     */
    public function getGainTotalOperateur(): float
    {
        $result = $this->selectSum('frais')->get()->getRowArray();

        return (float) ($result['frais'] ?? 0);
    }

    /**
     * Récupère le détail des gains interne et externe.
     */
    public function getGainsSplit(): array
    {
        $db = $this->db;
        $row = $db->query("SELECT gain_interne, gain_externe FROM v_gain_operateur")->getRowArray();
        return [
            'gain_interne' => (float) ($row['gain_interne'] ?? 0.0),
            'gain_externe' => (float) ($row['gain_externe'] ?? 0.0)
        ];
    }
}
