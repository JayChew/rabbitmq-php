<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

$channel->exchange_declare('logs', 'fanout', false, false, false);
$data = "Fanout message!";
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'logs');

echo " [x] Sent '$data'\n";

// Close connection
$rabbitMQ->close();
?>