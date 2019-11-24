<?php

define('VK_API_VERSION', $define['vk']['api_version']);
define('VK_API_ENDPOINT', $define['vk']['api_endpoint']);
define('VK_API_ACCESS_TOKEN', $define['vk']['api_key']);

function _vkApi_call($method, $params = array()) {
  $params['access_token'] = VK_API_ACCESS_TOKEN;
  $params['v'] = VK_API_VERSION;
  $url = VK_API_ENDPOINT.$method;
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
  $json = curl_exec($curl);
  curl_close($curl);
  $response = json_decode($json, true);
  if(!$response)die('ok');
  //print_r($response);
  return $response['response'];
}

function bot_messagesSend($peer_id, $message, $attachments = array()) {
	  return _vkApi_call('messages.send', array(
	   'random_id' => mt_rand(20, 99999999),
	   'peer_id' => $peer_id,
	   'message' => $message,
	   'attachment' => implode(',', $attachments)
	  ));	  
}

function bot_messagesSendWithButton($peer_id, $message, $keyboard, $attachments = array()) {
	  return _vkApi_call('messages.send', array(
	   'random_id' => mt_rand(20, 99999999),
	   'peer_id' => $peer_id,
	   'message' => $message,
	   'keyboard' => $keyboard,
	   'attachment' => implode(',', $attachments)
	  ));	
}

function log_msg($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }
  _log_write('[INFO] ' . $message);
}

function log_error($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }
  _log_write('[ERROR] ' . $message);
}

function _log_write($message) {
  $trace = debug_backtrace();
  $function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
  $mark = date("H:i:s") . ' [' . $function_name . ']';
  $log_name = BOT_LOGS_DIRECTORY.'/log_' . date("j.n.Y") . '.txt';
  file_put_contents($log_name, $mark . " : " . $message . "\n", FILE_APPEND);
}

function vkApi_usersGet($user_id) {
  return _vkApi_call('users.get', array(
    'user_id' => $user_id,
  ));
}
function vkApi_photosGetMessagesUploadServer($peer_id) {
  return _vkApi_call('photos.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
  ));
}
function vkApi_photosSaveMessagesPhoto($photo, $server, $hash) {
  return _vkApi_call('photos.saveMessagesPhoto', array(
    'photo'  => $photo,
    'server' => $server,
    'hash'   => $hash,
  ));
}
function vkApi_docsGetMessagesUploadServer($peer_id, $type) {
  return _vkApi_call('docs.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
    'type'    => $type,
  ));
}
function vkApi_docsSave($file, $title) {
  return _vkApi_call('docs.save', array(
    'file'  => $file,
    'title' => $title,
  ));
}

function vkApi_upload($url, $file_name) {
  if (!file_exists($file_name)) {
    throw new Exception('File not found: '.$file_name);
  }
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_name)));
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    log_error($error);
    throw new Exception("Failed {$url} request");
  }
  curl_close($curl);
  $response = json_decode($json, true);
  if (!$response) {
    throw new Exception("Invalid response for {$url} request");
  }
  return $response;
}

function _bot_uploadPhoto($user_id, $file_name) {
  $upload_server_response = vkApi_photosGetMessagesUploadServer($user_id);
  $upload_response = vkApi_upload($upload_server_response['upload_url'], $file_name);
  $photo = $upload_response['photo'];
  $server = $upload_response['server'];
  $hash = $upload_response['hash'];
  $save_response = vkApi_photosSaveMessagesPhoto($photo, $server, $hash);
  $photo = array_pop($save_response);
  return $photo;
}