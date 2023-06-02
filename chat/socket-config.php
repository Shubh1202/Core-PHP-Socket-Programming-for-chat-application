<?php
require("config.php");

$ipv4 = AF_INET;
$duplex_mode = SOCK_STREAM;
$protocol = 0;

$host = "127.0.0.1";
$port = 8181;

$socket = socket_create($ipv4, $duplex_mode, $protocol) or die("Socket not created.");


?>