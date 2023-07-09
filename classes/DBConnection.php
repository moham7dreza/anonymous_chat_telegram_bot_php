<?php

namespace classes;
use PDO;
use PDOException;

class DBConnection
{
    private static $dbInstance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$dbInstance == null) {

            self::$dbInstance = self::pdoInstance();
        }

        return self::$dbInstance;
    }

    private static function pdoInstance()
    {
        try {

            $option = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);

            return new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD, $option);
        } catch (PDOException $error) {
            set_log('DATABASE', $error->getMessage());
            return false;
        }
    }
}

