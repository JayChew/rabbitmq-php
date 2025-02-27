<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

// Declare a quene
$channel->queue_declare('task_quene', false, true, false, false);

// Create a message
$data = isset($argv[1]) ? implode(' ', array_slice($argv, 1)) : "Hello, RabbitMQ!";
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

// Publish the message to the queue
$channel->basic_publish($msg, '', 'task_queue');

echo " [x] Sent '$data'\n";

// Close connection
$rabbitMQ->close();
?>