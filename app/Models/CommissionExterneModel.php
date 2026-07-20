<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionExterneModel extends Model
{
    protected $table            = 'commission_externe';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['pourcentage'];
    protected $useTimestamps    = false;

    /**
     * Retourne le pourcentage de commission externe configuré.
     */
    public function getPourcentage(): float
    {
        $config = $this->first();
        return $config ? (float) $config['pourcentage'] : 2.00;
    }

    /**
     * Met à jour le pourcentage de commission.
     */
    public function modifierPourcentage(float $pourcentage): bool
    {
        $config = $this->first();
        if ($config) {
            return $this->update($config['id'], ['pourcentage' => $pourcentage]);
        } else {
            return $this->insert(['pourcentage' => $pourcentage]) !== false;
        }
    }
}
