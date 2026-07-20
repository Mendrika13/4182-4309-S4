<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\TransactionModel;
use App\Models\AutreOperateurModel;
use App\Models\PrefixeExterneModel;
use App\Models\CommissionExterneModel;
use App\Models\CreditFraisRetraitModel;

class ClientController extends BaseController
{
    protected ClientModel $clientModel;
    protected TransactionModel $transactionModel;
    protected BaremeFraisModel $baremeFraisModel;
    protected PrefixeModel $prefixeModel;
    protected AutreOperateurModel $autreOperateurModel;
    protected PrefixeExterneModel $prefixeExterneModel;
    protected CommissionExterneModel $commissionExterneModel;
    protected CreditFraisRetraitModel $creditFraisRetraitModel;

    public function __construct()
    {
        $this->clientModel              = new ClientModel();
        $this->transactionModel         = new TransactionModel();
        $this->baremeFraisModel         = new BaremeFraisModel();
        $this->prefixeModel             = new PrefixeModel();
        $this->autreOperateurModel      = new AutreOperateurModel();
        $this->prefixeExterneModel      = new PrefixeExterneModel();
        $this->commissionExterneModel   = new CommissionExterneModel();
        $this->creditFraisRetraitModel  = new CreditFraisRetraitModel();
    }



    public function dashboard()
    {
        $clientId = (int) session()->get('client_id');
        $client   = $this->clientModel->find($clientId);

        if (! $client) {

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


    public function retrait()
    {
        $clientId = (int) session()->get('client_id');
        $montant  = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            session()->setFlashdata('error', 'Le montant du retrait doit être supérieur à 0.');

            return redirect()->to('/client/dashboard');
        }

        // Vérifier s'il y a un crédit de frais de retrait prépayé et non utilisé
        $credit = $this->creditFraisRetraitModel->getOldestUnusedCredit($clientId);
        $frais = 0.0;
        $hasCreditUsed = false;

        if ($credit !== null) {
            $frais = 0.0;
            $hasCreditUsed = true;
        } else {
            $frais = $this->baremeFraisModel->getFraisApplicable('retrait', $montant);
        }

        $solde = $this->clientModel->getSolde($clientId);

        if ($solde < ($montant + $frais)) {
            session()->setFlashdata('error', 'Solde insuffisant pour ce retrait (montant + frais de ' . number_format($frais, 0, ',', ' ') . ' Ar).');

            return redirect()->to('/client/dashboard');
        }

        $transactionId = $this->transactionModel->enregistrerRetrait($clientId, $montant, $frais);

        if ($hasCreditUsed) {
            // Marquer le crédit comme utilisé et lier à la transaction
            $this->creditFraisRetraitModel->update($credit['id'], [
                'utilise' => 1,
                'transaction_retrait_id' => $transactionId
            ]);
            session()->setFlashdata('success', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué. Vos frais de retrait de ' . number_format($credit['montant_credit'], 0, ',', ' ') . ' Ar ont été offerts (crédit prépayé de l\'expéditeur).');
        } else {
            session()->setFlashdata('success', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).');
        }

        return redirect()->to('/client/dashboard');
    }


    public function transfert()
    {
        $clientId           = (int) session()->get('client_id');
        $telephoneDest      = trim((string) $this->request->getPost('telephone_destinataire'));
        $montant            = (float) $this->request->getPost('montant');
        $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');

        if (! preg_match('/^0[0-9]{9}$/', $telephoneDest)) {
            session()->setFlashdata('error', 'Numéro de destinataire invalide. Format attendu : 0XXXXXXXXX.');

            return redirect()->to('/client/dashboard');
        }

        $client = $this->clientModel->find($clientId);

        if ($telephoneDest === $client['telephone']) {
            session()->setFlashdata('error', 'Vous ne pouvez pas effectuer un transfert vers votre propre numéro.');

            return redirect()->to('/client/dashboard');
        }

        if ($montant <= 0) {
            session()->setFlashdata('error', 'Le montant du transfert doit être supérieur à 0.');

            return redirect()->to('/client/dashboard');
        }

        $prefixeDest = substr($telephoneDest, 0, 3);
        
        // Détecter si interne ou externe
        $isInterne = $this->prefixeModel->estAutorise($prefixeDest);
        $externeInfo = null;
        
        if (! $isInterne) {
            $externeInfo = $this->prefixeExterneModel->trouverParPrefixe($prefixeDest);
            if ($externeInfo === null) {
                session()->setFlashdata('error', "Le préfixe {$prefixeDest} du destinataire n'est pas reconnu par un opérateur.");
                return redirect()->to('/client/dashboard');
            }
        }

        $frais = $this->baremeFraisModel->getFraisApplicable('transfert', $montant);
        $commission = 0.0;
        $fraisRetraitPrepaye = 0.0;

        if ($isInterne) {
            if ($inclureFraisRetrait) {
                $fraisRetraitPrepaye = $this->baremeFraisModel->getFraisApplicable('retrait', $montant);
                $frais += $fraisRetraitPrepaye; // Ajouté aux frais de l'expéditeur
            }
        } else {
            // Externe : calcul de la commission et exclusion des frais de retrait
            $pourcentageCommission = $this->commissionExterneModel->getPourcentage();
            $commission = ($pourcentageCommission / 100) * $montant;
        }

        $solde = $this->clientModel->getSolde($clientId);
        $totalDebite = $montant + $frais + $commission;

        if ($solde < $totalDebite) {
            session()->setFlashdata('error', 'Solde insuffisant pour ce transfert (requis : ' . number_format($totalDebite, 0, ',', ' ') . ' Ar, votre solde : ' . number_format($solde, 0, ',', ' ') . ' Ar).');

            return redirect()->to('/client/dashboard');
        }

        // Créer l'enregistrement client pour les besoins d'historique (interne ou externe)
        $destinataire = $this->clientModel->trouverOuCreer($telephoneDest);

        $transactionId = $this->transactionModel->enregistrerTransfert(
            $clientId,
            (int) $destinataire['id'],
            $montant,
            $frais,
            !$isInterne,
            $externeInfo ? (int) $externeInfo['autre_operateur_id'] : null,
            $commission
        );

        // Si interne et frais inclus, insérer un crédit frais de retrait
        if ($isInterne && $inclureFraisRetrait && $fraisRetraitPrepaye > 0) {
            $this->creditFraisRetraitModel->insert([
                'client_id'              => (int) $destinataire['id'],
                'transaction_origine_id' => $transactionId,
                'montant_credit'         => $fraisRetraitPrepaye,
                'utilise'                => 0,
                'transaction_retrait_id' => null
            ]);
        }

        if ($isInterne) {
            $msg = 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $telephoneDest . ' effectué.';
            if ($inclureFraisRetrait) {
                $msg .= ' (frais transfert : ' . number_format($frais - $fraisRetraitPrepaye, 0, ',', ' ') . ' Ar, frais retrait prépayés : ' . number_format($fraisRetraitPrepaye, 0, ',', ' ') . ' Ar).';
            } else {
                $msg .= ' (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).';
            }
        } else {
            $msg = 'Transfert externe de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $telephoneDest . ' (' . $externeInfo['operateur_nom'] . ') effectué.';
            $msg .= ' (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar, commission : ' . number_format($commission, 0, ',', ' ') . ' Ar).';
        }

        session()->setFlashdata('success', $msg);

        return redirect()->to('/client/dashboard');
    }

    public function transfertMultiple()
    {
        $clientId           = (int) session()->get('client_id');
        $telephonesRaw      = (string) $this->request->getPost('telephones');
        $montantTotal       = (float) $this->request->getPost('montant_total');
        $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');

        if ($montantTotal <= 0) {
            session()->setFlashdata('error', 'Le montant total doit être supérieur à 0.');
            return redirect()->to('/client/dashboard');
        }

        $telephones = preg_split('/[\s,;]+/', trim($telephonesRaw));
        $telephones = array_filter(array_map('trim', $telephones));

        if (empty($telephones)) {
            session()->setFlashdata('error', 'Veuillez saisir au moins un numéro de téléphone.');
            return redirect()->to('/client/dashboard');
        }

        $destinatairesValid = [];
        $client = $this->clientModel->find($clientId);

        foreach ($telephones as $tel) {
            if (! preg_match('/^0[0-9]{9}$/', $tel)) {
                session()->setFlashdata('error', "Numéro de destinataire invalide : {$tel}.");
                return redirect()->to('/client/dashboard');
            }

            if ($tel === $client['telephone']) {
                session()->setFlashdata('error', "Vous ne pouvez pas inclure votre propre numéro dans l'envoi multiple.");
                return redirect()->to('/client/dashboard');
            }

            $prefixe = substr($tel, 0, 3);
            if (! $this->prefixeModel->estAutorise($prefixe)) {
                session()->setFlashdata('error', "Le numéro {$tel} n'est pas interne (préfixe {$prefixe} non reconnu). L'envoi multiple est réservé à l'interne.");
                return redirect()->to('/client/dashboard');
            }

            $destinatairesValid[] = $tel;
        }

        $nbDestinataires = count($destinatairesValid);
        $montantParDestinataire = $montantTotal / $nbDestinataires;

        // Calcul des frais par destinataire
        $fraisParDestinataire = $this->baremeFraisModel->getFraisApplicable('transfert', $montantParDestinataire);
        $fraisRetraitParDestinataire = 0.0;

        if ($inclureFraisRetrait) {
            $fraisRetraitParDestinataire = $this->baremeFraisModel->getFraisApplicable('retrait', $montantParDestinataire);
        }

        $totalFraisParDest = $fraisParDestinataire + $fraisRetraitParDestinataire;
        $totalRequis = ($montantParDestinataire + $totalFraisParDest) * $nbDestinataires;

        $solde = $this->clientModel->getSolde($clientId);

        if ($solde < $totalRequis) {
            session()->setFlashdata('error', "Solde insuffisant pour cet envoi multiple (requis : " . number_format($totalRequis, 0, ',', ' ') . " Ar, votre solde : " . number_format($solde, 0, ',', ' ') . " Ar).");
            return redirect()->to('/client/dashboard');
        }

        // Générer un lot_id (UUID v4)
        $lotId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        foreach ($destinatairesValid as $tel) {
            $dest = $this->clientModel->trouverOuCreer($tel);
            
            $transactionId = $this->transactionModel->enregistrerTransfert(
                $clientId,
                (int) $dest['id'],
                $montantParDestinataire,
                $totalFraisParDest,
                false,
                null,
                0.0,
                $lotId
            );

            if ($inclureFraisRetrait && $fraisRetraitParDestinataire > 0) {
                $this->creditFraisRetraitModel->insert([
                    'client_id'              => (int) $dest['id'],
                    'transaction_origine_id' => $transactionId,
                    'montant_credit'         => $fraisRetraitParDestinataire,
                    'utilise'                => 0,
                    'transaction_retrait_id' => null
                ]);
            }
        }

        session()->setFlashdata('success', "Envoi multiple de " . number_format($montantTotal, 0, ',', ' ') . " Ar vers {$nbDestinataires} destinataires effectué avec succès (divisé à " . number_format($montantParDestinataire, 0, ',', ' ') . " Ar par numéro).");
        return redirect()->to('/client/dashboard');
    }
}
