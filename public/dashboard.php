<!-- dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap JS (optional, for components like modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8efd4; /* Warm background */
            color: #563d7c; /* Warm text color */
        }
        .dashboard-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard-container h1 {
            color: #563d7c;
        }
        .btn-custom {
            background-color: #f7b267;
            border-color: #f7b267;
            color: white;
        }
        .btn-custom:hover {
            background-color: #f99a5f;
            border-color: #f99a5f;
        }
    </style>
</head>
<body>

    <div class="container dashboard-container">
        <h1>Welcome to the Dashboard!</h1>
        <p>Hello, you have successfully logged in. Use the links below to navigate the system:</p>
        
        <div class="d-grid gap-2">
            <a href="posts.php" class="btn btn-custom btn-lg">Go to Posts</a>
            <a href="logout.php" class="btn btn-secondary btn-lg">Logout</a>
        </div>
    </div>

<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
?>

</body>
</html>