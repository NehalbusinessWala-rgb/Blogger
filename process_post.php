<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $author = $_POST['author'] ?? '';
    $category = $_POST['category'] ?? '';

    if (empty($title) || empty($content) || empty($author) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, author, category) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $author, $category]);
            echo json_encode(['status' => 'success']);
        } elseif ($action === 'edit') {
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'Missing post ID.']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, author = ?, category = ? WHERE id = ?");
            $stmt->execute([$title, $content, $author, $category, $id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        }
    } catch (\PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
