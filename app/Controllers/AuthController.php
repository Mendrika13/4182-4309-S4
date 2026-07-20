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


    public function index()
    {
        if (session()->get('client_id')) {
            return redirect()->to('/client/dashboard');
        }

        return view('auth/login.php');
    }


    public function login()
    {
        $telephone = trim((string) $this->request->getPost('telephone'));


        if (! preg_match('/^0[0-9]{9}$/', $telephone)) {
            session()->setFlashdata('error', 'Numéro de téléphone invalide. Format attendu : 0XXXXXXXXX (10 chiffres).');

            return redirect()->to('/login');
        }

        $prefixe = substr($telephone, 0, 3);

        if (! $this->prefixeModel->estAutorise($prefixe)) {
            session()->setFlashdata('error', "Le préfixe {$prefixe} n'est pas reconnu par un opérateur Mobile Money.");

            return redirect()->to('/login');
        }


        $client = $this->clientModel->trouverOuCreer($telephone);

        session()->set([
            'client_id'   => $client['id'],
            'telephone'   => $client['telephone'],
            'isLoggedIn'  => true,
        ]);

        session()->setFlashdata('success', 'Connexion réussie. Bienvenue ' . $client['telephone'] . ' !');

        return redirect()->to('/client/dashboard');
    }


    public function logout()
    {
        session()->remove(['client_id', 'telephone', 'isLoggedIn']);
        session()->setFlashdata('success', 'Vous avez été déconnecté.');

        return redirect()->to('/login');
    }
}
