<?php /* Smarty version Smarty-3.1.19, created on 2018-02-21 08:31:01
         compiled from "/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/auction_price.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3069392975a8d2035b04196-09470707%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6366213264ed9b6bcbc5f19ef590ed903c5995dd' => 
    array (
      0 => '/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/auction_price.tpl',
      1 => 1513145675,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3069392975a8d2035b04196-09470707',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'allegro_fields' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a8d2035b12f13_63303028',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a8d2035b12f13_63303028')) {function content_5a8d2035b12f13_63303028($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/home/ergold/domains/ergold.pl/public_html/vendor/prestashop/smarty/plugins/modifier.replace.php';
?><?php if (isset($_smarty_tpl->tpl_vars['allegro_fields']->value[8])) {?>
    <?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['allegro_fields']->value[8],'.',',');?>
 zł
<?php }?>
<?php }} ?>
