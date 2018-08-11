<form action="" method="post" class="panel form-horizontal shipping">
	<div class="panel-heading">
		{l s='Category mapping' mod='allegro'}
	</div>

	<div class="alert alert-info">
		<p><b>{l s='On this page you can map your shop category to Allegro category and set up category parameters.' mod='allegro'}</b></p>
		<br />
		<p>{l s='To do that follow steps below:' mod='allegro'}</p>
		<ul>
			<li>{l s='1. Select one or more shop category' mod='allegro'}</li>
			<li>{l s='2. Select Allegro category' mod='allegro'}</li>
			<li>{l s='3. (optiona) Set category parameters that match to all products in catgory' mod='allegro'}</li>
		</ul>
		<br />
		<p>{l s='Rest of parameters you can set individually for each product on product page.' mod='allegro'}</p>
	</div>

	<hr />

	<div class="form-group">
		<label for="" class="control-label col-lg-3 required">{l s='Store category' mod='allegro'}</label>
		<div class="allegro-categories-tree col-lg-6">
			{$categories_tree}
		</div>
	</div>

	<hr />

	<div class="form-group">
		<label for="" class="control-label col-lg-3 required">{l s='Service category' mod='allegro'}</label>
		<div class="col-lg-9">
            <div id="categoryContainer" {if $id_allegro_category}style="display: none"{/if}>
                {include file='./../form/category_select.tpl'}
            </div>
			{if $id_allegro_category}
				<p id="categoryPath" style="font-size: 13px; padding: 5px">
					{$allegro_category_path} ({$id_allegro_category})&nbsp;&nbsp;<a href="#" id="updateCategory"><i class="icon-pencil"></i> {l s='update' mod='allegro'}</a>
					<input type="hidden" name="field[2]" value="{$id_allegro_category|intval}" />
				</p>
			{/if}
		</div>
	</div>

	<div id="features">
	{if isset($form_fields) && count($form_fields)}	
		{include file='./../form/fields.tpl'}
	{else}
		<div class="alert alert-info col-lg-offset-3">{l s='To see available features select category above' mod='allegro'}</div>
	{/if}
	</div>

	<hr />

	{if isset($form_fields_global) && count($form_fields_global)}	
		{include file='./../form/fields.tpl' form_fields=$form_fields_global}
	{/if}


	<!-- Footer -->
	<div class="panel-footer">
		<a href="{$currentIndex}&token={$smarty.get.token}" class="btn btn-default" onclick="window.history.back();"><i class="process-icon-cancel"></i> {l s='Cancel' mod='allegro'}</a>
		<button type="submit" name="submitSaveCategory" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='allegro'}</button>
	</div>
</form>