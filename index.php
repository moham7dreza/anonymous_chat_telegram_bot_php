<?php

require_once('config.php');
require_once('telegram_json.php');
require_once('classes/DBConnection.php');
require_once('classes/DB.php');
require_once('classes/TelegramService.php');
require_once('classes/Telegram.php');
require_once('helper.php');
require_once('default_keyboard.php');

use classes\DB;
use classes\Telegram;

global $db;
global $telegram;
global $user;
$db = new DB();
$telegram = new Telegram();
if (in_array($chat_id, ADMINS)) {
    $telegram->setReplyMarkupDefault($mainKeyboard_admin);
} else {
    $telegram->setReplyMarkupDefault($mainKeyboard);
}
$telegram->default();


$user = $db->table('users')->where('chat_id', $chat_id)->first();



if (empty($user)) {
    $code =  generateCode();
    $db->table('users')->insert(['username', 'chat_id', 'code'], [$first_name, $chat_id, $code]);
    $user = $db->table('users')->where('chat_id', $chat_id)->first();
}

if (!isset($callback_data)) {
    if ($text == '❌ انصراف' or $text == '/reset') {
        setWork();
        $text_send = 'عملیات با موفقیت کنسل شد';
        $telegram->setText($text_send)->sendMessage();

        return;
    }

    if ($user['work'] == 'send') {

        $target_user = $db->table('users')->where('id', $user['send_to_user_id'])->where('status', 1)->first();


        if (empty($file_type)) {

            if ($target_user['get_message'] == 0) {
                setWork();
                $text_from  = "این کاربر دسترسی ارسال پیام متنی را بسته است";
                $telegram->setText($text_from)->sendMessage();
                return;
            }

            $db->table('message')->insert(['message', 'send_from_chat_id', 'send_from_user_id', 'send_from_message_id', 'send_to_user_id', 'reply_to_message_id'], [$text, $chat_id, $user['id'], $message_id, $user['send_to_user_id'], $user['send_to_message_id']]);
        } else {

            $field = "";
            $field_persian = "";
            // [ photo, audio, video, animation, voice, videoNote, sticker]

            switch ($file_type) {
                case "photo":
                    $field = "get_photo";
                    $field_persian = "ارسال عکس";
                    break;
                case "audio":
                    $field = "get_audio";
                    $field_persian = "ارسال فایل صوتی";


                    break;
                case "video":
                    $field = "get_video";
                    $field_persian = "ارسال ویدئو";


                    break;
                case "animation":
                    $field = "get_animation";
                    $field_persian = "ارسال گیف";


                    break;
                case "voice":
                    $field = "get_voice";
                    $field_persian = "ارسال ویس";


                    break;
                case "videoNote":
                    $field = "get_videoNote";
                    $field_persian = "ارسال ویدئو مسیج";


                    break;
                case "sticker":
                    $field = "get_sticker";
                    $field_persian = "ارسال استیکر";


                    break;
            }

            if ($target_user[$field] == 0) {
                setWork();
                $text_from  = "این کاربر دسترسی $field_persian را بسته است";
                $telegram->setText($text_from)->sendMessage();
                return;
            }
            $db->table('message')->insert(['message', 'file_id', 'file_type', 'send_from_chat_id', 'send_from_user_id', 'send_from_message_id', 'send_to_user_id', 'reply_to_message_id'], [isset($caption) ? $caption : "", $file_id, $file_type, $chat_id, $user['id'], $message_id, $user['send_to_user_id'], $user['send_to_message_id']]);
        }
        $text_from  = "پیام شما با موفقیت ارسال شد";
        $telegram->setText($text_from)->sendMessage();

        $text_to  = '🔔شما یک پیام جدید دارید🔔' . "\n";
        $text_to .= 'برای دریافت پیام روی /getmsg کلیک کنید';
        $telegram->setChatID($target_user['chat_id'])->setMessageID(null)->setText($text_to)->sendMessage();
        setWork();
        return;
    }

    if ($user['work'] == 'send-2') {



        $main_text = "این پیام توسط " . $user['username'] . " ارسال شده است";
        foreach (ADMINS as $admin) {
            if (empty($file_type)) {
                $text_send  = $main_text . "\n";
                $text_send  .= $text;

                $telegram->setChatID($admin)->setMessageID(null)->setText($text_send)->sendMessage();
            } else {
                $text_send  = $main_text . "\n";
                $text_send  .= $caption;

                $telegram->setChatID($admin)->setMessageID(null)->setText($text_to_admin)->setFileId($file_id, $file_type)->sendFile();
            }
        }

        setWork();

        $telegram->setText("پیام شما با موفقیت ارسال شد");
    }


    if ($text == '/start') {
        $text_send = 'سلام ' . $user['username'] . "\n" . "چه کاری از دستم بر میاد؟ 😁" . "\n" . " از دکمه ها هم میتونی استفاده کنی 👀 " . "\n";
        $text_send .= "👈برای گرفتن لینک اختصاصی لازمه /link رو لمس کنی تا لینک اختصاصی دریافت پیام خودتو دریافت کنی";
        $telegram->setText($text_send)->sendMessage();
        return;
    } else if (startsWith($text, '/start')) {
        $code = explode(' ', $text)[1];
        $target_user = $db->table('users')->where('code', $code)->where('status', 1)->first();


        if (!empty($target_user)) {

            $check_block = checkBlock($user['id'], $target_user['id']);

            if (!$check_block) {
                if ($target_user['id'] == $user['id']) {
                    $text_from  = "شما نمی توانید برای خود پیام ارسال کنید!";
                    $telegram->setText($text_from)->sendMessage();
                    return;
                }

                setWork('send', $target_user['chat_id'], $target_user['id']);
                $text_send = 'شما در حال ارسال پیام به ' . $target_user['username'] . " هستی" . "\n";
                $text_send .= "‼️میتونی هر حرفی رو که داری به صورت ناشناس براش بفرستی:";
                $telegram->setText($text_send)->setReplyMarkup($exitKeyboard)->sendMessage();
                return;
            }
            $text_send = 'شما توسط این کاربر بلاک شده اید';
            $telegram->setText($text_send)->sendMessage();
            return;
        }
        $text_send = 'کاربری با این مشخصات یافت نشد ';
        $telegram->setText($text_send);
        $telegram->sendMessage();


        return;
    } else if ($text == '/link' or $text == 'لینک ناشناس من 📬') {
        $text_send = 'سلام ' . $user['username'] . " هستم " . "\n";
        $text_send .= 'لینک زیر رو لمس کن تا بتونی به صورت ناشناس با من در ارتباط باشی' . "\n";
        $text_send .= 'خودتم می‌تونی امتحان کنی تا بقیه برات پیام بزارن 🫡' . "\n";
        $text_send .= '                                                 ' . "\n";
        $text_send .= '👇👇' . "\n";
        $text_send .= 'https://telegram.me/ToplearnApiTestBot?start=' . $user['code'] . "\n";
        $text_send .= ' این لینک رو توی شبکه های مجازی میتونی به اشتراک بگذاری ☝️' . "\n";
        $telegram->setText($text_send);
        $telegram->sendMessage();
        return;
    } else if ($text == 'راهنما ⁉️') {
        $text_send = 'راهنمای استفاده از بات' . "\n";
        $text_send .= '👈برای گرفتن لینک اختصاصی لازمه /link رو لمس کنی تا لینک اختصاصی دریافت پیام خودتو دریافت کنی ' .  "\n";
        $text_send .= ' 👈برای ایجاد محدودیت پیام ها میتونی از دستور /setting استفاده کنی  ' .  "\n";
        $text_send .= '  👈برای جواب دادن به پیام های ناشناس فقط کافیست روی پیام ها ریپلای کنید و پیام خود را بنویسید  ' .  "\n";
        $telegram->setText($text_send)->sendMessage();
        return;
    } else if ($text == 'تنظیمات ⚙️') {
        $text_send = '⚙️بخش تنظیمات پیام ناشناس⚙️' . "\n";
        $text_send .= 'این بخش به شما امکان این را می دهد تا بتوانید تنظیم کنید چه پیام هایی برای شما ارسال شود';
        $reply_markup_setting = getInlineKeyboardSetting($user);
        $telegram->setText($text_send)->setReplyMarkup($reply_markup_setting)->sendMessage();
        return;
    } else if ($text == '/getmsg') {
        $messages = $db->table('message')->where('is_seen', 0)->where('send_to_user_id', $user['id'])->get();

        foreach ($messages as $message) {

            $keyboard_get_msg = [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '✏️پاسخ',
                            'callback_data' => 'reply|' . $message['id']
                        ],
                        [
                            'text' => '🔒بلاک',
                            'callback_data' => 'block|' . $message['id']
                        ]

                    ],
                    [
                        [
                            'text' => '⚠️گزارش',
                            'callback_data' => 'alert|' . $message['id']
                        ]

                    ]
                ]
            ];


            if (empty($message['file_type'])) {
                $text_send =  "\n \n " . '‌                                    ‌ ‌‌‌    ‌';
                $text_send .= $message['message'];

                $result =  $telegram->setText($text_send)->setReplyMarkup($keyboard_get_msg)->setMessageID($message['reply_to_message_id'])->sendMessage();
            } else {
                $result =  $telegram->setText($message['message'])->setReplyMarkup($keyboard_get_msg)->setMessageID($message['reply_to_message_id'])->setFileId($message['file_id'], $message['file_type'])->sendFile();
            }
            $result = json_decode($result, true);
            $message_id_send = $result['result']['message_id'];
            $db->table('message')->where('id', $message['id'])->update(['is_seen', 'delivered_message_id'], [1, $message_id_send]);
            $telegram->setChatID($message['send_from_chat_id'])->setMessageID($message['send_from_message_id'])->setText("این پیام مشاهده شد")->sendMessage();
        }

        return;
    } else if ($text == '🧰️ پشتیبانی 🧰️') {
        setWork('send-2');

        $text_send = "شما در حال ارسال پیام به پشتیبانی هستی" . "\n";
        $text_send .= "شما می توانید انتقادات و پیشنهادات خود را برای ما ارسال کنید";
        $telegram->setText($text_send)->setReplyMarkup($exitKeyboard)->sendMessage();

        return;
    }


    if(in_array($chat_id, ADMINS)){
        $mainKeyboard_setting_admin = [
            'keyboard' => [
                [
                    ['text' => '👤تعداد کاربران'],
                    ['text' => '🟦 پیام های ارسال شده🟦'],
                ],
                [
                    ['text' => 'بازگشت']
                ]
            ],
            'resize_keyboard' => true
        ];
        if($text == '🔧پنل مدیریت🔧'){
            $telegram->setReplyMarkup($mainKeyboard_setting_admin)->setText('به پنل مدیریت خوش آمدید')->sendMessage();
        }else if($text == "بازگشت"){
            $telegram->setText('پنل مدیریت بسته شد')->sendMessage();
        }else if($text == "👤تعداد کاربران"){
            $count  = $db->table('users')->first('COUNT(*)');
            $text_send = "تعداد کاربران شما " . $count['COUNT(*)']  . "  می باشد  ";
            $telegram->setReplyMarkup($mainKeyboard_setting_admin)->setText( $text_send)->sendMessage();
        }else if($text == "🟦 پیام های ارسال شده🟦"){
            $count  = $db->table('message')->first('COUNT(*)');
            $text_send = "تعداد پیام های ارسال شده در ربات  " . $count['COUNT(*)']  . "  می باشد  ";
            $telegram->setReplyMarkup($mainKeyboard_setting_admin)->setText( $text_send)->sendMessage();
        }

    }
} else {
    if (startsWith($callback_data, 'setting')) {
        $column = explode('|', $callback_data)[1];
        if ($column != 'close') {
            $db->table('users')->where('id', $user['id'])->update($column, $user[$column] == 1 ? 0 : 1);
            $user = $db->table('users')->where('chat_id', $chat_id)->first();
            $reply_markup_setting = getInlineKeyboardSetting($user);
            $telegram->setText($text)->setReplyMarkup($reply_markup_setting)->editMessageText();
        } else {
            $text_send = 'تنظیمات بسته شد';
            $telegram->setText($text_send)->setReplyMarkup(null)->editMessageText();
        }

        return;
    }

    if (startsWith($callback_data, 'reply')) {
        $message_id_reply = explode('|', $callback_data)[1];

        $message = $db->table('message')->where('id', $message_id_reply)->first();
        $target_user = $db->table('users')->where('id', $message['send_from_user_id'])->where('status', 1)->first();

        if (!empty($target_user)) {

            $check_block = checkBlock($user['id'], $target_user['id']);

            if (!$check_block) {


                setWork('send', $target_user['chat_id'], $target_user['id'], $message['send_from_message_id']);

                $text_send = 'شما در حال پاسخ به این پیام هستید: ' . "\n";
                $text_send .= "لطفا پیام خود را ارسال کنید یا از /reset برای انصراف از پاسخ استفاده کنید";

                $telegram->setText($text_send)->setReplyMarkup($exitKeyboard)->sendMessage();
                return;
            }

            $text_send = 'شما توسط این کاربر بلاک شده اید';
            $telegram->setText($text_send)->setReplyMarkup($exitKeyboard)->sendMessage();
            return;
        } else {
            $telegram->setText("این شخص در سیستم وجود ندارد یا اکانت این کاربر غیر فعال شده است")->sendMessage();
        }


        return;
    }

    if (startsWith($callback_data, 'alert')) {
        $message_id_reply = explode('|', $callback_data)[1];

        $message = $db->table('message')->where('id', $message_id_reply)->first();
        $target_user = $db->table('users')->where('id', $message['send_from_user_id'])->where('status', 1)->first();

        if (!empty($target_user)) {


            $inline_keyboard = $json_decode['callback_query']['message']['reply_markup']['inline_keyboard'];

            array_splice($inline_keyboard, 1);


            $telegram->setReplyMarkup(['inline_keyboard' => $inline_keyboard])->editMessageReplyMarkup();

            $text_to_admin = "این پیام توسط " . $user['username'] . " گزارش شده است" . "\n";
            $text_to_admin .= $message['message'] . "\n";
            $text_to_admin .= "این پیام توسط " . $target_user['username'] . "ارسال شده است";

            foreach (ADMINS as $admin) {
                if ($message['file_type'] == null) {
                    $telegram->setChatID($admin)->setMessageID(null)->setReplyMarkup(null)->setText($text_to_admin)->sendMessage();
                } else {
                    $telegram->setChatID($admin)->setMessageID(null)->setReplyMarkup(null)->setText($text_to_admin)->setFileId($message['file_id'], $message['file_type'])->sendFile();
                }
            }
        } else {
            $telegram->setText("این شخص در سیستم وجود ندارد یا اکانت این کاربر غیر فعال شده است")->setReplyMarkup($exitKeyboard)->sendMessage();
        }


        return;
    }

    if (startsWith($callback_data, 'block')) {
        $message_id_reply = explode('|', $callback_data)[1];

        $message = $db->table('message')->where('id', $message_id_reply)->first();
        $target_user = $db->table('users')->where('id', $message['send_from_user_id'])->where('status', 1)->first();

        $block = $db->table('block')->where('user_id', $user['id'])->where('user_id_blocked', $target_user['id'])->first();
        if (!empty($target_user)) {
            $text_button = "🔓آنبلاک";
            $alert_send = "";
            if (empty($block)) {
                $alert_send = "کاربر با موفقیت بلاک شد";
                $db->table('block')->insert(['user_id', 'user_id_blocked', 'message', 'file_id', 'file_type'], [$user['id'], $target_user['id'], $message['message'], $message['file_id'], $message['file_type']]);
            } else {
                $alert_send = "کاربر با موفقیت آنبلاک شد";

                $text_button = "🔒بلاک";
                $db->table('block')->where('user_id', $user['id'])->where('user_id_blocked', $target_user['id'])->delete();
            }

            $inline_keyboard = $json_decode['callback_query']['message']['reply_markup']['inline_keyboard'];
            $inline_keyboard[0][1]['text'] = $text_button;
            $telegram->setReplyMarkup(['inline_keyboard' => $inline_keyboard])->editMessageReplyMarkup();
            $telegram->setText($alert_send)->answerCallbackQuery($json_decode['callback_query']['id']);
        } else {
            $telegram->setText("این شخص در سیستم وجود ندارد یا اکانت این کاربر غیر فعال شده است")->setReplyMarkup($exitKeyboard)->sendMessage();
        }
    }
}
