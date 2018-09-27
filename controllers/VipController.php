<?php
namespace controllers;

use models\Vip;

class VipController{
    // 列表页
    public function index()
    {
        $model = new Vip;
        $data = $model->findAll();
        view('vip/index', $data);
    }

    // 显示添加的表单
    public function create()
    {
        view('vip/create');
    }

    // 处理添加表单
    public function insert()
    {
        $model = new Vip;
        $model->fill($_POST);
        $model->insert();
        redirect('/vip/index');
    }

    // 显示修改的表单
    public function edit()
    {
        $model = new Vip;
        $data = $model->findOne($_GET['id']);
        view('vip/edit', [
            'data' => $data,    
        ]);
    }

    // 修改表单的方法
    public function update()
    {
        $model = new Vip;
        $model->fill($_POST);
        $model->update($_GET['id']);
        redirect('/vip/index');
    }

    // 删除
    public function delete()
    {
        $model = new Vip;
        $model->delete($_GET['id']);
        redirect('/vip/index');
    }
}