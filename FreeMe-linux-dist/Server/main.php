#!/usr/bin/php
<?php
    /**
     * OnePort Project
     * 
     * https://github.com/ourCloudSky/OnePort/
     * 
     * Copyright 2016-2017
     * 
     * @package OnePort
     * @author xtl<xtl@xtlsoft.top>
     * @license MIT
     * 
     * @regards Workerman
     * 
     */
    
    use \Workerman\Worker;
    use \Workerman\Connection\AsyncTcpConnection;
    
    //require libs
    require "Workerman/Autoloader.php";
    require "iniReader.php";
    
    define("PHPDir", '/usr/bin/');
    
    //Get the config
    $GLOBALS['conf'] = getiniconfig("./config.ini");
    global $conf;
    $conf['web']['handle'] = str_replace("\${SysDir}",__DIR__,$conf['web']['handle']);
    
    $GLOABLS['atc'] = array(); //AsyncTCPConnection
    
    //Define the worker
    $GLOBALS['worker'] = new Worker("websocket://" . $conf['listen']['ip'] . ':' . $conf['listen']['port']);
    
    global $worker;
    
    $worker->count = $conf['listen']['thread'];
    $worker->name = "OnePort";
    
    $worker->onConnect = function ($con){
        
        $wID = $con->worker->id;
        $ID = $con->id;
        $ID = $wID.'_'.$ID;
        $con->id = $ID;
        
        echo "[CONN] Client $ID Connected.\n";
        
    };
    
    $worker->onMessage = function ($con, $data){
        
        global $atc;
        global $conf;
        
        $wID = $con->worker->id;
        $ID = $con->id;
        
        $data = base64_decode($data);
        
        //var_dump(isset($atc[$ID]['username']));
        
        if(!isset($atc[$ID]['username'])){
            $e = explode("||", $data);
            $usr = $e[0]; //username
            $pwd = $e[1]; //password
            $adr = $e[2]; //address
            $trans = $e[3]; //ssl or tcp
            
            if(isset($conf['user_'.$usr])){
                $u = $conf['user_'.$usr];
                if($usr === $u['username'] && $pwd === $u['password']){
                    /*
                    ** @@ The area to add disallow address @@
                    */
                    
                    $atc[$ID]['username'] = $usr;
                    $atc[$ID]['password'] = $pwd;
                    $atc[$ID]['address']  = $adr;
                    
                    $atc[$ID]['con'] = new AsyncTCPConnection($adr);
                    
                    $atc[$ID]['con']->transport = $trans;
                    
                    $atc[$ID]['con']->connect();
                    
                    $atc[$ID]['con']->onConnect = function ($conn){
                        
                        //$conn->send("xxx||xxx||xxx");
                        
                    };
                    
                    $atc[$ID]['con']->onMessage = function ($conn, $datan) use($ID){
                        
                        global $worker;
                        foreach($worker->connections as $connect){
                            if($connect->id == $ID){
                                /**
                                 * @@ Place to add encrypt @@
                                 * @@@@
                                 */
                                $connect->send( base64_encode($datan) );
                            }
                        }
                        
                    };
                    
                    
                }else{
                    $con->send("_MCON_:Password Wrong");
                    $con->close();
                }
            }else{
                $con->send("_MCON_:No Such User");
                $con->close();
            }
            
        }else{
            
            $atc[$ID]['con']->send($data);
            
        }
        
    };
    
    $worker->onClose = function ($con){
        
        $wID = $con->worker->id;
        $ID = $con->id;
        
        global $atc;
        
        if(isset($atc[$ID]['con'])){
            $atc[$ID]['con']->close();
        }
        
        
        echo "[CLOS] Client $ID Closed.\n";
        
    };
    
    //Run the Worker
    Worker::runAll();
    
