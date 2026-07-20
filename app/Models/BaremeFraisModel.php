<?php

namespace App\Models;

use App\Core\Database;

class BaremeFraisModel
{
    public function getFraisApplicable(string $typeOperation, float $montant): float
    {
        $stmt = Database::connexion()->prepare(
            'SELECT frais FROM baremes_frais WHERE type_operation = ? AND montant_min <= ? AND montant_max >= ? LIMIT 1'
        );
        $stmt->execute([$typeOperation, $montant, $montant]);
        $bareme = $stmt->fetch();

        return $bareme ? (float) $bareme['frais'] : 0.0;
    }
}
