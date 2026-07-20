<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\TransactionModel;
use App\Models\AutreOperateurModel;
use App\Models\PrefixeExterneModel;
use App\Models\CommissionExterneModel;

class OperateurController extends BaseController
{
    protected ClientModel $clientModel;
    protected TransactionModel $transactionModel;
    protected PrefixeModel $prefixeModel;
    protected AutreOperateurModel $autreOperateurModel;
    protected PrefixeExterneModel $prefixeExterneModel;
    protected CommissionExterneModel $commissionExterneModel;

    public function __construct()
    {
        $this->clientModel           = new ClientModel();
        $this->transactionModel      = new TransactionModel();
        $this->prefixeModel          = new PrefixeModel();
        $this->autreOperateurModel   = new AutreOperateurModel();
        $this->prefixeExterneModel   = new PrefixeExterneModel();
        $this->commissionExterneModel = new CommissionExterneModel();
    }

    



    public function index()
    {
        if (session()->get('is_operateur')) {
            return redirect()->to('/operateur/dashboard');
        }

        return view('operateur/login.php');
    }

    




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
        $gains = $this->transactionModel->getGainsSplit();
        
        $db = \Config\Database::connect();
        $montantsAEnvoyer = $db->table('v_montants_a_envoyer')->get()->getResultArray();

        $data = [
            'gainInterne'      => $gains['gain_interne'],
            'gainExterne'      => $gains['gain_externe'],
            'clients'          => $this->clientModel->getTousAvecSolde(),
            'prefixes'         => $this->prefixeModel->listeTriee(),
            'autresOperateurs' => $this->autreOperateurModel->orderBy('nom', 'ASC')->findAll(),
            'prefixesExternes' => $this->prefixeExterneModel->select('prefixes_externes.*, autres_operateurs.nom AS operateur_nom')
                ->join('autres_operateurs', 'autres_operateurs.id = prefixes_externes.autre_operateur_id')
                ->orderBy('prefixe', 'ASC')
                ->findAll(),
            'commission'       => $this->commissionExterneModel->getPourcentage(),
            'montantsAEnvoyer' => $montantsAEnvoyer
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
            session()->setFlashdata('error', "Le préfixe {$prefixe} existe déjà en interne.");

            return redirect()->to('/operateur/dashboard');
        }

        
        if ($this->prefixeExterneModel->where('prefixe', $prefixe)->first() !== null) {
            session()->setFlashdata('error', "Le préfixe {$prefixe} est déjà configuré comme externe.");

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

    public function ajouterAutreOperateur()
    {
        $nom = trim((string) $this->request->getPost('nom'));
        if (empty($nom)) {
            session()->setFlashdata('error', 'Le nom de l\'opérateur ne peut pas être vide.');
            return redirect()->to('/operateur/dashboard');
        }

        try {
            $this->autreOperateurModel->insert(['nom' => $nom]);
            session()->setFlashdata('success', "Opérateur {$nom} ajouté avec succès.");
        } catch (\Exception $e) {
            session()->setFlashdata('error', "Erreur : cet opérateur existe déjà.");
        }

        return redirect()->to('/operateur/dashboard');
    }

    public function supprimerAutreOperateur($id)
    {
        try {
            $this->autreOperateurModel->delete((int) $id);
            session()->setFlashdata('success', 'Opérateur externe supprimé.');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Impossible de supprimer cet opérateur (des préfixes ou des transactions y sont peut-être liés).');
        }

        return redirect()->to('/operateur/dashboard');
    }

    public function ajouterPrefixeExterne()
    {
        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $autreOperateurId = (int) $this->request->getPost('autre_operateur_id');

        if (! preg_match('/^[0-9]{3}$/', $prefixe)) {
            session()->setFlashdata('error', 'Le préfixe doit être composé de 3 chiffres exactement.');
            return redirect()->to('/operateur/dashboard');
        }

        if ($this->prefixeModel->estAutorise($prefixe)) {
            session()->setFlashdata('error', "Ce préfixe {$prefixe} est déjà configuré chez votre opérateur interne.");
            return redirect()->to('/operateur/dashboard');
        }

        if ($this->prefixeExterneModel->where('prefixe', $prefixe)->first() !== null) {
            session()->setFlashdata('error', "Le préfixe {$prefixe} existe déjà chez un autre opérateur.");
            return redirect()->to('/operateur/dashboard');
        }

        $this->prefixeExterneModel->insert([
            'prefixe'            => $prefixe,
            'autre_operateur_id' => $autreOperateurId
        ]);

        session()->setFlashdata('success', "Préfixe externe {$prefixe} ajouté avec succès.");
        return redirect()->to('/operateur/dashboard');
    }

    public function supprimerPrefixeExterne($id)
    {
        $this->prefixeExterneModel->delete((int) $id);
        session()->setFlashdata('success', 'Préfixe externe supprimé.');
        return redirect()->to('/operateur/dashboard');
    }

    public function modifierCommission()
    {
        $pourcentage = (float) $this->request->getPost('pourcentage');
        if ($pourcentage < 0 || $pourcentage > 100) {
            session()->setFlashdata('error', 'Le pourcentage doit être compris entre 0 et 100.');
            return redirect()->to('/operateur/dashboard');
        }

        $this->commissionExterneModel->modifierPourcentage($pourcentage);
        session()->setFlashdata('success', "Pourcentage de commission externe mis à jour à {$pourcentage}%.");
        return redirect()->to('/operateur/dashboard');
    }
}
