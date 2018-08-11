<?php /* Smarty version Smarty-3.1.19, created on 2018-02-21 08:31:01
         compiled from "/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/product_description.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18332116875a8d2035af6857-76194723%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'faba2d9fa685bc3f0492058dcf167eb0216d1253' => 
    array (
      0 => '/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/product_description.tpl',
      1 => 1513145675,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18332116875a8d2035af6857-76194723',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a8d2035b02622_55188308',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a8d2035b02622_55188308')) {function content_5a8d2035b02622_55188308($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/ergold/domains/ergold.pl/public_html/vendor/prestashop/smarty/plugins/modifier.escape.php';
?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->description, 'UTF-8');?>
<?php }} ?>
