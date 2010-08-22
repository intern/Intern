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
 +------------------------------------------------------------------------------
 * Database abstract layout Implement,See Usage with the core db.mysql.php
 +------------------------------------------------------------------------------
 * @package   inter.database
 * @version   $Id$
 * @abstract  database
 +------------------------------------------------------------------------------
 */
class mysql extends databaseAbstract{
    /**
     +------------------------------------------------------------------------------
     * blob data conversion
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access protected
     * @param array() $db_config the class param, only array
     * @return NULL
     */
    protected function  _encodeBlob( $data ) {
        return "'". mysql_real_escape_string($data, $this->_link ) ."'";
    }

    /**
     +------------------------------------------------------------------------------
     * Prepares the database layout link info, Merge$_config
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract protected
     * @param array() $db_config the class param, only array
     * @return NULL
     */
    protected function prepare_connect( $db_config ) {
        //
        !extension_loaded('mysql') && exit("Please Open the mysql,DLL");

        $this->_config = array_merge($this->_config, $db_config);
    }

    public function setDatabaseEncode( $db_encode = NULL ) {
        $db_encode = empty( $db_encode ) ? $this->_config['encoding'] : $db_encode;
        $this->query('SET NAMES "'.$db_encode.'"');
    }
    /**
     +------------------------------------------------------------------------------
     * init to connect database use $_config, and handle the $_link
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract protected
     * @param NULL
     * @return NULL
     */
    protected function connect() {
        $link = @mysql_connect( $this->_config['host'],
                                $this->_config['user'],
                                $this->_config['pass'],
                                false,
                                2 // CLIENT_FOUND_ROWS, fix update same res not return 1
                                 );
        if( $link ) {
            $this->_link = $link;
        }
    }

    /**
     +------------------------------------------------------------------------------
     * Select the db to use if $_link
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @param NULL
     * @return NULL
     */
    public function selectDatabase() {
        @mysql_select_db( $this->_config['database'], $this->_link ) or die( $this->getError() );
    }

    /**
     +------------------------------------------------------------------------------
     * get a query error message
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return string
     */
    public function getError() {
        return mysql_error( $this->_link );
    }

    /**
     +------------------------------------------------------------------------------
     * Get the connect db version
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @params NULL
     * @return string database Version
     */
    public function version() {
        list($version) = explode('-', mysql_get_server_info($this->_link));
        return $version;
    }

    /**
     +------------------------------------------------------------------------------
     * Exec the query
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @params $query A string containing an SQL query.
     * @return A database query result resource, or FALSE if the query was not
     *         executed correctly.
     */
    protected function _query( $query ) {
        $db_result = @mysql_query($query, $this->_link);
        if( !mysql_errno( $this->_link ) ) {
            $this->_query_handle = $db_result;
            return $db_result;
        }
        exit($this->getError());
    }

    /**
     +------------------------------------------------------------------------------
     * Get the ID generated in the last query
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @params NULL
     * @return int insert mysql AUTO_INCREMENT value
     */
    public function getLastInsertId() {
        return mysql_insert_id();
    }

    /**
     +------------------------------------------------------------------------------
     * Get the query Records as array,one Records
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @see   this function fetchAllToArray
     * @access abstract public
     * @params NULL
     * @return array
     */
    public function fetchArray( $result = NULL ) {
        $result = isset ( $result ) ? $result : $this->_query_handle;
        return mysql_fetch_array( $result, MYSQL_ASSOC/*MYSQL_BOTH & MYSQL_NUM*/);
    }

    /**
     +------------------------------------------------------------------------------
     * Get the query Records as object,one Records
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @see   this function fetchAllToObject
     * @access abstract public
     * @params NULL
     * @return object
     */
    public function fetchObject( $result = NULL ) {
        $result = isset ( $result ) ? $result : $this->_query_handle;
        return mysql_fetch_object($result);
    }

    /**
     +------------------------------------------------------------------------------
     * Clean up this object
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @params NULL
     * @return NULL
     */
    public function __destruct() {
       if( $this->_debug ) {
            // TODO debug
            print_r($this->_debug_info);
        }
        $this->free();
        isset($this->_link) && (mysql_close($this->_link) && ($this->_link = NULL));
    }
    /**
     +------------------------------------------------------------------------------
     * Clean up this object
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @params NULL
     * @return NULL
     */
    public function getResult( $result = NULL ) {
        $_result = isset($result) ? $result : $this->_query_handle;
        if ($_result && mysql_num_rows($_result) > 0) {
        // The mysql_fetch_row function has an optional second parameter $row
        // but that can't be used for compatibility with Oracle, DB2, etc.
           $array = $this->fetchArray($_result);
           print_R($array);
           return current($array);
        }
        echo 'asd';
        return FALSE;
    }
    /**
     +------------------------------------------------------------------------------
     * echo Object info [Magic func]
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return NULL
     */
    public function __toString(){}

    /**
     +------------------------------------------------------------------------------
     * get the all tables info
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return array all tables list
     */
    public function getTables( $dbName = NULL ) {
        if(!empty($dbName)) {
            $sql    = 'SHOW TABLES FROM '.$dbName;
        } else {
             $sql    = 'SHOW TABLES ';
        }
        $_tables = $this->fetchAllToArray($this->query( $sql ));
        array_walk($_tables,create_function('&$args', '$args = current($args);'));
        return  $_tables;
    }

    /**
     +------------------------------------------------------------------------------
     * get a table info with the Fields
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return array all Fields info list
     */
    public function getTableFieldsInfo( $table_name ) {
        $result =   $this->query('SHOW COLUMNS FROM {'.$table_name.'}');
        if($result && mysql_num_rows($result) > 0) {
            $info   =   array();
            foreach ( $this->fetchAllToArray( $result ) as $key => $val) {
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''),//not null is empty,null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
            return $info;
        }
        return false;
    }

    /**
     * To fetch the affected Rows
     */
    public function affectedRows() {
        return mysql_affected_rows( $this->_link );
    }

    /**
     * Close up the $_link
     */
    public function close() {
        $this->__destruct();
    }

    /**
     * Free a query result
     */
    public function free() {
        is_resource($this->_query_handle) && (mysql_free_result($this->_query_handle)
          && ($this->_query_handle = NULL));
    }
}