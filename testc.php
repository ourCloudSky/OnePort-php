<?php

require "vendor/autoload.php";

$client = new \CloudSky\OnePort\Client("127.0.0.1:5280");

$client->enableSSL(false);

$client->login("test", "123");

// $client->importMap("map.json");

$client->map("tcp://tzqsyzx.com:443", "tcp://127.0.0.23:443");

// $client->map("someservice", "22346");

$client->exportMap("map.json");

\Workerman\Worker::runAll();