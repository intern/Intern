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
 * Define the core module directory path
 */
define('CORE_NODULE_PATH', MASTER . 'modules' . DS );

/**
 * Define expand directory path
 */
define('EXPAND_NODULE_PATH', ROOT . 'modules' . DS );

/**
 * Define module class prefix
 */
define('MODULE_PREFIX', 'inter_' );

/**
 +----------------------------------------------------------------------------------
 * This is inter hooks system.
 * the all funcs or variables are static.
 +----------------------------------------------------------------------------------
 * @package   inter.hook
 * @version   $Id$
 * @access    Singleton
 +----------------------------------------------------------------------------------
 */
class hook {
    /**
     * @var $_core_modules inter core module the list need enable
     */
    private static $_core_modules = array();

    /**
     * @var $_load_modules inter plugin module the list name file
     */
    private $_load_modules = array();

    /**
     * @var $_load_module_files the loaded file list
     */
    private $_load_files = array();

    /**
     * The db layout handle
     * @var $_db
     */
    private $_db = NULL;

    /**
     * collect the module hook in this.
     * @var $_module_implementer
     */
    private $_module_implementer = array();

    /**
     * collect the not exists module
     * @var array
     */
    private $_not_exists_moudles = array();

    /**
     +------------------------------------------------------------------------------
     * hook system construct
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  null
     *
     * @access private
     */
    private function __construct() {
        $this->init();
    }

    /**
     +------------------------------------------------------------------------------
     * hook system construct
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  null
     *
     * @access private
     */
    private function hook() {
        $this->__construct();
    }

    /**
     +------------------------------------------------------------------------------
     * hook system construct
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     */
    private function init() {
        // init the db layout
        $this->_db = interCoreDatabase::getInstance();
        //load with the enabled module
        $this->listModule();
    }

    /**
     +------------------------------------------------------------------------------
     * the hook Singleton Pattern
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     */
    public static function getInstance() {
        static $hook_instance = null;
        if( !isset( $hook_instance ) ) {
            $hook_instance = new self;
        }
        return $hook_instance;
    }

    /**
     +------------------------------------------------------------------------------
     * check the named module exists in _load_modules
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *          will check module name
     * @access public
     *
     * @return boole
     *          true if the module is exists
     */
    public function moduleExists( $module ) {
        return isset($this->_load_modules[$module]);
    }

    /**
     +------------------------------------------------------------------------------
     * check the named module exists in _load_modules
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *          will check module name
     * @access public
     *
     * @return boole
     *          true if the module is exists
     */
    public function notExistsModule() {
        return $this->_not_exists_moudles;
    }

    /**
     +------------------------------------------------------------------------------
     * invoke the hook with all module reflection instance.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $hook string
     *               module hook
     * @access public
     */
    public function invokeAll( $hook ) {
        $_collect = array();
        $args = func_get_args();
        array_shift($args); //remove $hook variable with the $args
        foreach( $this->implementer( $hook ) as $module ) {
            $_hook = $this->_getModuleInstance( $module )->getMethod( $hook );
            if ( $_hook->isStatic() ) {
                $_collect[] = $_hook->invokeArgs(null, $args);
            } else {
                // generation a class instance
                $hook_instance = $this->_getModuleInstance( $module )->newInstance();
                $_collect[] = $_hook->invokeArgs($hook_instance, $args);
                //return ;
            }
            
        }
        return $_collect;
    }

    /**
     +------------------------------------------------------------------------------
     * invoke the hook with named module reflection instance.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *               module name
     * @param  $hook string
     *               module hook
     * @access public
     */
    public function invoke( $module , $hook ) {
        $args = func_get_args();
        array_shift($args); //remove $module variable
        array_shift($args); //remove $hook variable
        //ReflectionMethod
        
        if ( $this->moduleHookExists( $module , $hook ) ) {
            $_hook = $this->_getModuleInstance( $module )->getMethod( $hook );
            if ( $_hook->isStatic() ) {
                return $_hook->invokeArgs(null, $args);
            } else {
                // generation a class instance
                $hook_instance = $this->_getModuleInstance( $module )->newInstance();
                return $_hook->invokeArgs($hook_instance, $args);
            }
        }
        return false;
    }

