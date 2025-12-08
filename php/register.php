<?php

header('Content-Type: application/json');


$servername = "127.0.0.1";
$username = "guvi_user";
$password = "password123";
$dbname = "guvi_db";

try {
    $mysql_conn = new mysqli($servername, $username, $password, $dbname);
} catch (Exception $e) {
    die(json_encode(["status" => "error", "message" => "MySQL Connection Failed: " . $e->getMessage()]));
}

if ($mysql_conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "MySQL Connection Failed: " . $mysql_conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }


    $stmt = $mysql_conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        exit;
    }
    $stmt->close();


    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $mysql_conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed: " . $stmt->error]);
    }

    $stmt->close();
    $mysql_conn->close();
}
?>