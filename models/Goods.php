<?php
namespace models;

class Goods extends Model
{
    // 设置这个模型对应的表
    protected $table = 'goods';
    // 设置允许接收的字段
    protected $fillable = ['goods_name','logo','is_on_sale','description','cat1_id','cat2_id','cat3_id','brand_id'];

    protected function delete_logo(){
        // 如果是修改就删除原来的图片
        if(isset($_GET['id'])){
            // 先从数据库中取出原logo
            $ol = $this->findone($_GET['id']);
            // 删除
            @unlink(ROOT.'public'.$ol['logo']);
        }
    }


    // 添加、修改之前执行
    public function before_write(){
        echo "<pre>";
        var_dump($_FILES);
       
        // 如果删除了logo，就删除原来的logo再上传新的logo
        if($_FILES['logo']['error'] == 0 ){
            // 删除原来的logo
            $this->delete_logo();
            // 实现上传图片的代码
            $uploader =  \libs\Uploade::getuploads();
            $logo = '/uploads/'.$uploader->uploade('logo','goods');
            
            // $this->data ：将要插入到数据库中的数据（数组）
            // 把logo加到数组中，就可以插入到数据库
            $this->data['logo'] = $logo;
        }
    }

    // 添加、修改之后执行
    // 添加时：获取商品ID  $this->data['id']
    // 修改时：获取商品ID $_GET['id']
    public function after_write(){
        // 获取商品的ID
        $goodsId = isset($_GET['id']) ? $_GET['id'] : $this->data['id'];
        echo $goodsId;
        // 处理商品属性

        // 先删除原来的属性
        $stmt = $this->db->prepare("DELETE FROM goods_attribute WHERE goods_id = ?");
        $stmt->execute([$goodsId]);
        if(!isset($goodsId)){
            $stmt = $this->db->prepare("INSERT INTO goods_attribute (attr_name,attr_value,goods_id) VALUES(?,?,?)");
            // 循环每一个属性，插入到属性表中
            foreach($_POST['attr_name'] as $k=>$v){
                $stmt->execute([
                    $v,
                    $_POST['attr_value'][$k],
                    $goodsId,
                ]);
            }
        }
        // 商品图片

        // 如果有要删除的图片ID，那就删除
        if(isset($_POST['del_image']) && $_POST['del_image'] != ''){
            // 先根据ID把图片路径取出来
            $stmt = $this->db->prepare("SELECT path FROM goods_image WHERE id IN ({$_POST['del_image']})");
            $stmt->execute();
            $path = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // 循环每个图片的路径并删除
            foreach($path as $v){
                @unlink(ROOT.'public/'.$v['path']);
            }
            // 从数据库中把图片的记录删除
            $stmt = $this->db->prepare("DELETE FROM goods_image id IN({$_POST['del_image']})");
            $stmt->execute();
        }

        $uploader =  \libs\Uploade::getuploads();

        $stmt = $this->db->prepare("INSERT INTO goods_image(goods_id,path) VALUES(?,?)");
        $_tmp = [];
        // 循环图片
        foreach($_FILES['image']['name'] as $k => $v)
        {
            // 如果有图片并且上传成功时才处理图片
            if($_FILES['image']['error'][$k] == 0)
            {
                // 拼出每张图片需要的数组
                $_tmp['name'] = $v;
                $_tmp['type'] = $_FILES['image']['type'][$k];
                $_tmp['tmp_name'] = $_FILES['image']['tmp_name'][$k];
                $_tmp['error'] = $_FILES['image']['error'][$k];
                $_tmp['size'] = $_FILES['image']['size'][$k];
                // 放到 $_FILES 数组中
                $_FILES['tmp'] = $_tmp;
                // upload 这个类会到 $_FILES 中去找图片
                // 参数一、就代表图片在 $_FILES 数组中的名字
                // upload 方法现在就可以直接到 $_FILES 中去找到 tmp 来上传了
                $path = '/uploads/'.$uploader->uploade('tmp', 'goods');
                // 执行SQL
                $stmt->execute([
                    $goodsId,
                    $path,
                ]);
            }
        }

        // sku

        // 删除原来的sku
        $stmt = $this->db->prepare("DELETE FROM goods_sku WHERE goods_id=?");
        $stmt->execute([$goodsId]);
        $stmt = $this->db->prepare("INSERT INTO goods_sku (goods_id,sku_name,stock,price) VALUES(?,?,?,?)");
        foreach($_POST['sku_name'] as $k=>$v){
            $stmt->execute([
                $goodsId,
                $v,
                $_POST['stock'][$k],
                $_POST['price'][$k],
            ]);
        }

    }



    // 获取商品所有的信息
    public function getFullInfo($id){
        // 获取商品的基本信息
        $stmt = $this->db->prepare("SELECT * FROM goods WHERE id=?");
        $stmt->execute([$id]);
        $info = $stmt->fetch(\PDO::FETCH_ASSOC);
        // 获取商品属性信息
        $stmt = $this->db->prepare("SELECT * FROM goods_attribute WHERE goods_id=?");
        $stmt->execute([$id]);
        $attrs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 获取商品图片
        $stmt = $this->db->prepare("SELECT * FROM goods_image WHERE goods_id=?");
        $stmt->execute([$id]);
        $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 获取商品sku
        $stmt = $this->db->prepare("SELECT * FROM goods_sku WHERE goods_id=?");
        $stmt->execute([$id]);
        $skus = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 返回所有数据
        return [
            'info'=>$info,
            'images'=>$images,
            'skus'=>$skus,
            'attrs'=>$attrs,
        ];
    }

    // 删除之前被调用（钩子函数：定义好之后自动被调用）
    public function before_delete()
    {
        $this->delete_logo();
    } 

    public function arrdata($data){
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
    }

     
}