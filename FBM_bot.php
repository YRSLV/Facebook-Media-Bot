<?php
require 'vendor/autoload.php';
require 'weather_forecast.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class FBM_bot
{
 private $hubVerifyToken = null;
 private $accessToken = null;
 private $token = false;
 protected $client = null;
 function __construct()
 {
 }

 public function setHubVerifyToken($value)
 {
  $this->hubVerifyToken = $value;
 }

 public function setAccessToken($value)
 {
  $this->accessToken = $value;
 }

 public function verifyToken($challenge, $hub_verify_token)
 {
  try {
   if ($hub_verify_token === $this->hubVerifyToken) {
    return $challenge;
   }
   else {
    throw new Exception("Token not verified");
   }
  }

  catch(Exception $ex) {
   return $ex->getMessage();
  }
 }

 public function readMessage($input)
 {
  try {
   $payloads = null;
   $senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
   $messageText = $input['entry'][0]['messaging'][0]['message']['text'];
   $postback = $input['entry'][0]['messaging'][0]['postback'];
   $loctitle = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['title'];
   if (!empty($postback)) {
    $payloads = $input['entry'][0]['messaging'][0]['postback']['payload'];
    return [
      'senderid' => $senderId,
      'message' => $payloads
    ];
   }

   if (!empty($loctitle)) {
    $payloads = $input['entry'][0]['messaging'][0]['postback']['payload'];
    return [
      'senderid' => $senderId,
      'message' => $messageText,
      'location' => $loctitle
    ];
   }

   return ['senderid' => $senderId, 'message' => $messageText];
  }

  catch(Exception $ex) {
   return $ex->getMessage();
  }
 }

 public function sendMessage($input)
 {
  try {
   $client = new Client();
   $url = "https://graph.facebook.com/v2.6/me/messages";
   $messageText = strtolower($input['message']);
   $senderId = $input['senderid'];
   $msgarray = explode(' ', $messageText);
   $response = null;
   $header = array(
    'content-type' => 'application/json'
   );

   if (in_array('hi', $msgarray)) {
    $answer = "Oh hello there!";
    $response = [
      'recipient' => ['id' => $senderId],
      'message' => ['text' => $answer],
      'access_token' => $this->accessToken
    ];
   }

   elseif ($messageText == '#about') {
    $answer = [
      "attachment" => [
        "type" => "template",
        "payload" => [
          "template_type" => "generic",
          "elements" => [[
            "title" => "About this project",
            "item_url" => "https://github.com/YRSLV/Facebook-Media-Bot",
            "image_url" => "http://www.freepngimg.com/download/github/1-2-github-free-png-image.png",
            "subtitle" => "Learn more",
            "buttons" => [[
              "type" => "web_url",
              "url" => "https://github.com/YRSLV/Facebook-Media-Bot",
              "title" => "View Website"]
            ]
          ]]
        ]]];
    $response = [
      'recipient' => ['id' => $senderId],
      'message' => $answer,
      'access_token' => $this->accessToken
    ];
  }

  elseif (in_array('#weather', $msgarray)) {
      $forecast = new weather_wrapper(trim($msgarray[1] . " " . $msgarray[2] . " " . $msgarray[3]));
      $result = $forecast->get_forecast();
      $answer = implode("\r\n",$result);

      $response = [
        'recipient' => ['id' => $senderId],
        'message' => ['text' => $answer],
        'access_token' => $this->accessToken
      ];

    $response = $client->post($url, ['query' => $response, 'headers' => $header]);

  return true;
  }

  catch(RequestException $e) {
   $response = json_decode($e->getResponse()->getBody(true)->getContents());
   file_put_contents("test.json", json_encode($response));
   return $response;
  }
 }
}

?>
