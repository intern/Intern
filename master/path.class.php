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
 * To ruter the web def group start.
 * init with Singleton for path
 */
class path {
    /**
     * @var private the path variable, can't update the value.
     */
    private $_standard_path = '';
    
    /**
     * @var public the inter system(module routes) internal path url
     */
    public $_internal_path = '';
    
    /**
     * @var private To save the path instance
     */
    private static $_instance = null;
    
    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    private function __construct() {
        $this->_standard_path = $_GET['q'];
    }

    /**
     +------------------------------------------------------------------------------
     * php4 construct
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    private function path() {
        $this->__construct();
    }
    
    /**
     +------------------------------------------------------------------------------
     * get the path instance to the static,
     *  Singleton for the path
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param NULL
     */
    public static function getInstance() {
        if( !path::$_instance ) {
            path::$_instance = new self;
        }
        return path::$_instance;
    }
    
    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    public function init() {
        print_r($this->_standard_path);
    }
    
}