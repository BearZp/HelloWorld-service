#!/usr/bin/env php
<?php

function setupNginx()
{
    $serverPort = readline("nginx port: ");
    $serverName = readline("nginx server_name: ");
    $pathToPublic = dirname(__FILE__) . '/public';
    $nginxConf = file_get_contents($pathToPublic . '/../template_nginx.conf');
    $phpVersion = (float)phpversion();

    $nginxConf = str_replace('listen 8080;', 'listen ' . $serverPort . ';', $nginxConf);
    $nginxConf = str_replace('server_name template.local;', 'server_name ' . $serverName . ';', $nginxConf);
    $nginxConf = str_replace('root /var/www/project/public;', 'root ' . $pathToPublic . ';', $nginxConf);
    $nginxConf = str_replace('fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;', 'fastcgi_pass unix:/var/run/php/php' . $phpVersion . '-fpm.sock;', $nginxConf);
    $nginxConf = str_replace('fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;', 'fastcgi_pass unix:/var/run/php/php' . $phpVersion . '-fpm.sock;', $nginxConf);

    echo 'Update nginx config' . PHP_EOL;

    try {
        $result = (bool) @file_put_contents('/etc/nginx/sites-enabled/' . $serverName, $nginxConf);
        $error = error_get_last();
        if ($error && $error['message']) {
            throw new Exception($error['message']);
        }
        echo 'Done' . PHP_EOL;
    } catch (\Throwable $e) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
        exit(1);
    }

    echo 'Check nginx config' . PHP_EOL;
    $output = shell_exec('nginx -t 2>&1 | tee temp.txt');
    unlink('temp.txt');
    if (!strpos($output, 'test is successful')) {
        echo "ERROR: can`t get success from nginx. Run " . PHP_EOL . "    sudo nginx -t" . PHP_EOL . "for more details" . PHP_EOL;
        exit(1);
    } else {
        echo "Done" . PHP_EOL;
    }

    echo "Reload nginx config" . PHP_EOL;
    $output = shell_exec('nginx -s reload 2>&1 | tee temp.txt');

    var_dump($output);

    return $serverName;
}

function setupAsyncWorker(string $localEnv): string
{
    $input = readline("Choose worker port: ");
    $result = 'WORKER_PORT=' . $input . PHP_EOL;

    $input = readline("Choose worker input IPv4 address (default - 0.0.0.0): ");
    if (strlen($input) == 0) {
        $input = '0.0.0.0';
    }
    $result .= 'WORKER_HOST=' . $input . PHP_EOL;

    $input = readline("Choose worker input IPv6 address (default - [::]): ");
    if (strlen($input) == 0) {
        $input = '[::]';
    }
    $result .= 'WORKER_HOST_V6=' . $input . PHP_EOL;

    return $localEnv . $result;
}

function setupDatabase($localEnv): string
{
    $result = '';
    //DATABASE_HOST="localhost"
    $input = readline("Set database host (127.0.0.1): ");
    if (strlen($input) == 0) {
        $input = '127.0.0.1';
    }
    $result .= 'DATABASE_HOST=' . $input . PHP_EOL;

    //DATABASE_PORT=8001
    $input = readline("Set database port (5432): ");
    if (strlen($input) == 0) {
        $input = '5432';
    }
    $result .= 'DATABASE_PORT=' . $input . PHP_EOL;

    //DATABASE_NAME=postgres
    $input = readline("Set database name (postgres): ");
    if (strlen($input) == 0) {
        $input = 'postgres';
    }
    $result .= 'DATABASE_NAME=' . $input . PHP_EOL;

    //DATABASE_USER=postgres
    $input = readline("Set database user name (postgres): ");
    if (strlen($input) == 0) {
        $input = 'postgres';
    }
    $result .= 'DATABASE_USER=' . $input . PHP_EOL;

    //DATABASE_PASSWORD=postgres
    $input = readline("Set database user password (postgres): ");
    if (strlen($input) == 0) {
        $input = 'postgres';
    }
    $result .= 'DATABASE_PASSWORD=' . $input . PHP_EOL;

    return $localEnv . $result;
}

function setupRedis($localEnv): string
{
    //APP_REDIS_CONNECTION="redis://localhost:6379"
    $input = readline("Set redis connection url (redis://localhost:6379): ");
    if (strlen($input) == 0) {
        $input = 'redis://localhost:6379';
    }
    return $localEnv .  'APP_REDIS_CONNECTION="' . $input . '"' . PHP_EOL;
}

function setupRabbit($localEnv): string
{
    $result = '';
    //RABBITMQ_HOST="localhost"
    $input = readline("Set RabbitMQ host (127.0.0.1): ");
    if (strlen($input) == 0) {
        $input = '127.0.0.1';
    }
    $result .= 'RABBITMQ_HOST=' . $input . PHP_EOL;

    //RABBITMQ_PORT=5672
    $input = readline("Set RabbitMQ port (5672): ");
    if (strlen($input) == 0) {
        $input = '5672';
    }
    $result .= 'RABBITMQ_PORT=' . $input . PHP_EOL;

    //RABBITMQ_USER=guest
    $input = readline("Set RabbitMQ user name (guest): ");
    if (strlen($input) == 0) {
        $input = 'guest';
    }
    $result .= 'RABBITMQ_USER=' . $input . PHP_EOL;

    //RABBITMQ_PASS=guest
    $input = readline("Set RabbitMQ user password (guest): ");
    if (strlen($input) == 0) {
        $input = 'guest';
    }
    $result .= 'RABBITMQ_PASS=' . $input . PHP_EOL;

    //RABBITMQ_QUEUE=guest
    $input = readline("Set RabbitMQ queue name (guest): ");
    if (strlen($input) == 0) {
        $input = 'guest';
    }
    $result .= 'RABBITMQ_QUEUE=' . $input . PHP_EOL;

    //RABBITMQ_VHOST=guest
    $input = readline("Set RabbitMQ virtual host (guest): ");
    if (strlen($input) == 0) {
        $input = 'guest';
    }
    $result .= 'RABBITMQ_VHOST=' . $input . PHP_EOL;

    return $localEnv . $result;
}

function rollback($serverName)
{
    if (file_exists('/etc/nginx/sites-enabled/' . $serverName)) {
        unlink('/etc/nginx/sites-enabled/' . $serverName);

    }
}


$localEnv = '';

$input = readline("Setup nginx? (y/n): ");
if ($input === 'y') {
    $serverName = setupNginx();
}

$input = readline("Setup worker? (y/n): ");
if ($input === 'y') {
    $localEnv = setupAsyncWorker($localEnv);
}

$input = readline('Need to use database? (y/n)');
if ($input === 'y') {
    $localEnv = setupDatabase($localEnv);
}

$input = readline('Need to use redis? (y/n)');
if ($input === 'y') {
    $localEnv = setupRedis($localEnv);
}

$input = readline('Need to use RabbitMQ? (y/n)');
if ($input === 'y') {
    $localEnv = setupRabbit($localEnv);
}

$filename = './.env.local';
if (file_exists($filename)) {
    unlink($filename);
}
$result = (bool) @file_put_contents($filename, $localEnv);
$error = error_get_last();
if ($error && $error['message']) {
    echo 'ERROR: ' . $error['message'] . PHP_EOL;
    exit(1);
}
echo 'Done' . PHP_EOL;
