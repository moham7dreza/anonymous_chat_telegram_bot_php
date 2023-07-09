<?php

$mainKeyboard = [
    'keyboard' => [
        [
            ['text' => 'لینک ناشناس من 📬']
        ],
        [
            ['text' => 'تنظیمات ⚙️'],
            ['text' => 'راهنما ⁉️'],
        ],
        [
            ['text' => '🧰️ پشتیبانی 🧰️']
        ]
    ],
    'resize_keyboard' => true
];


$mainKeyboard_admin = [
    'keyboard' => [
        [
            ['text' => 'لینک ناشناس من 📬']
        ],
        [
            ['text' => 'تنظیمات ⚙️'],
            ['text' => 'راهنما ⁉️'],
        ],
        [
            ['text' => '🔧پنل مدیریت🔧']
        ]
    ],
    'resize_keyboard' => true
];


$exitKeyboard = [
    'keyboard' => [
        [
            ['text' => '❌ انصراف']
        ],
    ],
    'resize_keyboard' => true
];

function getInlineKeyboardSetting($user)
{
    $get_message = $user['get_message'] ? "✅" : "☑️";
    $get_sticker = $user['get_sticker'] ? "✅" : "☑️";
    $get_video = $user['get_video'] ? "✅" : "☑️";
    $get_audio = $user['get_audio'] ? "✅" : "☑️";
    $get_voice = $user['get_voice'] ? "✅" : "☑️";
    $get_animation = $user['get_animation'] ? "✅" : "☑️";
    $get_photo = $user['get_photo'] ? "✅" : "☑️";
    $reply_markup = [
        'inline_keyboard' => [
            [
                [
                    'text' => 'ارسال پیام متنی' . $get_message,
                    'callback_data' => 'setting|get_message'
                ],
                [
                    'text' => 'ارسال استیکر' . $get_sticker,
                    'callback_data' => 'setting|get_sticker'

                ],
            ],
            [
                [
                    'text' => 'ارسال ویدئو' . $get_video,
                    'callback_data' => 'setting|get_video'

                ],
                [
                    'text' => 'ارسال فایل صوتی' . $get_audio,
                    'callback_data' => 'setting|get_audio'

                ],


            ],
            [
                [
                    'text' => 'ارسال ویس' . $get_voice,
                    'callback_data' => 'setting|get_voice'

                ],
                [
                    'text' => 'ارسال گیف' . $get_animation,
                    'callback_data' => 'setting|get_animation'

                ],


            ],
            [
                [
                    'text' => 'ارسال تصویر' . $get_photo,
                    'callback_data' => 'setting|get_photo'

                ],

            ],
            [
                [
                    'text' => '❌بستن تنظیمات❌',
                    'callback_data' => 'setting|close'

                ],

            ],
        ]
    ];

    return $reply_markup;
}
