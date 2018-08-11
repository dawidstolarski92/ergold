<html>
<title>Instagram Verification Code</title>
<head>
<style>
body {
font-family: 'proxima-nova', 'Helvetica Neue', Arial, Helvetica, sans-serif;
line-height: 20px;
color: #222222;
}
.card {
background: #fcfcfc;
border: 1px solid #a7bcce;
margin: 40px auto;
border-radius: 4px;
box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
width:600px
}
.card header {
padding: 5px 5px 5px 20px;
position: relative;
margin: -1px -1px 0;
border: 1px solid #1c5380;
background-color: #517fa4;
background-image: url("//instagramstatic-a.akamaihd.net/h1/images/shared/noise-1.png/aae6cb061f08.png"), -webkit-gradient(linear, left top, left bottom, from(#517fa4), to(#306088));
background-image: url("//instagramstatic-a.akamaihd.net/h1/images/shared/noise-1.png/aae6cb061f08.png"), -webkit-linear-gradient(top, #517fa4, #306088);
background-image: url("//instagramstatic-a.akamaihd.net/h1/images/shared/noise-1.png/aae6cb061f08.png"), -moz-linear-gradient(top, #517fa4, #306088);
background-image: url("//instagramstatic-a.akamaihd.net/h1/images/shared/noise-1.png/aae6cb061f08.png"), -o-linear-gradient(top, #517fa4, #306088);
background-image: url("//instagramstatic-a.akamaihd.net/h1/images/shared/noise-1.png/aae6cb061f08.png"), -ms-linear-gradient(top, #517fa4, #306088);
background-image: url("//instagramstatic-a.akamaihd.net/h1/images/shared/noise-1.png/aae6cb061f08.png"), linear-gradient(top, #517fa4, #306088);
background-position: 50% 50%;
border-radius: 4px 4px 0 0;
box-shadow: 0 1px 0 rgba(111, 151, 182, 0.7) inset;
	}
.card th {
font-size: 12px;
font-weight: bold;
text-transform: uppercase;
	}
.card th, .card td {
	text-transform: uppercase;
	vertical-align: top;
padding: 5px 0;
border: none;
font-size: 14px;
line-height: 20px;
	}
.card td {padding-left: 20px;}
.card-info {padding: 10px 20px;border-bottom: 1px solid #ddd;}
.card h2 {
color: #fff;
font-size: 14px;
font-weight: 700;
line-height: 20px;
text-shadow: 0 1px 0 rgba(0, 0, 0, 0.5);
margin: 0;
padding: 4px 0 6px;
}
.button {
	border: 1px solid #c6daec;
	background-color: #f9fafb;
background-image: linear-gradient(top, #f9fafb, #eef1f4);
filter: progid: DXImageTransform.Microsoft.gradient(GradientType=0, StartColorStr='#f9fafb', EndColorStr='#eef1f4');
background-position: 50% 50%;
text-shadow: 0 1px 0 rgba(255, 255, 255, 0.2);
box-shadow: 0 1px 1px rgba(0, 0, 0, 0.08), inset 1px 0 0 rgba(255, 255, 255, 0.05), inset -1px 0 0 rgba(255, 255, 255, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.2);
font-size: 14px;
padding: 7px 10px 8px;
border-radius: 3px;

cursor: pointer;
font-weight: bold;
line-height: 1em;
text-decoration: none !important;
color: #111;
-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
-webkit-touch-callout: none;
-webkit-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;
display: inline-block;
position: relative;
	}
.card-description {
	border-radius: 0 0 4px 4px;
	position: relative;
	padding: 10px 20px;
	}
.card-description p {margin: 5px 0; font-size: 13px;}
#copyTarget {text-transform: none;}
</style>
</head>
<body>
	<?php

	?>
	<div class="card">
		<header><h2>Verification Code</h2></header>
		<div class="card-info">
			<table>
                <tbody>
                    <tr>
                        <th>Your Code</th>
                        <td id="copyTarget">
                        	<?php 
                        	if (isset($_GET['code'])) {
                        		echo $_GET['code'];
                        	} else {
                        		echo "Something goes wrong. Try again";
                        	} 
                        	?>
                       	</td>
                    </tr>
                </tbody>
            </table>
		</div>
		<div class="card-description client-description">
            <p>
            	<ol>
            		<li>Copy generated code</li>
            		<li><a href="<?php print_r($_SERVER['HTTP_REFERER']); ?>">Back</a> to your Instagram Settings</li>
            		<li>Paste it to the "Verification Code" field</li>
            	</ol>
            </p>
        </div>
	</div>
</body>
</html>