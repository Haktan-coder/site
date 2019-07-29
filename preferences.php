<?php
session_start();
require('core/config/config.php');
require('core/config/config-theme.php');
require('core/system.php');

$_SESSION['confirmation'] = true;

$page['name'] = $lang['Preferences'];
$menu['preferences'] = 'active';

$page['js'] .= '<script src="'.$domain.'/vendor/parsleyjs/dist/parsley.min.js"></script>';
$page['js'] .= '<script type="text/javascript">$("#preferences").parsley();</script>';
$page['js'] .= '<script src="'.$domain.'/vendor/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js"></script>';
$page['js'] .= '<script>$("[data-ui-slider]").slider();</script>';
$page['css'] .= '<link rel="stylesheet" href="'.$domain.'/vendor/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css">';

$countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

if($user['local_dating'] == 1) {
  $local_dating = 'checked';
}

$local_range = $user['local_range'];

if(isset($_POST['save'])) {
  $city = ucfirst(strip_tags($_POST['city']));
  $sexual_interest = $_POST['sexual_interest'];
  $local_range = $_POST['local_range'];
  $country = $_POST['country'];
  $age = strip_tags($_POST['age']);

  if(isset($_POST['local_dating'])) {
    $local_dating = 1;
  } else {
    $local_dating= 0;
  }

  if($user['updated_preferences'] == 0) {

    $page['js'] .= '
    <script>
    swal({ 
      title: "'.$lang['Ready_Set_Go'].'",
      text: "'.$lang['Updated_Preferences_Success'].'",
      imageUrl: "'.$domain.'/app/img/thumbs-up.png",
      confirmButtonText: "OK",
      confirmButtonColor: "#3a3f51;",
      closeOnConfirm: false,
      closeOnCancel: false
    },
    function(){
      window.location.href = "home.php";
    });
</script>
';

$db->query("UPDATE users SET city='$city',local_dating='$local_dating',sexual_interest='$sexual_interest',local_range='$local_range',country='$country',age='$age' WHERE id='".$user['id']."'");
$db->query("UPDATE users SET updated_preferences='1' WHERE id='".$user['id']."'");

} else {
  $db->query("UPDATE users SET city='$city',local_dating='$local_dating',sexual_interest='$sexual_interest',local_range='$local_range',country='$country',age='$age' WHERE id='".$user['id']."'");
  header("Location: preferences.php");
  exit; 
}

} 

require('inc/top.php');
?>
<section>
<div class="content-wrapper">
<div class="container-fluid">
<div class="col-md-9">
<div class="row">
<div class="panel panel-default">
<div class="panel-heading"><?php echo $lang['Preferences']?></div>
<div class="panel-body">
<p class="text-muted"><?php echo $lang['Preferences_Explanation']?></p>
<form action="" method="post" role="form" id="preferences">
  <div class="form-group">
    <label><?php echo $lang['Country']?></label>
    <select name="country" class="form-control">
      <?php foreach($countries as $country) { 
        if($country == $user['country']) {
          echo '<option value="'.$country.'" selected>'.$country.'</option>';
        } else {
          echo '<option value="'.$country.'">'.$country.'</option>';
        }
      } ?>
    </select>
  </div>
  <div class="form-group has-feedback">
    <label for="city"><?php echo $lang['City']?></label>
    <input type="text" name="city" class="form-control" value="<?php echo $user['city']?>" required>
  </div>
  <div class="form-group">
    <label><?php echo $lang['Age']?></label>
    <input type="text" name="age" class="form-control" value="<?php echo $user['age']?>" required>
  </div>
  <div class="form-group">
    <label><?php echo $lang['Gender']?></label>
    <input type="text" name="gender" class="form-control" value="<?php echo $lang[$user['gender']]?>" disabled>
  </div>
  <div class="form-group has-feedback">
    <label for="sexual_interest"><?php echo $lang['Sexual_Interest']?></label>
    <select name="sexual_interest" class="form-control" required>
      <option value="1" <?php if($user['sexual_interest'] == 1) { echo 'selected'; } ?>> <?php echo $lang['I_Like_Men']?> </option>
      <option value="2" <?php if($user['sexual_interest'] == 2) { echo 'selected'; } ?>> <?php echo $lang['I_Like_Women']?> </option>
      <option value="3" <?php if($user['sexual_interest'] == 3) { echo 'selected'; } ?>> <?php echo $lang['I_Like_Both']?> </option>
    </select>
  </div>
  <div class="form-group">
    <label><?php echo $lang['Local_Dating']?></label>
    <div class="checkbox c-checkbox">
      <label>
        <input type="checkbox" name="local_dating" <?php echo $local_dating?> value="">
        <span class="fa fa-check"></span><p class="text-muted" style="display:inline;"><?php echo $lang['Local_Dating_Explanation']?></p></label>
      </div>
    </div>
    <div class="form-group mb-xl">
      <label class="control-label mb"><?php echo $lang['Range_Km']?> </label>
      <br>
      <input data-ui-slider="" name="local_range" type="text" value="<?php echo $local_range?>" data-slider-min="0" data-slider-max="150" data-slider-step="5" data-slider-value="[<?php echo $local_range?>]" class="slider"> <br>
      <p class="text-muted" style="display:inline;"><?php echo $lang['Range_Explanation']?></p>
    </div>
    <input type="submit" name="save" class="btn btn-danger" value="<?php echo $lang['Save']?>">
  </form>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<?php
require('inc/bottom.php'); 
?>
