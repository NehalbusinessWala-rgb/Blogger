<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        
        // Use JS for redirection as requested
        echo "<script>window.location.href = 'index.php';</script>";
        exit;
    } catch (\PDOException $e) {
        die("Error deleting post: " . $e->getMessage());
    }
} else {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
?>
