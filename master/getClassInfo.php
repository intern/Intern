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
 * @var unknown_type
 */
class getClassInfo{
	
	/**
	 * @var save the tags
	 */
	public $_tags = array();
	
	/**
	 * @var Collect the named class
     */
    public $_classes = array();
    
    /**
     +------------------------------------------------------------------------------
     * 
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param
     * 
     +------------------------------------------------------------------------------
     */
    public function __construct( $class_name ) {
        !$class_name && die( "Param {\$className} can't empty!" );
        if( is_string( $class_name ) ) {
            $class_name = array( $class_name );
        }
        $this->_classes = $class_name;
    }
    
    
    /**
     +------------------------------------------------------------------------------
     * To get the methods info
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param 
     * 
     +------------------------------------------------------------------------------
     */
    public function methodInfo( $object, $method ) {
        new reflectionMethod();
    }
    
    
    
    /**
     +------------------------------------------------------------------------------
     * To get the methods info
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param 
     * 
     +------------------------------------------------------------------------------
     */
    private function _methodInfo($object, $method ) {
    	
    }
    
    
    /**
     +------------------------------------------------------------------------------
     * 
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param
     +------------------------------------------------------------------------------
     */
    public function classInfo( $object_name = null ) {
        if( !$object_name || !is_set( $this->_classes[$object_name] ) ){
            $object_name = current( $this->_classes );
        }
        return $this->_classInfo( $object_name );
    }
    
    
    /**
     +------------------------------------------------------------------------------
     * Helper for getClassInfo#classInfo and classesInfo
     +------------------------------------------------------------------------------
     * @version   $Id$
     +------------------------------------------------------------------------------
     * @param
     +------------------------------------------------------------------------------
     */
    private function _classInfo( $object_name ) {
        $_class = new ReflectionClass( $object_name );
        return $_class->getDocComment();
    }
}