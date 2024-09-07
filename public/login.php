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

        // Check if the user is logged in
        if ($user->login($username,$password)) {
            // Fetch the user's details from the database, including their role
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $userData = $stmt->fetch();

            if ($userData) {
                session_start();
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['role'] = $userData['role']; // Store the role in the session

                header('Location: dashboard.php');
                exit;
            } else {
                echo 'User not found...';
            }
        } else {
            echo 'Invalid username or password!';
        }
    }
?>

<a href="register.php">Sign Up</a>