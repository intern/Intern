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
ini_set('display_errors',1);
//error_reporting(1);
$start = memory_get_usage();

require_once 'master/bootstrap.php';

$new = interBootstrap::getInstance( INTER_INITIALIZE_SESSION );

$end = memory_get_usage();

echo "\n",$end - $start,"\n";