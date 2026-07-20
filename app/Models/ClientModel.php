<?php

namespace App\Models;

use App\Core\Database;

class ClientModel
{
    public function find(int $id): ?array
    {
        $stmt = Database::connexion()->prepare('SELECT * FROM clients WHERE id = ?');
        $stmt->execute([$id]);
        $client = $stmt->fetch();

        return $client ?: null;
    }

    public function findByTelephone(string $telephone): ?array
    {
        $stmt = Database::connexion()->prepare('SELECT * FROM clients WHERE telephone = ?');
        $stmt->execute([$telephone]);
        $client = $stmt->fetch();

        return $client ?: null;
    }

    public function trouverOuCreer(string $telephone): array
    {
        $client = $this->findByTelephone($telephone);

        if ($client !== null) {
            return $client;
        }

        $db = Database::connexion();
        $stmt = $db->prepare('INSERT INTO clients (telephone, date_creation) VALUES (?, ?)');
        $stmt->execute([$telephone, date('Y-m-d H:i:s')]);

        return $this->find((int) $db->lastInsertId());
    }

    public function getSolde(int $clientId): float
    {
        $stmt = Database::connexion()->prepare('SELECT solde FROM v_soldes_clients WHERE client_id = ?');
        $stmt->execute([$clientId]);
        $ligne = $stmt->fetch();

        return $ligne ? (float) $ligne['solde'] : 0.0;
    }

    public function getTousAvecSolde(): array
    {
        $stmt = Database::connexion()->query('SELECT * FROM v_soldes_clients ORDER BY date_creation DESC');

        return $stmt->fetchAll();
    }
}
