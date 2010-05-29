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
 * 数据库驱动类接口实现
 +------------------------------------------------------------------------------
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 
interface Db {
	//数据库连接信息
	public function connect();
	
	//当前数据库版本
	public function version();
	
	//
	public function getTablePrefix();
		
	//过滤安全字符
	public function escapeString( $string );
	
	//sql exec
	public function query($str);
	
	//获取数据库表名
	public function getTables();
	
	//获取insert ID
	public function insertId();
	
	//获取query 后的单个记录
	public function getResultOne();
	
	//获取表结构信息
	public function getTableInfo();
	
	//获取表字段
	public function getFields($table_name);
	
	//释放数据库连接
	public function close();
	
	//释放上一个SQL资源
	public function free();
	
	//连接状态
	public function state();
	
	//释放资源
    public function __destruct();
}
//end