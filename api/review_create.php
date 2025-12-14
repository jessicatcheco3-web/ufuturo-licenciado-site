<?php
/**
 * UFUTURO Licenciado - Criar/Actualizar Avaliação do Curso
 * POST /api/review_create.php
 * 
 * Body: { "rating": 5, "comment": "Excelente curso!" }
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

setupHeaders();
$student = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

$input = getJsonInput();
$rating = (int)($input['rating'] ?? 0);
$comment = sanitize($input['comment'] ?? '');

// Validação
if ($rating < 1 || $rating > 5) {
    jsonResponse([
        'success' => false,
        'message' => 'A avaliação deve ser entre 1 e 5 estrelas.'
    ], 400);
}

// Limitar tamanho do comentário
$comment = mb_substr($comment, 0, 1000);

try {
    $db = getDB();
    
    // Upsert da avaliação
    $stmt = $db->prepare('
        INSERT INTO course_reviews (student_id, rating, comment, created_at)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            rating = VALUES(rating),
            comment = VALUES(comment)
    ');
    $stmt->execute([$student['id'], $rating, $comment ?: null]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Obrigado pela sua avaliação!'
    ]);
    
} catch (PDOException $e) {
    error_log('Erro ao submeter avaliação: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Erro ao submeter avaliação.'
    ], 500);
}
