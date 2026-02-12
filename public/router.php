<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
require_once '../app/Helpers/formatacao.php';

use App\Core\Auth;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\ClienteController;
use App\Controllers\CompraController;
use App\Controllers\FuncionarioController;
use App\Controllers\ServicoController;
use App\Controllers\SindicoController;
use App\Controllers\SoftDeleteController;

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router = [
    'GET' => [
        '/funcionarios/obter' => [FuncionarioController::class, 'obter'],
        '/admin' => [AdminController::class, 'index'],
        '/admin/obter' => [AdminController::class, 'obter'],
        '/admin/ficha' => [AdminController::class, 'ficha'],
        '/clientes' => [ClienteController::class, 'index'],
        '/clientes/obter' => [ClienteController::class, 'obter'],
        '/clientes/ficha' => [ClienteController::class, 'ficha'],
        '/sindico' => [SindicoController::class, 'index'],
        '/sindico/obter' => [SindicoController::class, 'obter'],
        '/sindico/ficha' => [SindicoController::class, 'ficha'],
        '/servicos' => [ServicoController::class, 'index'],
        '/servicos/obter' => [ServicoController::class, 'obter'],
        '/servicos/ficha' => [ServicoController::class, 'ficha'],
        '/compras' => [CompraController::class, 'index'],
        '/compras/obter' => [CompraController::class, 'obter'],
    ],
    'POST' => [
        '/auth/login' => [AuthController::class, 'login'],
        '/auth/logout' => [AuthController::class, 'logout'],
        '/funcionarios/store' => [FuncionarioController::class, 'store'],
        '/funcionarios/update' => [FuncionarioController::class, 'update'],
        '/funcionarios/desativar' => [FuncionarioController::class, 'desativar'],
        '/admin/store' => [AdminController::class, 'store'],
        '/admin/update' => [AdminController::class, 'update'],
        '/clientes/store' => [ClienteController::class, 'store'],
        '/clientes/update' => [ClienteController::class, 'update'],
        '/sindico/store' => [SindicoController::class, 'store'],
        '/sindico/update' => [SindicoController::class, 'update'],
        '/sindico/obter' => [SindicoController::class, 'obter'],
        '/sindico/ficha' => [SindicoController::class, 'ficha'],
        '/servicos/store' => [ServicoController::class, 'store'],
        '/servicos/update' => [ServicoController::class, 'update'],
        '/compras/store' => [CompraController::class, 'store'],
        '/compras/update' => [CompraController::class, 'update'],
        '/softDelete/ativar' => [SoftDeleteController::class, 'ativar'],
        '/softDelete/desativar' => [SoftDeleteController::class, 'desativar'],
        '/softDelete/reativar' => [SoftDeleteController::class, 'reativar'],
    ],
];

$path = isset($_GET['path']) ? '/' . $_GET['path'] : parse_url($request, PHP_URL_PATH);
$base = dirname($_SERVER['SCRIPT_NAME']);
if ($base !== '/') {
    $path = str_replace($base, '', $path);
}

Auth::start();
$publicRoutes = [
    'POST' => ['/auth/login'],
];

$isPublicRoute = in_array($path, $publicRoutes[$method] ?? [], true);
if (!$isPublicRoute && !Auth::check()) {
    header('Location: ' . $base . '/login.php');
    exit();
}

if (isset($router[$method][$path])) {
    $controllerClass = $router[$method][$path][0];
    $methodName = $router[$method][$path][1];
    $controller = new $controllerClass();
    $controller->$methodName();
} else {
    http_response_code(404);
    echo 'Rota nao encontrada';
}
