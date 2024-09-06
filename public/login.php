<!-- public/login.php -->
<form action="login.php" method="post">
    <input type="text" name="username" placeholder="Username" required>
    <br><br>
    <input type="password" name="password" placeholder="Password" required>
    <br><br>
    <button type="submit">Login</button>
</form>

<?php
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\Database;
    use Nibun\CmsProject\User;

    $config = require __DIR__ . '/../config/database.php';
    $db = new Database($config);
    $pdo = $db->getConnection();
    $user = new User($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($user->login($username,$password)) {
            echo 'Login successful!';
            // optional, redirect to dashboard or home page
            header('Location: dashboard.php');
            // exit
        } else {
            echo 'Invalid username or password!';
        }
    }
?>

<a href="register.php">Sign Up</a>