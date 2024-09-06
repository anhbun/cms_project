<!-- public/create_post.php -->

<?php
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\Database;

    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Editor') {
        echo "You do NOT have permission to create this post...";
        exit;
    }

    $config = require __DIR__ . '/../config/database.php';
    $db = new Database($config);
    $pdo = $db->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $author_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare('INSERT INTO posts (title, content, author_id) VALUES (?, ?, ?)');
        if ($stmt->execute([$title, $content, $author_id])) {
            header('Location: posts.php');
        } else {
            echo "Failed to create post.";
        }
    }
?>

<form action="create_post.php" method="post">
    <input type="text" name="title" placeholder="Post Title" required>
    <textarea name="content" placeholder="Post Content" required></textarea>
    <button type="submit"> Create Post </button>
</form>