<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 10:01:51
         compiled from "/home/ergoldpl/web/ergold.pl/public_html/modules/allegro/views/theme/product_description.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1880516579599ea7733e02e3-61011954%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'adad3433f13c762fe3e52e90beb61de26dcd5be2' => 
    array (
      0 => '/home/ergoldpl/web/ergold.pl/public_html/modules/allegro/views/theme/product_description.tpl',
      1 => 1511276454,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1880516579599ea7733e02e3-61011954',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_599ea7733e3557_57758212',
  'variables' => 
  array (
    'product' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_599ea7733e3557_57758212')) {function content_599ea7733e3557_57758212($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/ergoldpl/web/ergold.pl/public_html/vendor/prestashop/smarty/plugins/modifier.escape.php';
?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->description, 'UTF-8');?>
<?php }} ?>
