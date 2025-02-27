<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQHelper;

// Connect to RabbitMQ
$rabbitMQ = new RabbitMQHelper;
$channel = $rabbitMQ->getChannel();

$channel->exchange_declare('headers_logs', 'headers', false, false, false);
$headers = ['os' => 'linux', 'format' => 'json'];
$data = "Headers Exchange Message";

$msg = new AMQPMessage($data, ['application_headers' => new AMQPTable($headers)]);
$channel->basic_publish($msg, 'headers_logs');

echo " [x] Sent '$data' with headers\n";

// Close connection
$rabbitMQ->close();
?>