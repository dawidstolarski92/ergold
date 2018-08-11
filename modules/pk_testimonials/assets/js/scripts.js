$(document).ready(function() {

  var page = $('.pk_testimonials').data('page');
  
  if (page == 'addtestimonial') {

    var options = $('.pk_addtestimonials').data('options');

    if (options.recaptcha) {
      Recaptcha.create(options.captchakey, "captcha_body", {
        theme: "white",
        callback: Recaptcha.focus_response_field
      });
    }
    
    $("#testimonialForm").submit(function(event){
      event.preventDefault();
      $.ajax({
        url: "//"+options.http_host+options.base_dir+"modules/pk_testimonials/queries.php",
        type: "POST",
        data: $(this).serialize(),
        success: function(msg){
          switch(msg) {
            case "field_error":            
              $(".alert").html(options.field_error).addClass("alert-danger").slideDown("fast");
              break;
            case "captcha_error":
              $(".alert").html(options.captcha_error).addClass("alert-danger").slideDown("fast");
              break;
            case "success":
              $(".alert").html(options.success).addClass("alert-success").slideDown("fast");
              $("#testimonial_submitter_name").val("");
              $("#testimonial_submitter_email").val("");
              $("#testimonial_title").val("");
              $("#testimonial_main_message").val("");
              break;
            case "DB_error":
              $(".alert").html(options.DB_error).addClass("alert-danger").slideDown("fast");
              break;
            default:
              $(".alert").html(options.other).addClass("alert-danger").slideDown("fast");
          }
          if (options.recaptcha)
            Recaptcha.reload();
        }
      });
    });

  }

  var hookn = $('.pk_testimonials').data('hookn');
   $("#"+hookn+"testimonials").flexisel({
    pref: "testimonials",
    visibleItems: 1,
    animationSpeed: 1000,
    autoPlay: false,
    autoPlaySpeed: 3500,            
    pauseOnHover: true,
    enableResponsiveBreakpoints: false,
    clone : true    
  });

  var hook = $('.testimonials-list').data('hook');
  if (typeof hook !== 'undefined') {
     $(".testimonials-"+hook).flexisel({
      pref: hook+"-testimonials",
      visibleItems: 1,
      animationSpeed: 1500,
      autoPlay: false,
      autoPlaySpeed: 3500,            
      pauseOnHover: true,
      enableResponsiveBreakpoints: false,
      clone : true    
    });
  }

  //{if ($hookn != "") && ($displayImage == 1) && ($theme_settings.preset != 4)}
   // parallax($(".testimonials-bg"));
  //{/if}

});
 