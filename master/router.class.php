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
  *  table struct
  *  feild: router | parent_router | count_part | callback_func | func_args | callback_title | title_args | description | weight | template_path
  */

// include theme boot
//require_once intern_join_path( MASTER, 'request.class.php' );

// include template boot
//require_once intern_join_path( MASTER, 'template.class.php' );


/**
 * router the web def group start.
 * To handle any http request
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
     * @var private To save the path standard request path, Can't change this value
     */
    private $_origin_path;

    /**
     * @var private To save the internal path with current request the alias path
     */
    private $_normal_path;

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
     * To initialize the request path for router with this instance, parse the url
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    public function init() {
        //this handle db
        $this->_db = internCoreDatabase::getInstance();
        //set path
        $this->_origin_path = $_GET['q'];
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
}

/**
 * To router the web def group start.
 * All routes to handle
 * init with Singleton for path
 */
class _Router {
    /**
     * @var private handle self Singleton
     */
    private static $_instance;

    /**
     * @var private To save the db handle instance
     */
    private $_db;

    /**
     * @var private To save the path standard request path, Can't change this value
     */
    private $_alias_path;

    /**
     * @var private To save the internal path with current request the alias path
     */
    private $_normal_path;

    /**
     * @var private current internal path all routes.
     *  is array
     * @see path a/b/c/d/e/f/g
     *    array(
     *          'a/b/c/d/e/f/g',
     *          'a/b/c/d/e/f/%',
     *          'a/b/c/d/e/%/g',
     *          .....
     *          'a/%/%/%/%/%/%'
     *    );
     */
    private $_iPathRoutes = array();

    /**
     * @var private the first router handle
     *      Matching the best route, like the menu hook format
     */
    private $_router_handle = null;

    /**
     * @var private the save the request all navigation links
     */
    private $_navigation = array();

    /**
     * @var private current request trail tree
     */
    private $_request_trail = array();

    /**
     * @var private template class
     */
    private $_template = null;

