<!-- public/create_post.php -->

<?php
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\Database;

    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Editor') {
        echo "You do NOT have permission to create this post...";
        exit;
    }

    // Establish databse connection first
    $config = require __DIR__ . '/../config/database.php';
    $db = new Database($config);
    $pdo = $db->getConnection();

    // Fetch categories after the connection is established
    $categoryStmt = $pdo->query('SELECT * FROM categories');
    $categories = $categoryStmt->fetchAll();

    // Fetch tags after the connection is established
    $tagStmt = $pdo->query('SELECT * FROM tags');
    $tags = $tagStmt->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Capture the data
        $title = $_POST['title'];
        $content = $_POST['content'];
        $author_id = $_SESSION['user_id'];
        // Default category if not selected
        $category_id = isset($_POST['category_id']) && !empty($_POST['category_id']) ? $_POST['category_id'] : 1; // Use default category ID

        // Insert the post with the selected category or default category
        $stmt = $pdo->prepare('INSERT INTO posts (title, content, author_id, category_id) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$title, $content, $author_id, $category_id])) {
            // Get the ID of the insert post
            $post_id = $pdo->lastInsertId();

            // Handle tags (if any tags are selected)
            if (isset($_POST['tags'])) {
                $tags = $_POST['tags'];
                foreach ($tags as $tag_id) {
                    // Insert each tag associated with the post into the post_tags table
                    $tagStmt = $pdo->prepare('INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)');
                    $tagStmt->execute([$post_id, $tag_id]);
                }
            }

            // Redirect to posts page after success
            header('Location: posts.php');
        } else {
            echo "Failed to create post.";
        }
    }
?>

<form action="create_post.php" method="post">
    <input type="text" name="title" placeholder="Post Title" required>
    <textarea name="content" placeholder="Post Content" required></textarea>
    
    <!-- Category Dropdown -->
    <label for="category">Category</label>
    <select name="category_id" id="category" required>
        <option value="">Select a category</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Tags (Checkboxes) -->
    <label for="tags">Tags:</label>
    <br>
    <?php foreach ($tags as $tag): ?>
        <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>"> <?php echo $tag['name']; ?><br>
    <?php endforeach; ?>
    
    <button type="submit"> Create Post </button>
</form>