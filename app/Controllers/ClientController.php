<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\TransactionModel;

class ClientController extends BaseController
{
    protected ClientModel $clientModel;
    protected TransactionModel $transactionModel;
    protected BaremeFraisModel $baremeFraisModel;
    protected PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->clientModel      = new ClientModel();
        $this->transactionModel = new TransactionModel();
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->prefixeModel     = new PrefixeModel();
    }

    /**
     * Tableau de bord client : numéro, solde actuel, historique complet.
     */
    public function dashboard()
    {
        $clientId = (int) session()->get('client_id');
        $client   = $this->clientModel->find($clientId);

        if (! $client) {
            // Sécurité : si le client de la session n'existe plus en base
            session()->remove(['client_id', 'telephone', 'isLoggedIn']);

            return redirect()->to('/login');
        }

        $data = [
            'client'     => $client,
            'solde'      => $this->clientModel->getSolde($clientId),
            'historique' => $this->transactionModel->getHistoriqueClient($clientId),
        ];

        return view('client/dashboard.php', $data);
    }

    /**
     * Traite un dépôt sur le compte du client connecté. Le dépôt est
     * toujours gratuit (frais = 0).
     */
    public function depot()
    {
        $clientId = (int) session()->get('client_id');
        $montant  = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            session()->setFlashdata('error', 'Le montant du dépôt doit être supérieur à 0.');

            return redirect()->to('/client/dashboard');
        }

        $this->transactionModel->enregistrerDepot($clientId, $montant);

        session()->setFlashdata('success', 'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès.');

        return redirect()->to('/client/dashboard');
    }

    /**
     * Traite un retrait sur le compte du client connecté, avec vérification
     * du solde suffisant (montant + frais applicable selon le barème).
     */
    public function retrait()
    {
        $clientId = (int) session()->get('client_id');
        $montant  = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            session()->setFlashdata('error', 'Le montant du retrait doit être supérieur à 0.');

            return redirect()->to('/client/dashboard');
        }

        $frais = $this->baremeFraisModel->getFraisApplicable('retrait', $montant);
        $solde = $this->clientModel->getSolde($clientId);

        if ($solde < ($montant + $frais)) {
            session()->setFlashdata('error', 'Solde insuffisant pour ce retrait (montant + frais de ' . number_format($frais, 0, ',', ' ') . ' Ar).');

            return redirect()->to('/client/dashboard');
        }

        $this->transactionModel->enregistrerRetrait($clientId, $montant, $frais);

        session()->setFlashdata('success', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).');

        return redirect()->to('/client/dashboard');
    }

    /**
     * Traite un transfert vers un autre numéro de téléphone. Le destinataire
     * est créé automatiquement s'il n'existe pas encore et que son préfixe
     * est autorisé, selon la même logique que le login automatique.
     */
    public function transfert()
    {
        $clientId           = (int) session()->get('client_id');
        $telephoneDest      = trim((string) $this->request->getPost('telephone_destinataire'));
        $montant            = (float) $this->request->getPost('montant');

        if (! preg_match('/^0[0-9]{9}$/', $telephoneDest)) {
            session()->setFlashdata('error', 'Numéro de destinataire invalide. Format attendu : 0XXXXXXXXX.');

            return redirect()->to('/client/dashboard');
        }

        $client = $this->clientModel->find($clientId);

        if ($telephoneDest === $client['telephone']) {
            session()->setFlashdata('error', 'Vous ne pouvez pas effectuer un transfert vers votre propre numéro.');

            return redirect()->to('/client/dashboard');
        }

        $prefixeDest = substr($telephoneDest, 0, 3);

        if (! $this->prefixeModel->estAutorise($prefixeDest)) {
            session()->setFlashdata('error', "Le préfixe {$prefixeDest} du destinataire n'est pas reconnu.");

            return redirect()->to('/client/dashboard');
        }

        if ($montant <= 0) {
            session()->setFlashdata('error', 'Le montant du transfert doit être supérieur à 0.');

            return redirect()->to('/client/dashboard');
        }

        $frais = $this->baremeFraisModel->getFraisApplicable('transfert', $montant);
        $solde = $this->clientModel->getSolde($clientId);

        if ($solde < ($montant + $frais)) {
            session()->setFlashdata('error', 'Solde insuffisant pour ce transfert (montant + frais de ' . number_format($frais, 0, ',', ' ') . ' Ar).');

            return redirect()->to('/client/dashboard');
        }

        // Trouve ou crée automatiquement le compte destinataire
        $destinataire = $this->clientModel->trouverOuCreer($telephoneDest);

        $this->transactionModel->enregistrerTransfert($clientId, (int) $destinataire['id'], $montant, $frais);

        session()->setFlashdata('success', 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $telephoneDest . ' effectué (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).');

        return redirect()->to('/client/dashboard');
    }
}
