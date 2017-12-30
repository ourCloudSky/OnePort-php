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

        public function __construct($conf){

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

            $data = json_encode($this->config);

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
            
            return $this;

        }

    }