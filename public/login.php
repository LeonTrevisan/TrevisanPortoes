<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Auth;
use App\Core\Database;
use App\Repositories\FuncionarioRepository;
use App\Services\FuncionarioService;

Auth::start();

if (Auth::check()) {
    $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
    header('Location: ' . $baseUrl . '/');
    exit();
}

// Garante schema de funcionarios e usuario admin padrao.
$db = Database::connect();
$repo = new FuncionarioRepository($db);
new FuncionarioService($repo);

$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
$loginToken = Auth::csrfToken('login');
$status = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Trevisan Portoes Automaticos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Trevisan Portoes Automaticos</h1>
            <p>Efetue o login para acessar o sistema.</p>

            <?php if ($status && $message): ?>
                <div class="auth-alert <?= $status === 'success' ? 'auth-alert-success' : 'auth-alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="<?= $baseUrl ?>/auth/login" method="POST" class="auth-form">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($loginToken) ?>">

                <div class="form-group">
                    <label for="login_email">Email:</label>
                    <input type="email" id="login_email" name="email" autocomplete="username" required>
                </div>

                <div class="form-group">
                    <label for="login_senha">Senha:</label>
                    <input type="password" id="login_senha" name="senha" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn btn-primary auth-submit">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
