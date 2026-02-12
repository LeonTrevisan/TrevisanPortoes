<?php

namespace App\Core;

class Auth
{
    private const SESSION_USER_KEY = 'auth_user';
    private const CSRF_KEY = 'csrf_tokens';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);

        $_SESSION[self::SESSION_USER_KEY] = [
            'id_funcionario' => (int)($user['id_funcionario'] ?? 0),
            'nome' => $user['nome'] ?? '',
            'email' => $user['email'] ?? '',
            'id_role' => (int)($user['id_role'] ?? 0),
            'role_nome' => $user['role_nome'] ?? ($user['role'] ?? ''),
            'is_admin' => !empty($user['is_admin']),
            'session_token' => bin2hex(random_bytes(32)),
        ];
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function user(): ?array
    {
        self::start();
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return !empty($user['is_admin']);
    }

    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }

        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header('Location: ' . $baseUrl . '/login.php');
        exit();
    }

    public static function csrfToken(string $context): string
    {
        self::start();

        if (!isset($_SESSION[self::CSRF_KEY])) {
            $_SESSION[self::CSRF_KEY] = [];
        }

        if (
            !isset($_SESSION[self::CSRF_KEY][$context]) ||
            !is_string($_SESSION[self::CSRF_KEY][$context]) ||
            $_SESSION[self::CSRF_KEY][$context] === ''
        ) {
            $_SESSION[self::CSRF_KEY][$context] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CSRF_KEY][$context];
    }

    public static function validateCsrfToken(string $context, ?string $token): bool
    {
        self::start();
        if (empty($token) || !isset($_SESSION[self::CSRF_KEY][$context])) {
            return false;
        }

        return hash_equals($_SESSION[self::CSRF_KEY][$context], $token);
    }
}
