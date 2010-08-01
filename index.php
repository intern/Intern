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
echo "\n",$_GET['p'],"\n";

ini_set('display_errors','on');

error_reporting(1);

$start = memory_get_usage();

require_once 'master/bootstrap.php';

$new = interBootstrap::getInstance( INTER_INIT_PATH );

$end = memory_get_usage();

echo "\n",$end - $start,"\n";