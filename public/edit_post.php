<!-- public/edit_post.php -->

<?php

require __DIR__ . '/../vendor/autoload.php';

use Nibun\CmsProject\Database;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Editor') {
    echo "You do NOT have permission to edit this post...";
    exit;
}

$config = require __DIR__ . '/../config/database.php';
$db = new Database($config);
$pdo = $db->getConnection();

// DEBUGGING output to ensure database connection
if ($pdo) {
    echo "DEBUG: Database connection established!<br>";
} else {
    echo "DEBUG: Database connection failed...<br>";
    exit;
}

// Check if the post ID is provided in the URL and is valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = (int)$_GET['id'];

    // DEBUGGING output to show the queried post ID
    echo "DEBUG: Querying post with ID: $postId<br>";

    // Fetch the post details from the database
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$postId]);
    $post = $stmt->fetch();

    // DEBUGGING output to confirm the fetch operation
    if ($post) {
        echo "DEBUG: Fetched post ID: " . htmlspecialchars($post['id']) . "<br>";
        echo "DEBUG: Fetched post Title: " . htmlspecialchars($post['title']) . "<br>";
    } else {
        echo "DEBUG: No post found with ID: $postId<br>";
    }

    // Check if the post exists
    if ($post) {
        // Check if the form is submitted to update the post
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];

            // Update the post in the database
            $updateStmt = $pdo->prepare('UPDATE posts SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            if ($updateStmt->execute([$title, $content, $postId])) {
                header('Location: posts.php');
            } else {
                echo "Failed to update post...";
            }
        }
    } else {
        echo "Post not found...";
        exit;
    }
} else {
    echo "Invalid request...";
    exit;
}

?>

<!-- Display the form to edit the post -->
<form action="edit_post.php?id=<?php echo $postId; ?>" method="post">
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" required>
    <textarea name="content" required><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
    <button type="submit">Update Post</button>
</form>

<a href="posts.php">Back to Posts</a>