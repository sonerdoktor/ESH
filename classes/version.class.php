<?php
// no direct access
defined( 'KAPAT' ) or die( 'Bu dosyayı görmeye yetkiniz yok!' ); 

class Version {
    var $PRODUCT     = 'Evde Sağlık Hizmetleri Sistemi';
    
    var $RELEASE     = '3.0';
    
    var $DEV_STATUS  = 'Beta';
    
    var $DEV_LEVEL   = '0';
    
    var $CODENAME    = 'Kibyra';
    
    var $RELDATE     = '21 Mayıs 2024';
    
    var $RELTIME     = '21:00';
    
    var $COPYRIGHT   = "Copyright © 2024 Soner Ekici. Tüm hakları saklıdır.";
    
    var $URL         = 'Coded by <a href="" target="_blank">Soner Ekici</a>';

    /**
     * @return string Long format version
     */
    function getLongVersion() {
        return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL .' '
            . $this->DEV_STATUS
            .' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
            . $this->RELTIME;
    }

    function getShortVersion() {
        return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL;
    }
    
    function getCopy() {
        return $this->COPYRIGHT;
    }
}