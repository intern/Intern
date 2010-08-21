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

ini_set('display_errors','on');

error_reporting( E_ALL );

$start = memory_get_usage();

require_once 'master/bootstrap.php';

interBootstrap::getInstance( INTER_INIT_PATH_AND_CACHE );

$handle = Router::getInstance()->runByRouterHandle();

//print_r($GLOBALS);

//Cache::set('test', Array('a',2,3,4,5,56), 800, CACHE_D);

//print_r(Cache::get('a555', CACHE_D));

//print_r(path::getInstance()->getMenuArray());



$end = memory_get_usage();

echo "\nmem:",$end - $start,"\n";
echo "\ntime:",inter_timer('dev'),"\n";