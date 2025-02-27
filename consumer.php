<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

// Declare the same queue
$channel->queue_declare('task_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit, press CTRL+C\n";

$callback = function ($msg) {
  try {
    echo " [x] Received ", $msg->body, "\n";
    sleep(2); // Simulate processing time
    echo " [x] Done\n";
    $msg->ack(); // Acknowledge successful processing
 } catch (Exception $e) {
    echo " [!] Error: " . $e->getMessage() . "\n";
    $msg->nack(true); // Requeue the message on failure
 }
};

// Enable manual acknowledgment
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
  $channel->wait();
}

$rabbitMQ->close();
?>