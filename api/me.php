<?php
/**
 * UFUTURO Licenciado - Dados do Estudante Autenticado
 * GET /api/me.php
 * 
 * Retorna os dados do estudante autenticado ou 401 se não estiver autenticado.
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

setupHeaders();
startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

// Verificar se há sessão activa
if (empty($_SESSION['student_id'])) {
    jsonResponse([
        'success' => false,
        'authenticated' => false,
        'message' => 'Não autenticado'
    ], 401);
}

try {
    $db = getDB();
    
    // Buscar dados actualizados do estudante
    $stmt = $db->prepare('
        SELECT id, username, full_name, is_active 
        FROM students 
        WHERE id = ?
    ');
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch();
    
    if (!$student || !$student['is_active']) {
        // Limpar sessão se estudante não existe ou está inactivo
        $_SESSION = [];
        session_destroy();
        
        jsonResponse([
            'success' => false,
            'authenticated' => false,
            'message' => 'Conta não encontrada ou desactivada.'
        ], 401);
    }
    
    jsonResponse([
        'success' => true,
        'authenticated' => true,
        'user' => [
            'id' => $student['id'],
            'username' => $student['username'],
            'full_name' => $student['full_name']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Erro ao verificar sessão: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Erro interno.'
    ], 500);
}
