<?php
require __DIR__ . '/../vendor/autoload.php';

$redis = new Predis\Client([
    'scheme' => 'tcp',
    'host' => '127.0.0.1',
    'port' => 6379
]);

try {
    $redis->ping();
    // echo "Redis Connected Successfully!";
} catch (Exception $e) {
    echo "Redis Connection Error: " . $e->getMessage();
    exit;
}
?>