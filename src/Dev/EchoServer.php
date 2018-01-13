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

    namespace CloudSky\OnePort\Dev;

    class EchoServer {

        protected $worker = null;

        public function __construct($addr, $name = "test", $count=1){

            $this->worker = new \Workerman\Worker($addr);

            $this->worker->name = "CloudSky_OnePort_Dev_EchoServer_" . $name;
            $this->worker->count = $count;

            $this->serve($this->worker);

        }

        public function getWorker($worker){

            return $this->worker;

        }

        public function serve($worker){

            $worker->onWorkerStart = function ($wkr) {

                echo "[Msg][" . date('Y-m-d h:i:s') . "] Worker " . $wkr->name . " StartUp\r\n";

            };

            $worker->onConnect = function ($con) {

                $con->send("HELLO FROM SERVER!\r\n");
                echo "[Msg][" . date('Y-m-d h:i:s') . "] Client Connected To " . $con->worker->name . "\r\n";
                
            };

            $worker->onMessage = function ($con, $msg){

                $con->send("Recv: $msg\r\n");
                echo "[Msg][" . date('Y-m-d h:i:s') . "] Client Sent '$msg' To " . $con->worker->name . "\r\n";

            };

            $worker->onClose = function ($con){

                echo "[Msg][" . date('Y-m-d h:i:s') . "] Client Closed. Server: " . $con->worker->name . "\r\n";
                
            };

            return $this;

        }

    }