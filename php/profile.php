<?php
// php/profile.php
header('Content-Type: application/json');
require 'db_mongo.php';
require 'db_redis.php';

// Helper to get token from headers or POST
function get_bearer_token()
{
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return $_POST['token'] ?? null;
}

$token = get_bearer_token();

if (!$token) {
    echo json_encode(["status" => "error", "message" => "Unauthorized: No token provided"]);
    exit;
}

// Verify Token in Redis
try {
    $session_data_json = $redis->get("session:" . $token);

    if (!$session_data_json) {
        echo json_encode(["status" => "error", "message" => "Unauthorized: Invalid or expired token"]);
        exit;
    }

    $session_data = json_decode($session_data_json, true);
    $user_id = (int) $session_data['user_id'];

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Redis Error: " . $e->getMessage()]);
    exit;
}

// Handle GET Request (Fetch Profile)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $profile = $mongo_collection->findOne(['user_id' => $user_id]);

        if ($profile) {
            // Convert BSONDocument to array
            $profile_array = (array) $profile;
            // Remove internal MongoDB ID for cleaner output
            unset($profile_array['_id']);
            echo json_encode(["status" => "success", "data" => $profile_array]);
        } else {
            echo json_encode(["status" => "success", "data" => null, "message" => "No profile found"]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "MongoDB Error: " . $e->getMessage()]);
    }
}

// Handle POST Request (Update Profile)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = $_POST['age'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $address = $_POST['address'] ?? '';

    // Server-side Validation
    if (empty($age) || empty($dob) || empty($contact) || empty($address)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    try {
        $updateResult = $mongo_collection->updateOne(
            ['user_id' => $user_id],
            [
                '$set' => [
                    'user_id' => $user_id, // Ensure user_id is set
                    'age' => $age,
                    'dob' => $dob,
                    'contact' => $contact,
                    'address' => $address
                ]
            ],
            ['upsert' => true] // Create if not exists
        );

        if ($updateResult->getModifiedCount() > 0 || $updateResult->getUpsertedCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
        } else {
            echo json_encode(["status" => "success", "message" => "No changes made"]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "MongoDB Error: " . $e->getMessage()]);
    }
}
?>