    /**
     +------------------------------------------------------------------------------
     * load the named file.
     *  @code format
     *    array(
     *          'module' => array(
     *              'module_name' => true | false
     *          )
     *      );
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $type string
     *              the load module file type eg: 'module.module,module.info'
     * @param  $module string
     *              the will load module name
     * @access public
     */
    public function loadModule( $type, $module ) {
        if ( !isset( $this->_load_files[$type][$module] ) ) {
            foreach( array( CORE_NODULE_PATH, EXPAND_NODULE_PATH ) as $path) {
                //print_r(array(CORE_NODULE_PATH, EXPAND_NODULE_PATH));
                //$file = $path . $module . DS . $module . '.' . $type;
                $file = inter_join_path($path, $module, $module . '.' . $type);
                if( file_exists( $file ) ) {
                    @include_once $file;
                    $this->_load_files[$type][$type] = true;
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    /**
     +------------------------------------------------------------------------------
     *  get the named module path
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *              the load module path
     * @access public
     */
    public function getPathBy( $module ) {
        foreach( array( CORE_NODULE_PATH, EXPAND_NODULE_PATH ) as $path) {
            $module_dir = inter_join_path($path, $module);
            if( is_dir( $module_dir ) ) {
                return $module_dir;
            }
            return false;
        }
    }

    /**
     +------------------------------------------------------------------------------
     * list the enabled module.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $reload boole
     *                reload the module list if the value is true
     * @access public
     */
    public function listModule( $reload = false ) {
        $this->_db->query("SELECT * FROM {core} WHERE status = 1 ORDER BY weight ASC");
        while( $module = $this->_db->fetchObject() ) {
            // to setting the self var $_load_modules
            //print_r($module);
            $this->loadModule( 'module', $module->module );
            // load the module instance
            $this->_setModuleInstance( $module->module );
        }
    }

    /**
     +------------------------------------------------------------------------------
     * collect hook implementer with enabled module.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $reload bool
     *              the reload module list of enabled
     * @param  $module string
     *              the will load module name
     * @access public
     * @return the all implement hook's modules
     */
    public function implementer( $hook , $reload = false ) {
        if ( !isset( $this->_module_implementer[$hook] ) || $reload == true ) {
            foreach( $this->_load_modules as $module => $value ) {
                if ( $this->_load_modules[$module]['instance'] != false ) {
                    if ( $this->moduleHookExists($module, $hook) ) {
                        $this->_module_implementer[$hook][] =  $module;
                        //array_push($this->_module_implementer[$hook], $module);
                    }
                }
            }
        }
        return isset( $this->_module_implementer[$hook] ) ? $this->_module_implementer[$hook] : array();
    }

    /**
     +------------------------------------------------------------------------------
     * Check the named module exists hook.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *              the reload module name
     * @param  $module string
     *              the check hook name
     * @access public
     *
     * @return boole
     *              true if the hook exists in the named module
     */
    public function moduleHookExists( $module, $hook ) {
        if ( isset( $this->_load_modules[$module] ) && false != $this->_load_modules[$module]['instance'] ) {
            return $this->_load_modules[$module]['instance']->hasMethod( $hook );
        }
        return false;
    }

    /**
     +------------------------------------------------------------------------------
     * collect all module class with loaded modules.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $reload boole
     *              the reload module list of enabled
     * @param  $module string
     *              the will load module name
     * @access public
     */
    private function _setAllModuleInstance( $refresh = false ) {
        static $loaded = false;
        if( $refresh == true || $loaded == false ) {
            foreach( $this->_load_modules as $module => $value ) {
                $this->_setModuleInstance($module, $refresh);
            }
        }
    }

    /**
     +------------------------------------------------------------------------------
     * collect all module class with loaded modules.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $reload boole
     *              the reload module list of enabled
     * @param  $module string
     *              the will load module name
     * @access public
     * @TODO finish this
     */
    private function _getAllModuleInstance() {
        $this->_setAllModuleInstance( false );
    }

    /**
     +------------------------------------------------------------------------------
     * setting the collected all module class instance.
     * Reflection instance with the named module class.
     * will remove and add it to $this->_not_exists_moudles if the named module
     * class not exists
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *              setting named module
     * @param  $refresh boole
     *              refresh the module instance if true
     * @access private
     */
    private function _setModuleInstance( $module, $refresh = false ) {
        if ( !isset( $this->_load_modules[$module]['instance'] ) || true == $refresh ) {
            if( class_exists( MODULE_PREFIX . $module ) ) {
                $this->_load_modules[$module]['instance'] = new ReflectionClass( MODULE_PREFIX . $module );
            } else {
                array_push($this->_not_exists_moudles, $module);
                return false;
            }
        }
        return $this->_load_modules[$module]['instance'];
    }

    /**
     +------------------------------------------------------------------------------
     * get the named module instance.
     * Reflection instance with the named module class
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *              get named module
     * @access private
     */
    private function _getModuleInstance( $module ) {
        return $this->_setModuleInstance($module);
    }

    /**
     +------------------------------------------------------------------------------
     * get the named module instance.
     * Reflection instance with the named module class
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param  $module string
     *              module name
     * @access public
     */
    public function getModuleInstance( $module ) {
        return $this->_getModuleInstance( $module );
    }
}// end hooks system