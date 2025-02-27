<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);
$channel->queue_declare('', false, false, true, false);
$queue_name = $channel->queue_declare()[0];

// Bind with wildcard-based routing keys (e.g., 'logs.*', 'logs.#')
foreach (array_slice($argv, 1) as $binding_key) {
    $channel->queue_bind($queue_name, 'topic_logs', $binding_key);
}

echo " [*] Waiting for topic logs...\n";
$callback = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

// Close connection
$rabbitMQ->close();
?>