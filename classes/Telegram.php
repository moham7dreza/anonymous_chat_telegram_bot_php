<?php

namespace classes;

class Telegram extends TelegramService
{

    public function __construct()
    {
        $this->default();
    }

    private function requestTelegram($method, $param)
    {
        $url = API_URL . $method;
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_TIMEOUT, 10);
        curl_setopt($handler, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($param));
        curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($handler);

        if (curl_errno($handler)) {
            set_log('API TELEGRAM', curl_error($handler));
        }

        curl_close($handler);
        return $result;
    }


    public function sendMessage($chat_id = null)
    {

        if ($chat_id == null) {
            $chat_id = $this->chat_id;
        }
        $param = array('chat_id' => $chat_id, 'text' => $this->text, 'parse_mode' => 'HTML');

        if ($this->reply) {
            $param['reply_to_message_id'] = $this->message_id;
        }

        if ($this->reply_markup != null) {
            $param['reply_markup'] = $this->reply_markup;
        }


        $result =  $this->requestTelegram('sendMessage', $param);
        $this->default();
        return $result;
    }

    public function sendFile($chat_id = null)
    {
        if ($chat_id == null) {
            $chat_id = $this->chat_id;
        }
        // [ photo, audio, video, animation, voice, videoNote, sticker]
        switch ($this->file_type) {
            case "photo":
                $this->sendPhoto($chat_id);
                break;
            case "audio":
                $this->sendAudio($chat_id);
                break;
            case "video":
                $this->sendVideo($chat_id);
                break;
            case "animation":
                $this->sendAnimation($chat_id);
                break;
            case "voice":
                $this->sendVoice($chat_id);
                break;
            case "videoNote":
                $this->sendVideoNote($chat_id);
                break;
            case "sticker":
                $this->sendSticker($chat_id);
                break;
        }
    }


    public function sendPhoto($chat_id = null)
    {
        if ($this->file_type == 'photo' and $this->file_id != null) {
            if ($chat_id == null) {
                $chat_id = $this->chat_id;
            }
            $param = array(
                'chat_id' => $chat_id, 'photo' => $this->file_id, 'parse_mode' => 'HTML', 'caption' => $this->text
            );

            if ($this->reply) {
                $param['reply_to_message_id'] = $this->message_id;
            }

            if ($this->reply_markup != null) {
                $param['reply_markup'] = $this->reply_markup;
            }


            $result =  $this->requestTelegram('sendPhoto', $param);
            $this->default();
            return $result;
        }


        return false;
    }

    public function sendAudio($chat_id = null)
    {
        if ($this->file_type == 'audio' and $this->file_id != null) {

            if ($chat_id == null) {
                $chat_id = $this->chat_id;
            }
            $param = array(
                'chat_id' => $chat_id, 'audio' => $this->file_id, 'parse_mode' => 'HTML', 'caption' => $this->text
            );

            if ($this->reply) {
                $param['reply_to_message_id'] = $this->message_id;
            }

            if ($this->reply_markup != null) {
                $param['reply_markup'] = $this->reply_markup;
            }


            $result =  $this->requestTelegram('sendAudio', $param);
            $this->default();
            return $result;
        }
        return false;
    }

    public function sendVideo($chat_id = null)
    {
        if ($this->file_type == 'video' and $this->file_id != null) {

            if ($chat_id == null) {
                $chat_id = $this->chat_id;
            }
            $param = array(
                'chat_id' => $chat_id, 'video' => $this->file_id, 'parse_mode' => 'HTML', 'caption' => $this->text
            );

            if ($this->reply) {
                $param['reply_to_message_id'] = $this->message_id;
            }

            if ($this->reply_markup != null) {
                $param['reply_markup'] = $this->reply_markup;
            }


            $result =  $this->requestTelegram('sendVideo', $param);
            $this->default();
            return $result;
        }
        return false;
    }

    public function sendSticker($chat_id = null)
    {
        if ($this->file_type == 'sticker' and $this->file_id != null) {

            if ($chat_id == null) {
                $chat_id = $this->chat_id;
            }
            $param = array('chat_id' => $chat_id, 'sticker' => $this->file_id);

            if ($this->reply) {
                $param['reply_to_message_id'] = $this->message_id;
            }

            if ($this->reply_markup != null) {
                $param['reply_markup'] = $this->reply_markup;
            }


            $result =  $this->requestTelegram('sendSticker', $param);
            $this->default();
            return $result;
        }
        return false;
    }

    public function sendAnimation($chat_id = null)
    {
        if ($this->file_type == 'animation' and $this->file_id != null) {

            if ($chat_id == null) {
                $chat_id = $this->chat_id;
            }
            $param = array(
                'chat_id' => $chat_id, 'animation' => $this->file_id, 'parse_mode' => 'HTML', 'caption' => $this->text
            );

            if ($this->reply) {
                $param['reply_to_message_id'] = $this->message_id;
            }

            if ($this->reply_markup != null) {
                $param['reply_markup'] = $this->reply_markup;
            }


            $result =  $this->requestTelegram('sendAnimation', $param);
            $this->default();
            return $result;
        }
        return false;
    }

    public function sendVoice($chat_id = null)
    {
        if ($this->file_type == 'voice' and $this->file_id != null) {

            if ($chat_id == null) {
                $chat_id = $this->chat_id;
            }
            $param = array(
                'chat_id' => $chat_id, 'voice' => $this->file_id, 'parse_mode' => 'HTML', 'caption' => $this->text
            );

            if ($this->reply) {
                $param['reply_to_message_id'] = $this->message_id;
            }

            if ($this->reply_markup != null) {
                $param['reply_markup'] = $this->reply_markup;
            }


            $result =  $this->requestTelegram('sendVoice', $param);
            $this->default();
            return $result;
        }
        return false;
    }

    public function sendVideoNote($chat_id = null)
    {
        if ($this->file_type == 'videoNote' and $this->file_id != null) {

            if ($chat_id == null) {
                $chat_id = $this->chat_id;
            }
            $param = array(
                'chat_id' => $chat_id, 'video_note' => $this->file_id, 'parse_mode' => 'HTML', 'caption' => $this->text
            );

            if ($this->reply) {
                $param['reply_to_message_id'] = $this->message_id;
            }

            if ($this->reply_markup != null) {
                $param['reply_markup'] = $this->reply_markup;
            }


            $result =  $this->requestTelegram('sendVideoNote', $param);
            $this->default();
            return $result;
        }

        return false;
    }

    public function answerCallbackQuery($callback_query_id, $show_alert = false)
    {

        $param = array('callback_query_id' => $callback_query_id, 'text' => $this->text, 'show_alert' => $show_alert);

        $result =  $this->requestTelegram('answerCallbackQuery', $param);
        $this->default();
        return $result;
    }
    public function editMessageText()
    {
        $param = array('chat_id' => $this->chat_id, 'text' => $this->text, 'parse_mode' => 'HTML', 'message_id' => $this->message_id);
        if ($this->reply_markup != null) {
            $param['reply_markup'] = $this->reply_markup;
        }


        $result =  $this->requestTelegram('editMessageText', $param);
        $this->default();
        return $result;
    }

    public function editMessageReplyMarkup(){
        $param = array('chat_id' => $this->chat_id, 'message_id' => $this->message_id);
        if ($this->reply_markup != null) {
            $param['reply_markup'] = $this->reply_markup;
        }


        $result =  $this->requestTelegram('editMessageReplyMarkup', $param);
        $this->default();
        return $result;

    }
}
