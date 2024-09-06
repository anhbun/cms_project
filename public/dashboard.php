<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    echo 'Welcome to the dashboard!';
    echo '<br>';
    echo '<a href="logout.php">Logout</a>';
    echo '<br>';
    echo '<a href="posts.php">Go to Posts</a>';
?>