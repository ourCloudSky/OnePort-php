<?php

require "vendor/autoload.php";

$client = new \CloudSky\OnePort\Client("127.0.0.1:5280");

$client->connect(["ssl" => true]);

$client->login("user1", "123456");

$client->importMap("map.json");

$client->map("tcp://10.0.50.23:5080", "tcp://127.0.0.23:5080");
$client->map("2345", "22345");

$client->map("someservice", "22346");

$client->exportMap("map.json");

\Workerman\Worker::runAll();