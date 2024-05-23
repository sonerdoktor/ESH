<?php
// no direct access
defined( 'KAPAT' ) or die('Bu dosyayı görmeye yetkiniz yok!');

$mainframe->setPageTitle('Giriş');
$mainframe->addStyleSheet(SITEURL.'/templates/'.TEMPLATE.'/css/template.css');

include('header.php');
?>

<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <span class="h4"><?php echo SHORTHEAD;?></span>
    </div>
    <div class="card-body">
      <p class="login-box-msg"></p>

      <form action="index.php" method="post">
      
        <div class="input-group mb-3">
          <input type="username" class="form-control" placeholder="Kullanıcı adı">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Parola">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Beni hatırla
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mb-1">
        <a href="forgot-password.html">Şifremi unuttum</a>
      </p>
      <p class="mb-0">
        <a href="register.html" class="text-center">Yeni kayıt oluştur</a>
      </p>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
</body>
<?php include('footer.php');?>