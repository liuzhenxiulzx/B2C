<?php
    define("ROOT",dirname(__FILE__).'/../');
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