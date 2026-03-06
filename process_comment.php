<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'] ?? 0;
    $author = $_POST['author'] ?? '';
    $comment = $_POST['comment'] ?? '';

    if (!$post_id || empty($author) || empty($comment)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, author, comment) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $author, $comment]);
        echo json_encode(['status' => 'success']);
    } catch (\PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
