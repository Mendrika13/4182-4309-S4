<?php

namespace App\Models;

use App\Core\Database;

class TransactionModel
{
    public function enregistrerDepot(int $clientId, float $montant): int
    {
        $db = Database::connexion();
        $stmt = $db->prepare(
            'INSERT INTO transactions (type_operation, expediteur_id, destinataire_id, montant, frais, date_transaction)
             VALUES (?, NULL, ?, ?, 0, ?)'
        );
        $stmt->execute(['depot', $clientId, $montant, date('Y-m-d H:i:s')]);

        return (int) $db->lastInsertId();
    }

    public function enregistrerRetrait(int $clientId, float $montant, float $frais): int
    {
        $db = Database::connexion();
        $stmt = $db->prepare(
            'INSERT INTO transactions (type_operation, expediteur_id, destinataire_id, montant, frais, date_transaction)
             VALUES (?, ?, NULL, ?, ?, ?)'
        );
        $stmt->execute(['retrait', $clientId, $montant, $frais, date('Y-m-d H:i:s')]);

        return (int) $db->lastInsertId();
    }

    public function enregistrerTransfert(int $expediteurId, int $destinataireId, float $montant, float $frais): int
    {
        $db = Database::connexion();
        $stmt = $db->prepare(
            'INSERT INTO transactions (type_operation, expediteur_id, destinataire_id, montant, frais, date_transaction)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute(['transfert', $expediteurId, $destinataireId, $montant, $frais, date('Y-m-d H:i:s')]);

        return (int) $db->lastInsertId();
    }

    public function getHistoriqueClient(int $clientId): array
    {
        $stmt = Database::connexion()->prepare(
            'SELECT * FROM v_historique_transactions
             WHERE expediteur_id = ? OR destinataire_id = ?
             ORDER BY date_transaction DESC, id DESC'
        );
        $stmt->execute([$clientId, $clientId]);

        return $stmt->fetchAll();
    }

    public function getGainTotalOperateur(): float
    {
        $stmt = Database::connexion()->query('SELECT gain_total FROM v_gain_operateur');
        $ligne = $stmt->fetch();

        return $ligne ? (float) $ligne['gain_total'] : 0.0;
    }
}
