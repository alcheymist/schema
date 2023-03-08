<?php
namespace mutall;

//use mutall;
//
//This file supports the link between the server and client sub-systems
//
//Start the buffering as early as possible. All html outputs will be 
//bufferred 
\ob_start();
//
//Catch all errors, including warnings.
\set_error_handler(function(
    $errno, 
    $errstr, 
    $errfile, 
    $errline /*, $errcontext*/
){
    throw new \ErrorException($errstr, $errno, \E_ALL, $errfile, $errline);
});
//The output structure has the format: {ok, result, html} where:-
//ok: is true if the returned result is valid and false if not. 
//result: is the user request if ok is true; otherwise it is the error message
//html: is any buffered html output message that may be helpful to interpret
//  the error message 
$output = new \stdClass();
//
//Catch any error that may arise.
try{
    //Resolve the reference to the mutall class
    include_once '../schema.php';
    //
    //Resolve the references to the migration class.
    include_once './migration.php';
    //
    //Resove the reference to the questionnaire class. Note its absolute location
    //because we may be calling this code from a folder whose relationship with 
    //the schema is not defined.
    $path = $_SERVER['DOCUMENT_ROOT'].'/schema/v/code/questionnaire.php';
    //
    include_once ($path);
    //
    mutall::save_requests('../post.json');
//    mutall::set_requests('./post.json');
    //
    //Use the global variables to extract the cmd property; it represents the 
    //request from the receiver.
    $cmd_glabal = $_REQUEST['cmd'];
    //
    //Extract the command parameters: class, cargs, method, margs.
    //
    //Decode the command string.
    $cmd = json_decode($cmd_glabal, true);
    //
    //Get the classname.
    $class = $cmd['class'];
    //
    //Get the constructor arguements.
    $cargs = $cmd['cargs'];
    //
    //Get the method to execute.
    $method = $cmd['method'];
    //
    //Get the method arguements.
    $margs = $cmd['margs'];
    //
    //Use the class name to construct the matching objects.  
    $object = new $class(...$cargs);
    //
    //Execute the requested method using the given arguements and collect the 
    //result.
    $result = $object->$method(...$margs);
    //
    //Set the output property to the executed result.
    $output->result = $result;
    //
    //The process is successful; register that fact
    $output->ok=true;
}
//
//The user request failed
catch(\Exception $ex){
    //
    //Register the failure fact.
    $output->ok=false;
    //
    //Compile the full message, including the trace
     //
    //Replace the hash with a line break in the terace message
    $trace = \str_replace("#", "<br/>", $ex->getTraceAsString());
    //
    //Record the error message in a friendly way
    $output->result = $ex->getMessage() . "<br/>$trace";
}
finally{
    //
    //Empty the output buffer to the output html property
    $output->html = ob_end_clean();
    //
    //Convert the output to a string
    $encode = json_encode($output, \JSON_THROW_ON_ERROR);
    //
    //Return output to the client
    echo $encode;
}