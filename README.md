# Template for microservice
To run service as classic php symfony application under http server use public/index.php as index file.

To run non-bloking server under Swoole framework use following command:
php ./runSwooleHttpServer.php

To run non-bloking server under Ampphp framework use following command:
php ./runAmpHttpServer.php

To run Amqp consumer use following command:
php ./runAmqpConsumer.php