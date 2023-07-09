<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('API_TOKEN', '5811843834:AAHPcCokwFBw74kG9G96NrPFqJoiBnIm3dA');
define('API_URL', 'https://api.telegram.org/bot' . API_TOKEN . '/');
define('PROGRAMER', '5890853817');
define('ADMINS', array('5890853817'));
define('IGNORE_DEBUG_USER', array(''));
define('DEBUG', false);

// codealli_toplearn
// codealli_toplearnUser
// {lNPprW(Hm)0

set_error_handler('customError', E_ALL);    


function customError($errorNumber, $error){
    set_log('PHP ERROR', '{' . $errorNumber . '}' . $error);
}
function set_log($title, $error){

    
    date_default_timezone_set('Asia/Tehran');

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . "log.txt";
    
    $text = "[" . $title . "] " . " (" . date('Y-m-d H:i:s') . ") =>" . $error . "\n";   

    file_put_contents($filePath, $text, FILE_APPEND);
}