<?php
namespace util;

trait Singleton{
    public static $instance = null;

    public static function getInstance(){
        self::$instance || self::$instance = new self();
        return self::$instance;
    }

    private function __wakeup(){
        // TODO: Implement __wakeup() method.
    }

    private function __clone(){
        // TODO: Implement __clone() method.
    }
}