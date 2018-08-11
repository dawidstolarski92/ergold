<?php

class pk_testimonialsAddTestimonialModuleFrontController extends ModuleFrontController {

  public function __construct() {

    parent::__construct();
    $this->context = Context::getContext();
    require_once(_PS_MODULE_DIR_.'pk_testimonials/pk_testimonials.php');
    require_once(_PS_MODULE_DIR_.'pk_testimonials/recaptchalib.php');
    
  }

  public function initContent() {

    parent::initContent();

    $blockTestimonial = new Pk_Testimonials();

    $smarty_opts['opts'] = array(
      'recaptcha' => intval(Configuration::get('testimonial_captcha')),
      'captchakey' => Configuration::get('testimonial_captcha_pub'),
      'base_dir' => __PS_BASE_URI__,
      'http_host' => $_SERVER['HTTP_HOST'],
      'addtestimonial' => true,
      'field_error' => $this->trans('Please fill in all the required fields', array(), 'Modules.Testimonials.Shop'),
      'captcha_error' => $this->trans('Please type captcha words correctly and try again!', array(), 'Modules.Testimonials.Shop'),
      'success' => $this->trans('Your message has been sent and will be published soon', array(), 'Modules.Testimonials.Shop'),
      'DB_error' => $this->trans('Can\'t add testimonial to DB', array(), 'Modules.Testimonials.Shop'),
      'other' => $this->trans('Something is wrong. Please try again', array(), 'Modules.Testimonials.Shop')
    );

    $smarty_opts['json_opts'] = json_encode($smarty_opts['opts'], JSON_PRETTY_PRINT);

    $this->context->smarty->assign($smarty_opts);

    $this->setTemplate('module:pk_testimonials/views/templates/front/addtestimonial.tpl');

  }

}
?>