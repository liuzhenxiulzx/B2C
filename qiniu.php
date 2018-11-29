<?php 
     require('./vendor/autoload.php');

     use Qiniu\Storage\UploadManager;
     use Qiniu\Auth;

     $pdo = new \PDO('mysql:host=127.0.0.1;dbname=b2c', 'root','');
    //  连接redis
     $client = new \Predis\Client([
        'scheme' => 'tcp',
        'host'   => 'localhost',
        'port'   => 6379,
    ]);

    // 设置 socket 永不超时
    ini_set('default_socket_timeout', -1); 
    
    // 上传到七牛云
    $accessKey = 'MTdzBcP66AhXwzrKzf2nDT2gOjrft0wYZc9FwXYW';
    $secretKey = '_PZctlny0U_TfoAWar-av-a8L3ma03W9gMZmgkxz';
    $domain = 'http://pixpeuy85.bkt.clouddn.com';       // 访问域名
    // 配置参数
    $bucketName = 'vue-shop';   // 创建的 bucket(新建的存储空间的名字)

    $upManager = new UploadManager();

    // 登录获取令牌
    $expire = 86400 * 365 * 10; //令牌有效期为10年
    $auth = new Auth($accessKey, $secretKey);
    $token = $auth->uploadToken($bucketName,null,$expire);  //登录到七牛云，参数一：存储空间名，参数二：策略，参数三：令牌有效时间


    //循环监听列表
    while(true){
        // 从列表中取出数据，设置为永不超时 （如果队列为空，则一直堵塞）
        $redata = $client->brpop('jxshop:qiniu',0);
        // 处理数据 
        $data = unserialize($redata[1]); //转成数组

        // 参数一、令牌
        // 参数二、上传之后服务器上的文件名(生成一个唯一随机的名字)
        // 参数三、要上传的本地文件名

        $name = ltrim(strrchr($data['logo'],'/'),'/');
        // 上传的文件
        $file = './public'.$data['logo'];
       
        list($ret, $error) = $upManager->putFile($token, $name, $file);
       
        // 判断是否成功
        if ($error !== null) {
            // 如果失败，重新将数据放入队列中
            $client->lpush('jxshop:qiniu',$redata[1]);
        } else {
            // 更新数据库
            $new = $domain.'/'.$ret['key'];
            $sql = "UPDATE brand SET logo = '$new' WHERE id = ".$data['id'];
            $pdo->exec($sql);
            // 删除本地文件
            @unlink($file);
            echo 'ok';

        }
        
    }
?>