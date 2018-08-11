$(function(){
	$("#contact").submit(function(){
		$(".message").removeClass("success").removeClass("error").addClass("loader").html("Sending message").fadeIn("slow");
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: $(this).serialize(),
			dataType: 'text',
			success: function(msg){
				switch(msg) {
					case "field_error":
						$(".message").removeClass("loader").addClass("error");
						$(".message").html("Please fill in all the required fields.");
						break;
					case "captcha_error":
						$(".message").removeClass("loader").addClass("error");
						$(".message").html("Please type the words correctly and try again!");
						break;
					case "success":
						$(".message").removeClass("loader").addClass("success");
						$(".message").html("Your message has been sent. You'll soon hear from us!");
						break;
					default:
						alert("Something is wrong. Please try again.");
				}
			}
		});
		Recaptcha.reload();
		return false;
	});
});
