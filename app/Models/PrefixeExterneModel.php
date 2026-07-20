<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeExterneModel extends Model
{
    protected $table            = 'prefixes_externes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['prefixe', 'autre_operateur_id'];
    protected $useTimestamps    = false;

    /**
     * Recherche le préfixe externe et retourne les informations associées.
     */
    public function trouverParPrefixe(string $prefixe): ?array
    {
        return $this->select('prefixes_externes.*, autres_operateurs.nom AS operateur_nom')
            ->join('autres_operateurs', 'autres_operateurs.id = prefixes_externes.autre_operateur_id')
            ->where('prefixe', $prefixe)
            ->first();
    }
}
