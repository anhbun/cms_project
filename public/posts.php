<!-- public/posts.php -->

<?php
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\Database;

    session_start();

    echo "Role: " . $_SESSION['role'];  // Debugging output

    $config = require __DIR__ . '/../config/database.php';
    $db = new Database($config);
    $pdo = $db->getConnection();

    $stmt = $pdo->query('SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id = users.id ORDER BY created_at DESC');
    $posts = $stmt->fetchAll();

    echo "<br><a href='create_post.php?id={$post['id']}'> New </a>";

    foreach ($posts as $post) {
        echo "<h2>{$post['title']}</h2>";
        echo "<p>By: {$post['username']} on {$post['created_at']}</p>";
        echo "<p>{$post['content']}</p>";
        // Added Edit and Delete links
        echo "<a href='edit_post.php?id={$post['id']}'>Edit</a> | ";
        echo "<a href='delete_post.php?id={$post['id']}'>Delete</a>";
        echo "<hr>";
    }
?>

<a href="dashboard.php">Back to Dashboard</a>