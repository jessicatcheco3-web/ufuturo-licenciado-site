<?php
/**
 * UFUTURO Licenciado - Criar Estudante (Admin)
 * POST /api/admin_create_student.php
 * 
 * Body: { 
 *   "admin_key": "CHAVE_SECRETA_ADMIN",
 *   "username": "estudante.nome", 
 *   "full_name": "Nome Completo",
 *   "password": "senha123",
 *   "whatsapp": "+258841234567"
 * }
 * 
 * IMPORTANTE: Altere ADMIN_SECRET_KEY antes de usar em produção!
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

setupHeaders();

// Chave secreta para operações administrativas
define('ADMIN_SECRET_KEY', 'Jesuseocaminho1');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

$input = getJsonInput();
$adminKey = $input['admin_key'] ?? '';
$username = sanitize($input['username'] ?? '');
$fullName = sanitize($input['full_name'] ?? '');
$password = $input['password'] ?? '';
$whatsapp = sanitize($input['whatsapp'] ?? '');

// Verificar chave administrativa
if ($adminKey !== ADMIN_SECRET_KEY) {
    jsonResponse([
        'success' => false,
        'message' => 'Acesso não autorizado.'
    ], 403);
}

// Validação
if (empty($username) || empty($fullName) || empty($password)) {
    jsonResponse([
        'success' => false,
        'message' => 'Nome de utilizador, nome completo e palavra-passe são obrigatórios.'
    ], 400);
}

if (strlen($password) < 6) {
    jsonResponse([
        'success' => false,
        'message' => 'A palavra-passe deve ter pelo menos 6 caracteres.'
    ], 400);
}

// Validar formato do username (estudante.nome)
if (!preg_match('/^[a-z0-9]+\.[a-z0-9]+$/i', $username)) {
    jsonResponse([
        'success' => false,
        'message' => 'O nome de utilizador deve estar no formato: estudante.nome'
    ], 400);
}

try {
    $db = getDB();
    
    // Verificar se username já existe
    $checkStmt = $db->prepare('SELECT id FROM students WHERE username = ?');
    $checkStmt->execute([$username]);
    
    if ($checkStmt->fetch()) {
        jsonResponse([
            'success' => false,
            'message' => 'Este nome de utilizador já está registado.'
        ], 409);
    }
    
    // Hash da palavra-passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Inserir estudante
    $stmt = $db->prepare('
        INSERT INTO students (username, full_name, whatsapp, password_hash, is_active)
        VALUES (?, ?, ?, ?, 1)
    ');
    $stmt->execute([$username, $fullName, $whatsapp ?: null, $passwordHash]);
    
    $studentId = $db->lastInsertId();
    
    jsonResponse([
        'success' => true,
        'message' => 'Estudante criado com sucesso!',
        'data' => [
            'id' => $studentId,
            'username' => $username,
            'full_name' => $fullName
        ]
    ], 201);
    
} catch (PDOException $e) {
    error_log('Erro ao criar estudante: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Erro ao criar estudante.'
    ], 500);
}
