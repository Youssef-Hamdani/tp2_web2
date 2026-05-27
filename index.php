<?php

declare(strict_types=1);

use App\Controllers\AccountController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Core\App;
use App\Core\Router;
use App\Core\Security;

require __DIR__ . '/app/Core/bootstrap.php';

App::boot(__DIR__ . '/config/config.php');
Security::boot();

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/inscription', [AuthController::class, 'showRegister']);
$router->post('/inscription', [AuthController::class, 'register']);
$router->get('/connexion', [AuthController::class, 'showLogin']);
$router->post('/connexion', [AuthController::class, 'login']);
$router->post('/deconnexion', [AuthController::class, 'logout']);
$router->get('/activation', [AuthController::class, 'activate']);
$router->get('/mot-de-passe-oublie', [AuthController::class, 'showForgotPassword']);
$router->post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink']);
$router->get('/reinitialiser-mot-de-passe', [AuthController::class, 'showResetPassword']);
$router->post('/reinitialiser-mot-de-passe', [AuthController::class, 'resetPassword']);

$router->get('/compte', [AccountController::class, 'index']);
$router->get('/compte/mot-de-passe', [AccountController::class, 'showPasswordForm']);
$router->post('/compte/mot-de-passe', [AccountController::class, 'updatePassword']);
$router->get('/compte/achats', [AccountController::class, 'purchases']);
$router->get('/compte/ventes', [AccountController::class, 'sales']);

$router->get('/produits', [ProductController::class, 'index']);
$router->get('/produits/ajouter', [ProductController::class, 'create']);
$router->post('/produits', [ProductController::class, 'store']);
$router->get('/produits/{id}', [ProductController::class, 'show']);
$router->get('/produits/{id}/modifier', [ProductController::class, 'edit']);
$router->post('/produits/{id}/modifier', [ProductController::class, 'update']);
$router->post('/produits/{id}/supprimer', [ProductController::class, 'destroy']);

$router->get('/panier', [CartController::class, 'show']);
$router->post('/panier/ajouter/{id}', [CartController::class, 'add']);
$router->post('/panier/vider', [CartController::class, 'clear']);

$router->get('/commande', [CheckoutController::class, 'show']);
$router->post('/commande/session-stripe', [CheckoutController::class, 'createStripeSession']);
$router->get('/commande/succes', [CheckoutController::class, 'success']);
$router->get('/commande/annulee', [CheckoutController::class, 'cancel']);

$router->dispatch();
