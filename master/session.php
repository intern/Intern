<?php
// +----------------------------------------------------------------------
// |  [ WE CAN DO IT JUST programmer ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.hongrs.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: lan_chi <lan_chi@163.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * session for database and init
 +------------------------------------------------------------------------------
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class interSessionData{
    /**
     *
     * @var unknown_type
     */
    private $_time;

    /**
     *
     * @var unknown_type
     */
    private $_ip;
    /**
     *
     */
    public function interSessionData() {
        $this->__construct();
    }

    /**
     *
     */
    public function __construct() {}

    /**
     *
     */
    public static function getInstance() {
        return new self;
    }

    /**
     *
     */
    public function session_open($save_path, $session_name) {
        global $a;
        $a = $save_path." || ".$session_name;
        return true;
    }

    /**
     *
     */
    public function session_close() {
        return true;
    }

    /**
     *
     */
    public function session_read() {}

    /**
     *
     */
    public function session_write() {}

    /**
     *
     */
    public function session_destroy() {}

    /**
     *
     */
    public function session_gc() {}
}