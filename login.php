<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

if(isset($_GET['mobileapp'])) {
   $_SESSION['mobileapp'] = true;
} 

if(isset($_SESSION['mobileapp'])) {
   $mobileapp = true;
} else {
   $mobileapp = false;
}

if(isset($_COOKIE['mm-email'])) {
   $email = $_COOKIE['mm-email'];
   $remember = 'checked';
}

// Geolocation
$geo = geoinfo();
$ip = $_SERVER['REMOTE_ADDR'];
$latitude = $geo['lat'];
$longitude = $geo['lon'];
$country = $geo['country'];

if(isset($_POST['login'])) {

   $email = $_POST['email'];
   $password = trim($_POST['password']);

   $check = $db->query("SELECT * FROM users WHERE email='$email'");
   if($check->num_rows >= 1) {
      $user = $check->fetch_array();
      if(_hash($password) == $user['password']) {

         if(isset($_POST['remember'])) {
            setcookie('mm-email',$email,time()+60*60*24*30,'/');
         } else {
            setcookie('mm-email', null, -1, '/');
            $remember = "";
         }

         $_SESSION['auth'] = true;
         $_SESSION['email'] = $email;
         $_SESSION['full_name'] = $user['full_name'];

         // Geolocation
         $geo = geoinfo();
         $ip = $_SERVER['REMOTE_ADDR'];
         $latitude = $geo['geoplugin_latitude'];
         $longitude = $geo['geoplugin_longitude'];

         $db->query("UPDATE users SET last_login=UNIX_TIMESTAMP(),latitude='$latitude',longitude='$longitude',ip='$ip' WHERE email='$email'");

         header("Location: home.php");

      } else {
         $error = 'Invalid Cridentials';
      }

   } else {
      $error = 'Invalid Cridentials';
   }
}

foreach ($lang as $l => $v) {
   $lang[$l] = utf8_encode($v);
}

foreach ($lang as $l => $v) {
   $lang[$l] = utf8_decode($v);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" content="">
   <meta name="keywords" content="">
   <title><?php echo $lang['Login']?></title>
   <link rel="stylesheet" href="<?php echo $domain?>/vendor/fontawesome/css/font-awesome.min.css">
   <link rel="stylesheet" href="<?php echo $domain?>/vendor/simple-line-icons/css/simple-line-icons.css">
   <link rel="stylesheet" href="<?php echo $domain?>/app/css/bootstrap.css" id="bscss">
   <link rel="stylesheet" href="<?php echo $domain?>/app/css/app.css" id="maincss">
   <link rel="stylesheet" href="<?php echo $domain?>/vendor/bootstrap-social/bootstrap-social.css">
   <?php 
   if($theme == 'default') { echo '<link rel="stylesheet" href="'.$domain.'/app/css/theme-red.css">'; } 
   else { echo '<link rel="stylesheet" href="'.$domain.'/app/themes/'.$theme.'/theme.css">'; }
   ?>
</head>

<body>
   <div class="wrapper">
      <div class="block-center col-md-6" style="margin:0 auto;float:none;padding:30px;">
         <!-- START panel-->
         <div class="panel panel-dark panel-flat">
            <div class="panel-heading text-center" <?php if($mobileapp == true) { echo 'style="display:none !important;"'; } ?>>
               <a href="#">
                  <img src="img/logo.png" alt="Image" class="block-center img-rounded">
               </a>
            </div>
            <div class="panel-body">
               <p class="text-center pv"><?php echo $lang['Log_In_To_Continue']?></p>
               <?php if(isset($_COOKIE['justRegistered'])) { ?> <div class="alert alert-success bg-success-light"> <i class="fa fa-check"></i> &nbsp <?php echo $lang['You_Can_Now_Log_In']?> </div> <?php } ?>
               <?php if(isset($error)) { ?> <div class="alert alert-danger bg-danger-light"> <i class="fa fa-warning"></i> &nbsp <?php echo $error?> </div> <?php } ?>
               <form action="fb-login.php" method="POST">
                  <button type="submit" name="fb-login" class="btn btn-block btn-social btn-lg btn-facebook"><i class="fa fa-facebook"></i><?php echo $lang['Log_In_With_Facebook']?></button> <br>
               </form>
               <form action="" method="post" role="form" data-parsley-validate="" novalidate="" class="mb-lg">
                  <div class="form-group has-feedback">
                     <input id="exampleInputEmail1" type="email" name="email" placeholder="<?php echo $lang['Enter_Email']?>" autocomplete="off" required class="form-control" value="<?php echo $email?>">
                     <span class="fa fa-envelope form-control-feedback text-muted"></span>
                  </div>
                  <div class="form-group has-feedback">
                     <input id="exampleInputPassword1" type="password" name="password" placeholder="<?php echo $lang['Enter_Password']?>" required class="form-control">
                     <span class="fa fa-lock form-control-feedback text-muted"></span>
                  </div>
                  <div class="clearfix">
                     <div class="checkbox c-checkbox pull-left mt0">
                        <label>
                           <input type="checkbox" value="" name="remember" <?php echo $remember?>>
                           <span class="fa fa-check"></span><?php echo $lang['Remember_Me']?></label>
                        </div>
                        <div class="pull-right"><a href="#" class="text-muted"><?php echo $lang['Forgot_Your_Password']?></a>
                        </div>
                     </div>
                     <button type="submit" name="login" class="btn btn-block btn-danger mt-lg"><?php echo $lang['Login']?></button>
                  </form>
                  <p class="pt-lg text-center"><?php echo $lang['No_Account']?></p><a href="register.php" class="btn btn-block btn-default"><?php echo $lang['Register']?></a>
               </div>
            </div>
            <!-- END panel-->
            <div class="p-lg text-center">
               <span>&copy; <?php echo date("Y")?> <?php echo $site_name?> </span>
            </div>
         </div>
      </div>
      <script src="<?php echo $domain?>/vendor/modernizr/modernizr.js"></script>
      <script src="<?php echo $domain?>/vendor/jquery/dist/jquery.js"></script>
      <script src="<?php echo $domain?>/vendor/bootstrap/dist/js/bootstrap.js"></script>
      <script src="<?php echo $domain?>/vendor/jQuery-Storage-API/jquery.storageapi.js"></script>
      <script src="<?php echo $domain?>/vendor/parsleyjs/dist/parsley.min.js"></script>
      <script src="<?php echo $domain?>/app/js/app.js"></script>
   </body>
   </html>
