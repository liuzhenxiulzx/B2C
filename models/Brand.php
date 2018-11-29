<?php
namespace models;

class Brand extends Model
{
    // 设置这个模型对应的表
    protected $table = 'brand';
    // 设置允许接收的字段
    protected $fillable = ['brand_name','logo'];

    // 添加、修改之前执行
    public function before_write(){
        // echo "<pre>";
        // var_dump($_FILES);
    
        // 如果删除了logo，就删除原来的logo再上传新的logo
      
            // 实现上传图片的代码
            $uploader =  \libs\Uploade::getuploads();
            $logo = '/uploads/'.$uploader->uploade('logo','goods');

            // $this->data ：将要插入到数据库中的数据（数组）
            // 把logo加到数组中，就可以插入到数据库
            $this->data['logo'] = $logo;
  
    }

        // 添加之后执行的钩子函数
        public function after_write(){
            // var_dump($this->data);
            // exit;
            // 构造数据(七牛云)
            $data = [
                'logo'=>$this->data['logo'],
                'id'=>$this->data['id']
            ];
    
          
            $client = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => 'localhost',
                'port'   => 6379,
            ]);
        
            // // 转成字符串保存到队列中
            $client->lpush('jxshop:qiniu', serialize($data));
        }

    // 删除之前被调用（钩子函数：定义好之后自动被调用）
    public function before_delete()
    {
        $this->delete_logo();
    }
    protected function delete_logo()
    {
        // 如果是修改就删除原图片
        if(isset($_GET['id']))
        {
            // 先从数据库中取出原LOGO
            $ol = $this->findone($_GET['id']);
            // 删除
            @unlink(ROOT . 'public'. $ol['logo']);
        }
    }



}