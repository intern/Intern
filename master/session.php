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

// config
define('SALT', $salt);

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
    public function __construct() {
        session_name( SALT . md5(SALT) );
    }

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
    public function session_read($key) {
        global $user,$db_handle;

        if ( !isset($_COOKIE[session_name()]) ) {
            $user = inter_init_anonymous_user();
            return '';
        }
        $handle = $db_handle->query("SELECT u.*,s.* FROM {users} u INNER JOIN {sessions} s ON u.uid = s.uid WHERE s.sid = '%s'", $key);
        $user = $db_handle->fetchObject();
        if ($user && $user->uid > 0 && $user->status ==  1 ) {
            print($res);
        } else {
            $user = inter_init_anonymous_user();
        }
        return $user->data; // data is session table data, not is users table data
    }

    /**
     * @param $key the session_id()
     */
    public function session_write($key, $value) {
        global $db_handle;


    }

    /**
     *
     */
    public function session_destroy() {}

    /**
     *
     */
    public function session_gc() {}
}