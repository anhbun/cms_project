<!-- public/delete_post.php -->

<?php

require __DIR__ . '/../vendor/autoload.php';

use Nibun\CmsProject\Database;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Editor') {
    echo "You do NOT have permission to delete this post...";
    exit;
}

$config = require __DIR__ . '/../config/database.php';
$db = new Database($config);
$pdo = $db->getConnection();

// Check if the posr ID is provided in the URL
if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Delete the post from the database
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    if ($stmt->execute([$postId])) {
        header('Location: posts.php');
    } else {
        echo "Failed to delete post...";
    }
} else {
    echo "Invalid request...";
    exit;
}

?>
