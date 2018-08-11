<?php /* Smarty version Smarty-3.1.19, created on 2018-06-19 12:15:47
         compiled from "/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/product_features.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4856904575b28d7d3a44a49-86481677%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6da1f121500a22e6869d664760fe8dc3b206a333' => 
    array (
      0 => '/home/ergold/domains/ergold.pl/public_html/modules/allegro/views/theme/product_features.tpl',
      1 => 1513145675,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4856904575b28d7d3a44a49-86481677',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'features' => 0,
    'f' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5b28d7d3a4abf6_13089972',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5b28d7d3a4abf6_13089972')) {function content_5b28d7d3a4abf6_13089972($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['features']->value)&&count($_smarty_tpl->tpl_vars['features']->value)) {?>
	<ul>
	<?php  $_smarty_tpl->tpl_vars['f'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['f']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['features']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['f']->key => $_smarty_tpl->tpl_vars['f']->value) {
$_smarty_tpl->tpl_vars['f']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['f']->key;
?>
		<li><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['f']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
 - <b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['f']->value['value'], ENT_QUOTES, 'UTF-8', true);?>
</b></li>
	<?php } ?>
	</ul>
<?php }?>
<?php }} ?>
