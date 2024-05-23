<?php
// no direct access
defined( 'KAPAT' ) or die('Bu dosyayı görmeye yetkiniz yok!'); 

// Sistemin ayar dosyası
// Tüm veritabanı ve site bilgileri bu dosyada olacak

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'DDH123**?');
define('DB', 'esh');
define('DB_PREFIX', 'esh_');

define('SITEURL', 'http://localhost/esh30/ESH');

define('ABSPATH', dirname(__FILE__));
define('TEMPLATEPATH', dirname(__FILE__).'/templates');
define('MODULEPATH', dirname(__FILE__).'/modules');
define('INCPATH', dirname(__FILE__).'/includes');
define('CLASSPATH', dirname(__FILE__).'/classes');
define('DBTABLEPATH', dirname(__FILE__).'/dbtables');
define('ADMINPATH', dirname(__FILE__).'/admin');

    
define('SITEHEAD', 'Evde Sağlık Hizmetleri Sistemi');
define('SHORTHEAD', 'ESH Sistemi');
define('META_DESC', 'Evde Sağlık Hizmetleri Sistemi');
define('META_KEYS', '');

define('TEMPLATE', 'new');

define('OFFSET', 'Asia/Istanbul');

define('DEBUGMODE', 0);

define('SECRETWORD', 'esh');

define('ERROR_REPORT', 1);

define('OFFLINE', 0);

define('SESSION_TYPE', 2); 