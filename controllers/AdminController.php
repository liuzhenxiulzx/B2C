<?php
namespace controllers;

use models\Admin;

class AdminController extends BaseController{

 
    // 列表页
    public function index()
    {
        $model = new Admin;
        $data = $model->findAll([
            'fields'=>'a.*,GROUP_CONCAT(c.pri_name) pri_list',
            'join'=>' a LEFT JOIN role_privilege b ON a.id=b.role_id LEFT JOIN privilege c ON b.pri_id=c.id ',
            'groupby'=>' GROUP BY a.id ',
        ]);
      
        view('admin/index', $data);

      
    }

    // 显示添加的表单
    public function create()
    {
        // 取出所有角色的数据
        $priModel = new \models\Role;
        $data = $priModel->findAll();
        view('admin/create',$data);
    }

    // 处理添加表单
    public function insert()
    {
        $model = new Admin;
        $model->fill($_POST);
        $model->insert();
        redirect('/admin/index');
    }

    // 显示修改的表单
    public function edit()
    {
        $model = new Admin;
        $data=$model->findOne($_GET['id']);
        view('admin/edit', [
            'data' => $data,    
        ]);
    }

    // 修改表单的方法
    public function update()
    {
        $model = new Admin;
        $model->fill($_POST);
        $model->update($_GET['id']);
        redirect('/admin/index');
    }

    // 删除
    public function delete()
    {
        $model = new Admin;
        $model->delete($_GET['id']);
        redirect('/admin/index');
    }
}