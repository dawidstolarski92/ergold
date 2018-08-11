<?php
include_once("recaptchalib.php");
define("PUB_KEY", "YOUR PUBLIC KEY HERE");
?>
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>Contact Form With reCaptcha</title>
<style type='text/css'> 
body {
font-size: 14px;
line-height:1.3em;
text-align:center;
}
 
#wrapper {
width:600px;
margin:0 auto;
text-align:left;
padding:6px;
}

.message {
	text-align:left;
	width:100%;
	padding:15px 22px;
	display:none;
}

.loader {
	background:url("images/ajax-loader.gif") no-repeat center left;
}

.success {
	background:url("images/success.png") no-repeat center left;
}

.error {
	background:url("images/error.png") no-repeat center left;
}

.infoWrapper {
	clear:both;
	margin-top:10px;
}

.infoTitle {
	color:#808080;
	float:left;
	width:110px;
	text-align:right;
}

.infoContent {
	padding-left:130px;
	text-align: left;
}

label {
	cursor:pointer;
}

.input-text {
	border:1px solid #808080;
}

.long {
	width:450px;
}

.tall {
	height:150px;
}
</style>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js'></script>
<script type="text/javascript" src="functions.js"></script>
</head>
<body>
<div id='wrapper'>

<h4>Contact Us</h4>
<div class='message'>

</div>

<form name='contact' id='contact'>
	<div class="infoWrapper">
		<div class="infoTitle">
			<label for='name'>Name</label>
		</div>
		<div class="infoContent">
			<input type="text" name='name' id='title' class='input-text long' />
		</div>
	</div>
	<div class="infoWrapper">
		<div class="infoTitle">
			<label for='email'>Email</label>
		</div>
		<div class="infoContent">
			<input type="text" name='email' id='title' class='input-text long' />
		</div>
	</div>
	<div class="infoWrapper">
		<div class="infoTitle">
			<label for="message">Message</label>
		</div>
		<div class="infoContent">
			<textarea name='message' id='note' class='input-text long tall'></textarea>
		</div>
	</div>
	<div class="infoWrapper">
		<div class="infoTitle">
			<label for="">Are you human?</label>
		</div>
		<div class="infoContent">
			<?php echo recaptcha_get_html(PUB_KEY); ?>
		</div>
	</div>
	<div class="infoWrapper">
		<div class="infoTitle"></div>
		<div class="infoContent">
			<input type='submit' value='Send Message'/>
		</div>
	</div>
</form>
</div>
</body>
</html>
