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

if ( DEBUG ) {
    open_debug_info();
}
function open_debug_info() {
    ini_set('display_errors','on');
    error_reporting( E_ALL );
}

/**
 * Def Group boot Type for INTERN_GLOBAL_FILTER
 *  @todo INTERN_GLOBAL_FILTER content by
 */
if (internBootstrap::$boot_type >= INTERN_GLOBAL_FILTER) :
    /**
     * Develop tool, collect time begin ,stop, end time statistics
     *   @param string $name
     *      the timer named.
     *   @param string $action options:
     *       'set'   start set a time record.
     *       'get'   get the record of named by default.
     *       'stop'  sotp the record. clear the named.
     *       'all'   get all data with $timer.
     *       'clear' clear all timer if not named.
     */
    function intern_timer( $name, $action = 'get' ) {
        static $timer = array();
        if ( null == $name && $action == 'clear' ) {
            $timer = array(); // reset the timer
            return true;
        } else {
            list($time, $_time) = explode(' ', microtime());
            switch( $action ) {
                case 'set':
                    if ( !isset($timer[$name]) ) {
                        $timer[$name]['start'] = $time + $_time;
                        $timer[$name]['count'] = 1;
                    } else {
                        $timer[$name]['count']++;
                    }
                    break;
                case 'get':
                    if ( $timer[$name] ) {
                        return bcsub($time + $_time, $timer[$name]['start'], 5);
                    }
                    break;
                case 'stop':
                    if ( !isset($timer[$name]) ) {
                        return false;
                    }
                    unset($timer[$name]);
                    break;
                case 'count':
                    if ( isset($timer[$name]) ) {
                        return $timer[$name]['count'];
                    }
                    return false;
                case 'all':
                    return $timer; // return all timer with 'all' as array();
                default :
                    return false;
            }
        }
        return true;
    }

    /**
     * Clear all timer with static intern_timer()
     *  @params null
     */
    function intern_timer_clear() {
        intern_timer( null, 'clear' );
    }

    /**
     * Remove all unsafe variables
     * @param null
     */
    function unset_global_variable() {
        if ( !ini_get('register_globals') ) return ;

        $use = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

        //$queue = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

        foreach( $GLOBALS as $key=>$value ) {
            if ( !in_array($key, $use) ) {
                unset($GLOBALS[$key]);
            }
        }
    }

    /**
     * To building path with params
     * 	@param string or array
     * 	@return path string
     */
    function intern_join_path( $args ) {
        if ( !is_array( $args ) ) {
            $args = func_get_args();
        }
        return str_replace( DS.DS, DS, implode(DS, $args) );
    }

    /**
     * @return request IP
     */
    function intern_get_ip() {
        static $ip;
        if ( isset($ip) ) return $ip;
        if ( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip != false) {
                array_unshift($ips,$ip);
                $ip = false;
            }
            $count = count($ips);
            // Exclude IP addresses that are reserved for LANs
            for ($i = 0; $i < $count; $i++) {
                if ( !preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i]) ) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        if ( false == $ip && isset($_SERVER['REMOTE_ADDR']) )
            $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }
endif;

/**
 * Def Group boot Type for INTERN_INITIALIZE_CONFIG
 */
if (internBootstrap::$boot_type >= INTERN_INITIALIZE_CONFIG):

/**
 * to parse the databases config, use array to define new db link
 */
function intern_parse_db_config( $db_config = NULL ) {
    if ( !isset( $db_config ) ) {
        global $db_config;
    }
    $_db_config = array();
    if ( !is_array( $db_config ) ) {
        $_db_config = parse_url( $db_config );
        list(, $_db_config['database'], $_db_config['prefix'], $_db_config['encoding']) = explode( '/', $_db_config['path'] );
    } else {
        $_db_config = $db_config;
    }
    return $_db_config;
}

endif;

/**
 * Def Group boot Type for INTERN_INITIALIZE_DATABASE
 */