    /**
     * @var private page call
     */
    private $_page_callback = null;

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
     * Build menu router create to router table, Will delete all router if it exists
     *      create routers, Update router permission and the Navigation links
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
        // format the hook menu data to Standard
        $this->_menuRoutersFormat($routes);
        // routers table
        $this->_rebuildMenuRouters($routes);
        // permission
        $this->_rebuildMenuRoutersPermission($routes);
        // Navigation
        $this->_rebuildMenuRoutersNavigation($routes);
    }

    /**
     +------------------------------------------------------------------------------
     * helper for #menuRouterBuild to create router by hook menu
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $routes array the router path
     * @access private
     */
    private function _rebuildMenuRouters( $routes ) {
        //$menu_router = array();
        $this->_db->query("DELETE FROM {routes}");

        foreach( $routes as $item ) {
            $this->_db->query("INSERT INTO {routes} (router,module,parent_router,
                          count_parts,function,function_args,title,title_callback,
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
                          $item['weight'],
                          $item['template'],
                          $item['type']
                          );
        }
    }

    /**
     +------------------------------------------------------------------------------
     * helper for #rebuildMenuRouters to format routes array
     *
     * @see https://gist.github.com/8628b46545e860bf3260#file_menu_hook description!
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $routes array the router path
     * @access private
     */
    private function _menuRoutersFormat( &$routes ) {
        foreach( $routes as $router => $item ) {
            // title callback check
            if ( isset($item['title callback']) ) {
                $item ['title_callback'] = $item['title callback'];
                unset($item['title callback']);
            }

            //callback
            if ( isset( $item['callback'] ) ) {
                if ( isset( $item['callback']['template'] ) ) {
                    $item['template'] = inter_join_path(module::getPath($item['module']), $item['callback']['template']);
                } else if( isset( $item['callback']['function'] ) ) {
                    if( isset( $item['callback']['function args'] ) && is_array($item['callback']['function args'] ) ) {
                        $item['function_args'] = serialize($item['callback']['function args']);
                    }
                    $item['function'] = $item['callback']['function'];
                }
                unset($item['callback']);
            }
            if($item['type'] == ADMIN_MAIN_MENU) {
                $item['parent_router'] = '';
            } else if( isset( $item['parent router'] ) ) {
                $item['parent_router'] = $item['parent router'];
                unset($item['parent router']);
            }
            $item['count_parts'] = count( explode('/', trim($router, '/')) );
            $item['router'] = $router;
            $item += array(
                'type'           => ADMIN_MAIN_MENU,
                'title'          => '',
                'title_callback' => '',
                'function'       => '',
                'function_args'  => '',
                'template'       => '',
                'description'    => '',
                'parent_router'  => '',
                'permission'     => '',
                'weight'         => 0
            );
            $routes[$router] = $item;
        }
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
        foreach( $routes as $router => $item ) {
            $this->_db->query("UPDATE {routes_permission} SET description = '%s' WHERE router='%s'", $item['router']);
            if ( !$this->_db->affectedRows() ) {
                $this->_db->query("INSERT INTO {routes_permission} ( router, description) VALUES('%s', '%s')", $router, $item['permission']);
            }
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
        foreach( $routes as $item ) {
            $this->_db->query("UPDATE {router_links} SET link_type='%s', title='%s', options='%s', postion_type=%d, parent_router='%s' WHERE router='%s' AND module='%s'",
                                'navigation', $item['title'], serialize(array('description' => $item['description'])) , $item['type'], $item['parent_router'], $item['router'], $item['module'] );
            if ( !$this->_db->affectedRows() ) {
                $this->_db->query("INSERT INTO {router_links} (link_type,router,module,parent_router,postion_type,title,options,weight) VALUES('%s','%s','%s','%s',%d,'%s','%s',%d)",
                              'navigation',
                              $item['router'],
                              $item['module'],
                              $item['parent_router'],
                              $item['type'],
                              $item['title'],
                              serialize(array('description' => $item['description'])),
                              $item['weight']
                              );
            }
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
        $this->_template = Template::getInstance();
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
     * Return a component of the current inter path.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param NULL
     * @access public static
     */
    public static function arg($index = null) {
        static $args = null;
        if ( !isset($args) ) {
            $args = explode('/', Router::getInstance()->_normal_path);
        }
        if ( isset($index) ) {
            return isset($args[$index]) ? $args[$index] : false;
        }
        return $args;
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
        $menu_routes = array();
        $this->_db->query("SELECT * FROM {routes} WHERE count_parts=%d ORDER BY weight ASC", count(explode('/', $this->_normal_path )));
        while($item = $this->_db->fetchArray()) {
            $menu_routes[$item['router']] = $item;
        }
        foreach( $this->_iPathRoutes as $_router ) {
            if ( isset( $menu_routes[$_router] ) ) {
                $this->_router_handle = $menu_routes[$_router];
                break;
            }
        }
        if( !$this->_router_handle ) {
            exit("404 not found!\n");
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
     * execute web request
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    public function execute() {
        //ivoke all modules init hook
        module::invokeAll('init');
        if ( is_admin_page() ) {
            $this->_adminExecute();
        } else {
            $this->_frontExecute();
        }
        return $this;
    }

    /**
     +------------------------------------------------------------------------------
     * Helper for execute to im admin
     *
     * execute web request with admin page
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    private function _adminExecute() {
        $this->_setRequestTrail();
        //print_r($this->_request_trail);
        //$this->rebuildMenuRouters();
        $this->_setNavigationLinks();
        $this->_routerCallbackPageHandle();
        //print_r($this->_menuRouterBuild);
    }

    /**
     +------------------------------------------------------------------------------
     * Helper for execute to im front
     *
     * execute web request with front page
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     */
    public function _frontExecute() {
        //
    }

    /**
     +------------------------------------------------------------------------------
     * get all trail links menu for current path
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param a path string
     *
     * @access private
     */
    private function _setRequestTrail($parent_router = null) {
        //array_unshift($this->_request_trail,)
        if ( empty($this->_request_trail) && $this->_router_handle) {
            $this->_request_trail = array($this->_router_handle['router']);
        }
        $this->_db->query("SELECT parent_router FROM {routes} WHERE router='%s' ORDER BY weight ASC", $parent_router ? $parent_router : $this->_router_handle['router']);
        if( $parent_router = $this->_db->getResult() ) {
            array_unshift($this->_request_trail,$parent_router);
            $this->_setRequestTrail( $parent_router );
        }
    }

    /**
     +------------------------------------------------------------------------------
     * Generated menu structure
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access private
     */
    private function _setNavigationLinks() {
        $navigation = array();
        $this->_db->query("SELECT * FROM {router_links} WHERE hidden=0 AND link_type='navigation' AND postion_type=%d ORDER BY weight ASC", ADMIN_MAIN_MENU);
        while($item = $this->_db->fetchObject()) {
            $optins = unserialize( $item->options );
            $navigation['ADMIN_MAIN_MENU'][] = array(
                'title'       => $item->title,
                'options'     => array(
                    'title' => $optins['description'],
                    'href' => base_path().$item->router
                )
            );
        }
        $this->_db->query("SELECT * FROM {router_links} WHERE hidden=0 AND link_type='navigation' AND postion_type=%d AND parent_router='%s' ORDER BY weight ASC", ADMIN_SUB_MENU, $this->_request_trail[0] );
        while($item = $this->_db->fetchObject()) {
            $optins = unserialize( $item->options );
            $navigation['ADMIN_SUB_MENU'][] = array(
                'title'    => $item->title,
                'options'  => array(
                     'title' => $optins['description'],
                     'href'  => base_path().$item->router
                  )
             );
        }
        $this->_db->query("SELECT * FROM {router_links} WHERE hidden=0 AND link_type='navigation' AND postion_type=%d AND parent_router='%s' AND router NOT LIKE '%%\\%%%%' ORDER BY weight ASC", ADMIN_TAB_MENU, isset($this->_request_trail[1]) ? $this->_request_trail[1] : $this->_request_trail[0]);
        while($item = $this->_db->fetchObject()) {
            $optins = unserialize( $item->options );
            $navigation['ADMIN_TAB_MENU'][] = array(
                'title'       => $item->title,
                'options'     => array(
                    'title' => $optins['description'],
                    'href'  => base_path().$item->router
                )
            );
        }
        $this->_navigation = $navigation + array(
                        'ADMIN_MAIN_MENU' => '',
                        'ADMIN_SUB_MENU' => '',
                        'ADMIN_TAB_MENU' => '',
                    );
    }

    /**
     +------------------------------------------------------------------------------
     * Generated router callback data
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access private
     */
    private function _routerCallbackPageHandle() {
        $page_callback = '';
        if ( isset($this->_router_handle['title']) ) {
            inter_set_title($this->_router_handle['title']);
        }
        if ( !empty($this->_router_handle['function']) ) {
            $function = $this->_router_handle['function'];
            if( !empty($this->_router_handle['function_args']) ) {
                $args = unserialize($this->_router_handle['function_args']);
            } else {
                $args = array();
            }
            $instance = module::instance( $this->_router_handle['module'] );
            if ( $instance && $instance->hasMethod( $function ) && $_hook = $instance->getMethod( $function ) ) {
                if ( $_hook->isStatic() ) {
                    $page_callback = $_hook->invokeArgs(null, $args);
                } else {
                    // generation a class instance
                    $module_instance = module::instance( $this->_router_handle['module'] )->newInstance();
                    $page_callback = $_hook->invokeArgs($module_instance, $args);
                }
             } else if ( function_exists( $function ) ) {
                return call_user_func_array( $function, $args );
             } else {
                exit("module {$this->_router_handle['module']} do't have methods {$function}! exit; \n");
             }
            //
        } else if ( !empty($this->_router_handle['template']) ) {
            $module = $this->_router_handle['module'];
            require_once $this->_router_handle['template'];
            $page_callback = isset($return) ? $return : '' ;
        } else {
            exit("module {$this->_router_handle['module']} do't have function or module");
        }
        $this->_page_callback = $page_callback;
    }

    /**
     +------------------------------------------------------------------------------
     * Render the template
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access private
     */
    public function render() {
        if ( is_admin_page() ) {
            $variables['ADMIN_MAIN_MENU'] = inter_navigation_build($this->_navigation['ADMIN_MAIN_MENU'], array('id' => 'main_navigation'));
            $variables['ADMIN_SUB_MENU']  = inter_navigation_build($this->_navigation['ADMIN_SUB_MENU'], array('id' => 'sub_navigation'));
            $variables['ADMIN_TAB_MENU']  = inter_navigation_build($this->_navigation['ADMIN_TAB_MENU'], array('id' => 'tabs_navigation'));
            //, array( '__JS__' => $this->_request_trail )
            $this->_template->set_path( theme_path() );
        } else {
            // not admin
            $variables = array();
        }
        module::invokeAll('renderPageBefore', $variables);
        $variables['PAGE_DESC']    = module::invoke($this->_router_handle['module'], 'pageDescription', $this->_router_handle['router'] );
        $variables['PAGE_CONTENT'] = $this->_page_callback;
        $variables['CHARSET']      = inter_html_get_charset();
        $variables['HEAD']         = inter_html_get_head();
        $variables['TITLE']        = inter_get_title();
        module::invokeAll('renderPageAfter', $variables);
        print $this->_template->set_vars($variables)->render('index.php');
    }
}//routes
