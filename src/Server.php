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

            if(isset($this->atc[$conn->id]))
                $this->atc[$conn->id]->close();

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

            $this->atc[$conn->id]->send(base64_decode($msg));

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
                $conn->send(base64_encode($m));
            };
            $atc->onClose = function($con) use ($conn){
                $conn->close();
            };

            $atc->connect();

            $this->atc[$id] = $atc;

        }

        public function addUser($name, $password){

            @$this->config['user'][$name] = [
                "password"  => hash('sha512', $password),
                "whitelist" => [],
                "blacklist" => [],
                "shortcut"  => []
            ];

            return $this;

        }

        public function removeUser($name){

            unset($this->config['user'][$name]);

            return $this;

        }

        public function setPassword($name, $password){

            $this->config['user'][$name]['password'] = hash('sha512', $password);

            return $this;

        }

        public function addWhiteList($user, $addr){

            if(is_array($addr)){

                foreach($addr as $a){
                    $this->addWhiteList($user, $a);
                }

            }else{

                $this->config['user'][$user]['whitelist'][] = $addr;

            }

            return $this;

        }

        public function addBlackList($user, $addr){
            
            if(is_array($addr)){
            
                foreach($addr as $a){
                    $this->addBlackList($user, $a);
                }
            
            }else{
            
                $this->config['user'][$user]['blacklist'][] = $addr;
            
            }
            
            return $this;
            
        }

        public function disableWhiteList($user){

            $this->config['user'][$user]['whitelist'] = [];

            return $this;

        }

        public function disableBlackList($user){

            $this->config['user'][$user]['blacklist'] = [];
            
            return $this;

        }

        public function addUserShortcut($name, $alias, $addr){

            $this->config['user'][$name]['shortcut'][$alias] = $addr;

            return $this;

        }

        public function removeUserShortcut($name, $alias){

            unset($this->config['user'][$name]['shortcut'][$alias]);

            return $this;

        }

        public function addShortcut($alias, $addr, $name = null){

            if($name !== null){
                return $this->addUserShortcut($name, $alias, $addr);
            }

            $this->config['shortcut'][$alias] = $addr;

            return $this;

        }

        public function removeShortcut($alias, $user = null){

            if($name !== null){
                return $this->removeUserShortcut($user, $alias);
            }

            unset($this->config['shortcut'][$alias]);

            return $this;

        }

        protected function auth($name, $pass){

            $realpass = $this->config[$name]['password'];
            $inputpass = hash('sha512', $pass);

            return ( $inputpass === $realpass );

        }

        protected function dealUri($uri, $user){

            $wl = $this->config['user'][$user]['whitelist'];
            $bl = $this->config['user'][$user]['blacklist'];
            $al = array_merge($this->config['shortcut'], $this->config['user'][$user]['shortcut']);

            $ok = false;

            if($wl != []){
                $ok = in_array($uri, $wl);
            }else{
                $ok = !(in_array($uri, $bl));
            }

            if(!$ok){
                return false;
            }

            if(isset($al[$uri])){
                $uri = $al[$uri];
            }

            return $uri;

        }

        public function handleHttp($conn, $msg){

            $type = $this->config['http.type'];
            $param = $this->config['http.param'];
            switch($type){

                case 'jump':
                    $conn->send("HTTP/1.1 302 Found\r\nServer: OnePort\r\nlocation: $param\r\n\r\n\r\n", 1);
                    $conn->close();
                    break;

                case 'handle':
                    $conn->send("HTTP/1.1 200 OK\r\nServer: OnePort\r\n" . shell_exec("php-cgi $param"), 1);
                    $conn->close();
                    break;

                case 'proxy':
                    $atc = new \Workerman\Connection\AsyncTcpConnection($param);
                    $atc->onMessage = function($c, $m) use ($conn){
                        $conn->send($m, 1);
                    };
                    $atc->onClose = function($c) use ($conn){
                        $conn->close();
                    };

                    $atc->connect();
                    $atc->send($msg);
            }

        }

    }