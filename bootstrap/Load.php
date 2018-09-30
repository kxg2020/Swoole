<?php
namespace bootstrap;

class Load{
    public static $container = [];
    public static function loadFile($classNamespace){
        if(isset(self::$container[$classNamespace])){
            require self::$container[$classNamespace];
            return;
        }
        $class = str_replace("\\",DIRECTORY_SEPARATOR,$classNamespace);
        self::$container[$classNamespace] = ROOT_PATH.$class.FILE_EXT;
        require self::$container[$classNamespace];
        return;
    }
}
// 注册自动加载类
spl_autoload_register("bootstrap\\Load::loadFile");
// 注册异常处理类
set_exception_handler("bootstrap\\ExceptionHandle::render");
// 注册错误处理类
set_error_handler("bootstrap\\ErrorHandle::render");
// 注册脚本终止类
register_shutdown_function("bootstrap\\FatalHandle::handleFatal");

