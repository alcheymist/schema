<?php
//
namespace mutall;
//
//Respolve refence to twilio autoloader.
require_once '../twilio/vendor/autoload.php';
//
//Our twilio class extends  client.
class twilio extends \Twilio\Rest\Client {
    //
    // Obtain the account ssid, the account token, and the account phone number 
    // from the twilio console. You must have logged in to twilio to obtain these
    //Get the twilio ACCOUNT_SID
    const sid = "AC59f608749c260f84f95a31a6e0fd1fc7";
    //
    //Get the account AUTH_TOKEN
    const token = "bfb025bd6f7b302c235831e0f3bdf9b7";
    //
    // This is the twilio account phone number.
    const phone = "+12182929241";
    //
    function __construct(){
        //
        //Instantiate the parent class.
        parent::__construct(self::sid, self::token);
    }
    //
    //Use Twilio to send an message.
    public function send_message(string $to, string $subject, string $body):string /*'ok'|error*/
    {
        //
        //Combine the body and the subject of the message as a single message
        $msg = "Subject: $subject\nBody: $body";
        //
        //Prepare to trap exceptions where the phone number is unregistered or
        //invalid.
        try{
            //
            //Send the message.
             $this->messages->create(
                //
                //The phone address to send the message to
                $to,
                //
                //The details of the message.     
                [   //
                    //The body of the message
                    "body" => $msg,
                    //
                    //The twilio phone number where the message is coming from
                    "from" => self::phone
                ]
            );
            return 'ok';
        }
        catch(\Exception $ex ){
            return $ex->getMessage();
        } 
    }
}