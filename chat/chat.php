<?php
require("config.php");

// require("socket-config.php");

$ipv4 = AF_INET;
$duplex_mode = SOCK_STREAM;
$protocol = 0;

$host = "127.0.0.1";
$port = 8181;

$socket = socket_create($ipv4, $duplex_mode, $protocol) or die("Socket not created.");


$my_login_id = $_SESSION["USER_ID"];
$to_login_id = (!isset($_SESSION["TO_PHONE"])) ? "Admin" : $_SESSION["TO_PHONE"];

$request = file_get_contents("php://input");
$_arr = json_decode($request, true);
array_walk_recursive($_arr, "");
$msg = $_arr["prx"];
$table = "messages";

$sock_connect = socket_connect($socket, $host, $port) or die(json_encode(array("status" => "error", "msg"=>"Socket not connectd")));
$sock_write = socket_write($socket, $msg, strlen($msg));

$reply_data = socket_read($socket, 1024);
echo $reply_data;

socket_close($socket);



if($sock_write){
  $arra_data = array("from_uxid" => $my_login_id, "to_uxid" => $to_login_id, "msg" => $msg);
  insertData($table, $arra_data);
  $array = array("status" => "success");
}else{
  $array = array("status" => "error");
}
echo json_encode($array);

?>