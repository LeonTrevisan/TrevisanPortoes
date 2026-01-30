<?php

use App\Controllers\AdminController;

$router->get('/admin', [AdminController::class, 'index']);
$router->post('/admin/store', [AdminController::class, 'store']);

$router->post('/softDelete/ativar', [SoftDeleteController::class, 'ativar']);
$router->post('/softDelete/desativar', [SoftDeleteController::class, 'desativar']);


