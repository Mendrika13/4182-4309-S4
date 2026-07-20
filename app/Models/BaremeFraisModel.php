<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['type_operation', 'montant_min', 'montant_max', 'frais'];
    protected $useTimestamps    = false;

    /**
     * Retourne le montant du frais applicable pour un type d'opération
     * ('retrait' ou 'transfert') et un montant donné, selon la tranche
     * définie dans la table baremes_frais.
     *
     * Retourne 0.0 si aucune tranche ne correspond (par sécurité, on
     * considère alors qu'aucun barème n'est configuré pour ce montant).
     */
    public function getFraisApplicable(string $typeOperation, float $montant): float
    {
        $bareme = $this->where('type_operation', $typeOperation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        return $bareme ? (float) $bareme['frais'] : 0.0;
    }
}
