<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

$id = $db->real_escape_string($_GET['id']);

$_user = $db->query("SELECT full_name,profile_picture,last_active,id FROM users WHERE id='".$id."'");
if($_user->num_rows >= 1) {
	$_user = $_user->fetch_array();
} else {
	header('Location: home.php');
	exit;
}

$split_name = splitName($_user['full_name']);

$page['name'] = $lang['Chat_With'].' '.$split_name['first_name'];
$menu['chat'] = 'active';

$user1 = $user['id'];
$user2 = $_GET['id'];

$convers = $db->query("SELECT id FROM conversations WHERE (user1='".$user1."' AND user2='".$user2."') OR (user1='".$user2."' AND user2='".$user1."')");

if($convers->num_rows < 1) {
  $db->query("INSERT INTO conversations (user1,user2,time) VALUES ('".$user1."','".$user2."','".time()."')");
  $convers_id = $db->insert_id;
} else {
  $convers = $convers->fetch_array();
  $convers_id = $convers['id'];
}


$page['js'] = '
<script src="'.$domain.'/vendor/slimScroll/jquery.slimscroll.min.js"></script>

<script>

function refreshChat() {
 var id = "'.$convers_id.'";
 var receiver = "'.$split_name['first_name'].'";
 $.get("'.$domain.'/app/ajax/refreshChat.php?id="+id+"&receiver="+receiver, function(data) {
    $("#messages").html(data);
  });
}

window.setInterval(function(){
  refreshChat();
}, 1000);

function sendMessage() {
  var user2 = "'.$id.'";
  var message = $("#message");
    if(message.val() != "" && message.val() != " ") {
    $.get("'.$domain.'/app/ajax/sendMessage.php?id="+user2+"&msg="+encodeURIComponent(message.val()), function(data) {
    $("#messages").html(data);
    message.val("");
    });
    }
}

$(document).keypress(function(e) {
    if(e.which == 13) {
        sendMessage();
    }
});

function appendToMessage(str) {
var message = $("#message");
message.val(message.val()+" "+str);
}

</script>
';


$messages = $db->query("SELECT * FROM messages WHERE convers_id='".$convers_id."' AND (sender_id = '".$user1."' OR sender_id='".$user2."') ORDER BY id DESC");

require('inc/top.php');
?>
<section>
<div class="content-wrapper">
<div class="container-fluid">
<div class="col-md-12">
<div class="panel panel-default">
<div class="panel-heading">
<div class="panel-title"><?php echo $_user['full_name']?> <?php if(time()-$_user['last_active'] <= 300) { ?> <span class="circle circle-success circle-lg pull-right" style="margin:0px;margin-top:4px;"></span> <?php } else { ?> <span class="circle circle-danger circle-lg pull-right" style="margin:0px;margin-top:4px;"></span> <?php } ?> </div>
</div>
<div data-height="350" data-scrollable="" class="list-group">
<div id="messages">
<?php 
if($messages->num_rows >= 1) { 
while($message = $messages->fetch_array()) { 
$sender = $db->query("SELECT id,profile_picture,full_name FROM users WHERE id='".$message['sender_id']."'")->fetch_array(); 
?>
<a href="#" class="list-group-item">
<div class="media-box">
<div class="pull-left">
  <img src="<?php echo getProfilePicture($domain,$sender)?>" class="media-box-object img-circle thumb32">
</div>
<div class="media-box-body clearfix">
  <small class="pull-right"><?php echo time_ago($message['time'])?></small>
  <strong class="media-box-heading text-primary">
    <?php echo $sender['full_name']?></strong>
    <p class="mb-sm">
      <small><?php echo parseEmoticons($domain,$message['message'])?></small>
    </p>
  </div>
</div>
</a>
<?php 
} 
} else { 
$split_name = splitName($_user['full_name']); 
echo '<p class="text-center" style="height:90px;line-height:90px"> '.$lang['Start_Conversation'].' <b>'.$split_name['first_name'].'</b></p>'; 
} 
?>
</div>
</div>
<div class="panel-footer clearfix has-feedback">
<div class="well" id="emoticons" style="display:none;">
<img src="<?php echo $domain?>/app/img/emoticons/Smile.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(':)')">
<img src="img/emoticons/Wink.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(';)')">
<img src="img/emoticons/Laughing.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(':D')">
<img src="img/emoticons/Heart.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage('<3')">
<img src="img/emoticons/Crazy.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(':P')">
<img src="img/emoticons/Money-Mouth.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(':$')">
<img src="img/emoticons/Kiss.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(':*')">
<img src="img/emoticons/Frown.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(':(')">
<img src="img/emoticons/Sealed.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage(':X')">
<img src="img/emoticons/Cool.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage('8|')">
<img src="img/emoticons/Thumbs-Up.png" style="width:18px !important; height:18px !important;" onclick="appendToMessage('(y)')">
</div>
<div class="input-group">
<input type="text" id="message" placeholder="<?php echo $lang['Enter_Message']?>" class="form-control input-sm" required>
<span class="input-group-btn">
<button class="btn btn-default btn-sm" onclick="sendMessage()"><i class="fa fa-send-o"></i>
<button class="btn btn-default btn-sm" onclick="$('#emoticons').toggle()"><i class="fa fa-smile-o"></i>
</button>
</span>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<?php
require('inc/bottom.php');