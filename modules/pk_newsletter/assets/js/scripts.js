$(document).ready(function(){

	$(".registerEmail").click(function() {
		var email = $('.inputNew').val();
		
		if (isEmail(email)) {
			$.ajax({
				type: "POST",
				headers: { "cache-control": "no-cache" },
				async: false,
				url: pk_newsletter_path+'pk_newsletter/ajax.php',
				//url: currentIndex + "&token=" + token + "&" + "rand=" + new Date().getTime(),
				data: "email="+email,
				success: function(data) {
					var d = jQuery.parseJSON(data);
					if (d.tp == 'success') {
						var cls = 'success_inline';
					}
					if (d.tp == 'err') {
						var cls = 'warning_inline';
					}
					console.log(d);
					$('.msg-cont').empty().append("<p class='"+cls+"'>"+d.msg+"</p>");
				}
			});
		} else {
			$('.msg-cont').empty().append("<p class='warning_inline'>Wrong email address</p>");
		}

	});

});
function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}