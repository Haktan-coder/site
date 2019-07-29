<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');
require('core/dom.php');

$id = $db->real_escape_string($_GET['id']);

function getInstagramPhotos($username) {
  $images = array();
  $html = file_get_html('http://websta.me/n/'.$username);
  if($html) {
    foreach($html->find('a[class=mainimg]') as $element) {
      $result = $element->innertext;
      $result = str_replace('src', '', $result);
      $result = str_replace('"', '', $result);
      $result = str_replace('img', '', $result);
      $result = str_replace('width', '', $result);
      $result = str_replace('height', '', $result);
      $result = str_replace('>', '', $result);
      $result = str_replace('<', '', $result);
      $result = str_replace('=', '', $result);
      $result = str_replace('306 heigh306', '', $result);
      $result = str_replace('s320x320/', '', $result);
      $images[] = trim($result);
    }
  }
  return $images;
}

$profile = $db->query("SELECT * FROM users WHERE id='$id'");
if($profile->num_rows >= 1) {
 $profile = $profile->fetch_array();
} else {
 header("Location: home.php");
 exit;
}

$split_name = splitName($profile['full_name']);

$page['name'] = sprintf($lang['sProfile'], $split_name['first_name']);
$menu['home'] = 'active';

$profile_picture = $domain.'/app/uploads/'.$profile['profile_picture'];

$page['css'] .= '<link rel="stylesheet" href="'.$domain.'/vendor/lightbox/ekko-lightbox.min.css"><style> @media (min-width:750px){ .ekko-lightbox .modal-dialog { height:40% !important; width:40% !important; } } .glyphicon-chevron-right,.glyphicon-chevron-left { text-decoration:none !important; } </style>';
$page['js'] .= '<script src="'.$domain.'/vendor/lightbox/ekko-lightbox.min.js"></script>';
$page['js'] .= '
<script>
$(document).delegate(\'*[data-toggle="lightbox"]\', \'click\', function(event) {
  event.preventDefault();
  $(this).ekkoLightbox();
});
</script>
';

