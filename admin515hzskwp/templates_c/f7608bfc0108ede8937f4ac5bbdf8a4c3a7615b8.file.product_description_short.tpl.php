<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 10:01:51
         compiled from "/home/ergoldpl/web/ergold.pl/public_html/modules/allegro/views/theme/product_description_short.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2048692474599ea7733da521-93023695%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f7608bfc0108ede8937f4ac5bbdf8a4c3a7615b8' => 
    array (
      0 => '/home/ergoldpl/web/ergold.pl/public_html/modules/allegro/views/theme/product_description_short.tpl',
      1 => 1511276454,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2048692474599ea7733da521-93023695',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_599ea7733dd878_96116628',
  'variables' => 
  array (
    'product' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_599ea7733dd878_96116628')) {function content_599ea7733dd878_96116628($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/ergoldpl/web/ergold.pl/public_html/vendor/prestashop/smarty/plugins/modifier.escape.php';
?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->description_short, 'UTF-8');?>
<?php }} ?>
