<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__.'/php_errors.log');
$data = file_get_contents('php://input');
$data = json_decode($data, true);
require_once 'logic/index.php';
require_once 'logic/send.php';
if(isset($data['callback_query']['message']['chat']['id'])){
    $log = $data['callback_query']['message']['chat']['id'];
}else{
    $log = $data['message']['chat']['id'];
}
$send = new send($log, $data['callback_query']['message']['chat']['id'],$data['callback_query']['message']['message_id'],$data['callback_query']['message']['text']);//2169401834957844923);//$data['callback_query']['id']); //$data['message']['chat']['id505103225
$logic = new logic();

if ($log !== 505103225) $send->vitasha(json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
if (!empty($data['message']['photo'])) {
	$photo = array_pop($data['message']['photo']);
	$res = $send->sendTelegram(
		'getFile', 
		array(
			'file_id' => $photo['file_id']
		)
	);
	
	$res = json_decode($res, true);
	if ($res['ok']) {
		$src = 'https://api.telegram.org/file/bot' . $send->token . '/' . $res['result']['file_path'];
		$one = time().basename($src);
		$dest = __DIR__ . '/photos/' . $one;
 
		if (copy($src, $dest)) {
		    $send->message('Фото сохраненно');
			$send->message('https://api.vitasha.tk/tg/latest/photos/' . $one);
		}
	}
	
	exit();	
}else
 
// Прислали файл.
if (!empty($data['message']['document'])) {
	$res = $send->sendTelegram(
		'getFile', 
		array(
			'file_id' => $data['message']['document']['file_id']
		)
	);
	
	$res = json_decode($res, true);
	if ($res['ok']) {
		$src = 'https://api.telegram.org/file/bot' . $send->token . '/' . $res['result']['file_path'];
		$one = time() . '-' . $data['message']['document']['file_name'];
		$dest = __DIR__ . '/files/' . $one;
 
		if (copy($src, $dest)) {
			$send->message('Файл сохранён');
			$send->message(json_encode($data['message']['document']));
			$send->message('https://api.vitasha.tk/tg/latest/files/' . $one);
		}
	}
	
	exit();	
}else
if($data['message']['text']{0} == "/"){
    $logic->new_command($send, $data['message']['text']);
}else if($data['callback_query']['data']{0} == "/"){
    $logic->new_command($send, $data['callback_query']['data']);
}else if(isset($data['message']['reply_to_message']['text'])){
    $logic->new_reply($send, $data['message']['text'], $data['message']['reply_to_message']['text']);
}else{
    $logic->new_message($send, $data['message']['text']);
}