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
 * To init the config data
 * @var unknown_type
 */
define('INTER_BOOT_ENV', 0);


function inter_bootstrap( $type = 1 ) {
    static $types = array(INTER_BOOT_ENV);
    foreach( $types as $key => $value ) {

    _inter_bootstrap();
    }
}

