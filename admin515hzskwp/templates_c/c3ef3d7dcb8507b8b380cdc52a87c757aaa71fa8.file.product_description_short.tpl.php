<?php /* Smarty version Smarty-3.1.19, created on 2018-02-21 08:31:01
         compiled from "/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/product_description_short.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15754119985a8d2035abd642-63714283%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3ef3d7dcb8507b8b380cdc52a87c757aaa71fa8' => 
    array (
      0 => '/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/product_description_short.tpl',
      1 => 1513145675,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15754119985a8d2035abd642-63714283',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a8d2035af47c2_63238391',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a8d2035af47c2_63238391')) {function content_5a8d2035af47c2_63238391($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/ergold/domains/ergold.pl/public_html/vendor/prestashop/smarty/plugins/modifier.escape.php';
?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->description_short, 'UTF-8');?>
<?php }} ?>
