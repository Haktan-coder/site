<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

$page['name'] = 'Admin Panel';

if($user['is_admin'] != 1) {
	header('Location: index.php');
	exit;	
}

if(isset($_GET['page']) && $_GET['page'] == 'users' && !isset($_GET['edit'])) {

	$page['css'] = '<style> td { text-align:center !important; vertical-align:middle !important; } </style>';

}

if(isset($_GET['page'])) {
	if($_GET['page'] == 'users') { $menu['users'] = 'active'; }
	elseif($_GET['page'] == 'dashboard') { $menu['dashboard'] = 'active'; }
	elseif($_GET['page'] == 'ads') { $menu['ads'] = 'active'; }
	elseif($_GET['page'] == 'builder') { $menu['builder'] = 'active'; }
	elseif($_GET['page'] == 'payments') { $menu['payments'] = 'active'; }
	elseif($_GET['page'] == 'generator') { $menu['generator'] = 'active'; }
	elseif($_GET['page'] == 'settings') { $menu['settings'] = 'active'; }
	elseif($_GET['page'] == 'themes') { $menu['themes'] = 'active'; }
}

if(isset($_GET['delete'])) {
	$id = $_GET['delete'];	
	if($_GET['page'] == 'users') {
		$db->query("DELETE FROM users WHERE id='".$id."'");
		header('Location: admin.php?page=users');
		exit;
	} elseif($_GET['page'] == 'builder') {
		$db->query("DELETE FROM pages WHERE id='".$id."'");
		header('Location: admin.php?page=builder');
		exit;	
	}
}

if(isset($_POST['save']) && isset($_POST['bio'])) {
	$id = $_GET['edit'];
	$email = $_POST['email'];
	$full_name = $_POST['full_name'];
	$bio = $_POST['bio'];
	if(!empty($_POST['password'])) {
		$password = $_POST['password'];
	} else {
		$password = $user['password'];
	}
	$gender= $_POST['gender'];
	$energy = $_POST['energy'];
	if(empty($energy) || !is_numeric($energy)) {
		$energy = 0;	
	}
	$db->query("UPDATE users SET email='".$email."',full_name='".$full_name."',bio='".$bio."',password='"._hash($password)."',gender='".$gender."',energy='".$energy."' WHERE id='".$id."'");
	header('Location: admin.php?page=users');
	exit;
}

if(isset($_POST['save']) && isset($_POST['ad_bottom'])) {
	$status = $_POST['status'];
	$ad_bottom = $_POST['ad_bottom'];
	$ad_side = $_POST['ad_side'];
	$newConfig = "<?php
	\$ads = ".$status.";

	\$ad_bottom = '".stripcslashes($ad_bottom)."';
	\$ad_side = '".stripcslashes($ad_side)."';
	";
	file_put_contents('core/config/config-ads.php',$newConfig);
	header("Location: admin.php?page=ads");
	exit;
}

if(isset($_POST['save']) && isset($_POST['site_name'])) {
	$site_name = $_POST['site_name'];
	$keywords = $_POST['meta_keywords'];
	$description = $_POST['meta_description'];
	$paygol_service_id = $_POST['paygol_service_id'];
	$secret_key = $_POST['secret_key'];
	$publishable_key = $_POST['publishable_key'];
	$paypal_email = $_POST['paypal_email'];
	$fb_app_id = $_POST['fb_app_id'];
	$fb_secret_key = $_POST['fb_secret_key'];
	if(empty($paygol_service_id) || strlen($paygol_service_id) < 2) {
		$paygol_service_id=0;	
	}
	$newConfig = "<?php
	\$domain = '".$domain."';

    // Database Configuration
	\$_db['host'] = '".$_db['host']."';
	\$_db['user'] = '".$_db['user']."';
	\$_db['pass'] = '".$_db['pass']."';
	\$_db['name'] = '".$_db['name']."';

	\$site_name = '".$site_name."';
	\$meta['keywords'] = '".$keywords."';
	\$meta['description'] = '".$description."';

    // PayPal Configuration
	\$paypal_email = '".$paypal_email."'; // Email of PayPal Account to receive money

    // PayGol Configuration (SMS Payments)
	\$paygol_service_id = '".$paygol_service_id."';

    // Stripe Configuration
	\$secret_key = '".$secret_key."'; // Stripe API Secret Key
	\$publishable_key = '".$publishable_key."'; // Stripe API Publishable Key

    // Facebook Login Configuration
	\$fb_app_id = '".$fb_app_id."'; 
	\$fb_secret_key = '".$fb_secret_key."'; 

	\$db = new mysqli(\$_db['host'], \$_db['user'], \$_db['pass'], \$_db['name']) or die('MySQL Error');

	";
	file_put_contents('core/config/config.php',$newConfig);
	header("Location: admin.php?page=settings");
	exit;
}

if(isset($_POST['save']) && isset($_POST['page_title'])) {
	$id = $_GET['edit'];
	$page_title = $_POST['page_title'];
	$content = $_POST['content'];
	$db->query("UPDATE pages SET page_title='".$page_title."',content='".$content."' WHERE id='".$id."'");
	header("Location: admin.php?page=builder");
	exit;
}

if(isset($_POST['add']) && isset($_POST['page_title'])) {
	$page_title = $_POST['page_title'];
	$content = $_POST['content'];
	$db->query("INSERT INTO pages(page_title,content) VALUES ('".$page_title."','".$content."')");
	header("Location: admin.php?page=builder");
	exit;
}

