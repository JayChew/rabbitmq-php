<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

$channel->exchange_declare('logs', 'fanout', false, false, false);
$channel->queue_declare('', false, false, true, false);
$queue_name = $channel->queue_declare()[0];

// Bind queue to the exchange (no routing key)
$channel->queue_bind($queue_name, 'logs');

echo " [*] Waiting for logs...\n";
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