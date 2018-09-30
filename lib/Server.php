<?php
namespace lib;

use util\Log;

class Server{
    // 工作进程容器
    private $worker = [];
    // 工作进程个数
    private $workerNum;
    // 是否守护
    public $daemon = false;
    // 打印次数
    private $times = 3;
    public function __construct(){
        $this->workerNum = Config::getInstance()->get("server.workerNum");
    }

    // 启动服务
    public function start(){
        swoole_set_process_name("master-master");
        if($this->daemon){
            \swoole_process::daemon(true,true);
        }
        $masterPidFile = Config::getInstance()->get("masterPidFile");
        if(file_exists($masterPidFile)){
            $pid = file_get_contents($masterPidFile);
            if($pid){
                // 检测三次主进程状态
                for($i = 0; $i < 3; $i ++){
                    if(\swoole_process::kill($pid,SIG_DFL)){
                        die("server is already running now\r\n");
                    }
                    sleep(1);
                }
            }
        }
        $pid = posix_getpid();
        file_put_contents($masterPidFile,$pid);
        // 启动子进程
        for($i = 0; $i < $this->workerNum;$i ++){
            $this->registerChild($i);
            sleep(1);
        }
        // 注册信号
        $this->registerSignal();
        usleep(500);
    }

    // 关闭服务
    public function stop(){
        $masterPidFile = Config::getInstance()->get("masterPidFile");
        if(file_exists($masterPidFile)){
            $pid = file_get_contents($masterPidFile);
            if($pid){
               $result = \swoole_process::kill($pid,SIGTERM);
               if($result){
                   Log::getInstance()->write("master process {$pid} was stopped success");
                   unlink($masterPidFile);
               }else{
                   Log::getInstance()->write("master process {$pid} was stopped failed");
               }
               exit();
            }
            die("server status is exception\r\n");
        }
        die("server is not running\r\n");
    }

    // 重启服务
    public function restart(){
        $this->stop();
        sleep(2);
        $this->start();
    }

    // 展示信息
    private function  tag($name,$value){
        echo "\e[32m" . str_pad($name, 20, ' ', STR_PAD_RIGHT) . "\e[34m" . $value . "\e[0m\n";
    }

    // 注册子进程
    private function registerChild($workerNum){
        $process = new \swoole_process(function () use ($workerNum){
            $pid = posix_getpid();
            Log::getInstance()->write(" worker process {$pid} start");
            swoole_set_process_name("worker-".$workerNum);
            // 每两秒打印时间
            swoole_timer_tick(3000,function ($timerId) use ($workerNum,$pid){
                $this->times ++;
                Log::getInstance()->write("time:".date("H:i:s")." from worker ".$workerNum." process id is {$pid}");
            });
        });
        $pid = $process->start();
        $this->worker[$pid] = $process;
    }

    // 信号函数
    private function registerSignal(){

        // SIGTERM信号回调(软中断)
        \swoole_process::signal(SIGTERM,function (){
            foreach ($this->worker as $pid => $process){
                $result = \swoole_process::kill($pid,SIGTERM);
                if($result){
                    Log::getInstance()->write(" worker process {$pid} is stop");
                    unset($this->worker[$pid]);
                }
            }
            exit;
        });

        // SIGKILL信号回调(强中断)
        \swoole_process::signal(SIGKILL,function (){
            foreach ($this->worker as $pid => $process){
                $result = \swoole_process::kill($pid,SIGTERM);
                if($result){
                    Log::getInstance()->write("worker process {$pid} is stop");
                    unset($this->worker[$pid]);
                }
            }
           $masterPidFile =  Config::getInstance()->get("masterPidFile");
            unlink($masterPidFile);
            exit;
        });

        // SIGINT信号回调(前台运行中断,进程组所有的父、子都会收到)
        \swoole_process::signal(SIGINT,function (){
            $masterPidFile = Config::getInstance()->get("masterPidFile");
            $masterLogFile = Config::getInstance()->get("logFile");
            if(file_exists($masterPidFile)){
                unlink($masterPidFile);
            }
            if(file_exists($masterLogFile)){
                unlink($masterLogFile);
            }
            exit;
        });
        // SIGCHLD信号回调(子进程退出)
        \swoole_process::signal(SIGCHLD,function (){
            while (true){
                $result = \swoole_process::wait(false);
                if($result){
                    $pid     = $result["pid"];
                    $process = $this->worker[$pid];
                    // 唤醒新进程
                    $pidNew  =  $process->start();
                    $this->worker[$pidNew] = $pidNew;
                    unset($this->worker[$pid]);
                    Log::getInstance()->write("new child {$pidNew} is create");
                }else{
                    break;
                }
            }
        });
    }
}