
<?php
//
//Start a session
session_start();
//
//Resp;ve the refence to the Twilio php autoloader
require_once './vendor/autoload.php';
//
//Instantiate the Alias to the app, the Namespace Rest, and the Class Client
use Twilio\Rest\Client;
//
// Obtain the account ssod, the account token, and the account phone number from the 
// twilio console. You must have logged in to twilio to obtain these
//Get the twilio ACCOUNT_SID
$sid = "AC59f608749c260f84f95a31a6e0fd1fc7";
//
//Get the account AUTH_TOKEN
$token = "bfb025bd6f7b302c235831e0f3bdf9b7";
//
//Get the account PHONE_NUMBER
$phone = "+12182929241";
//
//Obtain the phone number of the recipient
$to ='+254759380195';
//
//Obtain the body of the message
$body ='Hey buddy!';
//
//Instantiate a new instance of the Client class to enable senfing the messaged
$twilio = new Client($sid, $token);
//
//Create twilio messagess and send them using the parameters obtained from the fule
/* From the $TWILIO->messages->create indicates that there wihin the clas client, accessible
usiing two parameters, namely the client and the token, there is a method messaeges with the
create closure that takes two arguements, the reciever, and an array of the message and the 
receoients phone number,i.e., <string, Array<key:value>> */
$message = $twilio->messages
    ->create(
        $to, // to
        [
            "body" => $body,
            "from" => $phone
        ]
    );
$msg= implode($phone, $_SESSION);
//
session_abort();
?>