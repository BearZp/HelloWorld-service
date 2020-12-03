<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 18.09.20
 * Time: 15:50
 */

declare(strict_types=1);

require dirname(__FILE__).'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Dotenv\Dotenv;

/* collect env variables */
(new Dotenv())->bootEnv(dirname(__FILE__).'/.env');
/* ----------- */

$connection = new AMQPStreamConnection(
    $_ENV['RABBITMQ_HOST'],
    $_ENV['RABBITMQ_PORT'],
    $_ENV['RABBITMQ_USER'],
    $_ENV['RABBITMQ_PASS']
);
$channel = $connection->channel();

$channel->queue_declare($_ENV['RABBITMQ_QUEUE'], false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    /** @var \PhpAmqpLib\Message\AMQPMessage $msg */
    try {

        $props = $msg->get_properties();
        if (!$props) {
            $props = 'false';
        }
        $postString =  http_build_query([
            'props' => $props,
            'packet'=> $msg->body
        ]);

        var_dump($postString);

        $headers = [];
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Content-Length'] = strlen($postString);

        $fp = fsockopen(
            $_ENV['WORKER_HOST'],
            (int) $_ENV['WORKER_PORT'],
            $errno,
            $errstr,
            30
        );
        $out = "POST / HTTP/1.1\r\n";
        $out.= "Host: " . $_ENV['WORKER_HOST'] . "\r\n";
        $out.= "Content-Type: " . $headers['Content-Type'] . "\r\n";
        $out.= "Content-Length: " . $headers['Content-Length'] . "\r\n";
        $out.= "Connection: Close\r\n\r\n";
        $out.= $postString;
        fwrite($fp, $out);
        fclose($fp);

    } catch (\Throwable $e) {
        var_dump($e->getMessage());
    }
};

$channel->basic_consume($_ENV['RABBITMQ_QUEUE'], '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
