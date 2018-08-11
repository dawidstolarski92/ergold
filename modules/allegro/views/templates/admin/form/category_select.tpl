{if count($allegro_categories)}
	<select size="11" style="max-width: 220px; float: left; margin: 0 10px 10px 0" class="cSelect">
		{foreach from=$allegro_categories item=category}
		<option value="{$category.id_category|intval}">{$category.name}</option>
		{/foreach}
	</select>
    <input type="text" name="field[2]" placeholder="{l s='Type category ID' mod='allegro'}" style="margin: 10px 0 10px 0">
{else if !count($allegro_categories) && !isset($id_parent)}
    <div class="alert alert-danger">{l s='No categories in DB, update categories list.' mod='allegro'}</div>
{else if isset($id_parent)}
	<div style="clear: both"></div><input type="text" name="field[2]" value="{$id_parent|intval}" style="width: 100px" readonly="readonly" />
{/if}
