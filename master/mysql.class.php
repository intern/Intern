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
 * MYSQL 数据库驱动类实现
 +------------------------------------------------------------------------------
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 class Mysql implements Db{
        /*
         * 数据库 DEBUG 调试开关
         */
        public $debug = false;
        /*
         * 数据库 DEBUG 调试仓库;
         */
        public static $_store_debug = array();
        /*
         * 数据库连接句柄 
         */
        private $link = NULL;
        /*
         * 数据库连接ID  $config 数据的key
         */
        private $_link_id = NULL;
        /*
         * 数据库信息连接信息 ,所有的连接信息
         */
        private static $config = array();
        /*
         * 默认数据库编码 UTF-8,多个连接也使用同一编码
         *  可通过$this->query修改
         */
        private $database_encoding ="utf8";
        /*
         * 数据资源句柄 最近SQL句柄
         * @access 私有
         */
        private $_result;
        /**
         +------------------------------------------------------------------------------
         * MYSQL 初始化
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params
         *  $conf = array(
         *		'scheme' => 'mysql',
         *		'host' => 'localhost',
         *		'user' => 'root',
         *		'pass' => 'root',
         *		'database' => 'cake',
         *		'prefix' => '',
         *		'port'	=> 5432
         *		)
         *  $link_new   是否是新的连接
         +------------------------------------------------------------------------------
         */
        public function __construct( $conf ,$link_new = false ) {
            if( !extension_loaded('mysql') ) exit("Please Open the mysql,DLL");
            if( method_exists(&$this,'__destruct') ) {
                register_shutdown_function( array(&$this,'__destruct') );
            }
            if( is_array($conf) ) {
                    $this->connect( $conf ,$link_new);
            }else{
                return false;
            }
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 格式化 数据连接信息
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         */
        private function _config( &$conf ) {
            $config['host']      = isset($config['host'])?$config['host']:'localhost';
            $config['port']      = isset($config['port'])?$config['port']:'3306';
            $config['user']      = isset($config['user'])?$config['user']:'root';
            $config['pass']      = isset($config['pass'])?$config['pass']:'root';
            $config['database']  = isset($config['database'])?$config['database']:NULL;
            $config['prefix']     = isset($config['prefix'])?$config['prefix']:'';
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 初始化 [使用UTF8存取数据库 需要mysql 4.1.0以上支持]
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params
         *    $config 数据库连接信息数组
         *    $linkNum 连接ID号 0为主连接
         */
	public function connect( $config = array() ,$new_link = false ) {
            if( !is_array( $config ) ) exit("ERROR:请填写数据连接信息");
            $this->_config( $config );
            $_link = @mysql_connect( $config['host'], $config['user'], $config['pass'], $new_link != false );
            if( $_link ) {
                    self::$config[] = $config;
                    $this->_link_id = bcsub(count(self::$config),1);
                    $this->link = $_link;
                    $this->selectDatabase( $_link,$config['database'] );
                    //编码设置
                    $this->query('SET NAMES "'.$this->database_encoding.'"');
                    return $this->link;
            }
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 统计MYSQL数据库连接句柄数量
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         * @return Int
         */
        public function getConnectCount() {
            return count(self::$config);
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取配置信息
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params $link_id
         +------------------------------------------------------------------------------
         * @return
         *          array link config
         */
        public function getConfig( $link_id = NULL ) {
            if( isset($link_id) && !isset (self::$config[$link_id]) )
                return FALSE;
            return isset($link_id)?self::$config[$link_id]:self::$config;
        }
        
        /**
         +------------------------------------------------------------------------------
         * MYSQL 选择数据库
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params
         *          $link 连接标示
         *          $database NAME
         */
        public function selectDatabase( $link ,$database ) {
                if( !isset( $this->link ) ) return false;
                mysql_select_db( $database , $link ) or die ("Error".__LINE__);
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 添加数据库连接句柄
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params
         *      $config @see __construct()
         *      $link_new return this link if not is string ,or set $this->$link_new = return value
         */
        public function addConnect( $config = NULL , $link_new = true) {
            if( is_string($link_new) ) {
                $this->$link_new = new self( $config , $link_new );
                return true;
            }
            return new self( $config , $link_new );
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 添加数据库版本号
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         +------------------------------------------------------------------------------
         * @return VERSION
         */
	public function version(){
            list($version) = explode('-', mysql_get_server_info($this->link));
            return $version;
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取配置中的表前缀  $this->link
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         * @return String
         */
	public function getTablePrefix( $link_id = null ){
            if(isset ($link_id) && !isset ( self::$config[$link_id] ))
                return false;
             return isset(self::$config[$link_id])?self::$config[$link_id]['prefix']:self::$config[$this->_link_id]['prefix'];
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 过滤安全字符
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params String
         * @return String
         */
        public function escapeString( $str ) {
           return addslashes( $str );
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 初始化 format SQL string [Cross-Site Request Forgery]
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params
         *    $SQL //SQL语句  {%s, %d, %f, %b}
         *     Valid %-modifiers are: %s, %d, %f, %b (binary data, do not enclose in '') and %%.
         *    Eg:
         *      $res = self->query( "SELECT * FROM {tableName} WHERE id = %d AND name = '%s'" , 1, 'name');
         */
        public function query($query) {
              $args = func_get_args();
              array_shift($args);
              $query = $this->_addTablePrefix($query);
              if (isset($args[0]) and is_array($args[0])) { // 'All arguments in one array' syntax
                  $args = $args[0];
              }
              $this->_queryCallback($args,true);
              $query = preg_replace_callback('/(%d|%s|%%|%f|%b|%n)/', array($this,'_queryCallback'), $query,E_USER_WARNING);
              
              if( $this->debug ) {
                  self::$_store_debug[__CLASS__][$this->_link_id][] = ' SQL: '.$query."\n";
              }
              return $this->_query( $query );
        }
       /**
         +------------------------------------------------------------------------------
         * MYSQL 私有辅助方法 helper This query func
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params *
         */
        private function _query( $query ) {
            $db_result = mysql_query( $query, $this->link);
            if( !mysql_errno( $this->link ) ) {
                $this->_result = $db_result;
                return $db_result;
            }else{
                exit($this->getError());
            }
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 从数据资源结果集中取得一行作为对象 Object
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params Result ID or NULL
         *
         * @return Object();
         */
        function fetchObject( $result = NULL ) {
          $result = isset ( $result )?$result:$this->_result;
          return mysql_fetch_object($result);
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 从数据资源结果集中取得一行作为数组 Array
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params Result ID or NULL,NULL is the default $this->db_result
         *
         * @return Array();
         */
        function fetchArray( $result = NULL ) {
            $result = isset ( $result )?$result:$this->_result;
            return mysql_fetch_array($result, MYSQL_ASSOC);
        }

        /**
         +------------------------------------------------------------------------------
         * MYSQL 从数据资源结果集中取得所有对象并返回
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params Result ID or NULL
         *
         * @return Object();
         */
        function fetchAllO( $result = NULL ) {
          $results = array();
          while( $res = $this->fetchObject($result) ) {
              $results[] = $res;
          }
          return (object) $results;
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 从数据资源结果集中取得所有数据并以数组方式返回
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params Result ID or NULL
         *
         * @return Array();
         */
        function fetchAllA( $result = NULL ) {
            $results = array();
            while( $res = $this->fetchArray($result) ) {
                $results[] = $res;
            }
            return (array) $results;
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取数据库所有表名
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params
         *      $dbName database name
         *      $_link link ID or link handle,default current $link
         * @return array();
         */
        public function getTables( $dbName = NULL ,$_link = NULL ) {
            if(!empty($dbName)) {
               $sql    = 'SHOW TABLES FROM '.$dbName;
            } else {
               $sql    = 'SHOW TABLES ';
            }
            $_tables = $this->fetchAllA($this->query( $sql ));
            array_walk($_tables,create_function('&$args', '$args = current($args);'));
            return  $_tables;
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取上一个 MySQL 操作产生的文本错误信息
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         */
        public function getError( $link = NULL ) {
          return mysql_error( $this->link );
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 私有辅助方法
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params *
         */
        private function _encodeBlob($data) {
            return "'". mysql_real_escape_string($data, $this->link ) ."'";
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL Helper with _query 支持 (%d|%s|%%|%f|%b|%n)  转义
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params $sql String
         */
        private function _queryCallback( $match, $init = false ) {
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
                  }
                  else {
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
         * MYSQL 数据表前缀实现
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params $sql String
         */
        private function _addTablePrefix( $sql ) {
            return strtr($sql, array('{' => self::$config[$this->_link_id]['prefix'], '}' => ''));
        }
	//获取insert ID
	public function insertId() {
            return $this->getResultOne($this->query('SELECT LAST_INSERT_ID()'));
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取sql存在的第一个值
         +------------------------------------------------------------------------------
         * @abstract getResultOne
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params $_result
         +------------------------------------------------------------------------------
         * @return string or false
         */
	public function getResultOne( $_result = NULL ) {
            $_result = isset($_result)?$_result:$this->_result;
            if ($_result && mysql_num_rows($_result) > 0) {
            // The mysql_fetch_row function has an optional second parameter $row
            // but that can't be used for compatibility with Oracle, DB2, etc.
               $array = $this->fetchArray($_result);
               return current($array);
            }
            return FALSE;
        }
	//获取表结构信息
	public function getTableInfo() {

        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取数据库表信息 表结构
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         +------------------------------------------------------------------------------
         * @return TRUE if succeed
         */
	public function getFields( $table_name ) {
            $result =   $this->query('SHOW COLUMNS FROM {'.$table_name.'}');
            if($result && mysql_num_rows($result) > 0) {
                $info   =   array();
                foreach ( $this->fetchAllA( $result ) as $key => $val) {
                    $info[$val['Field']] = array(
                        'name'    => $val['Field'],
                        'type'    => $val['Type'],
                        'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                        'default' => $val['Default'],
                        'primary' => (strtolower($val['Key']) == 'pri'),
                        'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                    );
                }
                return $info;
            }
            return false;
        }

	//释放数据库连接
	public function close(){
            if( $this->link ) {
                mysql_close($this->link);
                $this->link = NULL;
            }
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 释放上一个SQL资源
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         +------------------------------------------------------------------------------
         * @return TRUE if succeed
         */
	public function free() {
            if( $this->_result ) {
                mysql_free_result($this->_result);
            }
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取数据库连接状态
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         +------------------------------------------------------------------------------
         * @return TRUE if succeed
         */
        public function state() {
           return is_resource($this->link);
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 格式化 [魔术函数]
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         */
        public function __get( $attribute )
        {
            //property_exists >= 5.1.0
            if( isset( $this->$attribute ) ) {
               return $this->$attribute;
            }
               return false;
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 格式化 [魔术函数]
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         */
//        public function __set( $attribute ,$value )
//        {
//            //property_exists >= 5.1.0
//            if( isset( $this->$attribute ) ) {
//                return $this->$attribute;
//            }
//               return false;
//        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 获取当前数据连句柄的配置数组键值 $config[key] [魔术函数]
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         */
        public function  __toString() {
            return $this->_link_id;
        }
        /**
         +------------------------------------------------------------------------------
         * MYSQL 释放连接资源 [魔术函数]
         +------------------------------------------------------------------------------
         * @version   $Id$
         +------------------------------------------------------------------------------
         * @params NULL
         */
        public function __destruct(){
            $this->close();
            unset(self::$config[$this->_link_id]);
            if( $this->debug ) {
                print_r( self::$_store_debug );
                $this->debug = false;
            }
        }
}