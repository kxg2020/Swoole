<?php
namespace bootstrap;


class ExceptionHandle{
    private static $exception = [];

    public static function render($errorCode = "", $errorMes = "", $errorFile = "", $errorLine = ""){
        self::$exception = [
            "msg"  => $errorMes,
            "code" => $errorCode,
            "file" => $errorFile,
            "line" => $errorLine
        ];
        print_r(self::$exception);
    }


}