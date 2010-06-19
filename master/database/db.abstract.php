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
 +------------------------------------------------------------------------------
 * Database abstract layout Implement
 +------------------------------------------------------------------------------
 * @package   database
 * @version   $Id$
 * @abstract  database
 +------------------------------------------------------------------------------
 */
abstract class databaseAbstract {
    /**
     * Debug if constant is true
     */
    protected $_debug = DEBUG;

    /**
     * Save the debug info
     */
    public $_debug_info = array();

    /**
     * Save the database link handle
     * @var    Resources
     * @access protected
     */
    protected $_link = null;

    /**
     * the current query handle
     * @var    Resources
     * @access protected
     */
    protected $_query_handle = null;

    /**
     * Link config
     *    see default var
     *     'host' => 'databses_addr',
     *     'user' => 'db_user_name',
     *     'pass' => 'db_password',
     *     'database' => 'db_name',
     *     'prefix' => 'table_prefix',
     *     'port'    => 'db_port'
     * @access protected
     */
    protected $_config = array(
                         'host'    => '127.0.0.1',
                         'user'    => 'root',
                         'pass'    => '',
                         'port'    => 3306, //default mysql port
                         'database'=> 'inter',
                         'prefix'  => '_',
                         'encoding'=> 'utf8'
                         );

    /**
     +------------------------------------------------------------------------------
     * databse construct [Magic func]
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param array() db config info
     * @access public
     */
    public function __construct( $db_config ) {
        $this->prepare_connect( $db_config );
        $this->connect();
        $this->selectDatabase();
        $this->setDatabaseEncode();
        if( method_exists(&$this,'__destruct') ) {
            register_shutdown_function( array(&$this,'__destruct') );
        }
    }

    /**
     +------------------------------------------------------------------------------
     * Set databse encode
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @param string $db_encode db encoding string
     * @access public
     */
    abstract public function setDatabaseEncode( $db_encode = NULL );

    /**
     +------------------------------------------------------------------------------
     * Prepare user input for use in a database query, preventing SQL injection
     * attacks. layout can rewrite this
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param string $str parse str
     * @access public
     */
    public function escapeString( $str ) {
        return addslashes( $str );
    }
    /**
     +------------------------------------------------------------------------------
     * Helper with abstract func query, Support (%d|%s|%%|%f|%b|%n)
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access protected
     * @params $sql String
     */
    protected function _queryCallback( $match, $init = false ) {
        static $args = NULL;
        if ($init) {
          $args = $match;
          return ;
        }
        switch ($match[1]) {
          case '%d': // We must use type casting to int to convert FALSE/NULL/(TRUE?)
              $value = array_shift($args);
              // Do we need special bigint handling?
              if ($value > PHP_INT_MAX) {
                  $precision = ini_get('precision');
                  @ini_set('precision', 16);
                  $value = sprintf('%.0f', $value);
                  @ini_set('precision', $precision);
              } else {
                  $value = (int) $value;
              }
               // We don't need db_escape_string as numbers are db-safe.
               return $value;
          case '%s':
                return $this->escapeString(array_shift($args));
          case '%n':
                // Numeric values have arbitrary precision, so can't be treated as float.
                // is_numeric() allows hex values (0xFF), but they are not valid.
                $value = trim(array_shift($args));
                return is_numeric($value) && !preg_match('/x/i', $value) ? $value : '0';
          case '%%':
                return '%';
          case '%f':
                return (float) array_shift($args);
          case '%b': // binary data
                return $this->_encodeBlob(array_shift($args));
          }
    }

    /**
     +------------------------------------------------------------------------------
     * Implement table prefix
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @param $query A string containing an SQL query.
     * @return string $query
     */
    protected function _addTablePrefix( $query ) {
        return strtr($query, array('{' => $this->_config['prefix'], '}' => ''));
    }
    /**
     +------------------------------------------------------------------------------
     * conversion query records to Object
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @param $result a query result, see query()
     * @return array
     */
    public function fetchAllToObject( $result = NULL ) {
        $_object_array = array();
        while( $res = $this->fetchObject( $result ) ) {
            $_object_array[] = $res;
        }
        return $_object_array;
    }

    /**
     +------------------------------------------------------------------------------
     * conversion query records to array
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @param $result a query result, see query()
     * @return array
     */
    public function fetchAllToArray( $result = NULL ) {
        $_array = array();
        while( $res = $this->fetchArray( $result ) ) {
            $_array[] = $res;
        }
        return $_array;
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
    public function query( $query ) {
        $args = func_get_args();
        array_shift($args);
        $query = $this->_addTablePrefix($query); //Implement table prefix
        if (isset($args[0]) && is_array($args[0])) { // 'All arguments in one array' syntax
           $args = $args[0];
        }
        $this->_queryCallback($args,true);
        $query = preg_replace_callback('/(%d|%s|%%|%f|%b|%n)/', array($this, '_queryCallback'), $query, E_USER_WARNING);
        if( $this->_debug ) {
            // TODO debug
            $this->_debug_info['sql'][] = $query;
        }
        return $this->_query( $query );
    }

    /**
     +------------------------------------------------------------------------------
     * blob data conversion
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract protected
     * @param array() $db_config the class param, only array
     * @return NULL
     */
    abstract protected function  _encodeBlob( $data );

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
    abstract protected function prepare_connect( $db_config );

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
    abstract protected function connect();

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
    abstract public function selectDatabase();

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
    abstract public function version();

    /**
     +------------------------------------------------------------------------------
     * Helper for query,To exec query
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract protected
     * @param $query A string containing an SQL query.
     * @return A database query result resource, or FALSE if the query was not
     *         executed correctly.
     */
    abstract protected function _query( $query );

    /**
     +------------------------------------------------------------------------------
     * Get the ID generated in the last query
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @params NULL
     * @return int insert primary key
     */
    abstract public function getLastInsertId();

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
    abstract public function fetchArray();

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
    abstract public function fetchObject();

    /**
     +------------------------------------------------------------------------------
     * Get the query first Records
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @see   this function fetchAllToObject
     * @access abstract public
     * @params NULL
     * @return
     */
    abstract public function getResult();
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
    abstract public function __destruct();

    /**
     +------------------------------------------------------------------------------
     * echo Object info [Magic func]
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return NULL
     */
    abstract public function __toString();

    /**
     +------------------------------------------------------------------------------
     * get the all tables info
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return array all tables list
     */
    abstract public function getTables();

    /**
     +------------------------------------------------------------------------------
     * get a table info with the Fields
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return array all Fields info list
     */
    abstract public function getTableFieldsInfo( $table_name );

    /**
     +------------------------------------------------------------------------------
     * get a query error message
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @access abstract public
     * @return string
     */
    abstract public function getError();

    /**
     * Close up the $_link
     */
    abstract public function close();

    /**
     * Free a query result
     */
    abstract public function free();
}
// end