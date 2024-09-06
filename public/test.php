<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\Database;

    $config = require __DIR__ . '/../config/database.php';
    $db = new Database($config);

    try {
        $pdo = $db->getConnection();
        echo "Database conncetion successful!";
    } catch (Exception $e) {
        echo "Database connection FAILED: " . $e->getMessage();
    }
?>