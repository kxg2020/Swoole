<?php
namespace lib;

use lib\component\SplArray;
use util\Singleton;

class Config{
    use Singleton;
    private $config = null;


    public function __construct(){
        $data = require CONFIG_PATH."/Config".FILE_EXT;
        $this->config = new SplArray($data);
    }

    public function get($key){
        return $this->config->get($key);
    }

    public function set($key,$value){
        $this->config->set($key,$value);
    }
}