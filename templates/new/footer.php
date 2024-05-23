<?php
// no direct access
defined( 'KAPAT' ) or die('Bu dosyayı görmeye yetkiniz yok!'); 

if (DEBUGMODE) {
?>
<div class="card">    
    <div class="card-header">SQL SORGULARI</div>
    <div class="card-body">
    <?php
    echo $dbase->_ticker . ' sorgu çalıştırıldı';
    ?>
    </div>
    <table class="table table-striped">
    <thead>
    <tr>
    <th>SIRA</th>
    <th>SQL SORGUSU</th>
    </tr>
    </thead>
    <tbody>
    <?php
     foreach ($dbase->_log as $k=>$sql) {
         ?>
         <tr>
         <td><?php echo $k+1;?></td>
         <td><?php echo $sql;?></td>
         </tr>
         <?php
    }
    ?>
    </tbody>
    </table>
</div>
    <?php
}  
?>
