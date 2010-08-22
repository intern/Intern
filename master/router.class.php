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
    public function rebuildMenuRouters() {
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
        $this->_rebuildMenuRouters($routes);
        $this->_rebuildMenuRoutersPermission($routes);
        //$this->_rebuildMenuRoutersNavigation($routes);
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
    private function _rebuildMenuRouters( $routes ) {
        //$menu_router = array();
        foreach( $routes as $router => $item ) {
            //implode explode
            $router_parts = explode('/', $router);
            $item['count_parts'] = count( $router_parts );
            $this->_menuRouterVerify($item);
            $item['router'] = $router;
            $routes[$router] = $item;
        }
        $this->_db->query("DELETE FROM {routes}");
        
        foreach( $routes as $item ) {
            $this->_db->query("INSERT INTO {routes} (router,module,parent_router,
                          count_part,function,function_args,title,title_callback,
                          description,weight,template,type) VALUES('%s', '%s',
                          '%s', %d, '%s','%s','%s','%s','%s',%d,'%s',%d)",
                          $item['router'], 
                          $item['module'],
                          $item['parent_router'],
                          $item['count_parts'],
                          $item['function'],
                          $item['function_args'],
                          $item['title'],
                          $item['title_callback'],
                          $item['description'],
                          $item['widget'],
                          $item['template'],
                          $item['type']
                          );
        }
    }

    /**
     +------------------------------------------------------------------------------
     * helper for #_rebuildMenuRouters to verify router
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
                $item['template'] = inter_join_path(module::getPath($item['module']),
                                                      $item['callback']['template']);
            } else if( isset( $item['callback']['function'] ) ) {
                if( isset( $item['callback']['function args'] ) 
                        && is_array($item['callback']['function args'] ) ) {
                    $item['function_args'] = serialize($item['callback']['function args']);
                }
                $item['function'] = serialize($item['callback']['function']);
            }
            unset($item['callback']);
        }
        if($item['type'] == ADMIN_MAIN_MENU) {
            $item['parent_router'] = '';
        } else if( isset( $item['parent router'] ) ) {
            $item['parent_router'] = $item['parent router'];
            unset($item['parent router']);
        }
        $item += array(
            'type'           => ADMIN_MAIN_MENU,
            'title'          => '',
            'title_callback' => '',
            'widget'         => 0,
            'function'       => '',
            'function_args'  => '',
            'template'       => '',
            'description'    => '',
            'parent_router'  => ''
        );
    }
    
    /**
     +------------------------------------------------------------------------------
     * helper for #rebuildMenuRouters to router rebuild Permission table
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
	 * @param $routes array the router path
     * @access private
     */
    private function _rebuildMenuRoutersPermission( $routes ) {
        //clear the routes_permission data
        $this->_db->query("DELETE FROM {routes_permission}");
        foreach( $routes as $router => $item ) {
            if ( !isset($item['permission']) ) {
                $item['permission'] = '';
            }
            $this->_db->query("INSERT INTO {routes_permission} ( router, description)
                              VALUES('%s', '%s')", $router, $item['permission']);
        }
    }

    /**
     +------------------------------------------------------------------------------
     * helper for #rebuildMenuRouters to router rebuild navigation links
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
	 * @param $routes array the router path
     * @access private
     */
    private function _rebuildMenuRoutersNavigation( $routes ) {
        //clear the routes_permission data
        $this->_db->query("DELETE FROM {routes_permission}");
        foreach( $routes as $router => $item ) {
            if ( !isset($item['permission']) ) {
                $item['permission'] = '';
            }
            $this->_db->query("INSERT INTO {routes_permission} ( router, description)
                              VALUES('%s', '%s')", $router, $item['permission']);
        }
    }

    /**
     +------------------------------------------------------------------------------
     * get all trail links menu for current path
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
	 * @param $routes array the router path
     * @access public
     */
    public function getAdminMenu() {
        //clear the routes_permission data
        static $links = array();
        $this->_db->query("SELECT * FROM {routes} WHERE router='%s'", $this->_normal_path);
        $current_path = $this->_db->fetchObject();
        if ( $current_path->type == ADMIN_MAIN_MENU ) {
            $this->_db->query("SELECT * FROM {routes} WHERE type = %d ORDER BY weight ASC", ADMIN_MAIN_MENU);
            $links['ADMIN_MAIN_MENU'] = array();
            while( $item = $this->_db->fetchObject() ) {
                if( $item->router == $current_path->router ) {
                    $item->active = true;
                }
                array_push($links['ADMIN_MAIN_MENU'], $item);
            }
            $this->_db->query("SELECT * FROM {routes} WHERE parent_router = '%s' ORDER BY weight ASC", $current_path->router );
            $links['ADMIN_MAIN_MENU'] = array();
            while( $item = $this->_db->fetchObject() ) {
                if( $item->router == $current_path->router ) {
                    $item->current = true;
                }
                array_push($links['ADMIN_MAIN_MENU'], $item);
            }
        }
        if ( $current_path->type == ADMIN_SUB_MENU ) {
            $this->_db->query("SELECT * FROM {routes} WHERE type = %d ORDER BY weight ASC", ADMIN_SUB_MENU);
            $links['ADMIN_MAIN_MENU'] = array();
            while( $item = $this->_db->fetchObject() ) {
                if( $item->router == $current_path->router ) {
                    $item->current = true;
                }
                array_push($links['ADMIN_MAIN_MENU'], $item);
            }
        }
        return $links;
    }

    private function menuGetActiveTrail( $trail_type = null ) {
        static $links = array();
        if ( !isset($links[$trail_type]) ) {
            
        }
        return $links[$trail_type];
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
		$this->rebuildMenuRouters();
        //print_r($this->_menuRouterBuild);
	}
}//routes