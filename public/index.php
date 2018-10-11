<?php

    // 动态的修改 php.ini 配置文件
    ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
    ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=1');  // 设置 redis 服务器的地址、端口、使用的数据库

    session_start();

    define("ROOT",dirname(__FILE__).'/../');
    // 设置时区
    date_default_timezone_set('PRC');

    // 引入函数文件
    require(ROOT.'libs/function.php');
    //类的自动加载
    function autoload($class){
        $path = str_replace('\\','/',$class);
        require(ROOT.$path.'.php');
    }

    spl_autoload_register('autoload');

    //$_SERVER['PATH_INFO'];    /Blog/index
    if(isset($_SERVER['PATH_INFO'])){ 

        $pathInfo = $_SERVER['PATH_INFO'];
        //转成数组
        $pathInfo = explode('/',$pathInfo);
        $controller = ucfirst($pathInfo[1]).'Controller';
        $action = $pathInfo[2];

    }else {

         $controller = 'Indexcontroller';
         $action = 'index';

    }

    $Allcontroller = 'controllers\\'.$controller;

    $c = new $Allcontroller;
    $c->$action();

    // 加载视图
    function view($filename,$data=[]){
        // 解压数组
        extract($data);
        $fileview = str_replace('.','/',$filename).'.html';

        // 加载视图
        require(ROOT.'views/'.$fileview);
        
    }

?>        