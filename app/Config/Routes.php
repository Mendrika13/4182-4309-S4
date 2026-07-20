<?php

use CodeIgniter\Router\RouteCollection;





$routes->get('/', 'AuthController::index');
$routes->get('login', 'AuthController::index');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');


$routes->get('operateur/login', 'OperateurController::index');
$routes->post('operateur/login', 'OperateurController::login');
$routes->get('operateur/logout', 'OperateurController::logout');

$routes->group('operateur', ['filter' => 'operateurAuth'], static function ($routes) {
    $routes->get('dashboard', 'OperateurController::dashboard');
    $routes->post('prefixe/ajouter', 'OperateurController::ajouterPrefixe');
    $routes->get('prefixe/supprimer/(:num)', 'OperateurController::supprimerPrefixe/$1');
    
    
    $routes->post('autre-operateur/ajouter', 'OperateurController::ajouterAutreOperateur');
    $routes->get('autre-operateur/supprimer/(:num)', 'OperateurController::supprimerAutreOperateur/$1');
    
    
    $routes->post('prefixe-externe/ajouter', 'OperateurController::ajouterPrefixeExterne');
    $routes->get('prefixe-externe/supprimer/(:num)', 'OperateurController::supprimerPrefixeExterne/$1');
    
    
    $routes->post('commission/modifier', 'OperateurController::modifierCommission');
});

$routes->group('client', ['filter' => 'clientAuth'], static function ($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->post('depot', 'ClientController::depot');
    $routes->post('retrait', 'ClientController::retrait');
    $routes->post('transfert', 'ClientController::transfert');
    $routes->post('transfert-multiple', 'ClientController::transfertMultiple');
});

