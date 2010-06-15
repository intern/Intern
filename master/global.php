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
    return implode(DS, $args);
}

/**
 *
 */
function inter_parse_db_config( $db_config = NULL ) {
    if ( !isset( $db_config ) ) {
        global $db_config;
    }
    $_db_config = array();
    if ( !is_array( $db_config ) ) {
        $_db_config = parse_url( $db_config );
        list(, $_db_config['database'], $_db_config['prefix']) = explode( '/', $_db_config['path'] );
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