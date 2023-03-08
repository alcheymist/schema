<?php
//
//The localhost address.
$host = "127.0.0.1";
//
//The port number (same as the server port).
$port = 25303;
//
// No Timeout 
set_time_limit(0);
//
$message = "Welcome today";
//
//Create the socket.
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
//
//Connect to the server.
$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
//
while(true){
//Write to the server socket.
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
//
//Read the response from the server.
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
echo "Reply From Server :".$result;
//
}
//Close the socket.
socket_close($socket);

