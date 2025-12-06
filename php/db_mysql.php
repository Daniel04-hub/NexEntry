<?php
// php/db_mysql.php

$servername = "127.0.0.1";
$username = "guvi_user";
$password = "password123";
$dbname = "guvi_db";

// Create connection
try {
    $mysql_conn = new mysqli($servername, $username, $password, $dbname);
} catch (Exception $e) {
    die(json_encode(["status" => "error", "message" => "MySQL Connection Failed: " . $e->getMessage()]));
}

// Check connection
if ($mysql_conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "MySQL Connection Failed: " . $mysql_conn->connect_error]));
}
?>