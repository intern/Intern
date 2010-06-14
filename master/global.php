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
    if( !ini_get('register_globals') ) return ;

    $use = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

    //$queue = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

    foreach( $GLOBALS as $key=>$value ) {
        if( !in_array($key, $use) ){
            unset($GLOBALS[$key]);
        }
    }
}
/**
 * @params string
 * @return string
 */
function inter_join_path( $args ) {
    if( !is_array( $args ) ) {
        $args = func_get_args();
    }
    return implode(DS, $args);
}

/**
 *
 */
function inter_parse_db_config( $db_config = NULL ) {
    if( !isset( $db_config ) ) {
        global $db_config, $db_prefix;
    }
    $_db_config = array();
    if( !is_array( $db_config ) ) {
        $_db_config = parse_url( $db_config );
        list(, $_db_config['database'], $_db_config['prefix']) = explode( '/', $_db_config['path'] );
    }else{
        $_db_config = $db_config;
    }
    return $_db_config;
}