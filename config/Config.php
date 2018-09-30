<?php
namespace config;

return [
    "masterPidFile" => ROOT_PATH."masterPidFile.pid",
    "workerPidFile" => ROOT_PATH."workerPidFile.pid",
    "server" => [
        "workerNum" => 3
    ],
    "logFile"=> ROOT_PATH."/master.log",
    "daemon" => false
];