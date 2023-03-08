<?php
//
//This is a script executed using a browser to test migration
//of data from one database to another on this local machine. This code
//must be inatslled locally
//
//Resolve access to the shared migration code
include_once 'migration/classes.php';

//Create a server that will write data to mutallco_rental_test database
//on the local server
$local= new mutall\migration\server('mutallco_rental');
//
//Compile the data source
$from_local = ['dbname'=>'mutallco_rental_test', 'server'=>$local::LOCALHOST];
//
//Migrate data from mutallco_rental to the test in this local host
$local->migrate($from_local);