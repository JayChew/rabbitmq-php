<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);

$topic = $argv[1] ?? 'logs.info';
$data = implode(' ', array_slice($argv, 2));
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'topic_logs', $topic);

echo " [x] Sent '$data' with topic '$topic'\n";

// Close connection
$rabbitMQ->close();
?>