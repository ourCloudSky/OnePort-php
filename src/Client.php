<?php
    /**
     * OnePort Project
     * 
     * This program is licensed under
     * MIT, so please use it under the
     * license.
     * 
     * @package OnePort
     * @license MIT
     * @author  Tianle Xu <xtl@xtlsoft.top>
     * 
     */

    namespace CloudSky\OnePort;

    class Client {

        protected $server;
        protected $transport = "tcp";
        protected $username = "guest";
        protected $password = "guest";
        protected $map = [];

        public function __construct($server){

            $this->server = "ws://$server";

        }

        public function enableSSL($bool = true){

            if($bool) $this->transport = "ssl";
            else $this->transport = "tcp";

            return $this;

        }

        public function login($user, $pass){

            $this->username = $user;
            $this->password = $pass;

            return $this;

        }

        public function map($orgin, $mirror, $trans = "tcp"){

            // $atc = new \Workerman\Connection\AsyncTcpConnection($this->server);
            $server = $this->server;

            $fpkg = $this->username ."||" . $this->password . "||" . $orgin . "||" . $trans;

            $worker = new \Workerman\Worker($mirror);

            $worker->onConnect = function ($conn) use ($fpkg, $server) {
                $conn->atc = new \Workerman\Connection\AsyncTcpConnection($server);

                $conn->atc->onMessage = function($c, $m) use ($conn){
                    $conn->send(base64_decode($m));
                };
                $conn->atc->onClose = function($c) use ($conn){
                    $conn->close();
                };

                $conn->atc->connect();
                $conn->atc->send($fpkg);
            };

            $worker->onMessage = function($conn, $msg){
                // if($atc !== "")
                $conn->atc->send(base64_encode($msg));
            };

            $worker->onClose = function($conn){
                // if($atc !== "")
                $conn->atc->close();
            };

            $this->map[] = [$orgin, $mirror, $trans];

            return $this;

        }

        public function importMap($map){

            if(!is_array($map)){
                $map = \json_decode(file_get_contents($map), 1);
            }

            foreach($map as $v) {

                $this->map($v[0], $v[1], $v[2]);

            }

            return $this;

        }

        public function exportMap($map){

            \file_put_contents($map, \json_encode($this->map));

            return $this;

        }
        
    }