<?php
namespace lib\component;

class SplArray extends \ArrayObject{

    public function __get($name){
        if(isset($this->$name)){
            return $this->$name;
        }
        return null;
    }

    public function __set($name, $value){
        $this->$name = $value;
    }

    public function getArrayCopy(){
        return (array)$this;
    }

    public function set($path,$value){
        $path = explode(".",$path);
        $self = $this;
        while ($key = array_shift($path)) {
            $self = &$self[$key];
        }
        $self = $value;
    }

    function get($path){
        $paths = explode(".", $path);
        $data = $this->getArrayCopy();
        while ($key = array_shift($paths)){
            if(isset($data[$key])){
                $data = $data[$key];
            }else{
                return null;
            }
        }
        return $data;
    }

    public function delete($key): void{
        $path = explode(".", $key);
        $lastKey = array_pop($path);
        $data = $this->getArrayCopy();
        $copy = &$data;
        while ($key = array_shift($path)){
            if(isset($copy[$key])){
                $copy = &$copy[$key];
            }else{
                return;
            }
        }
        unset($copy[$lastKey]);
        parent::__construct($data);
    }


}