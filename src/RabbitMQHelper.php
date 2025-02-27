<?php

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQHelper {
  private AMQPStreamConnection $connection;
  private AMQPChannel $channel;

  public function __construct() {
    $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $this->channel = $this->connection->channel();
  }

  public function getChannel(): AMQPChannel {
    return $this->channel;
 }  

  public function close(): void {
    $this->channel->close();
    $this->connection->close();
  }
}