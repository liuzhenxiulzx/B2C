<?php
namespace controllers;

class BlogController{
    // 列表页
    public function index()
    {
        view('Blog/index');
    }

    // 显示添加的表单
    public function create()
    {
        view('Blog/create');
    }

    // 处理添加表单
    public function insert()
    {
        $blog = new \models\Blog;
        // 为模型填充数据
        $blog->fill($_POST);
        $blog->insert();
    }

    // 显示修改的表单
    public function edit()
    {
        view('Blog/edit');
    }

    // 修改表单的方法
    public function update()
    {

    }

    // 删除
    public function delete()
    {

    }
}