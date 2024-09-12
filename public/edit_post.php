<!-- public/edit_post.php -->

<?php

require __DIR__ . '/../vendor/autoload.php';

use Nibun\CmsProject\Database;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Editor') {
    echo "You do NOT have permission to edit this post...";
    exit;
}

// Establish database connection
$config = require __DIR__ . '/../config/database.php';
$db = new Database($config);
$pdo = $db->getConnection();

// Check if the post ID is provided in the URL and is valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = (int)$_GET['id'];

    // Fetch the post details from the database
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$postId]);
    $post = $stmt->fetch();

    // Check if the post exists
    if ($post) {
        // Fetch categories
        $categoryStmt = $pdo->query('SELECT * FROM categories');
        $categories = $categoryStmt->fetchAll();

        //Fatch all tags
        $tagStmt = $pdo->query('SELECT * FROM tags');
        $tags = $tagStmt->fetchAll();

        // Fetch current tags for post
        $currentTagsStmt = $pdo->prepare('SELECT tag_id FROM post_tags WHERE post_id = ?');
        $currentTagsStmt->execute([$postId]);
        $currentTags = $currentTagsStmt->fetchAll(PDO::FETCH_COLUMN); // Get current tag IDs

        // Check if the form is submitted to update the post
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $category_id = $_POST['category_id'];
            $status = $_POST['status'];

            // Update the post in the database
            $updateStmt = $pdo->prepare('UPDATE posts SET title = ?, content = ?, category_id = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            if ($updateStmt->execute([$title, $content, $category_id, $status, $postId])) {

                // Update tags(delete old ones and insert new ones)
                $deleteTagsStmt = $pdo->prepare('DELETE FROM post_tags WHERE post_id = ?');
                $deleteTagsStmt->execute([$postId]);

                if (isset($_POST['tags'])) {
                    $newTags = $_POST['tags'];
                    foreach ($newTags as $tag_id) {
                        $insertTagStmt = $pdo->prepare('INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)');
                        $insertTagStmt->execute([$postId, $tag_id]);
                    }
                }

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width", initial-scale="1.0">
    <title>Edit Post</title>
    <!-- Include CKEditor -->
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script> <!-- This version is OUTDATED! -->
</head>
<body>
<!-- Display the form to edit the post -->
    <form action="edit_post.php?id=<?php echo $postId; ?>" method="post">
        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" required>
        <textarea name="content" required><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
        
        <!-- Category Dropdown -->
        <label for="category">Category</label>
        <select name="category_id" id="category" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php echo ($post['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                    <?php echo $category['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Tags (Checkboxes) -->
        <label for="tags">Tags:</label>
        <br>
        <?php foreach ($tags as $tag): ?>
            <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $currentTags) ? 'checked' : ''; ?>>
            <?php echo $tag['name']; ?><br>
        <?php endforeach; ?>

        <!-- Post Status -->
        <label for="status">Post Status: </label>
        <select name="status" id="status">
            <option value="draft" <?php if ($post['status'] === 'draft') echo 'selected'; ?>>Save as Draft</option>
            <option value="published" <?php if ($post['status'] === 'published') echo 'selected'; ?>>Publish Now</option>
        </select>

        <button type="submit">Update Post</button>
    </form>

    <!-- Initialize CKEditor -->
    <script>
        CKEDITOR.replace('content');
    </script>

    <a href="posts.php">Back to Posts</a>
</body>
</html>