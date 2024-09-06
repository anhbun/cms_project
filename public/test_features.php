<?php
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\Database;
    use Nibun\CmsProject\User;

    $config = require __DIR__ . '/../config/database.php';
    $db = new Database($config);
    $pdo = $db->getConnection();
    $user = new User($pdo);

    echo "<h2>Testing CMS Features</h2>";

    // Test registration
    echo "<h3>Test Registration</h3>";
    $username = "testuser";
    $email = "testuser@example.com";
    $password = "securepassword";

    if ($user->register($username, $email, $password)) {
        echo "Registration successful for username: $username<br>";
    } else {
        echo "Registration failed for username: $username<br>";
    }

    // Test login
    echo "<h3>Test Login</h3>";
    if ($user->login($username, $password)) {
        echo "Login successful for username: $username<br>";
    } else {
        echo "Login failed for username: $username<br>";
    }

    // Test logout
    echo "<h3>Test Logout</h3>";
    $user->logout();
    // Start a new session to check the logout status
    session_start();
    if (!isset($_SESSION['user_id'])){
        echo "Logout successful for username: $username<br>";
    } else {
        echo "Logout failed for username: $username<br>";
    }

    // Test access for protected page
    echo "<h3>Test Access for Protected Page (Dashboard)</h3>";
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo "Access granted to dashboard.<br>";
    } else {
        echo "Access denied to dashboard (user is not logged in).<br>";
    }
?>