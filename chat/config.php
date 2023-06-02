<?php
session_start();
error_reporting(0);
array_walk_recursive($_POST, "filter_data");
array_walk_recursive($_GET, "filter_data");
array_walk_recursive($_REQUEST, "filter_data");
set_time_limit(0);

function write_error($number=NULL, $msg=NULL, $file=NULL){
    if(!is_null($file)){
        $prx = "Error: ".$number." ===> ".$msg." ==> ".$file;
    }else{
        $prx = "Error: ".$number." ===> ".$msg;
    }
    die($prx);
}

function db(){
    $host = "127.0.0.1";
    $db = "chat";
    $user = "root";
    $pass = "";

    $con = mysqli_connect($host, $user, $pass, $db) or write_error("101", "Cloud server not respond", "config.php");

    return $con;
}

$con = db();

function filter_data(&$data, $key=NULL){
    $data = strip_tags($data);
    $data = htmlentities($data);
    $data = htmlspecialchars($data);
    $data = trim($data);
    return $data;
}

function insertData($table, $array_data){
    $con = db();
    $keys = array_keys($array_data);
    $values = array_values($array_data);

    $keys = implode("`, `",$keys);
    $values = implode(" ', '", $values);

    $query = "INSERT INTO `$table` (`$keys`) VALUES('$values')";
    $sql = mysqli_query($con, $query) or write_error("102", "Data could not be saved", "config.php");

}

function updateData($table, $array_data){
    $con = db();
    $phone = $_SESSION["USER_PHONE"];
    $keys = array_keys($array_data);
    $values = array_values($array_data);

    $keys = implode("`, `",$keys);
    $values = implode(" ', '", $values);

    echo $query = "UPDATE `$table` SET `$keys`='$values' WHERE `phone`='$phone'";
    $sql = mysqli_query($con, $query) or write_error("103", "Data could not be updated", "config.php");

}

function insertDataExist($table, $selection_array, $is_check=NULL){
    $con = db();
    $keys = array_keys($selection_array);
    $values = array_values($selection_array);

    // $keys = implode("`, `",$keys);
    // $values = implode(" ', '", $values);

    $phone = $selection_array;
    $query = "SELECT * FROM `$table` WHERE `phone`='$phone'";
    $sql = mysqli_query($con, $query) or write_error("102", "Data could not be find data", "config.php");

    if(!is_null($is_check)){
        $rows = mysqli_num_rows($sql);
        return ($rows>0) ? array("status"=>"error", "msg"=>"Record found") : array("status"=>"success",  "msg"=>"Record not found");
    }

    if(is_null($is_check)){
        $data = mysqli_fetch_assoc($sql);
        return (count($data)>0) ? array("status"=>"success", "data"=>$data) : array("status"=>"error");
    }

}
?>