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


try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Redis Connection Error: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and Password required"]);
        exit;
    }


    $stmt = $mysql_conn->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            // Login Success



            $session_token = bin2hex(random_bytes(32));


            $session_data = json_encode([
                "user_id" => $row['id'],
                "username" => $row['username'],
                "email" => $email
            ]);

            try {
                $redis->setex("session:" . $session_token, 1800, $session_data);

                echo json_encode([
                    "status" => "success",
                    "message" => "Login successful",
                    "token" => $session_token
                ]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Redis Error: " . $e->getMessage()]);
            }

        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    $stmt->close();
    $mysql_conn->close();
}
?>