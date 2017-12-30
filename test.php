<?php

require "vendor/autoload.php";

$server = new \CloudSky\OnePort\Server();

// $server->importConfig('./conf.json');

// $server->addUser('name', 'value');

$server->config([
    "name" => "abc",
    "count" => "3"
]);

$server->config('http.method', 'proxy');
$server->config('http.proxy', 'http://127.0.0.1/');

$server->exportConfig('./conf.json');

$server->listen("127.0.0.1:5280");

\Workerman\Worker::runAll();