<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

$channel->exchange_declare('direct_logs', 'direct', false, false, false);
$channel->queue_declare('', false, false, true, false);
$queue_name  = $channel->queue_declare()[0];

// Bind quene to severity levels (e.g. 'error', 'info')
foreach (array_slice($argv, 1) as $severity) {
  $channel->queue_bind($queue_name, 'direct_logs', $severity);
}

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