<!-- public/posts.php -->

<?php
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\Database;

    session_start();

    // Display user role
    echo "Role: " . $_SESSION['role'] . "<br>";

    // Establish connection first
    $config = require __DIR__ . '/../config/database.php';
    $db = new Database($config);
    $pdo = $db->getConnection();

    // Initialize pagination
    $limit = 3; // Number of posts per page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Get total number of posts
    $countStmt = $pdo->query('SELECT COUNT(*) FROM posts');
    $totalPosts = $countStmt->fetchColumn();
    $totalPages = ceil($totalPosts / $limit);

    // Search functionality
    $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
    $stmt = $pdo->prepare('SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id = users.id WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
    $stmt->execute([$searchTerm, $searchTerm]);

    // Check if the user is an Admin or the post author
    $is_admin = ($_SESSION['role'] === 'Admin');

    // Display only published posts for regular users, but all posts for Admins and Authors
    if ($is_admin) {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE author_id = ? OR status = "published" ORDER BY created_at DESC');
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE status = "published" ORDER BY created_at DESC');
        $stmt->execute();
    }

    $posts = $stmt->fetchAll();

    // Display posts
    echo "<br><a href='create_post.php?id={$post['id']}'> New </a>";

    foreach ($posts as $post) {
        echo "<h2>{$post['title']}</h2>";
        echo "<p>By: {$post['username']} on {$post['created_at']}</p>";
        echo "<p>{$post['content']}</p>";
        echo "<a href='edit_post.php?id={$post['id']}'>Edit</a> | ";
        echo "<a href='delete_post.php?id={$post['id']}'>Delete</a>";
        echo "<hr>";
    }

    // Pagination controls
    echo "<div style=''margin-top: 20px;>";

    // Show total number of posts
    echo "<p>Total Posts: $totalPosts</p>";

    // 'Previous' button (disabled is on the first page)
    if ($page > 1) {
        echo "<a href='posts.php?page=" . ($page - 1) . "'>Previous </a>";
    } else {
        echo "<span style='color: gray;'>Previous </span>";       
    }

    // Page number links (1, 2, 3...)
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $page) {
            echo "<strong>$i </strong>"; // Current page
        } else {
            echo "<a href='posts.php?page=$i'>$i </a>";
        }
    }

    // "Next" button (disabled if on the last page)
    if ($page < $totalPages) {
        echo "<a href='posts.php?page=" . ($page + 1) . "'>Next</a>";
    } else {
        echo "<span style='color: gray;'>Next</span>";
    }

    echo "<p>Page $page of $totalPages</p>";
    echo "</div>";
?>

<!-- Search Form -->
<form action="posts.php" method="GET">
    <input type="text" name="search" placeholder="Search posts...">
    <button type="submit">Search</button>
</form>

<!-- Go to Page Input -->
<form action="posts.php" method="GET" style="margin-top: 10px;">
    <label for="page">Go to page:</label>
    <input type="number" name="page" min="1" max="<?php echo $totalPages; ?>" value="<?php echo $page; ?>">
    <button type="submit">Go</button>
</form>

<a href="dashboard.php">Back to Dashboard</a>