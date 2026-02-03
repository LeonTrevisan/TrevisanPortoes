<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\DistribuidoraRepository;
use App\Services\DistribuidoraService;

class DistribuidoraController
{
    private DistribuidoraService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new DistribuidoraRepository($db);
        $this->service = new DistribuidoraService($repo);
    }

    public function select() {
        $distribuidoras = $this->service->listarAtivos();
        require __DIR__ . '/../Views/distribuidoras/select.php';
    }
}