<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\TransactionModel;

class ClientController
{
    private ClientModel $clientModel;
    private TransactionModel $transactionModel;
    private BaremeFraisModel $baremeFraisModel;
    private PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->transactionModel = new TransactionModel();
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->prefixeModel = new PrefixeModel();
    }

    public function dashboard(): void
    {
        $clientId = (int) Session::get('client_id');
        $client = $this->clientModel->find($clientId);

        if (! $client) {
            Session::remove(['client_id', 'telephone', 'isLoggedIn']);
            header('Location: ' . View::baseUrl('login'));
            exit;
        }

        View::render('client/dashboard', [
            'client'     => $client,
            'solde'      => $this->clientModel->getSolde($clientId),
            'historique' => $this->transactionModel->getHistoriqueClient($clientId),
        ]);
    }

    public function depot(): void
    {
        if (! Session::verifierCsrf($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $clientId = (int) Session::get('client_id');
        $montant = (float) ($_POST['montant'] ?? 0);

        if ($montant <= 0) {
            Session::setFlash('error', 'Le montant du dépôt doit être supérieur à 0.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $this->transactionModel->enregistrerDepot($clientId, $montant);

        Session::setFlash('success', 'Dépôt de ' . View::argent($montant) . ' Ar effectué avec succès.');
        header('Location: ' . View::baseUrl('client/dashboard'));
        exit;
    }

    public function retrait(): void
    {
        if (! Session::verifierCsrf($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $clientId = (int) Session::get('client_id');
        $montant = (float) ($_POST['montant'] ?? 0);

        if ($montant <= 0) {
            Session::setFlash('error', 'Le montant du retrait doit être supérieur à 0.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $frais = $this->baremeFraisModel->getFraisApplicable('retrait', $montant);
        $solde = $this->clientModel->getSolde($clientId);

        if ($solde < ($montant + $frais)) {
            Session::setFlash('error', 'Solde insuffisant pour ce retrait (montant + frais de ' . View::argent($frais) . ' Ar).');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $this->transactionModel->enregistrerRetrait($clientId, $montant, $frais);

        Session::setFlash('success', 'Retrait de ' . View::argent($montant) . ' Ar effectué (frais : ' . View::argent($frais) . ' Ar).');
        header('Location: ' . View::baseUrl('client/dashboard'));
        exit;
    }

    public function transfert(): void
    {
        if (! Session::verifierCsrf($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $clientId = (int) Session::get('client_id');
        $telephoneDest = trim((string) ($_POST['telephone_destinataire'] ?? ''));
        $montant = (float) ($_POST['montant'] ?? 0);

        if (! preg_match('/^0[0-9]{9}$/', $telephoneDest)) {
            Session::setFlash('error', 'Numéro de destinataire invalide. Format attendu : 0XXXXXXXXX.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $client = $this->clientModel->find($clientId);

        if ($telephoneDest === $client['telephone']) {
            Session::setFlash('error', 'Vous ne pouvez pas effectuer un transfert vers votre propre numéro.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $prefixeDest = substr($telephoneDest, 0, 3);

        if (! $this->prefixeModel->estAutorise($prefixeDest)) {
            Session::setFlash('error', "Le préfixe {$prefixeDest} du destinataire n'est pas reconnu.");
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        if ($montant <= 0) {
            Session::setFlash('error', 'Le montant du transfert doit être supérieur à 0.');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $frais = $this->baremeFraisModel->getFraisApplicable('transfert', $montant);
        $solde = $this->clientModel->getSolde($clientId);

        if ($solde < ($montant + $frais)) {
            Session::setFlash('error', 'Solde insuffisant pour ce transfert (montant + frais de ' . View::argent($frais) . ' Ar).');
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        $destinataire = $this->clientModel->trouverOuCreer($telephoneDest);

        $this->transactionModel->enregistrerTransfert($clientId, (int) $destinataire['id'], $montant, $frais);

        Session::setFlash('success', 'Transfert de ' . View::argent($montant) . ' Ar vers ' . $telephoneDest . ' effectué (frais : ' . View::argent($frais) . ' Ar).');
        header('Location: ' . View::baseUrl('client/dashboard'));
        exit;
    }
}
