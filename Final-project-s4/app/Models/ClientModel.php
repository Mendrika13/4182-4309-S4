<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['telephone', 'date_creation'];
    protected $useTimestamps    = false;


    public function findByTelephone(string $telephone): ?array
    {
        return $this->where('telephone', $telephone)->first();
    }


    public function trouverOuCreer(string $telephone): array
    {
        $client = $this->findByTelephone($telephone);

        if ($client !== null) {
            return $client;
        }

        $id = $this->insert([
            'telephone'     => $telephone,
            'date_creation' => date('Y-m-d H:i:s'),
        ], true);

        return $this->find($id);
    }


    public function getSolde(int $clientId): float
    {
        $db = $this->db;

        $sql = "
            SELECT
                COALESCE(SUM(CASE WHEN type_operation = 'depot'     AND destinataire_id = ? THEN montant ELSE 0 END), 0) AS total_depots,
                COALESCE(SUM(CASE WHEN type_operation = 'transfert' AND destinataire_id = ? THEN montant ELSE 0 END), 0) AS total_transferts_recus,
                COALESCE(SUM(CASE WHEN type_operation = 'retrait'   AND expediteur_id   = ? THEN montant ELSE 0 END), 0) AS total_retraits,
                COALESCE(SUM(CASE WHEN type_operation = 'retrait'   AND expediteur_id   = ? THEN frais   ELSE 0 END), 0) AS total_frais_retrait,
                COALESCE(SUM(CASE WHEN type_operation = 'transfert' AND expediteur_id   = ? THEN montant ELSE 0 END), 0) AS total_transferts_envoyes,
                COALESCE(SUM(CASE WHEN type_operation = 'transfert' AND expediteur_id   = ? THEN frais   ELSE 0 END), 0) AS total_frais_transfert
            FROM transactions
        ";

        $result = $db->query($sql, [
            $clientId, $clientId, $clientId, $clientId, $clientId, $clientId,
        ])->getRowArray();

        $credits = (float) $result['total_depots'] + (float) $result['total_transferts_recus'];

        $debits = (float) $result['total_retraits']
            + (float) $result['total_frais_retrait']
            + (float) $result['total_transferts_envoyes']
            + (float) $result['total_frais_transfert'];

        return $credits - $debits;
    }


    public function getTousAvecSolde(): array
    {
        $clients = $this->orderBy('date_creation', 'DESC')->findAll();

        foreach ($clients as &$client) {
            $client['solde'] = $this->getSolde((int) $client['id']);
        }

        return $clients;
    }
}
