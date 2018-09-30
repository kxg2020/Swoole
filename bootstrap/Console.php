<?php
namespace bootstrap;
use lib\Server;

class Console{
    // 运行脚本
    private $requestFile;
    // 运行命令
    private $command;
    // 服务实例
    private $server = null;

    public function __construct(Server $server){
        $this->server  = $server;
        $this->parse();
    }

    // 启动脚本
    public function run(){
        if($this->runtime()){
            if(method_exists($this,$this->command)){
                call_user_func([$this,$this->command]);
            }else{
                $this->help();
            }
        }
    }

    // 启动服务
    private function start(){
        $this->logo()->status();
        $this->server->start();
    }

    // 停止服务
    private function stop(){
        $this->server->stop();
    }

    // 重启服务
    private function restart(){
        $this->server->restart();
    }

    // 解析参数
    private function parse(){
        global $argv;
        $this->requestFile = array_shift($argv);
        $this->command     = array_shift($argv);
        $daemon = array_shift($argv);
        if($daemon){
            if(substr($daemon,0,1) == "-"){
                $item = strpos($daemon,"d",true);
                $item ? $this->server->daemon = true : $this->server->daemon = false;
            }
        }
    }

    // 帮助信息
    private function help(){
        echo <<<HELP_START
\e[33m操作说明:\e[0m
\e[31m  php index.php start\e[0m
\e[33m简介:\e[0m
\e[36m  这是最近写的一个多进程消费者模型DEMO版本,没有做任务超时处理,仅供学习,直接使用,纯属傻逼--Macarinal\e[0m
\e[33m参数说明:\e[0m
\e[32m  -d \e[0m                   以守护模式启动框架,默认前台运行

HELP_START;
    }

    // 状态信息
    private function status(){
        echo <<<STATUS
\n\n欢迎使用张先生的\e[32m Macarinal\e[0m 框架 当前版本: \e[34m1.x\e[0m
\e[33m使用:\e[0m
  php index.php [操作] [选项]
\e[33m操作:\e[0m
\e[32m  start \e[0m        启动服务
\e[32m  stop \e[0m         停止服务
\e[32m  reload \e[0m       重载服务
\e[32m  restart \e[0m      重启服务
\e[32m  help \e[0m         查看命令的帮助信息\n
STATUS;
    }

    // 展示logo
    private function logo(){
        echo <<<LOGO
 __    __     ______     ______     ______     ______     __     __   __     ______     __        
/\ "-./  \   /\  __ \   /\  ___\   /\  __ \   /\  == \   /\ \   /\ "-.\ \   /\  __ \   /\ \       
\ \ \-./\ \  \ \  __ \  \ \ \____  \ \  __ \  \ \  __<   \ \ \  \ \ \-.  \  \ \  __ \  \ \ \____  
 \ \_\ \ \_\  \ \_\ \_\  \ \_____\  \ \_\ \_\  \ \_\ \_\  \ \_\  \ \_\\"\_\  \ \_\ \_\  \ \_____\ 
  \/_/  \/_/   \/_/\/_/   \/_____/   \/_/\/_/   \/_/ /_/   \/_/   \/_/ \/_/   \/_/\/_/   \/_____/
LOGO;
        return $this;
    }

    // 运行时环境
    private function runtime(){
        if(PHP_OS == WINDOWS){
            die("linux required\r\n");
        }
        if(php_sapi_name() !== "cli"){
            die("server only running in cli model\r\n");
        }
        if(!extension_loaded("swoole")){
            die("extension swoole required\r\n");
        }
        if(version_compare(PHP_VERSION,"5.6","<")){
            die("php 版本必须大于等于5.6");
        }
        return $this;
    }
}