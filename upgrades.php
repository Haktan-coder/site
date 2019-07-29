<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

$page['name'] = $lang['Upgrades'];
$menu['upgrades'] = 'active';

if(isset($_GET['enable'])) {
$feature = $_GET['enable'];

if($feature == 1) {
if($user['energy'] >= 50) {
$db->query("UPDATE users SET is_incognito='1' WHERE id='".$user['id']."'");
$db->query("UPDATE users SET energy=energy-50 WHERE id='".$user['id']."'");
header("Location: upgrades.php");
exit;
} else {
$error = 'Not enough energy!';
}
}

if($feature == 2) {
if($user['energy'] >= 100) {
$db->query("UPDATE users SET is_verified='1' WHERE id='".$user['id']."'");
$db->query("UPDATE users SET energy=energy-100 WHERE id='".$user['id']."'");	
header("Location: upgrades.php");
exit;
} else {
$error = 'Not enough energy!';
}
}

if($feature == 3) {
if($user['energy'] >= 200) {
$db->query("UPDATE users SET has_disabled_ads='1' WHERE id='".$user['id']."'");  
$db->query("UPDATE users SET energy=energy-200 WHERE id='".$user['id']."'");    
header("Location: upgrades.php");
exit;
} else {
$error = 'Not enough energy!';
}
}


}

// Process Payment
if(isset($_POST['continue'])) {

$payment_method = $_POST['payment_method'];

switch ($_POST['energy_amount']) {
    case 50:
        $price = 1;
        break;
    case 100:
        $price = 2;
        break;
    case 200:
        $price = 4;
        break;
    case 300:
        $price = 6;
        break;
    case 400:
        $price = 8;
        break;
    case 500:
        $price = 10;
        break;
    case 600:
        $price = 12;
        break;
    case 700:
        $price = 14;
        break;
    case 800:
        $price = 16;
        break;
    case 900:
        $price = 18;
        break;
    case 1000:
        $price = 20;
        break;
    default:
        $price = 1;
        break;
}

if($payment_method == 'paypal') {

// Prepare GET data
$query = array();
$query['notify_url'] = $domain.'/app/paypal.php';
$query['cmd'] = '_xclick';
$query['business'] = $paypal_email;
$query['currency_code'] = 'USD';
$query['custom'] = $_POST['energy_amount'].'/'.$user['id'];
$query['return'] = $domain.'/app/upgrades.php';
$query['item_name'] = $_POST['energy_amount'].' '.$lang['Energy'];
$query['quantity'] = 1;
$query['amount'] = $price;
// Prepare query string
$query_string = http_build_query($query);
header('Location: https://www.paypal.com/cgi-bin/webscr?' . $query_string);


} elseif($payment_method == 'stripe') {

$token = md5(time());
$name = $_POST['energy_amount'].' '.$lang['Energy'];
$db->query("INSERT INTO transactions(transaction_amount,transaction_name,status,user_id,token,energy_to_add,method) VALUES ('".$price."','".$name."','0','".$user['id']."','".$token."','".$_POST['energy_amount']."','2')");
header("Location: process.php?t=".$token);

} elseif($payment_method == 'sms') {

$token = md5(time());
$name = $_POST['energy_amount'].' '.$lang['Energy'];
$db->query("INSERT INTO transactions(transaction_amount,transaction_name,status,user_id,token,energy_to_add,method) VALUES ('".$price."','".$name."','0','".$user['id']."','".$token."','".$_POST['energy_amount']."','3')");
header("Location: process.php?t=".$token);

}

}

require('inc/top.php');
?>
<section>
<div class="content-wrapper">
<h3><?php echo $lang['Upgrades']?></h3>
<div class="container-fluid">
    <?php if(isset($error)) { ?> <div class="alert alert-danger"> <i class="fa fa-warning fa-fw"></i> <?php echo $error; ?> </div> <?php } ?>
    <div class="panel panel-default">
        <div class="panel-heading panel-title"> <?php echo $lang['Buy_Energy']?> </div>
        <div class="panel-body text-center">
            <?php echo sprintf($lang['Current_Energy'], $user['energy'])?> <br> <?php echo $lang['Energy_Explanation']?> <br><br>
            <a data-toggle="modal" data-target="#submitPayment" class="btn btn-danger"><i class="fa fa-shopping-cart fa-fw"></i> <?=$lang['Buy_Energy']?></a> 
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading panel-title"> <?php echo $lang['Features']?> </div>
        <div class="panel-body text-center">
            <table class="table table-responsive">
                <thead>
                    <th style="text-align:center;"> <?php echo $lang['Feature']?> </th>
                    <th style="text-align:center;"> <?php echo $lang['Description']?> </th>
                    <th style="text-align:center;"> <?php echo $lang['Price']?> </th>
                </thead>
                <tbody>
                    <tr>
                        <td> <b class="pull-left"> <i class="fa fa-eye-slash fa-lg fa-fw"></i> <?php echo $lang['Incognito_Mode']?> </b> </td>
                        <td> <?php echo $lang['Incognito_Mode_Explanation']?> </td>
                        <td> <?php if($user['is_incognito'] == 0) { ?> <a href="?enable=1" class="btn btn-danger"> <?php echo $lang['Enable']?> (50) </a> <?php } else { ?> <a href="#" class="btn btn-default" disabled> <i class="fa fa-check fa-fw"></i> <?php echo $lang['Active']?> </a> <?php } ?> </td>
                    </tr>
                    <tr>
                        <td> <b class="pull-left"> <i class="fa fa-check fa-lg fa-fw"></i> <?php echo $lang['Verified_Badge']?> </b> </td>
                        <td> <?php echo $lang['Verified_Badge_Explanation']?> </td>
                        <td> <?php if($user['is_verified'] == 0) { ?> <a href="?enable=2" class="btn btn-danger"> <?php echo $lang['Enable']?> (100) </a> <?php } else { ?> <a href="#" class="btn btn-default" disabled> <i class="fa fa-check fa-fw"></i> <?php echo $lang['Active']?> </a> <?php } ?> </td>
                    </tr>
                    <tr>
                        <td> <b class="pull-left"> <i class="fa fa-times fa-lg fa-fw"></i> <?php echo $lang['Disable_Ads']?> </b> </td>
                        <td> <?php echo $lang['Disable_Ads_Explanation']?> </td>
                        <td> <?php if($user['has_disabled_ads'] == 0) { ?> <a href="?enable=3" class="btn btn-danger"> <?php echo $lang['Enable']?> (200) </a> <?php } else { ?> <a href="#" class="btn btn-default" disabled> <i class="fa fa-check fa-fw"></i> <?php echo $lang['Active']?> </a> <?php } ?> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
</section>
<?php
require('inc/bottom.php'); 
?>
