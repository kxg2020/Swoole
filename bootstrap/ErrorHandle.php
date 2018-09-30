<?php
namespace bootstrap;

class ErrorHandle{
    private static $error = [];

    public static function render($errorCode = "", $errorMes = "", $errorFile = "", $errorLine = ""){
        self::$error = [
            "msg"  => $errorMes,
            "code" => $errorCode,
            "file" => $errorFile,
            "line" => $errorLine
        ];
        print_r(self::$error);
    }
}