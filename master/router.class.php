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
/*
 *  table struct
 *  feild: routes | parent_router | count_part | callback_func | func_args | callback_title | title_args | description | weight | template_path
 */

// include the core cache class
require_once inter_join_path( MASTER, 'cache.class.php' );

/**
 * The maximum number of path elements for a menu callback
 */
define('MENU_MAX_PARTS', 8);

//define menu type constants here
define( 'ADMIN_MAIN_MENU', 1);
define( 'ADMIN_SUB_MENU', 2);
define( 'ADMIN_TAB_MENU', 4);
define( 'CALLBACK', 16);

/**
 * To router the web def group start.
 * All routes to handle
 * init with Singleton for path
 */
class Router {
    /**
     * @var private handle self Singleton
     */
	private static $_instance;

    /**
     * @var private To save the db handle instance
     */
    private $_db;

    /**
     * @var private To save the path standard request path, Do't change this value
     */
    private $_alias_path;

	/**
     * @var private To save the internal path with current request the alias path
	 */
	private $_normal_path;

	/**
	 * @var private current internal path all routes. array
	 */
	private $_iPathRoutes = array();

	/**
	 * @var private the first router handle
	 */
	private $_router_handle = null;

    /**
     +------------------------------------------------------------------------------
     * Singleton class is prvate construct
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    private function __construct() {}
	private function Router() {}

    /**
     +------------------------------------------------------------------------------
     * menu routes create to routes table
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access public
     */
    public function menuRouterBuild() {
        $routes = array();
        foreach(module::implementer('menu') as $module) {
            $routes[$module] = module::invoke($module, 'menu');
        }
        $this->_menuRouterBuild($routes);
    }

    /**
     +------------------------------------------------------------------------------
     * helper for #menuRouterBuild
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
	 * @param $routes array the router path
     * @access private
     */
    private function _menuRouterBuild( $routes ) {
        $hook_menu = array();
        foreach($routes as $module => $item) {
            $count_part = explode('/', $path, MENU_MAX_PARTS);
        }
    }

    /**
     +------------------------------------------------------------------------------
     * To initialize the request path for router with this instance, parse the url
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    public function init() {
		//this handle db
		$this->_db = interCoreDatabase::getInstance();
		//set path
		$this->_alias_path = $_GET['q'];
		$this->_setiPath();
		$this->_setiPathRoutes();
		$this->_setiPathHandle();
    }

    /**
     +------------------------------------------------------------------------------
     * get the router instance to the static,
     *  Singleton for the path
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param NULL
	* @access public static
     */
    public static function getInstance() {
        if( !self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     +------------------------------------------------------------------------------
     *  Setting the 
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param NULL
	* @access private
     */
    private function _setiPath() {
		// TODO check the alias table if exists
        $this->_normal_path = trim($this->_alias_path, '/');
    }

    /**
     +------------------------------------------------------------------------------
     * Setting the router handle if find in the routes table
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    private function _setiPathHandle() {
        $menu_hooks = call_user_func_array('array_merge', module::invokeAll('menu'));
        foreach( $this->_iPathRoutes as $_route ) {
            if ( isset( $menu_hooks[$_route] ) ) {
                $this->_router_handle = $menu_hooks[$_route];
                break;
            }
        }
    }

    /**
     +------------------------------------------------------------------------------
     * get the internal all path routes if macth current path
     * set the class variable $this->_iPathRoutes as array
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    private function _setiPathRoutes() {
        $routes_vars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
        $parts = explode('/', $this->_normal_path );
        $parts_count = count( $parts );
        if( !$parts_count || $parts_count  > MENU_MAX_PARTS ) {
            $routes =  array();
        } else {
            $routes = array_combine( array_slice($routes_vars,0, $parts_count), $parts);
            extract($routes , EXTR_OVERWRITE );
            require_once inter_join_path( MASTER, 'routes', "path.routes.{$parts_count}.inc" );
        }
        $this->_iPathRoutes = $routes;
    }

    /**
     +------------------------------------------------------------------------------
     * get the the all data with the param
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
	public function runByRouterHandle() {
		print_r($this->_router_handle);
	}
}//routes