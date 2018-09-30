<?php
namespace bootstrap;

class FatalHandle{
    public static function handleFatal(){
        $error = error_get_last();
        if (isset($error['type'])) {
            switch ($error['type']) {
                case E_ERROR :
                    $severity = 'ERROR:Fatal run-time errors. Errors that can not be recovered from. Execution of the script is halted';
                    break;
                case E_PARSE :
                    $severity = 'PARSE:Compile-time parse errors. Parse errors should only be generated by the parser';
                    break;
                case E_DEPRECATED:
                    $severity = 'DEPRECATED:Run-time notices. Enable this to receive warnings about code that will not work in future versions';
                    break;
                case E_CORE_ERROR :
                    $severity = 'CORE_ERROR :Fatal errors at PHP startup. This is like an E_ERROR in the PHP core';
                    break;
                case E_COMPILE_ERROR :
                    $severity = 'COMPILE ERROR:Fatal compile-time errors. This is like an E_ERROR generated by the Zend Scripting Engine';
                    break;
                default:
                    $severity = 'OTHER ERROR';
                    break;
            }
            $message = $error['message'];
            $file = $error['file'];
            $line = $error['line'];
            $log = "$message ($file:$line)\nStack trace:\n";
            $trace = debug_backtrace();
            foreach ($trace as $i => $t) {
                if (!isset($t['file'])) {
                    $t['file'] = 'unknown';
                }
                if (!isset($t['line'])) {
                    $t['line'] = 0;
                }
                if (!isset($t['function'])) {
                    $t['function'] = 'unknown';
                }
                $log .= "#$i {$t['file']}({$t['line']}): ";
                if (isset($t['object']) && is_object($t['object'])) {
                    $log .= get_class($t['object']) . '->';
                }
                $log .= "{$t['function']}()\n";
            }
            if (isset($_SERVER['REQUEST_URI'])) {
                $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
            }
            file_put_contents(ROOT_PATH."/error.log",$log,8);
        }
    }
}