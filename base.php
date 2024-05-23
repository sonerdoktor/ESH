<?php
// no direct access
defined( 'KAPAT' ) or die('Bu dosyayı görmeye yetkiniz yok!');

if ( ERROR_REPORT === 0 || ERROR_REPORT === '0' ) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
} else if (ERROR_REPORT > 0) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

//sınıfları içeri alalım
$classes = readDirectory(CLASSPATH);
foreach ($classes as $class) {
    require_once(CLASSPATH.'/'.$class);
}

//veritabanı tablolarını alalım
$dbtables = readDirectory(DBTABLEPATH);
foreach ($dbtables as $dbtable) {
    require_once(DBTABLEPATH.'/'.$dbtable);
}

//DATABASE CONNECTOR
$dbase = new DB( DB_HOST, DB_USER, DB_PASS, DB, DB_PREFIX, OFFLINE );

if ($dbase->getErrorNum()) {
    $systemError = $dbase->getErrorNum();
    $systemErrorMsg = $dbase->getErrorMsg();
    include TEMPLATEPATH.'/'.TEMPLATE.'/error.php';
    exit();
}
$dbase->debug( DEBUGMODE );
/**
* Random password generator
* @return password
*/
function MakePassword($length=8) {
    $salt         = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
    $makepass    = '';
    mt_srand(10000000*(double)microtime());
    for ($i = 0; $i < $length; $i++)
        $makepass .= $salt[mt_rand(0,58)];
    return $makepass;
}
/**
 * Utility function to return a value from a named array or a specified default
 * @param array A named array
 * @param string The key to search for
 * @param mixed The default value to give if no key found
 * @param int An options mask: _NOTRIM prevents trim, _ALLOWHTML allows safe html, _ALLOWRAW allows raw input
 */
define( "_NOTRIM", 0x0001 );
define( "_ALLOWHTML", 0x0002 );
define( "_ALLOWRAW", 0x0004 );
function getParam( &$arr, $name, $def=null, $mask=0 ) {
    
    static $noHtmlFilter     = null;
    
    static $safeHtmlFilter     = null;

    $return = null;
    if (isset( $arr[$name] )) {
        $return = $arr[$name];

        if (is_string( $return )) {
            // trim data
            if (!($mask&_NOTRIM)) {
                $return = trim( $return );
            }

            if ($mask&_ALLOWRAW) {
                // do nothing
            } else if ($mask&_ALLOWHTML) {
                // do nothing - compatibility mode
            } else {
                // send to inputfilter
                if (is_null( $noHtmlFilter )) {
                    $noHtmlFilter = new InputFilter( /* $tags, $attr, $tag_method, $attr_method, $xss_auto */ );
                }
                $return = $noHtmlFilter->process( $return );

                if (!empty($return) && is_numeric($def)) {
                // if value is defined and default value is numeric set variable type to integer
                    $return = intval($return);
                }
            }
        }

        return $return;
    } else {
        return $def;
    }
}

/**
* Utility function to read the files in a directory
* @param string The file system path
* @param string A filter for the names
* @param boolean Recurse search into sub-directories
* @param boolean True if to prepend the full path to the file name
*/
function readDirectory( $path, $filter='.', $recurse=false, $fullpath=false  ) {
    $arr = array();
    if (!@is_dir( $path )) {
        return $arr;
    }
    $handle = opendir( $path );

    while ($file = readdir($handle)) {
        $dir = PathName( $path.'/'.$file, false );
        $isDir = is_dir( $dir );
        if (($file != ".") && ($file != "..")) {
            if (preg_match( "/$filter/", $file )) {
                if ($fullpath) {
                    $arr[] = trim( PathName( $path.'/'.$file, false ) );
                } else {
                    $arr[] = trim( $file );
                }
            }
            if ($recurse && $isDir) {
                $arr2 = readDirectory( $dir, $filter, $recurse, $fullpath );
                $arr = array_merge( $arr, $arr2 );
            }
        }
    }
    closedir($handle);
    asort($arr);
    return $arr;
}

/**
* Function to strip additional / or \ in a path name
* @param string The path
* @param boolean Add trailing slash
*/
function PathName($p_path,$p_addtrailingslash = true) {
    $retval = "";

    $isWin = (substr(PHP_OS, 0, 3) == 'WIN');

    if ($isWin)    {
        $retval = str_replace( '/', '\\', $p_path );
        if ($p_addtrailingslash) {
            if (substr( $retval, -1 ) != '\\') {
                $retval .= '\\';
            }
        }

        // Check if UNC path
        $unc = substr($retval,0,2) == '\\\\' ? 1 : 0;

        // Remove double \\
        $retval = str_replace( '\\\\', '\\', $retval );

        // If UNC path, we have to add one \ in front or everything breaks!
        if ( $unc == 1 ) {
            $retval = '\\'.$retval;
        }
    } else {
        $retval = str_replace( '\\', '/', $p_path );
        if ($p_addtrailingslash) {
            if (substr( $retval, -1 ) != '/') {
                $retval .= '/';
            }
        }

        // Check if UNC path
        $unc = substr($retval,0,2) == '//' ? 1 : 0;

        // Remove double //
        $retval = str_replace('//','/',$retval);

        // If UNC path, we have to add one / in front or everything breaks!
        if ( $unc == 1 ) {
            $retval = '/'.$retval;
        }
    }

    return $retval;
}
/**
* Copy the named array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
*/
function BindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
    if (!is_array( $array ) || !is_object( $obj )) {
        return (false);
    }

    $ignore = ' ' . $ignore . ' ';
    foreach (get_object_vars($obj) as $k => $v) {
        if( substr( $k, 0, 1 ) != '_' ) {            // internal attributes of an object are ignored
            if (strpos( $ignore, ' ' . $k . ' ') === false) {
                if ($prefix) {
                    $ak = $prefix . $k;
                } else {
                    $ak = $k;
                }
                if (isset($array[$ak])) {
                    $obj->$k = ($checkSlashes) ? mosStripslashes( $array[$ak] ) : $array[$ak];
                }
            }
        }
    }

    return true;
}
/**
 * Provides a secure hash based on a seed
 * @param string Seed string
 * @return string
 */
function getHash( $seed ) {
    return md5( SECRETWORD . md5( $seed ) );
}
/**
* Utility function redirect the browser location to another url
*
* Can optionally provide a message.
* @param string The file system path
* @param string A filter for the names
*/
function Redirect( $url, $msg='' ) {

   global $mainframe;

    // specific filters
    $iFilter = new InputFilter();
    $url = $iFilter->process( $url );
    if (!empty($msg)) {
        $msg = $iFilter->process( $msg );
    }

    // Strip out any line breaks and throw away the rest
    $url = preg_split("/[\r\n]/", $url);
    $url = $url[0];

    if ($iFilter->badAttributeValue( array( 'href', $url ))) {
        $url = SITEURL;
    }

    if (trim( $msg )) {
        if (strpos( $url, '?' )) {
            $url .= '&mosmsg=' . urlencode( $msg );
        } else {
            $url .= '?mosmsg=' . urlencode( $msg );
        }
    }
    

    if (headers_sent()) {
        echo "<script>document.location.href='$url';</script>\n";
    } else {
        @ob_end_clean(); // clear output buffer
        header( 'HTTP/1.1 301 Moved Permanently' );
        header( "Location: ". $url );
    }
    exit();
}
