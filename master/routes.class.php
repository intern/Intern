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
 * The maximum number of path elements for a menu callback
 */
define('MENU_MAX_PARTS', 8);

/**
 * To ruter the web def group start.
 * init with Singleton for path
 */
class routes {
    /**
     * @var private To save the db handle instance
     */
    private  $_db;

    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    private function __construct() {
        $this->_db = interCoreDatabase::getInstance();
    }

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
     * @access private
     */
    private function _menuRouterBuild( $routes ) {
        $hook_menu = array();
        foreach($routes as $module => $item) {
            $count_part = explode('/', $path, MENU_MAX_PARTS);
        }
    }
}//menu