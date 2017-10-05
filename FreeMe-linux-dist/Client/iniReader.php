<?php

##################################################################################################
/*
功        能：读取 ini 文件. 支持节
版        本：2.0
作        者：Jinsen
日        期：2011-05-31
入        参：ini 文件名:字符串
返    回    值：一个二维数组.第一维是节:简单字符串类型,第二维是节内的配置:关联数组
修        改：
注        意：ini 文件
            !!! 所有的配置节或配置项 均为小写!!!
            支持 "#" 或 ";" 开头的整行注释;
            支持 "//" 或 "--" 之后的行尾注释;
            没有节的配置(第一个节之前的配置) 定义为 [default]节;
            如果出现多个相同的节,后面的节覆盖前面定义的节;
            如果节下出现多个相同的配置,后面的配置覆盖前面定义的配置;
            
*/
##################################################################################################
function getiniconfig($configfilename)
{
    # debug 输出控制
    $debug=0;
    
    #创建空的配置栈
    $configs=array();    
    $rows=@file($configfilename); #逐行读取记录
    foreach($rows as $row)
    {
        #清理空白字符
        $config=trim($row);
        #过滤掉空行；处理为空行
        if ($config)
        {
            #过滤注释行；处理非注释行
            if(substr($config,0,1)<>"#" && substr($config,0,1)<>";")
            {
                #删除行尾注释
                //if ($pos=strpos($config,"//",0)) {$config=substr($config,0,$pos);}
                if ($pos=strpos($config,";",0)) {$config=substr($config,0,$pos);}
                
                $configs[]=$config;                        
            }
        }
    }
    //if ($debug) print_r ($configs);
    
    # **********************************************************************************8
    $ini=array();
    $section='default'; //定义默认节
    $section_config=array();

    foreach($configs as $value)
    {
        # 新的节
        if (substr($value,0,1)=='[')
        {
            if ($debug) print "$value\n";
            $ini[$section]=$section_config;
            $section=trim($value,"[]");
            $section_config=array();
        }
        # 节配置项
        else
        {
            #根据“=”分割配置项和配置值，并进行格式化处理.
            if ($pos=strpos($value,"=",0)) //该处的算法为 计算 $pos 的值，如果大于0则进行"{}"内的代码判断
            {
                #获取key：配置项
                $key=trim(substr($value,0,$pos));
                #获取value：配置值
                $value=trim(substr($value,$pos+1));
                #将配置入栈，等待返回
                if ($debug) print "$key=$value\n";          
                $section_config[$key]=$value;            
            }    
        }
        
    }
    # 抓取最后节的配置
    $ini[$section]=$section_config;
    print "\n";
    if ($debug) print_r ($ini);
    # **********************************************************************************8

    return $ini;
        
}

?>