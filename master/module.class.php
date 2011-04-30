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

// include the core hook system
require_once inter_join_path( MASTER, 'hook.class.php' );

/**
 +----------------------------------------------------------------------------------
 * This class is important for the inter. Module handle and invok hook.
 * enable and disabled them. This module is a static class.
 +----------------------------------------------------------------------------------
 * @package   inter.module
 * @version   $Id$
 * @access    static
 +----------------------------------------------------------------------------------
 */

class Module {
    /**
     * @var the hook class handle
     */
    public static $hook;

    /**
     +------------------------------------------------------------------------------
     * init the module class
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access public
     */
    public static function init () {
        self::$hook = hook::getInstance();
    }

    /**
     +------------------------------------------------------------------------------
     * Enable the named module, befor need install
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $modules string or modules list as array()
     * @access public
     */
    public static function enable( $modules ) {
        if( is_string( $modulers ) ) {
            $modules = func_get_args();
        }
        self::_moduleManage('enable', $modules );
    }

    /**
     +------------------------------------------------------------------------------
     * Disable the named module
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $modules string or modules list as array()
     * @access public
     */
    public static function disable( $modulers ) {
        if( is_string( $modulers ) ) {
            $modules = func_get_args();
        }
        self::_moduleManage('disable', $modules );
    }

    /**
     +------------------------------------------------------------------------------
     * install the named module
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $modules string or modules list as array()
     * @access public
     */
    public static function install( $modulers ) {
        if( is_string( $modulers ) ) {
            $modules = func_get_args();
        }
        self::_moduleManage('disable', $modules );
    }

    /**
     +------------------------------------------------------------------------------
     * uninstall the named module
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $modules string or modules list as array()
     * @access public
     */
    public static function uninstall( $modulers ) {
        if( is_string( $modulers ) ) {
            $modules = func_get_args();
        }
        self::_moduleManage('disable', $modules );
    }

    /**
     +------------------------------------------------------------------------------
     * Invoke a hook in all enabled modules that implement it.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $hook The name of the hook to invoke.
     *              ... Arguments to pass to the hook.
     * @access public
     */
    public static function invoke( $module , $hook ) {
        $args = func_get_args();
        return self::_moduleManage('invoke', $args);
    }

    /**
     +------------------------------------------------------------------------------
     * Invoke a hook with a enabled modules that implement it.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $hook The name of the hook to invoke.
     *              ... Arguments to pass to the hook.
     * @access public
     */
    public static function invokeAll( $hook ) {
        $args = func_get_args();
        return self::_moduleManage('invokeAll', $args );
    }

    /**
     +------------------------------------------------------------------------------
     * get enable modules name as array
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  null
     * @access public
     * @return array
     */
    public static function listModule( $reload = false ) {
        return self::_moduleManage('listModule', $reload);
    }

    /**
     +------------------------------------------------------------------------------
     * get enable modules name as array
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  null
     * @access public
     * @return array
     */
    public static function implementer( $hook, $reload = false ) {
        return self::$hook->implementer( $hook, $reload = false );
    }

    /**
     +------------------------------------------------------------------------------
     * get enable modules name as array
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  null
     * @access public
     * @return array
     */
    public static function getPath( $module ) {
        return self::$hook->getPathBy( $module );
    }

    /**
     +------------------------------------------------------------------------------
     * check named module exists
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $mosule module name
     * @param  $hook   
     * @access public
     * @return boole true if $hook is exists
     */
    public static function hookExists( $module, $hook ) {
        return self::$hook->moduleHookExists( $module, $hook );
    }

    /**
     +------------------------------------------------------------------------------
     * get the named module Reflection instance 
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module the named module
     * @access public
     * @return object the instance
     */
    public static function instance( $module ) {
        return self::$hook->getModuleInstance( $module );
    }

    /**
     +------------------------------------------------------------------------------
     * Helper module class action
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $module string or modules list as array()
     * @access private
     */
    private static function _moduleManage( $action, $_args = NULL ) {
        //self::$hook
        $args = func_get_args();
        array_shift( $args );
        switch ( $action ) {
            case 'enable':
                //
                break;
            case 'disable':
                //
                break;
            case 'install':
                //
                break;
            case 'uninstall':
                //
                break;
            case 'invoke':
                return call_user_func_array(array(self::$hook, 'invoke'), $_args );
            case 'invokeAll':
                //return array
                return call_user_func_array(array(self::$hook, 'invokeAll'), $_args );
            case 'listModule':
                return self::$hook->listModule( array_shift( $args ) );
                break;
            default :
                return false;
        }
    }
}