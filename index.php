<?php
ini_set('display_errors', false);
date_default_timezone_set("Asia/Shanghai");
require "./bootstrap/Constant.php";
require "./bootstrap/Load.php";
$console = new \bootstrap\Console(new \lib\Server());
$console->run();