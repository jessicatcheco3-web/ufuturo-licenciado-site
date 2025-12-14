<?php
/**
 * UFUTURO Licenciado - Login do Estudante
 * POST /api/auth_login.php
 * 
 * Body: { "username": "estudante.nome", "password": "senha" }
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

setupHeaders();
startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

$input = getJsonInput();
$username = sanitize($input['username'] ?? '');
$password = $input['password'] ?? '';

// Validação
if (empty($username) || empty($password)) {
    jsonResponse([
        'success' => false,
        'message' => 'Nome de utilizador e palavra-passe são obrigatórios.'
    ], 400);
}

try {
    $db = getDB();
    
    // Buscar estudante
    $stmt = $db->prepare('
        SELECT id, username, full_name, password_hash, is_active 
        FROM students 
        WHERE username = ?
    ');
    $stmt->execute([$username]);
    $student = $stmt->fetch();
    
    if (!$student) {
        jsonResponse([
            'success' => false,
            'message' => 'Credenciais inválidas.'
        ], 401);
    }
    
    // Verificar palavra-passe
    if (!password_verify($password, $student['password_hash'])) {
        jsonResponse([
            'success' => false,
            'message' => 'Credenciais inválidas.'
        ], 401);
    }
    
    // Verificar se está activo
    if (!$student['is_active']) {
        jsonResponse([
            'success' => false,
            'message' => 'A sua conta está desactivada. Contacte o suporte.'
        ], 403);
    }
    
    // Regenerar ID da sessão para segurança
    session_regenerate_id(true);
    
    // Guardar dados na sessão
    $_SESSION['student_id'] = $student['id'];
    $_SESSION['username'] = $student['username'];
    $_SESSION['full_name'] = $student['full_name'];
    
    // Actualizar último acesso
    $updateStmt = $db->prepare('UPDATE students SET last_login = NOW() WHERE id = ?');
    $updateStmt->execute([$student['id']]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Login efectuado com sucesso!',
        'user' => [
            'id' => $student['id'],
            'username' => $student['username'],
            'full_name' => $student['full_name']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Erro no login: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Erro interno. Tente novamente.'
    ], 500);
}
