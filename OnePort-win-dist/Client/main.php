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
    
    //Get the config
    $GLOBALS['conf'] = getiniconfig("./config.ini");
    global $conf;
    $conf['mirror'] = array();
    foreach($conf as $k=>$v){
        if(substr($k, 0, 7) == "mirror_"){
            $v['name'] = $k;
            $conf['mirror'][] = $v;
        }
    }
    
    $GLOBALS['atc'] = array();
    
    foreach($conf['mirror'] as $k=>$v){
        
        global $atc;
        
        $atc[$k] = array();
        
        $worker = new Worker($v['mirror']);
        $worker->name = $v['name'];
        $worker->count = 1;
        $worker->transport = $v['ssl'];
        $worker->onConnect = function($conn) use($k, $worker, $v){
            
            global $conf, $atc;
            
            $id = $conn->id;
            
            $atc[$k][$id] = new AsyncTcpConnection("ws://".$conf['common']['ip'].":".$conf['common']['port']);
            
            $atc[$k][$id]->onMessage = function($con, $msg) use ($conn){
                $conn->send(base64_decode($msg));
            };
            
            $atc[$k][$id]->onClose = function($con) use ($conn){
                $conn->close();
            };
            
            $atc[$k][$id]->connect();
            
            $atc[$k][$id]->send(base64_encode($conf['user']['username'].'||'.$conf['user']['password'].'||'.$v['base'].'||'.$v['protocol']));
            
        };
        
        $worker->onMessage = function($conn, $msg) use($k, $worker, $v){
            
            global $atc;
            
            $atc[$k][$conn->id]->send(base64_encode($msg));
            
        };
        
        $worker->onClose = function($conn) use($k, $worker, $v){
            
            global $atc;
            
            $atc[$k][$conn->id]->close();
            
        };
        
    }
    
    Worker::runAll();