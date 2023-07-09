<?php
$content = file_get_contents('php://input');
$json_decode = json_decode($content, true);


global $chat_id;
global $text;
global $first_name;
global $message_id;
global $message_id_reply;
global $file_id;
global $file_type;
global $caption;


if (isset($json_decode['message'])) {
    $chat_id = $json_decode['message']['chat']['id'];
    $text = $json_decode['message']['text'];
    $first_name = $json_decode['message']['from']['first_name'];
    $user_id = $json_decode['message']['from']['id'];
    $message_id = $json_decode['message']['message_id'];
    if (isset($json_decode['message']['reply_to_message'])) {
        $message_id_reply = $json_decode['message']['reply_to_message']['message_id'];
    }

    if (isset($json_decode['message']['photo'][0])) {
        $file_id = $json_decode['message']['photo'][0]['file_id'];
        $file_type = 'photo';
    }
    if (isset($json_decode['message']['voice'])) {
        $file_id = $json_decode['message']['voice']['file_id'];
        $file_type = 'voice';
    }

    if (isset($json_decode['message']['audio'])) {
        $file_id = $json_decode['message']['audio']['file_id'];
        $file_type = 'audio';
    }

    if (isset($json_decode['message']['animation'])) {
        $file_id = $json_decode['message']['animation']['file_id'];
        $file_type = 'animation';
    }


    if (isset($json_decode['message']['video'])) {
        $file_id = $json_decode['message']['video']['file_id'];
        $file_type = 'video';
    }

    if (isset($json_decode['message']['sticker'])) {
        $file_id = $json_decode['message']['sticker']['file_id'];
        $file_type = 'sticker';
    }

    if (isset($json_decode['message']['caption'])) {
        $caption = $json_decode['message']['caption'];
    }
}else{
    $chat_id = $json_decode['callback_query']['message']['chat']['id'];
    $text = $json_decode['callback_query']['message']['text'];
    $first_name = $json_decode['callback_query']['message']['from']['first_name'];
    $user_id = $json_decode['callback_query']['message']['from']['id'];
    $message_id = $json_decode['callback_query']['message']['message_id'];
    $callback_data = $json_decode['callback_query']['data'];
}
