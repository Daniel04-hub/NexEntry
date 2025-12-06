<?php
// php/login.php
header('Content-Type: application/json');
require 'db_mysql.php';
require 'db_redis.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and Password required"]);
        exit;
    }

    // Verify User
    $stmt = $mysql_conn->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            // Login Success

            // Generate Session Token
            $session_token = bin2hex(random_bytes(32));

            // Store in Redis (Key: session_token, Value: User Data, TTL: 30 mins)
            $session_data = json_encode([
                "user_id" => $row['id'],
                "username" => $row['username'],
                "email" => $email
            ]);

            try {
                $redis->setex("session:" . $session_token, 1800, $session_data); // 1800 seconds = 30 mins

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