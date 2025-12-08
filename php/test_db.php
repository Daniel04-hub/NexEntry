<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>System Check</h1>";

// 1. Check MySQL
echo "<h2>MySQL</h2>";
try {
    $conn = new mysqli("127.0.0.1", "guvi_user", "password123", "guvi_db");
    if ($conn->connect_error) {
        echo "<p style='color:red'>Failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>Connected Successfully</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
}

// 2. Check Redis
echo "<h2>Redis</h2>";
if (class_exists('Redis')) {
    try {
        $redis = new Redis();
        $connected = $redis->connect('127.0.0.1', 6379);
        if ($connected) {
            echo "<p style='color:green'>Connected Successfully</p>";
        } else {
            echo "<p style='color:red'>Failed to connect</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>Redis Class not found (Extension missing)</p>";
}

// 3. Check MongoDB
echo "<h2>MongoDB</h2>";
if (class_exists('MongoDB\Driver\Manager')) {
    try {
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $cursor = $manager->executeCommand('admin', $command);
        echo "<p style='color:green'>Connected Successfully</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>MongoDB Extension not found</p>";
}
?>