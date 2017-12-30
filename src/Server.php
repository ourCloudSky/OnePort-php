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

    class Server {

        protected $config;
        protected $id = 0;
        protected $firstMessage = [];
        protected $atc = [];

        public function __construct($conf = []){

            if(is_array($conf)){
                return $this->config($conf);
            }else{
                return $this->importConfig($conf);
            }

        }

        public function config($n, $v = ""){

            if(is_array($n)){
                foreach($n as $k=>$va){
                    $this->config($k, $va);
                }
            }else{
                $this->config[$n] = $v;
            }

            return $this;

        }

        public function importConfig($file){


            $this->config(
                json_decode(
                    file_get_contents($file),
                    1
                )
            );

            return $this;

        }

        public function exportConfig($file = ""){

            $data = json_encode($this->config, \JSON_PRETTY_PRINT);

            if($file){

                file_put_contents($file, $data);

                return $this;

            }else{

                return $data;

            }

        }

        public function listen($addr){

            $this->worker = new \Workerman\Worker("websockethack://$addr");

            $this->worker->name = $this->config['name'];
            $this->worker->count = $this->config['count'];

            $this->worker->onHttpRequest = [$this, 'handleHttp'];
            $this->worker->onMessage = [$this, 'handleMessageGateway'];
            $this->worker->onConnect = [$this, 'handleConnect'];
            $this->worker->onClose = [$this, 'handleClose'];
            
            return $this;

        }

        public function handleConnect($conn){

            $conn->id = ++$this->id;
            $this->firstMessage[$conn->id] = true;

        }

        public function handleClose($conn){

            @$this->atc[$conn->id]->close();

        }

        public function handleMessageGateway($conn, $msg){

            if($this->firstMessage[$conn->id]){
                $this->firstMessage[$conn->id] = false;
                $this->handleFirstMessage($conn, $msg);
            }else{
                $this->handleMessage($conn, $msg);
            }

        }

        public function handleMessage($conn, $msg){

            $this->atc[$conn->id]->send($msg);

        }

        public function handleFirstMessage($conn, $msg){

            $msg = explode("||", $msg);
            $user = $msg[0];
            $pass = $msg[1];
            $uri = $msg[2];
            $trans = $msg[3];

            if(!$this->auth($user, $pass)){
                $conn->send("__MCON__:Auth Error");
                $conn->close();
                return;
            }

            $uri = $this->dealUri($uri, $user);
            if($uri == false){
                $conn->send("__MCON__:Permission Denied");
                $conn->close();
                return;
            }

            $id = $conn->id;

            $atc = new \Workerman\Connection\AsyncTcpConnection($uri);
            $atc->transport = $trans;

            $atc->onMessage = function($con, $m) use ($conn){
                $conn->send($m);
            };
            $atc->onClose = function($con) use ($conn){
                $conn->close();
            };

            $atc->connect();

            $this->atc[$id] = $atc;

        }

    }