if (internBootstrap::$boot_type >= INTERN_INITIALIZE_DATABASE):
    /**
     * Create a Anonymous user data here.
     */
    function intern_init_anonymous_user( $session = '' ) {
        $user = new stdClass();
        $user->uid = 0;
        $user->hostname = inter_get_ip();
        $user->roles =array();
        $user->data = $session; // the sessions data
        $user->cache = 0;
        return $user;
    }

    /**
     * To Operate {options}
     * get the $name with {options}
     */
    function options_get( $name, $default ) {
        global $config;
        return isset($config[$name]) ? $config[$name] : $default;
    }

    /**
     * To Operate {options}
     * set the $name value
     * @param
     *      $name string name primary key
     *      $value mix   will to serialize
     *      $status int Whether the automatic loading,default 1
     */
    function options_set( $name, $value ) {
        global $config, $db_handle;
        $_value = serialize( $value );
        if ( isset($config[$name] ) ) {
            $db_handle->query("UPDATE {options} SET value = '%s' WHERE name = '%s'", $_value, $name );
        } //TODO fix mysql update sql. not affectedRows! $db_handle->affectedRows()
        else {
           $db_handle->query("INSERT INTO {options} (name, value) VALUES('%s', '%s')", $name, $_value );
        }
        $config[$name] = $value;
    }

    /**
     * Delete the named variable
     */
    function options_del( $name ) {
        global $config, $db_handle;
        $db_handle->query("DELETE {options} FROM WHERE name = '%s'" , $name );
        unset($config[$name]);
    }

    /**
     * Init the global $config
     */
    function options_init() {
        global $config, $db_handle;
        $_handle = $db_handle->query("SELECT * FROM {options}");
        while( $obj = $db_handle->fetchObject( $_handle ) ) {
            $config[$obj->name] = unserialize( $obj->value );
        }
    }
endif;


/**
 * Check the request is ajax request
 * @return true if ajax
 */
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

/**
 * Check the request is post
 */
function is_post() {
    return !empty($_POST);
}

/**
 * Check the request is a xml request
 */
function is_xml() {
    return isset($_REQUEST['__FORMAT__']) && strtolower($_REQUEST['__FORMAT__']) == 'XML';
}

/**
 * Check current request need a json response
 */
function is_json() {
    return isset($_REQUEST['__FORMAT__']) && strtolower($_REQUEST['__FORMAT__']) == 'JSON';
}

/**
 * Check the request is home page
 */
function is_home() {
    return ;
}

function is_admin_page() {
    return Router::arg(0) == 'admin';
}
/**
 * translate
 */
function __($t) {
    return $t;
}

/**
 * get the actived theme path
 */
function theme_path() {
    static $theme;
    if ( !isset($theme) ) {
        $theme = intern_join_path(ADMIN_THEME_PATH, options_get((is_admin_page() ? 'actived_admin_theme' : 'actived_theme'), 'default'));
    }
    return $theme;
}

/**
 * $head variable set
 * @parems
 *   $type 'inline'|'script'|'stylesheet'
 *   $where 'module' | 'theme'
 */
function intern_set_head( $type = null, $where= null, $data = null ) {
    static $head = array();
    if( !isset($head['script']) ) {
        $head['script'] = array(
            'core' => array(
                intern_join_path( MISC, 'jquery.js') => ''
            ),
            'module' => array(),
            'theme'  => array(),
            'inline' => array()
        );
        $head['stylesheet'] = array(
            'module' => array(),
            'theme'  => array(
                intern_join_path( theme_path(), 'style.css') => ''
            ),
            'inline' => array()
        );
    }
    if ( isset($type) && isset($where) && isset($data) && isset($head[$type][$where]) ) {
        if ( $where == 'inline' ) {
            $head[$type][$where][] = $data;
        } else {
            $head[$type][$where][$data] = '';
        }
    }
    return $head;
}

/**
 * $head variable get
 */
function intern_get_head() {
    return intern_set_head();
}

/**
 * $head variable get
 */
function intern_html_get_head() {
    $output = intern_html_get_stylesheet();
    $output .= intern_html_get_javascript();
    return $output;
}

/**
 * get the base url with the web
 */
function base_path() {
    return $GLOBALS['base_url'];
}

/**
 * $head variable get
 */
