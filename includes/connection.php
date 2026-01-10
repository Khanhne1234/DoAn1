<?php
if(!defined('_Khanh'))
{
    die('Truy cập không hợp lệ');
}
try{
    if(class_exists('PDO'))
    {
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES utf8",//hỗ trợ tiếng việt
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //đẩy lỗi vào ngoại lệ
        );

        $dsn = _DRIVER .':host='._HOST."; dbname="._DB;
        $conn = new PDO($dsn, _USER, _PASS, $options);
    }
}catch(Exception $ex)
{
    echo 'Lỗi kết nối: '. $ex->getMessage();
}
?>