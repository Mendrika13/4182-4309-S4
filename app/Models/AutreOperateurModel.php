<?php

namespace App\Models;

use CodeIgniter\Model;

class AutreOperateurModel extends Model
{
    protected $table            = 'autres_operateurs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nom'];
    protected $useTimestamps    = false;
}
