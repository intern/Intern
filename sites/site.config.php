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
 * Database URL format:
 *   $db_config = 'mysql://username:password@localhost/databasename/prefix/encoding';
 *   $db_config = 'mysqli://username:password@localhost/databasename/prefix/encoding';
 *   $db_config = 'pgsql://username:password@localhost/databasename/prefix/encoding';
 * Else
 *   array(
 *       'scheme'   => mysql,
 *       'host'     => localhost,
 *       'user'     => root,
 *       'pass'     => 123456,
 *       'path'     => inter,
 *       'prefix'   => inter,
 *       'encoding' => utf-8
 *   );
 */
$db_config = 'mysql://root:123456@localhost/intern/inter_/utf8';

/**
 * Inter web setting:
 *
 *  To setting the web base url.
 *  will create by defaule if not defined
 *    eg: http://www.example.com/
 *        http://www.example.com/folder/
 */
$base_url = 'http://192.168.1.7/intercms/';

/**
 * the default cache type
 *   options:
 *              file | database | memory (default)
 */
$cache_type = 'memory';

/**
 * PHP settings:
 *
 * To see what PHP settings are possible, including whether they can
 * be set at runtime (ie., when ini_set() occurs), read the PHP
 * documentation at http://www.php.net/manual/en/ini.php#ini.list
 * and take a look at the .htaccess file to see which non-runtime
 * settings are used there. Settings defined here should not be
 * duplicated there so as to avoid conflict issues.
 */
ini_set('arg_separator.output',     '&amp;');
ini_set('magic_quotes_runtime',     0);
ini_set('magic_quotes_sybase',      0);
ini_set('session.cache_expire',     200000);
ini_set('session.cache_limiter',    'none');
ini_set('session.cookie_lifetime',  2000000);
ini_set('session.gc_maxlifetime',   200000);
ini_set('session.save_handler',     'user');  // @see session_module_name
ini_set('session.use_cookies',      1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid',    0);
ini_set('url_rewriter.tags',        '');
