<?php
namespace util;

use lib\Config;

class Log{
    use Singleton;
    private $logFile;

    public function __construct()
    {
        $this->logFile = Config::getInstance()->get("logFile");
    }

    public function write($log){
        file_put_contents($this->logFile,$log.PHP_EOL,8);
    }
}