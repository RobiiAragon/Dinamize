<?php
// backend/security.php — shared helpers: hardened session cookie, HTML escaping and login guards.

if (!function_exists('start_secure_session')) {
    /**
     * Starts the session with a hardened cookie:
     *  - HttpOnly: JavaScript can't read the session id (limits damage of XSS).
     *  - SameSite=Lax: the browser won't send it on cross-site POST/fetch requests (CSRF protection).
     *  - Secure when served over HTTPS.
     */
    function start_secure_session(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $https,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    /** Escapes a value for safe output inside HTML text or attributes. */
    function e($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** For JSON / fetch endpoints: 401 if there is no logged-in user. */
    function require_login_json(): int
    {
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada. Inicia sesión de nuevo.']);
            exit();
        }
        return (int) $_SESSION['user_id'];
    }

    /** For form endpoints: redirect to the login page if there is no logged-in user. */
    function require_login_redirect(string $loginUrl = '../login.php'): int
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . $loginUrl);
            exit();
        }
        return (int) $_SESSION['user_id'];
    }
}
