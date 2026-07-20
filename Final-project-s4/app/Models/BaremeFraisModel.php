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


    public function getFraisApplicable(string $typeOperation, float $montant): float
    {
        $bareme = $this->where('type_operation', $typeOperation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        return $bareme ? (float) $bareme['frais'] : 0.0;
    }
}
