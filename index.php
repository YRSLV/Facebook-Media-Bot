<?php
require 'FBM_bot.php';

$token = $_REQUEST['hub_verify_token'];
$hubVerifyToken = 'specified_verify_token';
$challenge = $_REQUEST['hub_challenge'];
$accessToken = 'specified_page_access_token';
$bot = new FBM_bot();
$bot->setHubVerifyToken($hubVerifyToken);
$bot->setaccessToken($accessToken);
echo $bot->verifyToken($challenge, $token);

$input = json_decode(file_get_contents('php://input'), true);
$message = $bot->readMessage($input);
$textmessage = $bot->sendMessage($message);
?>
