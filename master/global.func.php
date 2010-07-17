<?php
// +----------------------------------------------------------------------
// |  [ WE CAN DO IT JUST programmer ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.hongrs.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: lan_chi <lan_chi@163.com>
// +----------------------------------------------------------------------
// $Id$

/**
 * @param null
 */
function unset_global_variable() {
    if ( !ini_get('register_globals') ) return ;

    $use = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

    //$queue = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

    foreach( $GLOBALS as $key=>$value ) {
        if ( !in_array($key, $use) ) {
            unset($GLOBALS[$key]);
        }
    }
}
/**
 * @params string
 * @return string
 */
function inter_join_path( $args ) {
    if ( !is_array( $args ) ) {
        $args = func_get_args();
    }
    return str_replace( DS.DS, DS, implode(DS, $args) );
}

/**
 * to parse the databases config, use array to define new db link
 */
function inter_parse_db_config( $db_config = NULL ) {
    if ( !isset( $db_config ) ) {
        global $db_config;
    }
    $_db_config = array();
    if ( !is_array( $db_config ) ) {
        $_db_config = parse_url( $db_config );
        list(, $_db_config['database'], $_db_config['prefix'], $_db_config['encoding']) = explode( '/', $_db_config['path'] );
    } else {
        $_db_config = $db_config;
    }
    return $_db_config;
}

/**
 * @return request IP
 */
function inter_get_ip() {
    static $ip;
    if ( isset($ip) ) return $ip;
    if ( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip != false) {
            array_unshift($ips,$ip);
            $ip = false;
        }
        $count = count($ips);
        // Exclude IP addresses that are reserved for LANs
        for ($i = 0; $i < $count; $i++) {
            if ( !preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i]) ) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    if ( false == $ip && isset($_SERVER['REMOTE_ADDR']) )
        $ip = $_SERVER['REMOTE_ADDR'];
    return $ip;
}
/**
 * Create a Anonymous user data here.
 */
function inter_init_anonymous_user( $session = '' ) {
    $user = new stdClass();
    $user->uid = 0;
    $user->hostname = inter_get_ip();
    $user->roles =array();
    $user->data = $session; // the sessions data
    $user->cache = 0;
    return $user;
}
/**
 * To Operate {options}
 * get the $name with {options}
 */
function options_get( $name, $default ) {
    global $config;
    return isset($config[$name]) ? $config[$name] : $default;
}
/**
 * To Operate {options}
 * set the $name value
 * @param
 *      $name string name primary key
 *      $value mix   will to serialize
 *      $status int Whether the automatic loading,default 1
 */
function options_set( $name, $value ) {
    global $config, $db_handle;
    $_value = serialize( $value );
    if ( isset($config[$name] ) ) {
        $db_handle->query("UPDATE {options} SET value = '%s' WHERE name = '%s'", $_value, $name );
    } //TODO fix mysql update sql. not affectedRows! $db_handle->affectedRows()
    else {
       $db_handle->query("INSERT INTO {options} (name, value) VALUES('%s', '%s')", $name, $_value );
    }
    $config[$name] = $value;
}

/**
 * Delete the named variable
 */
function options_del( $name ) {
    global $config, $db_handle;
    $db_handle->query("DELETE {options} FROM WHERE name = '%s'" , $name );
    unset($config[$name]);
}

/**
 * Init the global $config
 */
function options_init() {
    global $config, $db_handle;
    $_handle = $db_handle->query("SELECT * FROM {options}");
    while( $obj = $db_handle->fetchObject( $_handle ) ) {
        $config[$obj->name] = unserialize( $obj->value );
    }
}

/**
 * Develop tool, collect time begin ,stop, end time statistics
 *   @param string $name 
 *      the timer named.
 *   @param string $action options:
 *       'set'   start set a time record.
 *       'get'   get the record of named by default.
 *       'stop'  sotp the record. clear the named.
 *       'all'   get all data with $timer. 
 *       'clear' clear all timer if not named.
 */
function inter_timer( $name, $action = 'get' ) {
    static $timer = array();
    if ( null == $name && $action == 'clear' ) {
        $timer = array(); // reset the timer
        return true;
    } else {
        list($time, $_time) = explode(' ', microtime());
        switch( $action ) {
            case 'set':
                if ( !isset($timer[$name]) ) {
                    $timer[$name]['start'] = $time + $_time;
                    $timer[$name]['count'] = 1;
                } else {
                    $timer[$name]['count']++;
                }
                break;
            case 'get':
                if ( $timer[$name] ) {
                    return bcsub($time + $_time, $timer[$name]['start'], 5);
                }
                break;
            case 'stop':
                if ( !isset($timer[$name]) ) {
                    return false;
                }
                unset($timer[$name]);
                break;
            case 'count':
                if ( isset($timer[$name]) ) {
                    return $timer[$name]['count'];
                }
                return false;
            case 'all':
                return $timer; // return all timer with 'all' as array();
            default :
                return false;
        }
    }
    return true;
}

/**
 * Clear all timer with static inter_timer()
 *  @params null
 */
function inter_timer_clear() {
    inter_timer( null, 'clear' );
}



/**
 * dev test
 */
function import_sql() {
    $data = file_get_contents(ROOT.'data.sql');
    $l = interCoreDatabase::getInstance();
    if( !$l->query($data) ) {
        echo '===data.sql error!';
    }else{
        echo '===data.sql OK!';
    }
}

/**
 *  dev init global a admin user
 *
 */
function init_user($session = array()) {
    global $user;
    //session_destroy();
    $user = new stdClass();
    $user->uid = 1;
    $user->hostname = inter_get_ip();
    $user->roles =array();
    $user->data = $session; // the sessions data
    $user->cache = 0;
    //$_SESSION['y'] = array('a','b','c');
}



