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
 * To ruter the web def group start.
 * init with inter_path_init()
 */
function path() {
    $a = 'path1';
    $b = 'path2';
    $c = 'path3';
    $d = 'path4';
    $e = 'path5';
    $f = 'path6';
    $g = 'path7';
    $h = 'path8';
    include_once MASTER.'routes'.DS.'path.routes.8.inc';
    print_r($routes);
}
path();
class path {
    /**
     * @var private the path variable, can't update the value.
     */
    private $_path = '';
    
    /**
     * @var public the inter system(module routes) internal path url
     */
    public $internal_path = '';
    
    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    private function __construct() {}

    /**
     +------------------------------------------------------------------------------
     * php4 construct
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access private
     */
    private function path() {
        $this->__construct();
    }
    
    /**
     +------------------------------------------------------------------------------
     * init the url parse
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     */
    public static function getInstance() {
        
    }
}