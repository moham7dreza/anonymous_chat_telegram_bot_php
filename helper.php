<?php

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateCode()
{
    global $db;
    $code = 'toplearn-' . generateRandomString(5) . '-' . rand(11111, 99999) . '-' . generateRandomString(5);
    $user = $db->table('users')->where('code', $code)->first();
    if (empty($user)) {
        return $code;
    }

    return generateCode();
}

function startsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    return substr( $haystack, 0, $length ) === $needle;    
}

function setWork($work = 'none', $send_to_chat_id = null, $send_to_user_id = null, $send_to_message_id = null)
{
    global $db, $user;
    $db->table('users')->where('id', $user['id'])->update(['work', 'send_to_chat_id', 'send_to_user_id', 'send_to_message_id'], [$work, $send_to_chat_id, $send_to_user_id, $send_to_message_id]);
}

function checkBlock($id_user, $id_target_user)
{

    global $db;

    $block = $db->table('block')->where('user_id', $id_target_user)->where('user_id_blocked', $id_user)->first();



    if (empty($block)) {
        return false;
    }


    return true;
}


