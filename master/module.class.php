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
 +----------------------------------------------------------------------------------
 * This class is important for the inter. Module handle and invok hook.
 * enable and disabled them.
 +----------------------------------------------------------------------------------
 * @package   inter.module
 * @version   $Id$
 * @access    static
 +----------------------------------------------------------------------------------
 */

class Module {
    /**
     * @private array
     *      collect all enabled module instance.
     */
    private $_enabled_modules = array();

    /**
     * @_db resource the system database layout handler
     */
    private $_db;
    
    /**
     * @_core inter core module the list need enable
     */
    private $_core_module = array();

    /**
     +------------------------------------------------------------------------------
     * get the current instance.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access public
     */
    public static function getInstance( ) {
        static $_flag = null;
        if ( !isset( $_flag ) ) {
            $_flag = new self();
        }
        return $_flag;
    }

    /**
     +------------------------------------------------------------------------------
     * the construct with phpVERsion < 5.0.0
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access public
     */
    public function interModuleHandle() {
        $this->__construct();
    }
    
    /**
     +------------------------------------------------------------------------------
     * 
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access public
     */
    public function __construct() {
        global $db_handle;
        $this->_db = $db_handle;
        $this->_enabled_modules = $this->getAllModules();
    }
    
    /**
     +------------------------------------------------------------------------------
     * the construct with phpVERsion < 5.0.0
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param null
     * @access public
     */
    public function getAllModules( $enabled = true ) {
        $this->_db->query("SELECT * FROM {core} WHERE status = %d", $enabled );
    }
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     +------------------------------------------------------------------------------
     * Enable the named module
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $modules string or modules list as array()
     * @access public
     */
    public function enable( $modules ) {
        if( is_string( $modulers ) ) {
            $modules = func_get_args();
        }
        $this->_moduleManage('enable', $modules );
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
    public function disable( $modulers ) {
        if( is_string( $modulers ) ) {
            $modules = func_get_args();
        }
        $this->_moduleManage('disable', $modules );
    }
    
    
    /**
     +------------------------------------------------------------------------------
     * Helper enable and disable function
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $module string or modules list as array()
     * @access private
     */
    private function _moduleManage( $action ) {
        switch ( $action ) {
            case 'enable':
            
            break;
            case 'disable':
            
            break;
            default :
            
        }
    }
    
    
}