<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(_PS_MODULE_DIR_ . 'pk_testimonials/recaptchalib.php');
require_once(_PS_MODULE_DIR_ . 'pk_testimonials/pk_testimonials.php');

$blockTestimonial = new Pk_Testimonials();

//the response from reCAPTCHA
$resp = null;
$result = "empty";
$message = $title = $name = "";

$name = $blockTestimonial->cleanInput($_POST['testimonial_submitter_name']);
if (!$blockTestimonial->field_validator('Name', $name, '2', '20', '1')) {
	$result = "field_error";
}

$email = $blockTestimonial->cleanInput($_POST['testimonial_submitter_email']);
if (!$blockTestimonial->field_validator('Email', $email, '2', '40', '1')) {
	$result = "field_error";
}

$title = $blockTestimonial->cleanInput($_POST['testimonial_title']);
if (!$blockTestimonial->field_validator('Name', $title, '2', '40', '1')) {
	$result = "field_error";
}

$message = $blockTestimonial->cleanInput($_POST['testimonial_main_message']);
if (!$blockTestimonial->field_validator('Your Testimonial', $message, '2', '250', '1')) {
	$result = "field_error";
}

if (Configuration::get('testimonial_captcha') == 0 && ($result != "field_error") ) 
	$result = "success";

if (intval(Configuration::get('testimonial_captcha')) && ($result != "field_error")) {// if we need to use a recaptcha

	$recaptcha_challenge_field = "";
	$recaptcha_response_field = "";
	
	if (isset($_POST['recaptcha_challenge_field'])) $recaptcha_challenge_field = $_POST['recaptcha_challenge_field'];
	if (isset($_POST['recaptcha_response_field'])) $recaptcha_response_field = $_POST['recaptcha_response_field'];

	$privatekey = Configuration::get('testimonial_captcha_priv');

	$resp = recaptcha_check_answer(
		$privatekey,
		$_SERVER["REMOTE_ADDR"],
		$recaptcha_challenge_field,
		$recaptcha_response_field
	);

	if ($resp->is_valid) {
		$result = "success";
	} else {
		$result = "captcha_error";
	}
}

if ($result == "success") {
	$resp = $blockTestimonial->writeTestimonial(
		$title,
		$name,
		$email,
		$message
	);
	//if (!$resp) 
	//	$result = "DB_error";
}

echo $result;

?>