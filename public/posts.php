<!-- public/posts.php -->

<?php
    // DEBUGGING
    error_reporting(E_ALL);
    ini_set('display errors', 1);

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

    // Check if the user is an Admin or the post author
    $is_admin = ($_SESSION['role'] === 'Admin');

    // Get the sorting criteria from the query string (or set default to 'date_desc')
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_desc';

    // Determine the ORDER BY clause based on the selected sorting criteria
    switch ($sort_by) {
        case 'date_asc':
            $order_by = 'posts.created_at ASC';
            break;
        case 'title_asc':
            $order_by = 'posts.title ASC';
            break;
        case 'title_desc':
            $order_by = 'posts.title DESC';
            break;
        case 'author_asc':
            $order_by = 'users.username ASC';
            break;
        case 'author_desc':
            $order_by = 'users.username DESC';
            break;
        case 'date_desc':
        default:
            $order_by = 'posts.created_at DESC';
            break;    
    }



    if ($is_admin) {
        // Admins can see all posts, or posts where they're the author
        $stmt = $pdo->prepare(
    "SELECT posts.*, users.username 
            FROM posts
            JOIN users ON posts.author_id = users.id
            WHERE (author_id = :author_id OR status = 'published')
            AND (title LIKE :searchTerm OR content LIKE :searchTerm)
            ORDER BY $order_by
            LIMIT :limit OFFSET :offset"
            );
        // Bind parameters
        $stmt->bindParam(':author_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Non-admins only see published posts
        $stmt = $pdo->prepare(
    "SELECT posts.*, users.username 
            FROM posts 
            JOIN users ON posts.author_id = users.id
            WHERE status = 'published'
            AND (title LIKE :searchTerm OR content LIKE :searchTerm) 
            ORDER BY $order_by
            LIMIT :limit OFFSET :offset"
        );
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    $posts = $stmt->fetchAll();

    // Display posts
    echo "<br><a href='create_post.php'> New </a>";

    foreach ($posts as $post) {
        echo "<h2>{$post['title']}</h2>";
        echo "<p>By: {$post['username']} on {$post['created_at']}</p>";
        
        // Display the image if it exists
        if ($post['image']) {
            echo "<img src='/cms_project/{$post['image']}' alt='post-image' style='max-width: 200px;'><br>";
        }

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

<!-- Sorting Options Form -->
<form action="posts.php" method="GET" style="margin-bottom: 20px;">
    <label for="sort_by">Sort by: </label>
    <select name="sort_by" id="sort_by" onchange="this.form.submit()">
        <option value="date_asc" <?php if ($sort_by == 'date_asc') echo 'selected'; ?>>Date (Newest to Oldest)</option>
        <option value="date_desc" <?php if ($sort_by == 'date_desc') echo 'selected'; ?>>Date (Oldest to Newest)</option>
        <option value="title_asc" <?php if ($sort_by == 'title_asc') echo 'selected'; ?>>Title (A-Z)</option>
        <option value="title_desc" <?php if ($sort_by == 'title_desc') echo 'selected'; ?>>Title (Title Z-A)</option>
        <option value="author_asc" <?php if ($sort_by == 'author_asc') echo 'selected'; ?>>Author (A-Z)</option>
        <option value="author_desc" <?php if ($sort_by == 'author_desc') echo 'selected'; ?>>Author (Z-A)</option>
    </select>
    <input type="hidden" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
</form>

<a href="dashboard.php">Back to Dashboard</a>