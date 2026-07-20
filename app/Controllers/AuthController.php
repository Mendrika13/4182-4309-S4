<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;

class AuthController extends BaseController
{
    protected ClientModel $clientModel;
    protected PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->clientModel  = new ClientModel();
        $this->prefixeModel = new PrefixeModel();
    }

    /**
     * Affiche le formulaire de connexion. Si un client est déjà connecté,
     * on le redirige directement vers son tableau de bord.
     */
    public function index()
    {
        if (session()->get('client_id')) {
            return redirect()->to('/client/dashboard');
        }

        return view('auth/login.php');
    }

    /**
     * Traite la connexion par numéro de téléphone :
     * - vérifie le format du numéro,
     * - vérifie que le préfixe est autorisé,
     * - crée le compte automatiquement si le numéro est valide mais inconnu,
     * - enregistre le client en session.
     */
    public function login()
    {
        $telephone = trim((string) $this->request->getPost('telephone'));

        // Format attendu : 10 chiffres commençant par 0 (ex: 0331234567)
        if (! preg_match('/^0[0-9]{9}$/', $telephone)) {
            session()->setFlashdata('error', 'Numéro de téléphone invalide. Format attendu : 0XXXXXXXXX (10 chiffres).');

            return redirect()->to('/login');
        }

        $prefixe = substr($telephone, 0, 3);

        if (! $this->prefixeModel->estAutorise($prefixe)) {
            session()->setFlashdata('error', "Le préfixe {$prefixe} n'est pas reconnu par un opérateur Mobile Money.");

            return redirect()->to('/login');
        }

        // Récupère le client existant, ou le crée automatiquement
        $client = $this->clientModel->trouverOuCreer($telephone);

        session()->set([
            'client_id'   => $client['id'],
            'telephone'   => $client['telephone'],
            'isLoggedIn'  => true,
        ]);

        session()->setFlashdata('success', 'Connexion réussie. Bienvenue ' . $client['telephone'] . ' !');

        return redirect()->to('/client/dashboard');
    }

    /**
     * Déconnexion du client.
     */
    public function logout()
    {
        session()->remove(['client_id', 'telephone', 'isLoggedIn']);
        session()->setFlashdata('success', 'Vous avez été déconnecté.');

        return redirect()->to('/login');
    }
}
