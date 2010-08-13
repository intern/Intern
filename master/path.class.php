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
     * @var public the inter system(module routes) internal path routes
     */
    public $_internal_path_routes = array();

    /**
     * @var public the inter current path handle module struct array()
     */
    private $_handle_path = array();

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
        if( !self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
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
        $this->_iPath();
        $this->_iPathRoutes();
        $this->_iPathHandle();
        print_r(module::listModule());
    }

    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    public function getMenuArray() {
        return $this->_handle_path;
    }

    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    private function _iPath( ) {
        // TODO check the alias table if exists
        $this->_internal_path = $this->_standard_path;
    }

    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    public function getPathByAlias() {
        //
    }

    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    private function _iPathHandle() {
        $menu_hooks = call_user_func_array('array_merge', module::invokeAll('menu'));
        foreach( $this->_internal_path_routes as $_route ) {
            if ( isset( $menu_hooks[$_route] ) ) {
                $this->_handle_path = $menu_hooks[$_route];
                break;
            }
        }
    }

    /**
     +------------------------------------------------------------------------------
     * get the internal path routes
     * set the class variable $this->_internal_path_routes as array
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    private function _iPathRoutes() {
        $routes_vars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
        $parts = explode('/', $this->_internal_path );
        $parts_count = count( $parts );
        if( $parts_count  < 2 || $parts_count  > 8 ) {
            $routes =  array();
        } else {
            $routes = array_combine( array_slice($routes_vars,0, $parts_count), $parts);
            extract($routes , EXTR_OVERWRITE );
            require_once inter_join_path( MASTER, 'routes', "path.routes.{$parts_count}.inc" );
        }
        $this->_internal_path_routes = $routes;
    }
}