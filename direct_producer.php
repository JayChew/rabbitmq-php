<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

$channel->exchange_declare('direct_logs', 'direct', false, false, false);

$severity = $argv[1] ?? 'info';
$data = implode(' ', array_slice($argv, 2));
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'direct_logs', $severity);

echo " [x] Sent '$data' with severity '$severity'\n";

// Close connection
$rabbitMQ->close();
?>