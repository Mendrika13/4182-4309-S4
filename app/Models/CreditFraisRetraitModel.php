<?php

namespace App\Models;

use CodeIgniter\Model;

class CreditFraisRetraitModel extends Model
{
    protected $table            = 'credits_frais_retrait';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'client_id',
        'transaction_origine_id',
        'montant_credit',
        'utilise',
        'transaction_retrait_id'
    ];
    protected $useTimestamps    = false;

    /**
     * Récupère le plus ancien crédit non utilisé pour un client.
     */
    public function getOldestUnusedCredit(int $clientId): ?array
    {
        return $this->where('client_id', $clientId)
            ->where('utilise', 0)
            ->orderBy('id', 'ASC')
            ->first();
    }
}
