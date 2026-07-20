<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['prefixe'];
    protected $useTimestamps    = false;


    public function estAutorise(string $prefixe): bool
    {
        return $this->where('prefixe', $prefixe)->first() !== null;
    }


    public function listeTriee(): array
    {
        return $this->orderBy('prefixe', 'ASC')->findAll();
    }
}
