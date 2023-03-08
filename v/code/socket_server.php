<?php 
//
// set some variables for config 
$host = "127.0.0.1";
//
//The port number
$port = 25303;
//
// never timeout! then set 0
set_time_limit(0);
//
//
    // Create socket
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
    //
    // Bind socket to port
    $result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
    //
//Keep the server running.
while (true){
    
    //
    // Start listening for connections
    $result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
    //
    // Accept incoming connections
    // spawn another socket to handle communication
    $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
    //
    // Read client input
    $response = socket_read($spawn, 1024) or die("Could not read input\n");
    //
    // Clean up input string
    $response = trim($response);
    echo "Client Message : ".$response;
    //
    //To show that the message was indeed recieved and changed.
    $output = strrev($response);
    //
    socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
    //
}
// Close sockets
socket_close($spawn);
socket_close($socket);

?>