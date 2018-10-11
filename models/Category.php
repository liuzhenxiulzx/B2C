<?php
namespace models;

class Category extends Model
{
    // 设置这个模型对应的表
    protected $table = 'category';
    // 设置允许接收的字段
    protected $fillable = ['cat_name','parent_id','path'];

    // 参数：上级分类的ID
    public function getcate($parent_id=0){
        return  $this->findAll([
            'where'=>"parent_id=$parent_id"
        ]);
    }


    // 递归排序
    public function tree(){
        // 取出所有的分类
        $data = $this->findAll([
            'per_page'=>9999999999,
        ]);
        // 递归排序
        // echo '<pre>';
        // var_dump($data['data']);
       return $this->sort($data['data']);
       
    }

    // 第一个参数：排序的数据
    // 第二个参数：顶级父分类的ID
    // 第三个参数：当前分类的级别
    public function sort($data,$parent_id=0,$level=0){

        // 定义保存挑出来的分类
        static $arr = [];

        // 循环挑分类
        foreach($data as $v){
            if($v['parent_id'] == $parent_id){
                // 把level值放到$v里用来标记他是第几级的
                $v['level']= $level;
                $arr[] = $v;
                // 继续挑分类
                $this->sort($data,$v['id'],$level+1);
            }
        }

        // 把挑完的数组返回
        return $arr;
    }

}