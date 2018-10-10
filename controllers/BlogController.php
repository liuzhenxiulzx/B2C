<?php
namespace controllers;

use models\Blog;

class BlogController{
    // 列表页
    public function index()
    {
        $model = new Blog;
        $data = $model->findAll();
        view('Blog/index', $data);
    }

    // 显示添加的表单
    public function create()
    {
        view('Blog/create');
    }

    // 处理添加表单
    public function insert()
    {
        $model = new Blog;
        $model->fill($_POST);
        $model->insert();
        redirect('/Blog/index');
    }

    // 显示修改的表单
    public function edit()
    {
        $model = new Blog;
        $data=$model->findOne($_GET['id']);
        view('Blog/edit', [
            'data' => $data,    
        ]);
    }

    // 修改表单的方法
    public function update()
    {
        $model = new Blog;
        $model->fill($_POST);
        $model->update($_GET['id']);
        redirect('/Blog/index');
    }

    // 删除
    public function delete()
    {
        $model = new Blog;
        $model->delete($_GET['id']);
        redirect('/Blog/index');
    }
}