function intern_html_get_javascript() {
    $script = "\n";
    $inline = "";
    $head = intern_get_head();
    foreach( $head['script'] as $type => $values ) {
        switch( $type ) {
            case 'core'  :
            case 'module':
            case 'theme' :
                foreach( array_keys($values) as $value ) {
                    if( file_exists( $value ) ) {
                        $script .= '<script type="text/javascript" src="'. base_path() . $value . '"></script>' . "\n";
                    }
                }
                break;
            case 'inline':
                foreach( $values as $value ) {
                      $inline .= "<script type=\"text/javascript\">\n<!--//--><![CDATA[//><!--\n".$value."\n//--><!]]>\n</script>\n";
                }
                break;
        }
    }
    return $script . $inline;
}

/**
 * $head variable get
 */
function intern_html_get_stylesheet() {
    $stylesheet = $inline = "";
    $head = intern_get_head();
    foreach( $head['stylesheet'] as $type => $values ) {
        switch( $type ) {
            case 'module':
            case 'theme' :
                foreach( array_keys($values) as $value ) {
                    if( file_exists( $value ) ) {
                        $stylesheet .= '<link type="text/css" rel="stylesheet" href="'. base_path() . $value ."\" />\n";
                    }
                }
                break;
            case 'inline':
                break;
        }
    }
    return $stylesheet . $inline;
}

/**
 * Set the page title
 */
function intern_set_title( $title = null ) {
    static $_title;
    if( isset($title) ) {
        $_title = $title;
    }
    return $_title;
}
/**
 * Get the page title
 */
function intern_get_title() {
    return intern_set_title();
}

/**
 * inter_html_charset
 */
function intern_html_set_charset( $charset = null ) {
    static $_charset;
    if( isset($charset) ) {
        $_charset = $charset;
    }
    return isset($_charset) ? $_charset : 'utf-8';
}

/**
 * inter_html_charset
 */
function intern_html_get_charset() {
    return intern_html_set_charset();
}

/**
 * To send page header
 */
function intern_send_page_header() {
    header("Expires: Sun, 19 Nov 1978 05:00:00 GMT");
    header("Last-Modified: ". gmdate("D, d M Y H:i:s") ." GMT");
    header("Cache-Control: store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", FALSE);
}

/**
 * Load the theme helper file
 */
function intern_template_helper_load() {
    $helper = intern_join_path(theme_path() , '_helper.php');
    if ( file_exists( $helper ) ) {
        include_once( $helper );
    }
}

/**
 * set li breadcrumb
 */
function intern_set_breadcrumb( $breadcrumb = null ) {
    static $_breadcrumb;
    if(is_null($_breadcrumb)) {
        $_breadcrumb = $breadcrumb;
    }
    return $_breadcrumb;
}

/**
 * get li breadcrumb
 */
function intern_get_breadcrumb() {
    return intern_set_breadcrumb();
}

/**
 * inter
 */
function intern_attributes( $attributes = array() ) {
    if (is_array($attributes)) {
        $string = '';
        foreach ($attributes as $key => $value) {
          $string .= " $key=".'"'.$value .'"';
        }
        return $string;
     }
}

function intern_navigation_build( $items ,$options = array()) {
    if( is_array($items) ) {
        $output = '<ul'.intern_attributes($options).">\n";
        foreach($items as $value) {
            $output .= "<li><em><a".intern_attributes($value['options']).">".$value['title']."</a></em></li>\n";
        }
        $output .= "</ul>\n";
        return $output;
    }
}

/**
 *  dev test group begin
 *  will remove these
 */

/**
 * dev test
 */
function import_sql() {
    $data = file_get_contents(ROOT.'data.sql');
    $l = internCoreDatabase::getInstance();
    if( !$l->query($data) ) {
        echo '===data.sql error!';
    }else{
        echo '===data.sql OK!';
    }
}

/**
 *  dev init global a admin user
 *
 */
function init_user($session = array()) {
    global $user;
    //session_destroy();
    $user = new stdClass();
    $user->uid = 1;
    $user->hostname = intern_get_ip();
    $user->roles =array();
    $user->data = $session; // the sessions data
    $user->cache = 0;
    //$_SESSION['y'] = array('a','b','c');
}



