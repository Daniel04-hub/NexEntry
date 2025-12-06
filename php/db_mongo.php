<?php
// php/db_mongo.php
require __DIR__ . '/../vendor/autoload.php'; // Ensure Composer autoload is loaded

try {
    // Local MongoDB Connection
    $mongo_uri = "mongodb://localhost:27017";

    $mongo_client = new MongoDB\Client($mongo_uri);
    $mongo_db = $mongo_client->guvi_db;
    $mongo_collection = $mongo_db->user_profiles;

} catch (Exception $e) {
    die(json_encode(["status" => "error", "message" => "MongoDB Connection Error: " . $e->getMessage()]));
}
?>