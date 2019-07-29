<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

$geo = geoinfo();

if(isset($_POST['register'])) {

$full_name = ucwords($_POST['full_name']);
$email = $_POST['email'];
$password = trim($_POST['password']);
$time = time();
$age = $_POST['age'];
$gender = $_POST['gender'];

// Geolocation
$country = $geo['geoplugin_countryName'];
$city = '';
$ip = $_SERVER['REMOTE_ADDR'];
$latitude = $geo['geoplugin_latitude'];
$longitude = $geo['geoplugin_longitude'];

// Check duplicate
$check_d = $db->query("SELECT id FROM users WHERE email='".$email."'")->num_rows;
if($check_d == 0) {
$db->query("INSERT INTO users(profile_picture,full_name,email,password,registered,country,city,energy,age,gender,ip,latitude,longitude,local_range) VALUES ('default_avatar.png','$full_name','$email','"._hash($password)."','$time','$country','$city','10','$age','$gender','$ip','$latitude','$longitude','0,150')");
setcookie('justRegistered', 'true', time()+6);
setcookie('mm-email',$email,time()+60*60*24*30,'/');
header('Location: login.php');
exit;
}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" content="">
   <meta name="keywords" content="">
   <title><?php echo $lang['Register']?></title>
   <link rel="stylesheet" href="<?php echo $domain?>/vendor/fontawesome/css/font-awesome.min.css">
   <link rel="stylesheet" href="<?php echo $domain?>/vendor/simple-line-icons/css/simple-line-icons.css">
   <link rel="stylesheet" href="<?php echo $domain?>/app/css/bootstrap.css" id="bscss">
   <link rel="stylesheet" href="<?php echo $domain?>/app/css/app.css" id="maincss">
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
      <div class="panel-heading text-center">
         <a href="#">
            <img src="img/logo.png" alt="Image" class="block-center img-rounded">
         </a>
      </div>
      <div class="panel-body">
         <p class="text-center pv"><?php echo $lang['Register_To_Get_Instant_Access']?></p>
         <form action="" method="post" role="form" data-parsley-validate="" novalidate="" class="mb-lg">
            <div class="form-group has-feedback">
               <label for="signupInputFullName1" class="text-muted"><?php echo $lang['Full_Name']?></label>
               <input id="signupInputFullName1" type="text" name="full_name" placeholder="<?php echo $lang['Enter_Full_Name']?>" autocomplete="off" required class="form-control">
               <span class="fa fa-info-circle form-control-feedback text-muted"></span>
            </div>
            <div class="form-group has-feedback">
               <label for="signupInputEmail1" class="text-muted"><?php echo $lang['Email']?></label>
               <input id="signupInputEmail1" type="email" name="email" placeholder="<?php echo $lang['Enter_Email']?>" autocomplete="off" required class="form-control">
               <span class="fa fa-envelope form-control-feedback text-muted"></span>
            </div>
            <div class="form-group has-feedback">
               <label for="signupInputPassword1" class="text-muted"><?php echo $lang['Password']?></label>
               <input id="signupInputPassword1" type="password" name="password" placeholder="<?php echo $lang['Enter_Password']?>" autocomplete="off" required class="form-control">
               <span class="fa fa-lock form-control-feedback text-muted"></span>
            </div>
            <div class="form-group has-feedback">
               <label for="signupInputAge1" class="text-muted"><?php echo $lang['Age']?></label>
               <select name="age" autocomplete="off" required class="form-control">
               <?php for($i = 18; $i <= 70; $i++) { ?>
               <option value="<?php echo $i?>"> <?php echo $i?> </option>
               <?php } ?>
               </select>
            </div>
            <div class="form-group has-feedback">
               <label for="signupInputGender1" class="text-muted"><?php echo $lang['Gender']?></label>
               <select name="gender" autocomplete="off" required class="form-control">
               <option value="Male"> <?php echo $lang['Male']?> </option>
               <option value="Female"> <?php echo $lang['Female']?> </option>
               </select>
            </div>

            <button type="submit" name="register" class="btn btn-block btn-danger mt-lg"><?php echo $lang['Create_Account']?></button>
         </form>
         <p class="pt-lg text-center"><?php echo $lang['Have_account']?></p><a href="login.php" class="btn btn-block btn-default"><?php echo $lang['Log_In']?></a>
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
