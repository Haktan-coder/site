<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

$id = $db->real_escape_string($_GET['id']);

$media = $db->query("SELECT * FROM media WHERE id='".$id."'");

if($media->num_rows == 0) {
	header('Location: home.php');
	exit;
} 

$media = $media->fetch_array();


$page['name'] = $lang['Photo'];

$page['js'] .= '<script src="'.$domain.'/vendor/slimScroll/jquery.slimscroll.min.js"></script>';

$page['css'] .= '
<style> 
@media (min-width: 992px) {
	.col-md-7 {
		width: 53%;
	}
}
</style>
';

$page['js'] .= '
<script>
function appendToComment(str) {
	var message = $("#comment");
	message.val(message.val()+" "+str);
}
</script>
';

if(isset($_POST['post_comment'])) {
	$comment = $_POST['comment'];
	if(!empty($comment)) {
		$db->query("INSERT INTO media_comments (media_id,sender_id,text,time) VALUES ('".$id."','".$user['id']."','".$comment."','".time()."')");
	}
}

$comments = $db->query("SELECT * FROM media_comments WHERE media_id='".$id."'");

require('inc/top.php');
?>
<section>
<div class="content-wrapper">
<div class="row">

<div class="col-md-7 pull-left img-main">
<img src="<?php echo $domain?>/app/uploads/<?php echo $media['path']?>" class="img-single img-responsive img-rounded pull-left"> 
</div>
<div class="col-md-5 pull-left img-comment">
<div class="panel panel-default">
<div class="panel-heading panel-title"> Comments </div>
<div class="panel-body comment-box" style="padding:0px;">
<div data-height="350" data-scrollable="" class="list-group">
<?php while($comment = $comments->fetch_array()) { $sender = $db->query("SELECT full_name,profile_picture FROM users WHERE id='".$comment['sender_id']."'")->fetch_array(); ?>
<a href="#" class="list-group-item" style="border:none;">
<div class="media-box">
<div class="pull-left">
<img src="<?php echo getProfilePicture($domain,$sender)?>" class="media-box-object img-circle thumb32">
</div>
<div class="media-box-body clearfix">
<small class="pull-right"><?php echo time_ago($comment['time'])?></small>
<strong class="media-box-heading text-primary">
<?php echo $sender['full_name']?></strong>
<p class="mb-sm">
<small><?php echo parseEmoticons($domain,$comment['text'])?></small>
</p>
</div>
</div>
</a>
<?php } ?>
</div>
</div>
<div class="panel-footer clearfix has-feedback">
<div class="well" id="emoticons" style="display:none;">
<img src="<?php echo $domain?>/app/img/emoticons/Smile.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(':)')">
<img src="img/emoticons/Wink.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(';)')">
<img src="img/emoticons/Laughing.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(':D')">
<img src="img/emoticons/Heart.png" style="width:18px !important; height:18px !important;" onclick="appendToComment('<3')">
<img src="img/emoticons/Crazy.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(':P')">
<img src="img/emoticons/Money-Mouth.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(':$')">
<img src="img/emoticons/Kiss.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(':*')">
<img src="img/emoticons/Frown.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(':(')">
<img src="img/emoticons/Sealed.png" style="width:18px !important; height:18px !important;" onclick="appendToComment(':X')">
<img src="img/emoticons/Cool.png" style="width:18px !important; height:18px !important;" onclick="appendToComment('8|')">
<img src="img/emoticons/Thumbs-Up.png" style="width:18px !important; height:18px !important;" onclick="appendToComment('(y)')">
</div>
<form action="" method="post">
<div class="input-group">
<input type="text" name="comment" id="comment" placeholder="Enter your comment" class="form-control input-sm" onclick="$('#emoticons').show()">
<span class="input-group-btn">
<button type="submit" name="post_comment" class="btn btn-default btn-sm"><i class="fa fa-send-o"></i>
</span>
</div>
</form>
</div>
</div>
</div>

</div>
</div>
</section>
<?php
require('inc/bottom.php'); 
?>
