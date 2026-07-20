<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\TransactionModel;

class OperateurController
{
    private ClientModel $clientModel;
    private TransactionModel $transactionModel;
    private PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->transactionModel = new TransactionModel();
        $this->prefixeModel = new PrefixeModel();
    }

    public function index(): void
    {
        if (Session::get('is_operateur')) {
            header('Location: ' . View::baseUrl('operateur/dashboard'));
            exit;
        }

        View::render('operateur/login');
    }

    public function login(): void
    {
        if (! Session::verifierCsrf($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
            header('Location: ' . View::baseUrl('operateur/login'));
            exit;
        }

        $motDePasse = (string) ($_POST['mot_de_passe'] ?? '');
        $motDePasseAttendu = env('OPERATEUR_PASSWORD', 'admin123');

        if ($motDePasse !== $motDePasseAttendu) {
            Session::setFlash('error', 'Mot de passe opérateur incorrect.');
            header('Location: ' . View::baseUrl('operateur/login'));
            exit;
        }

        Session::set('is_operateur', true);
        Session::setFlash('success', 'Connexion opérateur réussie.');
        header('Location: ' . View::baseUrl('operateur/dashboard'));
        exit;
    }

    public function logout(): void
    {
        Session::remove(['is_operateur']);
        Session::setFlash('success', 'Déconnexion opérateur effectuée.');
        header('Location: ' . View::baseUrl('operateur/login'));
        exit;
    }

    public function dashboard(): void
    {
        View::render('operateur/dashboard', [
            'gainGlobal' => $this->transactionModel->getGainTotalOperateur(),
            'clients'    => $this->clientModel->getTousAvecSolde(),
            'prefixes'   => $this->prefixeModel->listeTriee(),
        ]);
    }

    public function ajouterPrefixe(): void
    {
        if (! Session::verifierCsrf($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
            header('Location: ' . View::baseUrl('operateur/dashboard'));
            exit;
        }

        $prefixe = trim((string) ($_POST['prefixe'] ?? ''));

        if (! preg_match('/^[0-9]{3}$/', $prefixe)) {
            Session::setFlash('error', 'Le préfixe doit être composé de 3 chiffres exactement.');
            header('Location: ' . View::baseUrl('operateur/dashboard'));
            exit;
        }

        if ($this->prefixeModel->estAutorise($prefixe)) {
            Session::setFlash('error', "Le préfixe {$prefixe} existe déjà.");
            header('Location: ' . View::baseUrl('operateur/dashboard'));
            exit;
        }

        $this->prefixeModel->ajouter($prefixe);

        Session::setFlash('success', "Préfixe {$prefixe} ajouté avec succès.");
        header('Location: ' . View::baseUrl('operateur/dashboard'));
        exit;
    }

    public function supprimerPrefixe(string $id): void
    {
        $this->prefixeModel->supprimer((int) $id);
        Session::setFlash('success', 'Préfixe supprimé.');
        header('Location: ' . View::baseUrl('operateur/dashboard'));
        exit;
    }
}
