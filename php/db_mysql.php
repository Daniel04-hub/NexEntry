<?php
// php/db_mysql.php

$servername = "localhost"; // Or your Cloud MySQL Host
$username = "root";        // Or your Cloud MySQL Username
$password = "rgns2004";            // Or your Cloud MySQL Password
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