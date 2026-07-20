<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\TransactionModel;

class OperateurController extends BaseController
{
    protected ClientModel $clientModel;
    protected TransactionModel $transactionModel;
    protected PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->clientModel      = new ClientModel();
        $this->transactionModel = new TransactionModel();
        $this->prefixeModel     = new PrefixeModel();
    }

    /**
     * Affiche le formulaire de connexion opérateur. Si déjà connecté,
     * redirige directement vers le dashboard.
     */
    public function index()
    {
        if (session()->get('is_operateur')) {
            return redirect()->to('/operateur/dashboard');
        }

        return view('operateur/login.php');
    }

    /**
     * Traite la connexion opérateur via un mot de passe simple défini
     * dans le fichier .env (OPERATEUR_PASSWORD). Mécanisme volontairement
     * minimal pour cette Version 1.
     */
    public function login()
    {
        $motDePasse     = (string) $this->request->getPost('mot_de_passe');
        $motDePasseAttendu = env('OPERATEUR_PASSWORD', 'admin123');

        if ($motDePasse !== $motDePasseAttendu) {
            session()->setFlashdata('error', 'Mot de passe opérateur incorrect.');

            return redirect()->to('/operateur/login');
        }

        session()->set('is_operateur', true);
        session()->setFlashdata('success', 'Connexion opérateur réussie.');

        return redirect()->to('/operateur/dashboard');
    }


    public function logout()
    {
        session()->remove('is_operateur');
        session()->setFlashdata('success', 'Déconnexion opérateur effectuée.');

        return redirect()->to('/operateur/login');
    }


    public function dashboard()
    {
        $data = [
            'gainGlobal' => $this->transactionModel->getGainTotalOperateur(),
            'clients'    => $this->clientModel->getTousAvecSolde(),
            'prefixes'   => $this->prefixeModel->listeTriee(),
        ];

        return view('operateur/dashboard.php', $data);
    }


    public function ajouterPrefixe()
    {
        $prefixe = trim((string) $this->request->getPost('prefixe'));

        if (! preg_match('/^[0-9]{3}$/', $prefixe)) {
            session()->setFlashdata('error', 'Le préfixe doit être composé de 3 chiffres exactement.');

            return redirect()->to('/operateur/dashboard');
        }

        if ($this->prefixeModel->estAutorise($prefixe)) {
            session()->setFlashdata('error', "Le préfixe {$prefixe} existe déjà.");

            return redirect()->to('/operateur/dashboard');
        }

        $this->prefixeModel->insert(['prefixe' => $prefixe]);

        session()->setFlashdata('success', "Préfixe {$prefixe} ajouté avec succès.");

        return redirect()->to('/operateur/dashboard');
    }

    public function supprimerPrefixe($id)
    {
        $this->prefixeModel->delete((int) $id);

        session()->setFlashdata('success', 'Préfixe supprimé.');

        return redirect()->to('/operateur/dashboard');
    }
}
