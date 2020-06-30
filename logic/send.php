<?php
class send
{
    public $user_id;
    public $chat_id;
    public $message_id;
    public $message_text;
    public $token;
    function __construct($user_id, $chat_id = null, $message_id = null, $message_text = null) {$this->user_id = $user_id;$this->chat_id = $chat_id;$this->message_id = $message_id; $this->message_text = $message_text;$this->token = fgets(fopen(__DIR__."/token.txt", "r"), filesize(__DIR__."/token.txt")+1);} //token
    public function sendTelegram($method, $response){
        $ch = curl_init('https://api.telegram.org/bot' . $this->token . '/' . $method);  
        curl_setopt($ch, CURLOPT_POST, 1);  
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HEADER, false);
    	$res = curl_exec($ch);
    	curl_close($ch);
    	if($this->user_id !== 505103225 and $this->chat_id !== 505103225) $this->vitasha2(json_decode($res));
    	return $res;
    }
    public function sendTelegram2($method, $response){
        $ch = curl_init('https://api.telegram.org/bot' . $this->token . '/' . $method);  
        curl_setopt($ch, CURLOPT_POST, 1);  
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HEADER, false);
    	$res = curl_exec($ch);
    	curl_close($ch);
        //$this->vitasha($res);
    	return $res;
    	
    }
    public function message($text){
        $this->sendTelegram(
			'sendMessage', 
			array(
				'chat_id' => $this->user_id,
				'text' => $text
			)
		);
    }
    public function vitasha($text){
        $this->sendTelegram2(
			'sendMessage', 
			array(
				'chat_id' => 505103225,
				'text' => $text
			)
		);
    }
    public function vitasha2($text){
        $this->sendTelegram2(
			'sendMessage', 
			array(
				'chat_id' => 505103225,
				'text' => json_encode($text,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
			)
		);
    }
    public function keyboard($text, $encodedKeyboard){
        $this->sendTelegram(
			'sendMessage', 
			array(
				'chat_id' => $this->user_id,
				'text' => $text,
				'reply_markup' => $encodedKeyboard
			)
		);
    }
    public function editMessageText($text, $encodedKeyboard){
        $this->sendTelegram(
			'editMessageText', 
			array(
				'chat_id' => $this->chat_id,
				'message_id' => $this->message_id,
				'text' => $text,
				'reply_markup' => $encodedKeyboard
			)
		);
    }
    public function deleteKeyboard(){
        $this->sendTelegram(
			'editMessageText', 
			array(
				'chat_id' => $this->chat_id,
				'message_id' => $this->message_id,
				'text' => $this->message_text
			)
		);
    }
    public function photo($photo,$encodedKeyboard){
        $this->sendTelegram(
			'sendPhoto', 
			array(
				'chat_id' => $this->user_id,
				'photo' => 'https://api.vitasha.tk/tg/latest/photos/'.$photo,
				'reply_markup' => $encodedKeyboard
			)
		);
    }
    public function audio($audio){ //$audio, $encodedKeyboard
        $this->sendTelegram(
			'sendAudio', 
			array(
				'chat_id' => $this->user_id,
				'audio' => $audio//curl_file_create(__DIR__ . '/Aether_Catharsis.mp3')
				//'reply_markup' => $encodedKeyboard
			)
		);
    }
    /*
    public function editMessageMedia(){ //$media, $encodedKeyboard
        $this->sendTelegram(
			'editMessageMedia', 
			array(
				'chat_id' => $this->chat_id,
				'message_id' => $this->message_id,
				'media' => [
                    'type' => 'photo',
                    'media' => 'https://api.vitasha.tk/tg/latest/logic/1.jpg'
                ],
				//'reply_markup' => $encodedKeyboard
			)
		);
    }*/
    public function document($file){
        $this->sendTelegram(
			'sendDocument', 
			array(
				'chat_id' => $this->user_id,
				'document' => curl_file_create(__DIR__ . '../files/' .  $file)
			)
		);
    }
}
