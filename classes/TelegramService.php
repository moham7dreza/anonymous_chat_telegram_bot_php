<?php

namespace classes;

class TelegramService
{
    protected $chat_id;
    protected $text;
    protected $message_id;
    protected $file_id;
    protected $file_type;
    protected $reply;
    protected $reply_markup;
    protected $reply_markup_default;

    public function setChatID($chat_id)
    {
        $this->chat_id = $chat_id;
        return $this;
    }
    public function getChatID()
    {
        return $this->chat_id;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
    public function getText()
    {
        return $this->text;
    }
    public function setMessageID($message_id)
    {
        $this->message_id = $message_id;
        return $this;
    }
    public function getMessageID()
    {
        return $this->message_id;
    }
    public function setFileId($file_id, $file_type)
    {
        $this->file_id = $file_id;
        $this->file_type = $file_type;

        return $this;
    }
    public function getFileId()
    {
        return $this->file_id;
    }

    public function getFileType()
    {
        return $this->file_type;
    }

    public function setReply(bool $reply)
    {
        $this->reply = $reply;
        return $this;
    }
    public function getReply()
    {
        return $this->reply;
    }

    public function setReplyMarkup($reply_markup)
    {
        $this->reply_markup = $reply_markup;
        return $this;
    }
    public function getReplyMarkup()
    {
        return $this->reply_markup;
    }

    public function setReplyMarkupDefault($reply_markup_default)
    {
        $this->reply_markup_default = $reply_markup_default;
    }

    public function default()
    {
        global $chat_id;
        $this->chat_id = $chat_id;

        global $message_id;
        $this->message_id = $message_id;
        global $file_id;
        global $file_type;
        if (!empty($file_id) and !empty($file_type)) {
            $this->file_id = $file_id;
            $this->file_type = $file_type;
        } else {
            $this->file_id = null;
            $this->file_type = null;
        }

        global $caption;
        if (!empty($caption)) {
            $this->text = $caption;
        }

        $this->reply = true;
        if ($this->reply_markup_default != null)
            $this->reply_markup = $this->reply_markup_default;
        else
            $this->reply_markup = null;
        $this->text = '';
    }
}
