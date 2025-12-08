<?php

header('Content-Type: application/json');


try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Redis Connection Error: " . $e->getMessage()]);
    exit;
}


try {

    $mongo_manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
} catch (Exception $e) {
    die(json_encode(["status" => "error", "message" => "MongoDB Connection Error: " . $e->getMessage()]));
}


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


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {

        $filter = ['user_id' => $user_id];
        $query = new MongoDB\Driver\Query($filter);


        $cursor = $mongo_manager->executeQuery('guvi_db.user_profiles', $query);
        $profiles = $cursor->toArray();

        if (!empty($profiles)) {
            $profile = $profiles[0];
            $profile_array = (array) $profile;

            unset($profile_array['_id']);
            echo json_encode(["status" => "success", "data" => $profile_array]);
        } else {
            echo json_encode(["status" => "success", "data" => null, "message" => "No profile found"]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "MongoDB Error: " . $e->getMessage()]);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = $_POST['age'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $address = $_POST['address'] ?? '';

    if (empty($age) || empty($dob) || empty($contact) || empty($address)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    try {

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['user_id' => $user_id],
            [
                '$set' => [
                    'user_id' => $user_id,
                    'age' => $age,
                    'dob' => $dob,
                    'contact' => $contact,
                    'address' => $address
                ]
            ],
            ['multi' => false, 'upsert' => true]
        );

        $result = $mongo_manager->executeBulkWrite('guvi_db.user_profiles', $bulk);

        if ($result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
        } else {
            echo json_encode(["status" => "success", "message" => "No changes made"]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "MongoDB Error: " . $e->getMessage()]);
    }
}
?>