<?php
// +----------------------------------------------------------------------
// |  [ WE CAN DO IT JUST programmer ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.hongrs.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: lan_chi <lan_chi@qq.com>
// +----------------------------------------------------------------------
// $Id$

/**
 * config the site Salt, global this.
 *  session_name();
 */
define('SALT', 'INTER');

/**
 +------------------------------------------------------------------------------
 * session for database and init
 +------------------------------------------------------------------------------
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class interSessionDataHandle{
    /**
     * @var db handle
     */
    private $_db;

    /**
     *
     */
    public function interSessionData() {
        $this->__construct();
    }

    /**
     * session to init
     */
    public function __construct() {
        $this->_init();
    }

    /**
     *
     */
    private function _init() {
        $this->_db = interCoreDatabase::getInstance();
        $this->_setSessionName();

    }

    public function _setSessionName() {
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
        global $user;
        //当php自身调用session_write_close()时,对象已经不存在, fix $this->session_write();
        // error: Call to a member function query() on a non-object
        register_shutdown_function('session_write_close');

        if ( !isset($_COOKIE[session_name()]) ) {
            $user = inter_init_anonymous_user();
            return '';
        }
        $handle = $this->_db->query("SELECT u.*,s.* FROM {users} u INNER JOIN {sessions} s ON u.uid = s.uid WHERE s.sid = '%s'", $key);
        $user = $this->_db->fetchObject();
        //print_r($user);
        if ($user && $user->uid > 0 && $user->status ==  1 ) {
            //print($res);
        } else {
            $user = inter_init_anonymous_user();
        }
        return $user->data; // data is session table data, not is users table data
    }

    /**
     * @param $key the session_id()
     */
    public function session_write($key, $value) {
        global $user;
        if ( $user->uid == 0 && isset($_COOKIE[session_name()]) && empty($value) ) {
            return true;
        }
        $this->_db->query("UPDATE {sessions} SET hostname = '%s', timestamp = %d, data = '%s' WHERE sid = '%s'", inter_get_ip(), time(), $value, $key);

        if ( !$this->_db->affectedRows() ) {
            $this->_db->query("INSERT INTO {sessions}(uid, sid, hostname, timestamp, data) VALUES( %d, '%s', '%s', %d, '%s')", $user->uid, $key, inter_get_ip(), time(), $value);
        }
        return true;
    }

    /**
     * Delete current user session
     */
    public function session_destroy( $key ) {
        $this->_db->query("DELETE FROM {sessions} WHERE sid = '%s'", $key);
        return true;
    }

    /**
     * Given time to delete the timeout task
     */
    public function session_gc($lifetime) {
        $this->_db->query("DELETE FROM {sessions} WHERE timestamp < %d", time() - $lifetime);
        return true;
    }
}