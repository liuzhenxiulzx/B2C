<?php
    namespace libs;

    class DB{

        private static $dbname = null;
        private $pdo;
        private function __clone(){}
        
        private function __construct(){
            // 连接数据库
            $this->$pdo = new \PDO('mysql:dbhost=127.0.0.1;dbname=b2c','root','');
            // 设置编码
            $this-$pdo->exec('set names utf8');
        }

        public function getDB(){

            if(self::$dbname === null){

                self::$dbname = new self;

            }
            return self::$dbname;

        }







    }
?>