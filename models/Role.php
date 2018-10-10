<?php
namespace models;

class Role extends Model
{
    // 设置这个模型对应的表
    protected $table = 'role';
    // 设置允许接收的字段
    protected $fillable = ['role_name'];

    // 添加、修改之后自动执行
    protected function after_write(){
        // 重新添加新勾选的权限
        $stmt = $this->db->prepare("INSERT INTO role_privilege(pri_id,role_id) VALUES(?,?)");
        // 循环所有勾选的权限ID插入到中间表
        foreach($_POST['pri_id'] as $v)
        {
            $stmt->execute([
                $v,
                $this->data['id'],
            ]);
          
        }
    }

    protected function before_delete(){

        $stmt = $this->db->prepare("delete from role_privilege where role_id = ?");
        $stmt->execute([
            $_GET['id']
        ]);
    }
}