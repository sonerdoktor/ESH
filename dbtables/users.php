<?php
// no direct access
defined( 'KAPAT' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Users extends DBTable {
    
    var $id       = NULL;
    
    var $name     = NULL;
    
    var $username = NULL;
    
    var $tckimlikno = NULL;
    
    var $email    = NULL;
    
    var $password = NULL;
    
    var $image = NULL;
    
    var $nowvisit = NULL;
    
    var $lastvisit = NULL;
    
    var $registerdate = NULL;
    
    var $activated    = NULL;
    
    var $activation = NULL;
    
    var $isAdmin = 0;
    
    /**
    * @param database A database connector object
    */
    function __construct( $db ) {
        $this->_tbl = "#__users";
        $this->_tbl_key = "id";
        $this->_db= $db;
    }    
    
    function createCode($len=12) {
        return MakePassword($len);
    }
    /**
     * Validation and filtering
     * @return boolean True is satisfactory
     */
    function check() {

        // Validate user information
        if (trim( $this->name ) == '') {
            $this->_error = addslashes( 'Lütfen bir isim belirtiniz' );
            return false;
        }

        if (trim( $this->username ) == '') {
            $this->_error = addslashes( 'Lütfen bir kullanıcı adı belirtiniz' );
            return false;
        }

        // check that username is not greater than 25 characters
        $username = $this->username;
        if ( strlen($username) > 25 ) {
            $this->username = substr( $username, 0, 25 );
        }

        // check that password is not greater than 50 characters
        $password = $this->password;
        if ( strlen($password) > 50 ) {
            $this->password = substr( $password, 0, 50 );
        }

        if (preg_match( "/[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]/", $this->username) || strlen( $this->username ) < 3) {
            $this->_error = sprintf( addslashes( 'Lütfen geçersiz karakterler kullanmayın' ), addslashes( 'Kullanıcı adı 3 karakterden kısa olamaz' ), 2 );
            return false;
        }

        if ((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email )==false)) {
            $this->_error = addslashes( 'E-posta adresi belirtilmemiş veya geçersiz e-posta adresi girilmiş' );
            return false;
        }

        // check for existing username
        $query = "SELECT id"
        . "\n FROM #__users "
        . "\n WHERE username = " . $this->_db->Quote( $this->username )
        . "\n AND id != " . (int)$this->id
        ;
        $this->_db->setQuery( $query );
        $xid = intval( $this->_db->loadResult() );
        if ($xid && $xid != intval( $this->id )) {
            $this->_error = addslashes( 'Bu kullanıcı zaten var' );
            return false;
        }

           // check for existing email
            $query = "SELECT id"
            . "\n FROM #__users "
            . "\n WHERE email = " . $this->_db->Quote( $this->email )
            . "\n AND id != " . (int) $this->id
            ;
            $this->_db->setQuery( $query );
            $xid = intval( $this->_db->loadResult() );
            if ($xid && $xid != intval( $this->id )) {
                $this->_error = addslashes( 'Bu e-posta adresi zaten var' );
                return false;
            }

        return true;
    }

    function store( $updateNULLs=false ) {
        
        $k = $this->_tbl_key;
        $key =  $this->$k;
        if( $key && !$migrate) {
            // existing record
            $ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNULLs );
        } else {
            // new record
            $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
        }
        if( !$ret ) {
            $this->_error = strtolower(get_class( $this ))."::kayıt başarısız <br />" . $this->_db->getErrorMsg();
            return false;
        } else {
            return true;
        }
    }

    function delete( $oid=NULL ) {

        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }
        
        $query = "DELETE FROM $this->_tbl"
        . "\n WHERE $this->_tbl_key = " . (int) $this->$k
        ;
        $this->_db->setQuery( $query );
    }
    

    function activateUser($oid=NULL) {
        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }
        
        $query = "UPDATE $this->_tbl"
        . "\n SET activated=1, activation=''"
        . "\n WHERE $this->_tbl_key = " . (int) $this->$k
        ;
        $this->_db->setQuery($query);
        $this->_db->query();        
    }   
}
