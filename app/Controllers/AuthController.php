<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\ClientModel;
use App\Models\PrefixeModel;

class AuthController
{
    private ClientModel $clientModel;
    private PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->prefixeModel = new PrefixeModel();
    }

    public function index(): void
    {
        if (Session::get('client_id')) {
            header('Location: ' . View::baseUrl('client/dashboard'));
            exit;
        }

        View::render('auth/login');
    }

    public function login(): void
    {
        if (! Session::verifierCsrf($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
            header('Location: ' . View::baseUrl('login'));
            exit;
        }

        $telephone = trim((string) ($_POST['telephone'] ?? ''));

        if (! preg_match('/^0[0-9]{9}$/', $telephone)) {
            Session::setFlash('error', 'Numéro de téléphone invalide. Format attendu : 0XXXXXXXXX (10 chiffres).');
            header('Location: ' . View::baseUrl('login'));
            exit;
        }

        $prefixe = substr($telephone, 0, 3);

        if (! $this->prefixeModel->estAutorise($prefixe)) {
            Session::setFlash('error', "Le préfixe {$prefixe} n'est pas reconnu par un opérateur Mobile Money.");
            header('Location: ' . View::baseUrl('login'));
            exit;
        }

        $client = $this->clientModel->trouverOuCreer($telephone);

        Session::set('client_id', $client['id']);
        Session::set('telephone', $client['telephone']);
        Session::set('isLoggedIn', true);

        Session::setFlash('success', 'Connexion réussie. Bienvenue ' . $client['telephone'] . ' !');
        header('Location: ' . View::baseUrl('client/dashboard'));
        exit;
    }

    public function logout(): void
    {
        Session::remove(['client_id', 'telephone', 'isLoggedIn']);
        Session::setFlash('success', 'Vous avez été déconnecté.');
        header('Location: ' . View::baseUrl('login'));
        exit;
    }
}
