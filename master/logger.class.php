<?php
// +----------------------------------------------------------------------
// |  [ WE CAN DO IT JUST programmer ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.hongrs.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: lan_chi <lan_chi@foxmail.com>
// +----------------------------------------------------------------------
// $Id$

if ( !defined('DEBUG') ) {
    define('DEBUG', false);
}

class logger {
    // logger file handle
    private static $logPath;

    // log ext
    private static $logExt;

    public static function init() {
        self::$logPath = defined('LOGS') ? LOGS : '.';
        self::$logExt  = defined('LOGS_EXT') ? LOGS_EXT : 'log';
    }

    public static function warn() {
        if (DEBUG) {
            self::_log($messages, 'WARNING');
        }
    }

    public static function debug() {
        if (DEBUG) {
            self::_log($messages, 'DEBUG');
        }
    }

    public static function log($messages) {
        if (DEBUG) {
            self::_log($messages);
        }
    }

    //
    public static function error($messages) {
        self::_log($messages, $type = 'ERROR');
        exit;
    }

    public static function SQL($messages) {
        $filename = DEBUG ? 'log' : 'sql';
        self::_log($messages, $type = 'SQL', $filename);
    }

    // alias log methods
    public static function notice($messages) {
        self::log($messages);
    }

    private static function _log($messages, $type = 'NOTICE', $filename = 'log') {
        $fp = fopen(self::$logPath . $filename . '.' . self::$logExt, 'a+') or die('Logger mybe not have permission!');
        if(!is_string($messages)) {
            $messages = "\n" . print_r($messages, true);
        }
        $_messages = sprintf("[%s]|%s|%s\n\n", $type, date("Y-m-d H:i:s"), $messages);
        fwrite($fp, $_messages);
        fclose($fp);
    }
}