if(isset($_POST['generate'])) {
	$nationality = $_POST['nationality'];
	$gender = $_POST['gender'];
	$info = url_get_contents('http://condor5.com/api/cu.php?gender='.$gender.'&nationality='.$nationality.'');
	$info = json_decode($info,true);
	foreach($info as $array => $arr) {
		$country = getCountryNameByCode($nationality);
		foreach($arr as $a) {   
			$gender = ucfirst($a['user']['gender']);
			$first_name = ucfirst($a['user']['name']['first']);
			$last_name = ucfirst($a['user']['name']['last']);
			$full_name = $first_name.' '.$last_name;
			$email = $a['user']['email'];
			if($nationality == 'DE') {
				$cities = array('Berlin','Hamburg','Minich','Cologne','Frankfurt','Stuttgart','Dortmund','Essen','Bremen','Hanover','Bonn','Aachen','Bochum','Wuppertal','Mannheim');
				$city = $cities[array_rand($cities)];
			} elseif($nationality == 'AR') {
				$cities = array('Buenos Aires','Córdoba','Rosario','Mendoza','Santa Fe','Salta','San Juan','Resistencia','La Plata','Avellaneda','Corrientes','Concordia','Lanús');
				$city = $cities[array_rand($cities)];
			} else {
			$city = ucfirst($a['user']['location']['city']);
			}
			$time = time();
			$profile_picture = $a['user']['picture']['large'];
			$age = mt_rand(20,45);
			$db->query("INSERT INTO users(profile_picture,full_name,email,password,registered,country,city,energy,age,gender,ip,latitude,longitude,local_range) VALUES ('$profile_picture','$full_name','$email','"._hash('123456')."','$time','$country','$city','10','$age','$gender','','60','45','0,150')");
		}
	}
	$success = true;
}

$themes = scandir('themes');
$themes_list = array();
foreach($themes as $theme) {
  if(file_exists('themes'.'/'.$theme.'/'.'manifest.json')) {
  	$info = json_decode(trim(file_get_contents('themes'.'/'.$theme.'/'.'manifest.json')),true);
  	$themes_list[] = array('name' => $info['name'],'author' => $info['author'], 'version' => $info['version'], 'path' => $theme);
  }
}

if(isset($_POST['set_theme'])) {
$theme = $_POST['theme'];
$newConfig = "<?php
	\$theme = '".$theme."';
	";
	file_put_contents('core/config/config-theme.php',$newConfig);
	header("Location: admin.php?page=themes");
	exit;	
}

$purchases = $db->query("SELECT * FROM transactions WHERE status='1'")->num_rows;

require('inc/admin/top.php');
?>
<!-- Main section-->
<section>
	<!-- Page content-->
	<div class="content-wrapper">
		<div class="container-fluid">
			<?php 
			if(isset($_GET['page'])) { 
				if($_GET['page'] == 'dashboard') { 
					$users = $db->query("SELECT id FROM users")->num_rows;
					$views = $db->query("SELECT id FROM profile_views")->num_rows;
					$likes = $db->query("SELECT id FROM profile_likes")->num_rows;
					require("inc/admin/dashboard.tpl.php");
				} elseif($_GET['page'] == 'users') {
					if(!isset($_GET['edit'])) {
						$per_page = 10;
						$count = $db->query("SELECT * FROM users")->num_rows;
						$last_page = ceil($count/$per_page);
						if(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
						if($p < 1) { $p = 1; } elseif($p > $last_page) { $p = $last_page; }
						$limit = 'LIMIT ' .($p - 1) * $per_page .',' .$per_page;
						$users = $db->query("SELECT * FROM users ORDER BY id DESC $limit");
						require("inc/admin/users.tpl.php");
					}
					elseif(isset($_GET['edit'])) {
						$id = $_GET['edit'];
						$user = $db->query("SELECT * FROM users WHERE id='".$id."'")->fetch_array();
						require("inc/admin/edit-user.tpl.php");
					} 
				} elseif($_GET['page'] == 'ads') {
					require("core/config/config-ads.php");
					require("inc/admin/ads.tpl.php");	
				} elseif($_GET['page'] == 'settings') {
					require("inc/admin/settings.tpl.php");	
				} elseif($_GET['page'] == 'builder') {
					if(!isset($_GET['edit']) && !isset($_GET['add'])) {
						$pages = $db->query("SELECT * FROM pages ORDER BY id ASC");
						require("inc/admin/builder.tpl.php");	
					} elseif(isset($_GET['edit'])) {
						$id = $_GET['edit'];
						$page = $db->query("SELECT * FROM pages WHERE id='".$id."'")->fetch_array();
						require("inc/admin/edit-page.tpl.php");
					} elseif(isset($_GET['add'])) {
						require("inc/admin/add-page.tpl.php");
					} 
				} elseif($_GET['page'] == 'payments') {
					$payments = $db->query("SELECT * FROM transactions WHERE status='1' ORDER BY id DESC");
					require("inc/admin/payments.tpl.php");	
				} elseif($_GET['page'] == 'generator') {
					require("inc/admin/generator.tpl.php");	
				} elseif($_GET['page'] == 'themes') {
					require("inc/admin/themes.tpl.php");	
				}
			} 
			?>
		</div>
	</div>
</section>
<?php
require('inc/bottom.php'); 
?>