<!-- public/register php -->
<form action="register.php" method="post">
    <input type="text" name="username" placeholder="Username" required>
    <br><br>
    <input type="email" name="email" placeholder="Email" required>
    <br><br>
    <input type="password" name="password" placeholder="Password" required>
    <br><br>
    <button type="submit"> Register </button>
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
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($user->register($username, $email, $password)) {
            echo "User registration successful. Head back to Login!";
        } else {
            echo "User registration failed!";
        }
    }
?>

<a href="login.php">Back to Login</a>