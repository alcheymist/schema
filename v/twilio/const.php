<?php
const TWILIO_ACCOUNT_SID = 'ACa0da98876ae7c59dea6fd839c0543643';
const TWILIO_AUTH_TOKEN = '0a585315fd98ee6b71ef9b5a414ff942';
const TWILIO_PHONE_NUMBER = '0a585315fd98ee6b71ef9b5a414ff942';

//
require_once '../../../dotenv/vendor/autoload.php';
//
//
use Dotenv\Dotenv;
//
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];

echo $token, TWILIO_AUTH_TOKEN;
