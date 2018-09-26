<?php
    namespace controllers;
    use PDO;
    class CodeController{
        // 生成代码
        public function make(){
            //1.接收参数
            $tableName = $_GET['name'];

            // 2.生成控制器
            // 拼出控制器的名字
            $cname = ucfirst($tableName).'Controller';
            // 加载模板
            ob_start();
            include(ROOT.'templates/controller.php');
            $str = ob_get_clean();
            file_put_contents(ROOT.'controllers/'.$cname.'.php',"<?php\r\n".$str);

            // 3.生成模型
            $mname = ucfirst($tableName);
            ob_start();
            include(ROOT.'templates/model.php');
            $str = ob_get_clean();
            file_put_contents(ROOT.'models/'.$mname.'.php',"<?php\r\n".$str);

            // 4.生成视图文件
            @mkdir(ROOT.'views/'.$tableName,0777);

            // 取出这个表中所有的字段信息
            $sql = "SHOW FULL FIELDS FROM $tableName";
            $db = \libs\DB::getDB();
            //预处理
            $stmt = $db->prepare($sql);
            // 执行SQL
            $stmt -> execute();
            // 取出数据
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);  
            // echo "<pre>";
            // var_dump($data);
            // exit;
            
            // create.html
            ob_start();
            include(ROOT.'templates/create.html');
            $str = ob_get_clean();
            file_put_contents(ROOT.'views/'.$tableName.'/create.html',$str);

            // edit.html
            ob_start();
            include(ROOT.'templates/edit.html');
            $str = ob_get_clean();
            file_put_contents(ROOT.'views/'.$tableName.'/edit.html',$str);

            // index.html
            ob_start();
            include(ROOT.'templates/index.html');
            $str = ob_get_clean();
            file_put_contents(ROOT.'views/'.$tableName.'/index.html',$str);
        }   
    }
 
?>