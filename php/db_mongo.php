<?php
// php/db_mongo.php
require __DIR__ . '/../vendor/autoload.php'; // Ensure Composer autoload is loaded

try {
    // REPLACE with your MongoDB Atlas Connection String
    // Example: mongodb+srv://<user>:<password>@cluster.mongodb.net/?retryWrites=true&w=majority
    $mongo_uri = "mongodb+srv://naveendaniel2004_db_user:qzuUgWGOEpD1tiAF@guvi.xexybyb.mongodb.net/?appName=guvi";

    $mongo_client = new MongoDB\Client($mongo_uri);
    $mongo_db = $mongo_client->guvi_db;
    $mongo_collection = $mongo_db->user_profiles;

} catch (Exception $e) {
    die(json_encode(["status" => "error", "message" => "MongoDB Connection Error: " . $e->getMessage()]));
}
?>