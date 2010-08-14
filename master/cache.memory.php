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
 * caching inter
 * hlper class
 */
class memoryCache {
    /**
     * Storage the cache data
     */
    private $_cache = array();

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
        if( isset($this->_cache[$key] ) ) {
            return $this->_cache[$key];
        }
    }

    /**
     +------------------------------------------------------------------------------
     * Delete the named $key cache, clear all cache if $key is not define
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key string Identifier for the data
     *
     * @return bool
     */
    public function _delete( $key = '' ) {
        if( isset($this->_cache[$key] ) ) {
            unset($this->_cache[$key]);
        } elseif ( $key == '' ) {
            unset($this->_cache);
        }
        return true;
    }

    /**
     +------------------------------------------------------------------------------
     * Setting the named $key cache
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key string Identifier for the data
     *        $data mix
     *        $expired  int
     * @return bool
     */
    public function _set($key, $data, $expire) {
        $this->_cache[$key] = $data;
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
        return true;
    }    
}