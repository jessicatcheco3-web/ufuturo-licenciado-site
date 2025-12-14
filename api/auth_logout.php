<?php
/**
 * UFUTURO Licenciado - Logout do Estudante
 * POST /api/auth_logout.php
 */

require_once __DIR__ . '/helpers.php';

setupHeaders();
startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

// Limpar dados da sessão
$_SESSION = [];

// Destruir cookie da sessão
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

// Destruir sessão
session_destroy();

jsonResponse([
    'success' => true,
    'message' => 'Sessão terminada com sucesso.'
]);
