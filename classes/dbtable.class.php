<?php
// no direct access
defined( 'KAPAT' ) or die( 'Bu alanı görmeye yetkiniz yok!' );  

class DBTable {
    /** @var string Name of the table in the db schema relating to child class */
    var $_tbl         = null;
    /** @var string Name of the primary key field in the table */
    var $_tbl_key     = null;
    /** @var string Error message */
    var $_error     = null;
    /** @var mosDatabase Database connector */
    var $_db         = null;

    /**
    *    Object constructor to set table and key field
    *
    *    Can be overloaded/supplemented by the child class
    *    @param string $table name of the table in the db schema relating to child class
    *    @param string $key name of the primary key field in the table
    */
    function __construct( $table, $key, $db ) {
        $this->set('_tbl', $table);
        $this->set('_tbl_key', $key);
        $this->set('_db', $db);
    }

    /**
     * Returns an array of public properties
     * @return array
     */
    function getPublicProperties() {
        static $cache = null;
        if (is_null( $cache )) {
            $cache = array();
            foreach (get_class_vars( get_class( $this ) ) as $key=>$val) {
                if (substr( $key, 0, 1 ) != '_') {
                    $cache[] = $key;
                }
            }
        }
        return $cache;
    }
    /**
     * Filters public properties
     * @access protected
     * @param array List of fields to ignore
     */
    function filter( $ignoreList=null ) {
        $ignore = is_array( $ignoreList );

        $iFilter = new InputFilter();
        foreach ($this->getPublicProperties() as $k) {
            if ($ignore && in_array( $k, $ignoreList ) ) {
                continue;
            }
            $this->$k = $iFilter->process( $this->$k );
        }
    }
    /**
     *    @return string Returns the error message
     */
    function getError() {
        return $this->_error;
    }
    /**
    * Gets the value of the class variable
    * @param string The name of the class variable
    * @return mixed The value of the class var (or null if no var of that name exists)
    */
    function get( $_property ) {
        if(isset( $this->$_property )) {
            return $this->$_property;
        } else {
            return null;
        }
    }

    /**
    * Set the value of the class variable
    * @param string The name of the class variable
    * @param mixed The value to assign to the variable
    */
    function set( $_property, $_value ) {
        $this->$_property = $_value;
    }

    /**
     * Resets public properties
     * @param mixed The value to set all properties to, default is null
     */
    function reset( $value=null ) {
        $keys = $this->getPublicProperties();
        foreach ($keys as $k) {
            $this->$k = $value;
        }
    }
    /**
    *    binds a named array/hash to this object
    *
    *    can be overloaded/supplemented by the child class
    *    @param array $hash named array
    *    @return null|string    null is operation was satisfactory, otherwise returns an error
    */
    function bind( $array, $ignore='' ) {
        if (!is_array( $array )) {
            $this->_error = strtolower(get_class( $this ))."::bağlama başarısız.";
            return false;
        } else {
            return BindArrayToObject( $array, $this, $ignore );
        }
    }

    /**
    *    binds an array/hash to this object
    *    @param int $oid optional argument, if not specifed then the value of current key is used
    *    @return any result from the database operation
    */
    function load( $oid=null ) {
        $k = $this->_tbl_key;

        if ($oid !== null) {
            $this->$k = $oid;
        }

        $oid = $this->$k;

        if ($oid === null) {
            return false;
        }
        
        $class_vars = get_class_vars(get_class($this));
        foreach ($class_vars as $name => $value) {
            if (($name != $k) and ($name != "_db") and ($name != "_tbl") and ($name != "_tbl_key")) {
                $this->$name = $value;
            }
        }

        $query = "SELECT *"
        . "\n FROM $this->_tbl"
        . "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $oid )
        ;
        $this->_db->setQuery( $query );

        return $this->_db->loadObject( $this );
    }

    /**
    *    generic check method
    *
    *    can be overloaded/supplemented by the child class
    *    @return boolean True if the object is ok
    */
    function check() {
        return true;
    }

    /**
    * Inserts a new row if id is zero or updates an existing row in the database table
    *
    * Can be overloaded/supplemented by the child class
    * @param boolean If false, null object variables are not updated
    * @return null|string null if successful otherwise returns and error message
    */
    function store( $updateNulls=false ) {
        $k = $this->_tbl_key;

        if ($this->$k != 0) {
            $ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
        } else {
            $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        }

        if (!$ret) {
            $this->_error = strtolower(get_class($this))."::kayıt başarısız <br />" . $this->_db->getErrorMsg();
            return false;
        } else {
            return true;
        }
    }
    /**
    *    Default delete method
    *
    *    can be overloaded/supplemented by the child class
    *    @return true if successful otherwise returns and error message
    */
    function delete( $oid=null ) {
        //if (!$this->canDelete( $msg )) {
        //    return $msg;
        //}

        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }

        $query = "DELETE FROM $this->_tbl"
        . "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $this->$k )
        ;
        $this->_db->setQuery( $query );

        if ($this->_db->query()) {
            return true;
        } else {
            $this->_error = $this->_db->getErrorMsg();
            return false;
        }
    }
    /**
    * Generic save function
    * @param array Source array for binding to class vars
    * @param string Filter for the order updating. This is expected to be a valid (and safe!) SQL expression
    * @returns TRUE if completely successful, FALSE if partially or not succesful
    * NOTE: Filter will be deprecated in verion 1.1
    */
    function save( $source ) {
        if (!$this->bind( $source )) {
            return false;
        }
        if (!$this->check()) {
            return false;
        }
        if (!$this->store()) {
            return false;
        }

        $this->_error = '';
        return true;
    }
    /**
    * Export item list to xml
    * @param boolean Map foreign keys to text values
    */
    function toXML( $mapKeysToText=false ) {
        $xml = '<record table="' . $this->_tbl . '"';

        if ($mapKeysToText) {
            $xml .= ' mapkeystotext="true"';
        }
        $xml .= '>';
        foreach (get_object_vars( $this ) as $k => $v) {
            if (is_array($v) or is_object($v) or $v === NULL) {
                continue;
            }
            if ($k[0] == '_') { // internal field
                continue;
            }
            $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
        }
        $xml .= '</record>';

        return $xml;
    }
}