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

require_once MASTER.'db.interface.php';

/**
 +------------------------------------------------------------------------------
 * Factory for database
 +------------------------------------------------------------------------------
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class db {
    public function __construct() {
    }
    /**
     +------------------------------------------------------------------------------
     * Factory for database
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     */
    public static function getInstance( $conf = NULL ) {
        static $_db_instance;
        if( is_object( $_db_instance ) ) return $_db_instance;
        $_db_config = inter_parse_db_config( $conf );
        if( !isset( $_db_config['scheme'] ) || !file_exists( MASTER.'db.'.$_db_config['scheme'].'.php' )) {
            die( 'Error: Db layout file "'.MASTER.'db.'.$_db_config['scheme'].'.php" not found!' );
        }else{
            require_once MASTER.'db.'.$_db_config['scheme'].'.php';
        }
        $_db_instance = & new Mysql( $_db_config );
        return $_db_instance;
    }
}