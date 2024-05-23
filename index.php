<?php
// Bu bizim ana dosyamız, tüm işlemler bu dosya içerisinden geçecek
// O yüzden diğer dosyalara direkt erişimi kapatalım
define('KAPAT', 1);

//Config dosyasını alalım
require( dirname( __FILE__ ) . '/config.php' );
//temel fonksiyonları alalım
require( dirname( __FILE__ ) . '/base.php' );

//Oturum oluşturma veya var olan oturuma devam etme
$mainframe = new mainFrame( $dbase );
$mainframe->initSession();

//kimsin nesin?
$my = $mainframe->getUser();

//ziyaretçi isen önce giriş yapman lazım
if (!$my->id) {
    include(TEMPLATEPATH.'/'.TEMPLATE.'/login.php');
    exit();
}






