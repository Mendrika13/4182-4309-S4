<?php

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\OperateurController;
use App\Core\Router;

$router = new Router();

$router->get('/', [AuthController::class, 'index']);
$router->get('/login', [AuthController::class, 'index']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/client/dashboard', [ClientController::class, 'dashboard'], 'clientAuth');
$router->post('/client/depot', [ClientController::class, 'depot'], 'clientAuth');
$router->post('/client/retrait', [ClientController::class, 'retrait'], 'clientAuth');
$router->post('/client/transfert', [ClientController::class, 'transfert'], 'clientAuth');

$router->get('/operateur/login', [OperateurController::class, 'index']);
$router->post('/operateur/login', [OperateurController::class, 'login']);
$router->get('/operateur/logout', [OperateurController::class, 'logout']);

$router->get('/operateur/dashboard', [OperateurController::class, 'dashboard'], 'operateurAuth');
$router->post('/operateur/prefixe/ajouter', [OperateurController::class, 'ajouterPrefixe'], 'operateurAuth');
$router->get('/operateur/prefixe/supprimer/(:num)', [OperateurController::class, 'supprimerPrefixe'], 'operateurAuth');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
