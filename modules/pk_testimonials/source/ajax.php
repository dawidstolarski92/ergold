<?php
require_once('recaptchalib.php');
define("PRIV_KEY", "YOUR PRIVATE KEY HERE");

$name = filter_var($_POST['name']);
$message = filter_var($_POST['message']);
$email = filter_var($_POST['email']);

if(in_array('', array($name, $message, $email))) {
//one (or more) of the required fields is empty
$result = "field_error";
} else {
	$resp = recaptcha_check_answer (PRIV_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
	if (!$resp->is_valid) {
	    //Captcha was entered incorrectly
		$result = "captcha_error";
	  } else {
	    //Captcha was entered correctly
	    $result = "success";
	}
}

echo $result;
 ?>
