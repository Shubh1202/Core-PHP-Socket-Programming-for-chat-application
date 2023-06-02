<?php
require("config.php");

// require("socket-config.php");

$ipv4 = AF_INET;
$duplex_mode = SOCK_STREAM;
$protocol = 0;

$host = "127.0.0.1";
$port = 8181;

$socket = socket_create($ipv4, $duplex_mode, $protocol) or die("Socket not created.");

$sock_bind = socket_bind($socket, $host, $port) or die("Socket not bind.");

$sock_listen = socket_listen($socket, 3) or die("Socket not listen"); 



while(true){
$sock_accept = socket_accept($socket) or die("Socket not accept");
$sock_read = socket_read($sock_accept, 1024) or die("Socket not read data");
$msg = trim($sock_read);
echo "Client Message : ".$msg. "\n";

echo "Enter Reply: ";

$reply = fgets(STDIN);

$sock_write = socket_write($sock_accept, $reply, strlen($reply));

};

socket_close($sock_accept);
socket_close($socket);

?>