<?php
namespace App\classes;
//require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
/**
 * Created by Japhari Muna.
 * User: User
 * Date: 11/14/2017
 * Time: 9:56 AM
 */



class rabbit
{
    public static function send()
    {
        $connection  = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);
        ini_set('max_execution_time', 300);
        $i=1;
        do {
          $msg = new AMQPMessage('Hello Loop Am Here'.$i);
          $channel ->basic_publish($msg, '','hello');
          $i++;
        } while($i<1000);
        //$msg = new AMQPMessage('Hello Japhari!');
       // $channel->basic_publish($msg, '', 'japhari');
        echo "Message has been sent to Server";
        $channel->close();
        $connection->close();

    }

    public static function receive()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };
        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();

    }

}