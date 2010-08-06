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
 * Developer settings
 */

// deubug
define('DEBUG', true);

/**
 * Developer setting end
 */




/**
 * global Group define
 */
// Define DIRECTORY_SEPARATOR to DS
define('DS', DIRECTORY_SEPARATOR);

// Define ROOT as this files root directory
define('ROOT', dirname(dirname(__FILE__)) . DS );

// Define MASTER as this files master directory
define('MASTER', ROOT . 'master' . DS );

// Define SITES as this files master directory
define('SITES', ROOT . 'sites' . DS );

/**
 * global Group define end
 */


/**
 * To filter the global data, so unset unused global variable
 * @var CONSTANT int
 */
define('INTER_GLOBAL_FILTER', 1);

/**
 * Include the site config
 * @var CONSTANT int
 */
define('INTER_INITIALIZE_CONFIG', 2);

/**
 * Initialize database layout
 * @var CONSTANT int
 */
define('INTER_INITIALIZE_DATABASE', 3);

/**
 * Initialize session for database;
 * @author lan-chi
 */
define('INTER_INITIALIZE_SESSION', 4);

/**
 * Initialize module hooks layout.
 * 
 */
define('INTER_INIT_HOOK_LAYOUT', 5);
 
/**
 * Initialize get url for router;
 * @author lan-chi
 */
define('INTER_INIT_PATH', 6);


class interBootstrap {
    /**
     * save the boot type.
     * @static true
     * @var string
     */
    public static $_boot_type;

    /**
     * To bootstarp the inter
     * @param contant $boot_type see the top CONTANT
     */
    public static function getInstance( $boot_type ) {
        interBootstrap::$_boot_type = $boot_type;
        return new self;
    }
    
    /**
     *
     * @param unknown_type $type
     */
    private function bootstrap( $type ) {
        $types = array(INTER_GLOBAL_FILTER, INTER_INITIALIZE_CONFIG, INTER_INITIALIZE_DATABASE, INTER_INITIALIZE_SESSION, INTER_INIT_HOOK_LAYOUT, INTER_INIT_PATH);
        foreach( $types as $key => $value ) {
            if( $value > $type ) {
                return ;
            }
           $this->_bootstrap( $value );
        }
    }
    /**
     * helper for function bootstrap to boot
     * @param const $type @see the header
     */
    private function _bootstrap( $type ) {
        global $db_config, $base_url;
        switch( $type ) {
            case INTER_GLOBAL_FILTER:
                require_once MASTER . 'global.func.php';
                // To unset unused var
                unset_global_variable();
                break;
            case INTER_INITIALIZE_CONFIG:
                require_once SITES . 'site.config.php';
                break;
            case INTER_INITIALIZE_DATABASE:
                require_once MASTER . 'db.factory.php';
                interCoreDatabase::getInstance();
                break;
            case INTER_INITIALIZE_SESSION:
                require_once MASTER . 'session.php';
                $session_handle = interSessionDataHandle::getInstance();
                session_set_save_handler(array($session_handle, 'session_open'),
                                         array($session_handle, 'session_close'),
                                         array($session_handle, 'session_read'),
										 array($session_handle, 'session_write'),
                                         array($session_handle, 'session_destroy'),
                                         array($session_handle, 'session_gc')
                                         );
                // session start
                session_start();
                //session_destroy();
                //init the global $config options
                options_init();
                break;
            case INTER_INIT_HOOK_LAYOUT:
                init_user(); //To dev
                require_once MASTER . 'module.class.php';
                Module::init();
                break;
            case INTER_INIT_PATH:
                //require_once MASTER . 'path.func.php';
                break;
            default :
                echo 'error';
        }
    }
    
    /**
     * init the web start
     */
    public function __construct() {
        $this->bootstrap( interBootstrap::$_boot_type );
    }
    
    /**
     * Compatible
     * @TODO remove this if less !(VERSION < php5)
     */
    public function interBootstrap() {
        $this->__construct();
    }
}