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

// Open debug info falg
define('DEBUG', true);

require_once 'master/bootstrap.php';

/**
 * Init the intern Environment
 *  @param constants list:
 *  INTERN_GLOBAL_FUNCTIONS
 *  INTERN_GLOBAL_LOGGER
 *  INTERN_INITIALIZE_CONFIG
 *  INTERN_INITIALIZE_DATABASE
 *  INTERN_INITIALIZE_SESSION
 *  INTERN_INITIALIZE_HOOKS_LAYOUT
 *  INTERN_INITIALIZE_CACHES_LAYOUT
 */
internBootstrap::getInstance( INTERN_INITIALIZE_PATH );
