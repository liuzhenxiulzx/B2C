<?php
namespace models;

class Privilege extends Model
{
    // 设置这个模型对应的表
    protected $table = 'privilege';
    // 设置允许接收的字段
    protected $fillable = ['pri_name','url_path','parent_id'];

    // 递归树形结构的数据
    public function tree(){
        // echo "<pre>";
        // var_dump(debug_backtrace());
        // die;
        // 先取出所有的权限
        $data = $this->findAll();
        // 递归重新排序
        $ret = $this->_tree($data['data']);
        return $ret;
    }









}