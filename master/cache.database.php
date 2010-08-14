<?php
// +--------------------------------------------------------------------------------
// |  [ WE CAN DO IT JUST programmer ]
// +--------------------------------------------------------------------------------
// | Copyright (c) 2010 http://www.hongrs.net All rights reserved.
// +--------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +--------------------------------------------------------------------------------
// | Author: lan_chi <lan_chi@qq.com>
// +--------------------------------------------------------------------------------
// $Id$

/*
 * caching data into database
 * static class
 */
class databaseCache {
    /**
     * @var the db handle
     */
    private $_db;
    
    /**
     * Construct init db handle
     */
    public function __construct() {
        $this->_db = interCoreDatabase::getInstance();
    }

    /**
     +------------------------------------------------------------------------------
     * Read a key from the cache.  Will automatically use the currently active
     *    get the $key cache
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key string Identifier for the data
     * @return cache data if exists
     */
    public function _get( $key ) {
        if ( isset($key) ) {
            $this->_db->query("SELECT data, serialized, expired FROM {cache} WHERE id='%s'", md5($key));
            if( $obj = $this->_db->fetchObject() ) {
                if( $obj->expired >= time() ) {
                    if( $obj->serialized ) {
                        // TODO postgres need decode_blob
                        return unserialize($obj->data); 
                    }
                    return $obj->data;
                } else {
                    $this->_db->query("DELETE FROM {cache} WHERE id='%s'", md5($key));
                }
            }
        }
    }

    /**
     +------------------------------------------------------------------------------
     * Setting cache with database.
     *    get the $key cache
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key string Identifier for the data
     * @return cache data if exists
     */
    public function _set( $key, $data, $expire ) {
        $serialized = 0;
        if ( is_object($data) || is_array( $data ) ) {
            $data = serialize($data);
            $serialized = 1;
        }
        if ( !is_int($expire) ) {
            $expired = 300 + time(); //
        } else {
            $expired = $expire + time();
        }
        $this->_db->query("UPDATE {cache} SET expired=%d, data=%b, serialized=%d  WHERE id='%s'", $expired, $data, $serialized, md5($key));
        if( !$this->_db->affectedRows() ) {
            $this->_db->query("INSERT INTO {cache}(id, data, expired, serialized) VALUES('%s', %b, %d, %d)", md5($key), $data, $expired, $serialized );
        }
    }

    /**
     +------------------------------------------------------------------------------
     * the cron task
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key string Identifier for the data
     *        $data mix
     *        $expired  int
     * @return bool
     */
    public function _gc() {
        $this->_db->query("DELETE FROM {cache} WHERE expired < %d", time());
    }
}