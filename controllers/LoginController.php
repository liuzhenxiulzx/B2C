<?php

namespace controllers;

use models\Admin;

class LoginController{

    // 显示登录的表单
    public function login(){
        view('login.login');
    }

    // 处理登录表单
    public function dologin(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $model = new Admin;
        
        try{
            $model->login($username,$password);

            // var_dump($_SERVER['PATH_INFO']);
            // var_dump($_SESSION['id']);
            redirect('/');
            // echo "登录成功";
            // var_dump($_SESSION['url_path']);
        }
        catch(\Exception $e)
        {
            echo "失败";
            redirect('/login/login');
        }
        
    }


    // 退出
    public function logout(){
        $model = new Admin;
        $model->logout();
        redirect('/login/login');
    }
}