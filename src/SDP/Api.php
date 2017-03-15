<?php

namespace Gap\SDP;

class Api {

  private $baseURL = 'https://api.gap.im/';

  protected $token;

  public function __construct($token) {
    $this->token = $token;
    if (is_null($this->token))
      throw new \Exception('Required "token" key not supplied');
  }
  
  /**
   * Send Action.
   *
   * @param int            $chat_id
   * @param string         $action
   *
   * @return Array
   */
  public function sendAction($chat_id, $action) {
    $actions = array(
      'typing',
    );
    if (isset($action) && in_array($action, $actions))
    {
      $params = compact('chat_id');
      return $this->sendRequest($action, $params, 'sendAction');
    }

    throw new \Exception('Invalid Action! Accepted value: '.implode(', ', $actions));
  }

  /**
   * Send text messages.
   *
   * @param int            $chat_id
   * @param string         $data
   *
   * @return Array
   */
  public function sendText($chat_id, $data, $reply_keyboard) {
    $params = compact('chat_id', 'data', 'reply_keyboard');

    return $this->sendRequest('text', $params);
  }
  
  /**
   * Send Location.
   *
   * @param int            $chat_id
   * @param float          $latitude
   * @param float          $longitude
   * @param string         $description
   *
   * @return Array
   */
  public function sendLocation($chat_id, $lat, $long, $desc, $reply_keyboard) {
    $data = json_encode(compact('lat', 'long', 'desc'));
    $params = compact('chat_id', 'data', 'reply_keyboard');

    return $this->sendRequest('location', $params);
  }

  /**
   * Send Contact.
   *
   * @param int            $chat_id
   * @param string         $phone
   * @param string         $name
   *
   * @return Array
   */
  public function sendContact($chat_id, $phone, $name, $reply_keyboard) {
    $data = json_encode(compact('phone', 'name'));
    $params = compact('chat_id', 'data', 'reply_keyboard');

    return $this->sendRequest('contact', $params);
  }

  /**
   * Send Image.
   *
   * @param int            $chat_id
   * @param string         $image
   * @param string         $description
   *
   * @return Array
   */
  public function sendImage($chat_id, $image, $desc, $reply_keyboard) {
    if (!is_dir($image)) {
      throw new \Exception("Image path is invalid");
    }
    $data = $this->uploadFile('image', $image);
    $params = compact('chat_id', 'data', 'reply_keyboard');

    return $this->sendRequest('image', $params);
  }

  /**
   * Send Audio.
   *
   * @param int             $chat_id
   * @param string          $audio
   *
   * @return Array
   */
  public function sendAudio($chat_id, $audio, $desc, $reply_keyboard) {
    if (!is_dir($audio)) {
      throw new \Exception("Audio path is invalid");
    }
    $data = $this->uploadFile('audio', $audio);
    $params = compact('chat_id', 'data', 'reply_keyboard');

    return $this->sendRequest('audio', $params);
  }

  /**
   * Send Sticker.
   *
   * @param int            $chat_id
   * @param string         $sticker
   *
   * @return Array
   */
  public function sendSticker($chat_id, $sticker) {
    if (!is_dir($sticker)) {
      throw new \Exception("Sticker path is invalid");
    }
    $data = $this->uploadFile('sticker', $sticker);
    $params = compact('chat_id', 'data');

    return $this->sendRequest('sticker', $params);
  }

  /**
   * Send Video.
   *
   * @param int             $chat_id
   * @param string          $video
   * @param string          $caption
   *
   * @return Array
   */
  public function sendVideo($chat_id, $video, $caption, $reply_keyboard) {
    if (!is_dir($video)) {
      throw new \Exception("Video path is invalid");
    }
    $data = $this->uploadFile('video', $video);
    $params = compact('chat_id', 'data', 'reply_keyboard');

    return $this->sendRequest('video', $params);
  }

  /**
   * Send Voice.
   *
   * @param int             $chat_id
   * @param string          $voice
   *
   * @return Array
   */
  public function sendVoice($chat_id, $voice, $desc, $reply_keyboard) {
    if (!is_dir($voice)) {
      throw new \Exception("Voice path is invalid");
    }
    $data = $this->uploadFile('voice', $voice);
    $params = compact('chat_id', 'data', 'reply_keyboard');

    return $this->sendRequest('voice', $params);
  }
  
  /**
   * Reply keyboard.
   *
   * @param array        $keyboard
   * @param bool         $once
   * @param bool         $selective
   *
   * @return String
   */
  public function replyKeyboard($keyboard, $once = true, $selective = false) {
    if (!is_array($keyboard)) {
      throw new \Exception("keyboard must be array");
    }
    $replyKeyboard = compact('keyboard', 'once', 'selective');
    return json_encode($replyKeyboard);
  }

  private function sendRequest($msgType, $params, $method = 'sendMessage') {
    $params['type'] = $msgType;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $this->baseURL . $method);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('token: ' . $this->token));

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_result = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if ($httpcode != 200) {
      if ($curl_result) {
        $curl_result = json_decode($curl_result, true);
        throw new \Exception($curl_result['error']);
      }
      throw new \Exception('an error was encountered');
    }
    
    return true;
  }

  private function uploadFile($method, $data) {
    throw new \Exception('Upload not supported yet');
  }

}