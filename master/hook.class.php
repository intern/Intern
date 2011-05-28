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
define('CORE_MODULE_PATH', MASTER . 'modules' . DS );

/**
 * Define expand directory path
 */
define('EXPAND_MODULE_PATH', ROOT . 'modules' . DS );

/**
 * Define module class prefix
 */
define('MODULE_PREFIX', 'intern_' );

/**
 +----------------------------------------------------------------------------------
 * This is intern hooks system.
 * the all funcs or variables are static.
 +----------------------------------------------------------------------------------
 * @package   intern.hook
 * @version   $Id$
 * @access    Singleton
 +----------------------------------------------------------------------------------
 */
class hook {
    /**
     * @var $_core_modules intern core module the list need enable
     */
    private static $_core_modules = array();

    /**
     * @var $_load_modules intern plugin module the list name file
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
        $this->_db = internCoreDatabase::getInstance();
        //load with the enabled module
        $this->loadModules();
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
     * check the named module exists in _load_modules and loaded
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
        return isset($this->_load_files[$module]) && !isset($this->_not_exists_moudles[$_module]);
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
    public function notExistsModule($module) {
        return !$this->moduleExists($module);
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
     * @param  $module string
     *              the will load module object form database
     * @access private
     */
    private function _loadModule( $module ) {
        $_module = $module->module;
        if ( !isset($this->_load_files[$_module]) || !isset($this->_load_files[$_module]['module']) || $this->_load_files[$_module]['module']) {
            $module_path = ROOT . $module->filepath;
            if (file_exists($module_path)) {
                @include_once $module_path;
                if (isset($this->_not_exists_moudles[$_module])) {
                    unset($this->_not_exists_moudles[$_module]);
                }
                $this->_load_files[$_module]['module'] = true;
                return true;
            }
            $this->_load_files[$_module]['module'] = false;
            $this->_not_exists_moudles[$_module] = array(
                'type'     => isset($module->type) ?  $module->type : 'user',
                'filepath' => $module->filepath
                );
            return false;
        }
        return true;
    }

    /**
     +------------------------------------------------------------------------------
     * load all enabled module form database.
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param $reload boole
     *                reload the module list if the value is true
     * @access public
     */
    public function loadModules( $reload = false ) {
        $this->_db->query("SELECT * FROM {core} WHERE status = 1 ORDER BY weight ASC");
        while( $module = $this->_db->fetchObject() ) {
            // to setting the self var $_load_files
            $this->_loadModule($module, $reload);
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
            foreach( $this->_load_files as $module => $value ) {
                if ($this->moduleHookExists($module, $hook)) {
                    $this->_module_implementer[$hook][] = $module;
                }
            }
        }
        return isset($this->_module_implementer[$hook]) ? $this->_module_implementer[$hook] : array();
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
            $function = $module . '_' . $hook;
            $_collect[] = call_user_func_array($function, $args);
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

        if ( $this->moduleHookExists( $module , $hook ) ) {
            return call_user_func_array($module . '_' . $hook, $args);
        }
        return false;
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
        return !isset($this->_not_exists_moudles[$module]) && function_exists($module . '_' .$hook);
    }

}// end hooks system
