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
 *  feild: router | parent_router | count_part | callback_func | func_args | callback_title | title_args | description | weight | template_path
 */

// include the core cache class
require_once inter_join_path( MASTER, 'cache.class.php' );

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
    public function menuRoutersBuild() {
        $routes = array();
        foreach(module::implementer('menu') as $module) {
            $_router = module::invoke($module, 'menu');
            if ( isset($_router) && is_array($_router) ) {
                foreach( array_keys($_router) as $path ) {
                    $_router[$path]['module'] = $module;
                }
                $routes = array_merge($routes, $_router);
            }
        }
        //$this->_menuRoutersFormat($routes);
        $this->_menuRoutersBuild($routes);
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
    private function _menuRoutersBuild( $routes ) {
        $menu_router = array();
        foreach( $routes as $router => $item ) {
            //implode explode
            $router_parts = explode('/', $router);
            $item['count_parts'] = $count_part = count( $router_parts );
            for($i = $count_part-1; $i > 0; $i--) {
                $current_parts = array_slice($router_parts, 0, $i);
                if( isset($routes[implode('/', $current_parts)]) ) {
                    $item['parent_router'] = implode('/', $current_parts);
                    break;
                }
            }
            $this->_menuRouterVerify($item);
            $routes[$router] = $item;
            //exit;
        }
        print_r($routes);
    }

    /**
     +------------------------------------------------------------------------------
     * helper for #_menuRouterBuild to verify router
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
	 * @param $routes array the router path
     * @access private
     */
    private function _menuRouterVerify( &$item ) {
        // title callback check
        if ( isset($item['title callback']) ) {
            $item ['title_callback'] = $item['title callback'];
            unset($item['title callback']);
        }

        //callback
        if ( isset( $item['callback'] ) ) {
            if ( isset( $item['callback']['template'] ) ) {
                $item['template'] = module::getPath($item['module']);
            } else if( isset( $item['callback']['function'] ) ) {
                if( isset( $item['callback']['function args'] ) 
                        && is_array($item['callback']['function args'] ) ) {
                    $item['function_args'] = serialize($item['callback']['function args']);
                }
                $item['function'] = serialize($item['callback']['function']);
            }
            unset($item['callback']);
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
		$this->menuRoutersBuild();
        //print_r($this->_menuRouterBuild);
	}
}//routes