<?php
/**
 * UFUTURO Licenciado - Obter Progresso do Estudante
 * GET /api/progress_get.php
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

setupHeaders();
$student = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

try {
    $db = getDB();
    
    $stmt = $db->prepare('
        SELECT module_id, lesson_id, completed, completed_at 
        FROM lesson_progress 
        WHERE student_id = ?
    ');
    $stmt->execute([$student['id']]);
    $progress = $stmt->fetchAll();
    
    // Formatar como array de "moduleId:lessonId"
    $completedLessons = [];
    foreach ($progress as $row) {
        if ($row['completed']) {
            $completedLessons[] = $row['module_id'] . ':' . $row['lesson_id'];
        }
    }
    
    jsonResponse([
        'success' => true,
        'data' => [
            'completedLessons' => $completedLessons
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Erro ao obter progresso: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Erro ao carregar progresso.'
    ], 500);
}
