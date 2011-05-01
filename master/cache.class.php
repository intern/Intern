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

/**
 * defaule the cache type
 *  @see site.config.php
 */
define('CACHE_DEFAULT', $cache_type);


/**
 * @var cache db | memory | file constants
 */
define('CACHE_D', 'database');

define('CACHE_F', 'file');

define('CACHE_M', 'memory');

/*
 * caching intern
 * static class
 */
class Cache {
    /**
     *  collect the cache instance here
     */
    private static $instance = array();

    /**
     * load the 3 cache plan
     */
    private static function _init() {
        foreach(array( CACHE_D, CACHE_M, CACHE_F) as $cache) {
            self::_getInstance($cache);
        }
    }

    /**
     +------------------------------------------------------------------------------
     * Read a key from the cache.  Will automatically use the currently active
     * cache configuration.
     *      the currently active configuration see: site.config.php
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key Identifier for the data
     * @return cache data
     */
    public static function get( $key, $type = null ) {
        return self::_getInstance($type)->_get( $key );
    }

    /**
     +------------------------------------------------------------------------------
     * Temporarily change settings to current config options.
     *
     * the currently active configuration see: site.config.php
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key Identifier for the data
     *        $data the cache data for the key
     *        $expired time until seconds
     *        $type the cache instance type
     * @return SEE instance
     */
    public static function set( $key, $data, $expire = 300, $type = '' ) {
        return self::_getInstance($type)->_set( $key, $data, $expire );
    }

    /**
     +------------------------------------------------------------------------------
     * Delete a cache data information.
     *
     * the currently active configuration see: site.config.php
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key Identifier for the data
     *        $type the cache instance type
     * @return SEE instance
     */
    public static function delete( $key, $type ) {
        return self::_getInstance($type)->_delete( $key );
    }

    /**
     *  TODO a task
     +------------------------------------------------------------------------------
     * Permanently remove all expired and deleted data with all $instance class
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key Identifier for the data
     * @return
     */
    public static function gc() {
        foreach( self::$instance as $instance ) {
            $instance->_gc();
        }
    }

    /**
     +------------------------------------------------------------------------------
     * run a clear expired cache data
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @return null
     */
    public static function runClear() {
        self::_init();
        $expired = options_get('cache_lifetime', 0 ) + options_get('cache_clear_corn', 0 );
        if ( $expired < time() ) {
            self::gc();
            options_set('cache_clear_corn', time());
        }
    }

    /**
     +------------------------------------------------------------------------------
     * Delete all cache data information.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $key Identifier for the data
     *        $type the cache instance type
     * @return SEE instance
     */
    public static function clear( $type ) {
        return self::_getInstance($type)->delete(); // not given param to clear all
    }

    /**
     +------------------------------------------------------------------------------
     * Check the instance exists with cache type
     *
     * create instance if the not exists
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $type cache type
     *      options: memory | file | database
     *      (defalut @see site.config.php)
     *
     * @return the type instacne
     */
    private static function _getInstance( $type ) {
        if( !is_string($type) || $type == '' ) {
            $type = CACHE_DEFAULT;
        }
        if( !isset( self::$instance[$type] ) ) {
            require_once intern_join_path( MASTER, "cache.{$type}.php" );
            $instance = $type . 'Cache';
            self::$instance[$type] = new $instance;
        }
        return self::$instance[$type];
    }
}
