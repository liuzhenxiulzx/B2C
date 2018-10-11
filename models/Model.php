<?php
namespace models;
use PDO;
    // 所有模型的父模型  
    // 在这实现所有的：增删改查 翻页 等功能
class Model{
    protected $db;
    // 操作的表名，具体的值有子类设置
    protected $table;
    // 表单中的数据，值由子类设置
    protected $data;
     /**
     * 钩子函数
     */
    protected function before_write(){} //写入前
    protected function after_write(){}  //写入后
    protected function before_delete(){} //删除前
    protected function after_delete(){}  //删除后

    public function __construct(){

        $this->db = \libs\DB::getDB();

    }

    public function insert(){
        // // 取出数组中所有的键，组成一个新的数组
        // $keys = array_keys($this->data);
        // //把数组转成字符串
        // $keys = implode(',',$keys);

        // // 取出数组中所有的值，组成一个新的数组
        // $values = array_values($this->data);
        // //把数组转成字符串
        // $values = implode("','",$values);  

        // $sql = "INSERT INTO {$this->table}($keys) VALUES('$values')";
        // $this->db->exec($sql);
        $this->before_write();

        $keys = [];
        $values = [];
        $token = [];

        foreach($this->data as $k=>$v){
            $keys[]= $k;
            $values[]=$v;
            $token[]='?';
        }

        $keys = implode(',',$keys);
        $token = implode(',',$token);

        $sql = "INSERT INTO {$this->table}($keys) VALUES($token)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        $this->data['id'] = $this->db->lastInsertId();
        
        $this->after_write();
    }

    // 删除
    public function delete($id){
        $this->before_delete();
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        
        $this->after_delete();
    }

    // 修改
    public function update($id){
        $this->before_write();
        $set=[];
        $token=[];
        foreach($this->data as $k => $v){
            $set[]="$k=?";
            $token='?';
            $values[]=$v;
        }
        $set = implode(',',$set);
        $values[]=$id;
        // var_dump($values);
        $sql = "UPDATE {$this->table} SET $set WHERE id=?";
        // var_dump($sql);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        $this->after_write();
    }

    // 翻页
    public function findAll($options=[]){
        $option_arr = [
            'fields'=>'*',
            'where'=>1,
            'order_by'=>'id',
            'order_way'=>'desc',
            'per_page'=>20,
            'join'=>'',
            'groupby'=>'',
        ];

        // 合并用户的配置
        if($options){
            $option_arr = array_merge($option_arr,$options);
        }

        // 翻页

        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        $offset = ($page-1)*$option_arr['per_page'];

        $sql = "SELECT {$option_arr['fields']}
                FROM {$this->table}
                {$option_arr['join']}
                WHERE {$option_arr['where']}
                {$option_arr['groupby']}
                ORDER BY {$option_arr['order_by']} {$option_arr['order_way']} 
                LIMIT $offset,{$option_arr['per_page']}
                ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 获取总的记录数
         $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$option_arr['where']}");
         $stmt->execute();
         $count = $stmt->fetch(PDO::FETCH_COLUMN);
         $pageCount = ceil($count/$option_arr['per_page']);

         $page_str = '';
         if($pageCount > 1){
             for($i=1;$i<=$pageCount;$i++){
                $page_str .= '<a href="?page='.$i.'">'.$i.'</a>';
              }
         }

         return [
             'data'=>$data,
             'page'=>$page_str,
         ];

    }

    // 取一条记录
    public function findone($id){

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 接收表单数据
    public function fill($data){
        // 判断是否在白名单中
        foreach($data as $k => $v){
            // 如果不在就删掉
            if(!in_array($k,$this->fillable)){
                unset($data[$k]);
            }
        }
        $this->data = $data;
    }


    // 递归排序
    // 参数一：排序的数据
    // 参数二：上级ID
    // 参数三：第几级
    public function _tree($data,$parent_id=0,$level=0){

        // 定义一个数组保存排好序的数据
        static $_ret = [];

        foreach($data as $v){
            if($v['parent_id'] == $parent_id){
                // 标记它的级别
                $v['level'] = $level;
                // 挪到排序之后的数组中
                $_ret[] = $v;
                // 找$v的子分类
                $this->_tree($data,$v['id'],$level+1);
            }
        }
        // 返回排序好的数组
        return $_ret;

    }


    public function getAll($sql, $data=[])
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRow($sql, $data=[])
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function getOne($sql, $data=[])
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetch(\PDO::FETCH_COLUMN);
    }


    
}