if($user['is_incognito'] == 0) {
// Record profile view
  $check = $db->query("SELECT id FROM profile_views WHERE
   viewer_id='".$user['id']."' AND profile_id='".$profile['id']."' LIMIT 1");
  if($check->num_rows == 0) {
    $time = time();
    $db->query("INSERT INTO profile_views (profile_id,viewer_id,time) VALUES ('$id','".$user['id']."','".$time."')");
  }
}
// Check profile likes
$check = $db->query("SELECT id FROM profile_likes WHERE 
  viewer_id='".$user['id']."' AND profile_id='".$profile['id']."' LIMIT 1");
if($check->num_rows == 0) {
  $can_like = 1;
} else {
  $can_like = 0;
}
// Check friend status
$check = $db->query("SELECT id FROM friend_requests WHERE 
  (user1='".$user['id']."' AND user2='".$profile['id']."') OR (user1='".$profile['id']."' AND user2='".$user['id']."') LIMIT 1");
if($check->num_rows == 0) {
  $send_request = 1;
} else {
  $send_request = 0;
}
$check = $db->query("SELECT id FROM friend_requests WHERE
 (user1='".$user['id']."' AND user2='".$profile['id']."' AND 
  accepted='1') OR (user1='".$profile['id']."' AND user2='".$user['id']."' AND accepted='1')");
if($check->num_rows == 0) {
  $is_friend = 0;
} else {
  $is_friend = 1;
}
// Get profile views
$profile_views = $db->query("SELECT * FROM profile_views WHERE profile_id='".$id."'");
$profile_views = $profile_views->num_rows;
// Get profile likes
$profile_likes = $db->query("SELECT * FROM profile_likes WHERE profile_id='".$id."'");
$profile_likes = $profile_likes->num_rows;
// Get friends
$friends = $db->query("SELECT * FROM friend_requests WHERE accepted='1' AND (user2='".$profile['id']."' OR user1='".$profile['id']."')");
$friend_count= $friends->num_rows;
$friends = $db->query("SELECT * FROM friend_requests WHERE accepted='1' AND (user2='".$profile['id']."' OR user1='".$profile['id']."') LIMIT 4");

$page['js'] .= '
<script>
function likeProfile(id) {
  $.get("'.$domain.'/app/ajax/likeProfile.php?id="+id, function(data) {
    $("#like").html(data);
    var current_likes = parseInt("'.$profile_likes.'");
    $("#likes_number").text(current_likes+1);
  });
}
</script>
';

$media = $db->query("SELECT * FROM media WHERE user_id='".$profile['id']."' LIMIT 20");

require('inc/top.php');
?>
  <section>
  <div class="content-wrapper">
  <div class="unwrap">
  <div class="container-fluid">
    <div style="background-image: url(img/profile-bg.png); background-repeat: repeat; background-size: inherit;" class="bg-cover">
      <div class="p-xl text-center text-white">
        <?php if(!isGraph(getProfilePicture($domain,$profile))) { ?>
        <a href="<?php echo getProfilePicture($domain,$profile)?>" data-toggle="lightbox">
          <img src="<?php echo getProfilePicture($domain,$profile)?>" alt="<?php echo $profile['full_name']?>" class="img-thumbnail img-circle thumb128">
        </a>
        <? } else { ?>
        <img src="<?php echo getProfilePicture($domain,$profile)?>" alt="<?php echo $profile['full_name']?>" class="img-thumbnail img-circle thumb128">
        <? } ?>
        <h3 class="m0"><?php echo $profile['full_name']?> <?php if(time()-$profile['last_active'] <= 300) { ?> <span class="circle circle-success circle-lg" style="margin:0px;"></span> <?php } else { ?> <span class="circle circle-danger circle-lg" style="margin:0px;"></span> <?php } ?></h3> <?php if($profile['is_verified'] == 1) { ?> <div class="label label-success" style="margin-right:5px;"> <i class="fa fa-check"></i> Verified </div> <?php } ?>
        <small><?php echo parseEmoticons($domain,$profile['bio'])?></small>
      </div>
    </div>
    <div class="text-center bg-gray-dark p-lg mb-xl">
      <div class="row row-table">
        <div class="col-xs-4 br">
          <h3 class="m0"><?php echo $profile_views?></h3>
          <p class="m0">
            <span class="hidden-xs"><?php echo $lang['Profile']?></span>
            <span><?php echo $lang['Views']?></span>
          </p>
        </div>
        <div class="col-xs-4 br">
          <h3 class="m0" id="likes_number"><?php echo $profile_likes?></h3>
          <p class="m0"><?php echo $lang['Likes']?></p>
        </div>
        <div class="col-xs-4">
          <h3 class="m0"><?php echo $friend_count?></h3>
          <p class="m0"><?php echo $lang['Friends']?></p>
        </div>
      </div>
    </div>
    <?php if($profile['id'] !== $user['id']) { ?>
    <div class="p-lg">
      <div class="row">
        <div class="col-md-12">
          <div class="pull-left">
            <span id="like">
              <?php if($can_like == 1) { ?>
              <button class="btn btn-danger bg-danger-light" onclick="likeProfile(<?php echo $id?>)"> <i class="fa fa-heart fa-fw"></i> <?php echo $lang['Like']?> </button>
              <?php } else { ?>
              <button class="btn btn-danger bg-danger-light" disabled> <i class="fa fa-heart fa-fw"></i> <?php echo $lang['Liked']?> </button>
              <?php } ?>
            </span>
          </div>
          <div class="pull-right">
            <a href="<?php echo $domain?>/app/chat.php?id=<?php echo $profile['id']?>" class="btn btn-primary bg-primary-light"> <i class="fa fa fa-comment fa-fw"></i> <?php echo $lang['Chat']?> </a> 
            <span id="addFriend">
              <?php if($is_friend == 0 && $send_request == 0) { ?>
              <button class="btn btn-primary bg-primary-light" disabled> <i class="fa fa-user-plus fa-fw"></i> <?php echo $lang['Friend_Request_Sent']?> </button>
              <?php } elseif($is_friend == 1 && $send_request == 0) { ?>
              <button class="btn btn-primary bg-primary-light" disabled> <i class="fa fa-user-plus fa-fw"></i> <?php echo $split_name['first_name']?> <?php echo $lang['is_your_friend']?> </button>
              <?php } elseif($is_friend == 0 && $send_request == 1) { ?>
              <button class="btn btn-primary bg-primary-light" onclick="sendFriendRequest(<?php echo $user['id']?>,<?php echo $id?>)"> <i class="fa fa-user-plus fa-fw"></i> <?php echo $lang['Add_As_Friend']?> </button>
              <?php } ?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
    <?php if($profile['private_profile'] == 0 || $profile['id'] == $user['id'] || $is_friend == 1) { ?>
    <div class="p-lg">
      <div class="row">
        <div class="col-md-9">

          <div class="panel panel-default">
            <div class="panel-heading panel-title"> <i class="fa fa-image"></i> Uploaded Photos <?php if($profile['id'] == $user['id']) { ?><div class="pull-right"> <a href="upload.php" class="btn btn-danger btn-xs">  <i class="fa fa-upload fa-fw"></i> <?php echo $lang['Upload']?> </a> <a href="manage-photos.php" class="btn btn-danger btn-xs">  <i class="fa fa-gear fa-fw"></i> <?php echo $lang['Manage']?> </a> </div><?php } ?></div>
            <div class="panel-body">
              <?php 
              if($media->num_rows == 0) { echo '<p>'.$lang['No_Photos_To_Show'].'</p>';
            } else {
              while($photo = $media->fetch_array()) {
                ?>
                <div class="col-md-3 pull-left">
                  <a href="<?php echo $domain?>/app/photo.php?id=<?php echo $photo['id']?>"><img src="uploads/<?php echo $photo['path']?>" class="img-responsive img-thumbnail" style="margin-bottom:4px;"></a>
                </div>
                <?  
              }
            } ?>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading panel-title"> <i class="fa fa-instagram"></i> <?php echo $lang['Instagram_Photos']?> </div>
          <div class="panel-body">
            <div class="row" style="margin:0px;">
              <?php 
              $c = 0;
              if(!empty($profile['instagram_username'])) { foreach(getInstagramPhotos($profile['instagram_username']) as $image) { 
                $c++;
                ?>
                <div class="col-md-3 pull-left">
                  <a href="<?php echo $image?>" data-toggle="lightbox" data-gallery="instagram_gallery"><img src="<?php echo $image?>" class="img-responsive img-thumbnail" style="margin-bottom:4px;"></a>
                </div>
                <?php 
              } if($c >= 3) { echo '</div> <div class="row">'; $c=0; } elseif($c == 0) { echo '<p>'.$lang['No_Photos_To_Show'].'</p>'; }
            } else { echo '<p>'.$lang['Instagram_Not_Connected'].'</p>'; } 
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="text-center">
            <h3 class="mt0"><?php echo $lang['About']?></h3>
          </div>
          <hr>
          <ul class="list-unstyled ph-xl">
            <li> <i class="fa fa-map-marker" style="margin-right:1px;"></i> <?php echo $profile['city']?> <?php if(!empty($profile['city'])) { echo ','; } ?> <?php echo $profile['country']?> </li>
            <li> <i class="fa fa-birthday-cake" style="margin-right:1px;"></i> <?php echo $profile['age']?> <?php echo $lang['years_old']?> </li>
          </ul>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <?php echo $lang['Friends']?></div>
          <?php if($friends->num_rows >= 1) { ?>
          <div class="list-group">
            <?php while($friend = $friends->fetch_array()) { $fr = $db->query("SELECT id,profile_picture,age,full_name FROM users WHERE (id='".$friend['user1']."' OR id='".$friend['user2']."') AND id != '".$profile['id']."'")->fetch_array(); ?>
            <a href="profile.php?id=<?php echo $fr['id']?>" class="media p mt0 list-group-item">
              <span class="pull-left">
                <img src="<?php echo getProfilePicture($domain,$fr)?>" alt="<?php echo $fr['full_name']?>" class="media-object img-circle thumb32">
              </span>
              <span class="media-body">
                <span class="media-heading">
                  <strong><?php echo $fr['full_name']?></strong>
                  <br>
                  <small class="text-muted"><?php echo $fr['age']?> <?php echo $lang['years_old']?></small>
                </span>
              </span>
            </a>
            <?php } ?>
            <a href="<?php echo $domain?>/app/friends.php?id=<?php echo $id?>" class="media p mt0 list-group-item text-center text-muted"><?php echo $lang['View_All_Friends']?></a>
          </div>
          <?php } else { echo '<p class="text-center">'.$lang['Nothing_To_Show'].'</p>'; } ?>
        </div>
        <?php
        // Ad Slot
        require("core/config/config-ads.php");
        if($ads && $user['has_disabled_ads'] == 0) {
          echo '<div class="img-responsive hidden-xs hidden-sm">'.$ad_side.'</div>';
        }
        ?>
      </div>
    </div>
    <?php
    // Ad Slot
    if($ads && $user['has_disabled_ads'] == 0) {
      echo '<div class="img-responsive centered-block hidden-xs hidden-sm">'.$ad_bottom.'</div>';
    }
    ?>
  </div>
  <?php } else { ?>
  <p class="text-center"> <i class="fa fa-lock" style="margin-right:2px;height:90px;line-height:90px;"></i> <?php echo $lang['This_Profile_Is_Private']?> </p>
  <?php } ?>
  </div>
  </div>
  </div>
  </section> 
<?php
require('inc/bottom.php'); 
?>
