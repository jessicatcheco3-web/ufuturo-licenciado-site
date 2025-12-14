<?php
/**
 * UFUTURO Licenciado - Guardar Progresso do Estudante
 * POST /api/progress_upsert.php
 * 
 * Body: { "module_id": "modulo-1", "lesson_id": "aula-1", "completed": true }
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

setupHeaders();
$student = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

$input = getJsonInput();
$moduleId = sanitize($input['module_id'] ?? '');
$lessonId = sanitize($input['lesson_id'] ?? '');
$completed = (bool)($input['completed'] ?? false);

// Validação
if (empty($moduleId) || empty($lessonId)) {
    jsonResponse([
        'success' => false,
        'message' => 'ID do módulo e da aula são obrigatórios.'
    ], 400);
}

try {
    $db = getDB();
    
    // Upsert (INSERT ou UPDATE)
    $stmt = $db->prepare('
        INSERT INTO lesson_progress (student_id, module_id, lesson_id, completed, completed_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            completed = VALUES(completed),
            completed_at = IF(VALUES(completed), IFNULL(completed_at, NOW()), NULL),
            updated_at = NOW()
    ');
    
    $completedAt = $completed ? date('Y-m-d H:i:s') : null;
    $stmt->execute([
        $student['id'],
        $moduleId,
        $lessonId,
        $completed ? 1 : 0,
        $completedAt
    ]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Progresso guardado com sucesso.'
    ]);
    
} catch (PDOException $e) {
    error_log('Erro ao guardar progresso: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Erro ao guardar progresso.'
    ], 500);
}
