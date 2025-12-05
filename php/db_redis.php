<?php
require __DIR__ . '/../vendor/autoload.php';

$redis = new Predis\Client([
    'scheme' => 'tcp',
    'host' => 'redis-13492.crce217.ap-south-1-1.ec2.cloud.redislabs.com',
    'port' => 13492,
    'username' => 'default',
    'password' => 'VejSRfLBP3NovxDve8BYihDheKxV6orL'
]);

try {
    $redis->ping();
    // echo "Redis Connected Successfully!";
} catch (Exception $e) {
    echo "Redis Connection Error: " . $e->getMessage();
    exit;
}
?>