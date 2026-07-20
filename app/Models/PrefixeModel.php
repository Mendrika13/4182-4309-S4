<?php

namespace App\Models;

use App\Core\Database;

class PrefixeModel
{
    public function estAutorise(string $prefixe): bool
    {
        $stmt = Database::connexion()->prepare('SELECT id FROM prefixes WHERE prefixe = ?');
        $stmt->execute([$prefixe]);

        return $stmt->fetch() !== false;
    }

    public function listeTriee(): array
    {
        $stmt = Database::connexion()->query('SELECT * FROM prefixes ORDER BY prefixe ASC');

        return $stmt->fetchAll();
    }

    public function ajouter(string $prefixe): void
    {
        $stmt = Database::connexion()->prepare('INSERT INTO prefixes (prefixe) VALUES (?)');
        $stmt->execute([$prefixe]);
    }

    public function supprimer(int $id): void
    {
        $stmt = Database::connexion()->prepare('DELETE FROM prefixes WHERE id = ?');
        $stmt->execute([$id]);
    }
}
