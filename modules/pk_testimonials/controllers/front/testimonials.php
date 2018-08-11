<?php

class pk_testimonialsTestimonialsModuleFrontController extends ModuleFrontController {

  public function __construct() {

    parent::__construct();
    $this->context = Context::getContext();
    require_once(_PS_MODULE_DIR_.'pk_testimonials/pk_testimonials.php');

  }

  public function initContent() {
      
    parent::initContent();
    $blockTestimonial = new Pk_Testimonials();

    $this->context->smarty->assign(array(
      'testimonials_list' => $blockTestimonial->displayTestimonials()
    ));        

    $this->setTemplate('module:pk_testimonials/views/templates/front/testimonials.tpl');

  }

}
?>