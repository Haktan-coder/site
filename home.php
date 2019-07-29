<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

$page['name'] = $lang['People'];
$menu['home'] = 'active';

if(!isLocalDating($user)) {
  if($user['sexual_interest'] == 1) {
    $users = $db->query("SELECT * FROM users WHERE id != ".$user['id']." AND gender='Male' ORDER BY RAND() LIMIT 8");
  } elseif($user['sexual_interest'] == 2) {
    $users = $db->query("SELECT * FROM users WHERE id != ".$user['id']." AND gender='Female' ORDER BY RAND() LIMIT 8");
  } else {
    $users = $db->query("SELECT * FROM users WHERE id != ".$user['id']." ORDER BY RAND() LIMIT 8");
  }
} else {
  if($user['sexual_interest'] == 1) {
    $users = $db->query("SELECT * FROM users WHERE id != ".$user['id']." AND gender='Male' AND country='".$user['country']."' ORDER BY RAND() LIMIT 8");
  } elseif($user['sexual_interest'] == 2) {
    $users = $db->query("SELECT * FROM users WHERE id != ".$user['id']." AND gender='Female' AND country='".$user['country']."' ORDER BY RAND() LIMIT 8");
  } else {
    $users = $db->query("SELECT * FROM users WHERE id != ".$user['id']." AND country='".$user['country']."' ORDER BY RAND() LIMIT 8");
  }
}

$c=0;

if($user['updated_preferences'] == 0) {
  $page['js'] .= '
  <script>
  swal({ 
    title: "'.$lang['Welcome_Window_Title'].'",
    text: "'.$lang['Welcome_Window_Text'].'",
    imageUrl: "'.$domain.'/app/img/salute.png",
    confirmButtonText: "'.$lang['OK'].'",
    confirmButtonColor: "#3a3f51",
    closeOnConfirm: false,
    closeOnCancel: false
  },
  function(){
    window.location.href = "preferences.php";
  });
</script>
';
} else {

  $page['js'] .= '
  <script>
  function clickHeart(id) {
    heart = $("#heart-"+id);
    $.get("'.$domain.'/app/ajax/clickHeart.php?id="+id, function(data) {
      $("#user-"+id).html(data);
    });
heart.css("color", "#f05050");
heart.animo( { animation: ["tada"], duration: 2 } );
}
</script>
';

}

require('inc/top.php');
?>
<section>
<div class="content-wrapper">
<h3><?php echo $lang['Suggestions']?> <span class="pull-right"> <a href="<?php echo $domain?>/app/home.php" style="color:#929292;"> <i class="fa fa-refresh"></i> </a> </span> </h3>
<div class="container-fluid">
<div class="row">
<?php
$cc=0;
while($match = $users->fetch_array()) { 
$c++; $cc++;
$km = round(coordsToKm($user['latitude'],$user['longitude'],$match['latitude'],$match['longitude']));
$local_range = explode(',',$user['local_range']);
if((isLocalDating($user) && $km >= $local_range[0] && $km <= $local_range[1]) || !isLocalDating($user)) { 
$split_name = splitName($match['full_name']);
?>
<div class="col-md-3" id="userWidget">
<div class="panel widget" style="user-select:none;">
  <div class="panel-heading panel-title" onclick="clickHeart(<?php echo $match['id']?>)" id="user-<?php echo $match['id']?>">
    <?php $check = $db->query("SELECT id FROM profile_likes WHERE viewer_id='".$user['id']."' AND profile_id='".$match['id']."' LIMIT 1"); ?>
    <?php if($check->num_rows == 0) { ?>
    <i class="fa fa-heart fa-lg" id="heart-<?php echo $match['id']?>"></i> 
    <?php } else { ?>
    <i class="fa fa-heart fa-lg" id="heart-<?php echo $match['id']?>" style="color:#f05050;"></i> 
    <?php } ?>
  </div>
  <div class="panel-body text-center">
    <a href="<?php echo $domain?>/app/profile.php?id=<?php echo $match['id']?>" style="color:#656565;text-decoration:none;">
      <img src="<?php echo getProfilePicture($domain,$match)?>" alt="<?php echo $user['full_name']?>" class="center-block img-thumbnail img-circle thumb96"> <br>
      <p style="font-size:17px;"><b> <?php echo $split_name['first_name']?> </b></p>
      <p><?php echo $match['age']?>, <?php echo $lang[$match['gender']]?> </p>
    </div>
    <div class="panel-footer text-center">
      <p>
        <i class="fa fa-map-marker fa-fw"></i>
        <?php 
        if(!isLocalDating($user)) { if($km > 150) { if(!empty($match['city'])) { echo $match['city'].', '.$match['country']; } else { echo $match['country']; } } else { if(isset($km)) { if($km > 0) { echo $km.' '.$lang['km_away']; } else { echo $lang['Very_Close_To_You']; } } } 
      } else { 
        if($km > 0) {
          if($km >= $local_range[0] && $km <= $local_range[1]) {
            echo $km.' '.$lang['km_away'];
          } 
        } else {
          echo $lang['Very_Close_To_You'];
        }
      }
      ?> </p>
    </div>
  </div>
</div>
</a>
<?php } if($cc >= 4) { echo '</div> <div class="row">'; $cc=0; } } ?>
<?php if($c==0) { echo $lang['No_Suggestions_Found']; } ?> 
</div>
</div>
</div>
</section>
<?php
require('inc/bottom.php'); 
?>