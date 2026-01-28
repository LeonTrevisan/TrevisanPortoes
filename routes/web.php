<?php

use App\Controllers\AdminController;

$router->get('/admin', [AdminController::class, 'index']);
$router->post('/admin/store', [AdminController::class, 'store']);

$router->post('/ativar', [SoftDeleteController::class, 'ativar']);
$router->post('/desativar', [SoftDeleteController::class, 'desativar']);

