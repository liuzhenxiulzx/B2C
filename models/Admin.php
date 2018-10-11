<?php
namespace models;

class Admin extends Model
{
    // 设置这个模型对应的表
    protected $table = 'admin';
    // 设置允许接收的字段
    protected $fillable = ['username','password'];
    
    // 添加之前自动执行
    public function before_write(){
        $this->data['password'] = md5($this->data['password']);
    }

    // 添加、修改之后自动执行
    protected function after_write(){
        $id = isset($_GET['id']) ? $_GET['id'] : $this->data['id'];
        // 删除原来的数据
        $stmt = $this->db->prepare("DELETE FROM admin_role WHERE admin_id = ?");
        $stmt->execute([
            $id,
        ]);
        // 重新添加新勾选的权限
        $stmt = $this->db->prepare("INSERT INTO admin_role(admin_id,role_id) VALUES(?,?)");
        // 循环所有勾选的权限ID插入到中间表
        foreach($_POST['role_id'] as $v)
        {
            $stmt->execute([
                $id,
                $v,
            ]);
            
        }
    }


    // 登录
    public function login($username,$password){
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->execute([
            $username,
            md5($password),
        ]);

        $info = $stmt->fetch(\PDO::FETCH_ASSOC);
       
        if($info){
            $_SESSION['id'] = $info['id'];
            $_SESSION['username'] = $info['username'];

            // 查看该管理员是否有一个角色ID为1
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM admin_role WHERE role_id = 1 AND admin_id = ?');
            $stmt->execute([$_SESSION['id']]);
            $c = $stmt->fetch(\PDO::FETCH_COLUMN);
          
            if($c>0){
                $_SESSION['root'] = true;
            }else{
                // 取出这个管理员有权访问的路径
                $_SESSION['url_path'] = $this->getpath($_SESSION['id']);
                // echo "<pre>";
                // var_dump($_SESSION['url_path']);
                // exit;
            }
            
        }else{
            throw new \Exception('用户名或密码错误');
        }
    } 

    // 退出
    public function logout(){
        $_SESSION=[];
        session_destroy();
    }


    // 取出该管理员的有权访问的路径
    public function getpath($adminId){
        $sql = "SELECT c.url_path
                FROM admin_role a
                LEFT JOIN role_privilege b ON a.role_id = b.role_id
                LEFT JOIN privilege c ON c.id = b.pri_id
                WHERE a.admin_id = ? AND c.url_path!=''";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$adminId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 把二维数组转成一维数组
          $_ret = [];
          foreach($data as $v){
                // 判断是否有多个url
                if(FALSE === strpos($v['url_path'],',')){
                    // 如果没有，就直接拿过来
                    $_ret[] = $v['url_path'];
                }else{
                    // 如果有，就转成数组
                    $arr = explode(',',$v['url_path']);
                    // 把转完之后的数组合并到一维数组中
                    $_ret = array_merge($_ret,$arr);
                }
              
          }
          return $_ret;
        // echo "<pre>";
        //   var_dump($_ret);
         
    }
}