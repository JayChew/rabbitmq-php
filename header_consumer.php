<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

// Declare the same queue
$channel->queue_declare('task_queue', false, true, false, false);

$channel->exchange_declare('headers_logs', 'headers', false, false, false);
$channel->queue_declare('', false, false, true, false);
$queue_name = $channel->queue_declare()[0];

// Bind queue using headers
$binding_headers = new AMQPTable(['os' => 'linux']);
$channel->queue_bind($queue_name, 'headers_logs', '', false, $binding_headers);

echo " [*] Waiting for messages with matching headers...\n";
$callback = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}


$rabbitMQ->close();
?>