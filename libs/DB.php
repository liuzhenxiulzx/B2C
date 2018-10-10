<?php
    namespace libs;

    class DB{

        private static $dbname = null;
        private $pdo;
        private function __clone(){}
        
        private function __construct(){
            // 连接数据库
            $this->pdo = new \PDO('mysql:host=127.0.0.1;dbname=b2c','root','');
            // 设置编码
            $this->pdo->exec('set names utf8');
        }

        public static function getDB(){

            if(self::$dbname === null){

                self::$dbname = new self;

            }
            return self::$dbname;

        }

        // 预处理
        public function prepare($sql){
           return $this->pdo->prepare($sql);
        }

        // 非预处理执行SQL
        public function exec($sql){
            return $this->pdo->exec($sql);
        }

        function redirect($url)
        {
            header('Location:'.$url);
            exit;
        }

        public function lastInsertId()
        {
            return $this->pdo->lastInsertId();
        }

    }
?>