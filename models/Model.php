<?php
namespace models;
    // 所有模型的父模型  
    // 在这实现所有的：增删改查 翻页 等功能
class Model{
    protected $db;
    // 操作的表名，具体的值有子类设置
    protected $table;
    // 表单中的数据，值由子类设置
    protected $data;

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


        $keys = [];
        $values = [];
        $token = [];

        foreach($data as $k=>$v){
            $keys[]= $k;
            $values[]=$v;
            $token[]='?';
        }

        $keys = implode(',',$keys);
        $token = implode(',',$token);

        $sql = "INSERT INTO {$this->table}($keys) VALUES($token)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete(){
        
    }

    public function update(){
        
    }

    public function findAll(){
        
    }

    public function findone(){
        
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
}