<script>
$(document).ready(function(){

	/* Init tinyMce */
	var ad = '{$ad}';
	var iso = '{$iso_tiny_mce}';

	/* Tabs */
	$('.list-group-item').click(function(event){
		event.preventDefault();
		$('.product-tab-content').hide();
		$('#product-tab-content-'+$(this).attr('id')).show();
		$('.list-group-item').removeClass('active');
		$(this).addClass('active');
		$('input[name="key_tab"]').val($(this).attr('id'));
	});

	/* Theme preview */
	$('.theme-preview').click(function(event){
		event.preventDefault();
		window.open(
			event.target.href,
			'MsgWindow',
			'width=1200, height=780, top=40, left=30'
		);
	});

	/* Description / theme */
	$('select#id_allegro_theme').change(function(){
		var value = $(this).val();
		if(value == '-1') {
			$('.nf-editor-group').show();
		} else {
			$('.nf-editor-group').hide();
		}
	});

	/* Title chars meter */
	updateTitleCharsMeter();
	$('#auction-title').keyup(function() {
		updateTitleCharsMeter();
	});
});

function fillAuctionTitle(input) {
	if(!$(input).val()) {
		$(input).val($(input).attr('placeholder'));
		updateTitleCharsMeter();
	}
}

function updateTitleCharsMeter() {
	var nbChars = countTitleChars($('#auction-title').val() ? $('#auction-title').val() : $('#auction-title').attr('placeholder'));
	$('#title-chars-meter').html((nbChars > 50 ? '<b style="color: red">'+nbChars+'</b>' : nbChars));
}

function countTitleChars(value) {
	return value.length;
}

</script>

{* Selected tab definition *}
{if isset($smarty.get.key_tab)}
	{$key_tab = $smarty.get.key_tab}
{elseif !isset($key_tab) || !$key_tab}
	{$key_tab = 'Informations'}
{/if}

{* Page *}
{$page = 1}
{if isset($smarty.get.page)}
	{$page = $smarty.get.page|intval}
{/if}

{if $is_15}
<script>
	$(document).ready(function(){
	    $('#desc-allegro_product-save').click(function(){
	        $('input[name="submitSave"]').click();
	        return false;
	    });
	    $('#desc-allegro_product-save-and-stay').click(function(){
	        $('input[name="submitSaveAndStay"]').click();
	        return false;
	    });
	});
	</script>
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=true title=$default_title}
{/if}

{capture footer_html assign=footer_html}
    {if !$is_15}
        <div class="panel-footer">
        	<a href="{$currentIndex}&amp;token={$token}&amp;page={$page}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='allegro'}</a>
        	<button type="submit" name="submitSave" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='allegro'}</button>
        	<button type="submit" name="submitSaveAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i>{l s='Save and stay' mod='allegro'}</button>
        </div>
    {/if}
{/capture}


{capture prev_next assign=prev_next}
<div class="panel-heading-action allegro-prev-next">
	<div class="btn-group" style="top: 0">
		<a class="btn btn-default{if !$previousProduct} disabled{/if}" href="{$previousProduct}">
			<i class="icon-backward"></i>
		</a>
		<a class="btn btn-default{if !$nextProduct} disabled{/if}" href="{$nextProduct}">
			<i class="icon-forward"></i>
		</a>
	</div>
</div>
{/capture}

<div class="row {if $is_15}is_15{/if}">
	<div class="productTabs col-lg-2 col-md-3">
		<div class="list-group">
			<a class="list-group-item{if $key_tab == 'Informations'} active{/if}" 	id="Informations" 	href="">{l s='General Informations' mod='allegro'}</a>
			<a class="list-group-item{if $key_tab == 'Category'} active{/if}" 		id="Category" 		href="">{l s='Category and parameters' mod='allegro'}</a>
			<a class="list-group-item{if $key_tab == 'Description'} active{/if}" 	id="Description" 	href="">{l s='Description / theme' mod='allegro'}</a>
			<a class="list-group-item{if $key_tab == 'Images'} active{/if}" 		id="Images" 		href="">{l s='Images' mod='allegro'}</a>
			<a class="list-group-item{if $key_tab == 'Additional'} active{/if}" 	id="Additional" 	href="">{l s='Additional parameters' mod='allegro'}</a>
			<a class="list-group-item{if $key_tab == 'Auctions'} active{/if}" 		id="Auctions" 		href="">{l s='Auctions' mod='allegro'} ({count($auctions)})</a>
			<a class="list-group-item{if $key_tab == 'SaleManager'} active{/if}" 	id="SaleManager" 	href="">{l s='Sale manager' mod='allegro'}</a>
		</div>
	</div>

	<form id="product_form" class="form-horizontal col-lg-10 col-md-9" action="{$currentIndex}&amp;token={$token}&amp;id_allegro_product={$smarty.get.id_allegro_product|intval}&amp;page={$page}" method="post" enctype="multipart/form-data" name="product" novalidate="novalidate">
		<input type="hidden" name="key_tab" value="{$key_tab}">
		<input type="hidden" name="page" value="{$page}">

        {if $is_15}
            {* Hidden submit buttons *}
            <input style="display: none;" type="submit" name="submitSave" value="" />
            <input style="display: none;" type="submit" name="submitSaveAndStay" value="" />
        {/if}

		<div id="product-tab-content-Informations" class="product-tab-content" {if $key_tab != 'Informations'}style="display: none"{/if}>
			<div class="panel product-tab">
				<h3 class="tab">{l s='General informations' mod='allegro'}{$prev_next}</h3>

                <!-- Params info -->
                <div class="warn alert alert-warning">
                    {l s='In "multistore" mode all allegro product params are shared between shops except a "Sale manager" params.' mod='allegro'}
                </div>

				<!-- Auction title -->
				<div class="form-group">
					<div class="col-lg-1"><span class="pull-right"></span></div>
					<label class="control-label col-lg-2 required allegro" for="name_1">
						{l s='Auction title' mod='allegro'}
					</label>
					<div class="col-lg-6">
						<input
							type="text"
							id="auction-title"
							class="form-control"
							name="field[1]"
							onfocus="fillAuctionTitle(this)"
							required="required"
							value="{if $title_saved}{$title_saved|escape:'html':'UTF-8'}{/if}"
							placeholder="{$default_title|escape:'html':'UTF-8'}">
						<p class="help-block">{l s='Leave empty to use first 50 characters of product name as auction title.' mod='allegro'}</p>
					</div>
					<div class="col-lg-2">
						<a href="#" class="btn btn-default pull-left" onclick="$('#auction-title').val('');  updateTitleCharsMeter(); return false;"><i class="icon icon-refresh"></i> {l s='reset' mod='allegro'}</a>
						<label class="control-label pull-left">&nbsp;&nbsp;<span id="title-chars-meter">0</span>/50</label>
					</div>
				</div>

				<hr />

				{* Quantity *}
				{if isset($form_fields[5])}
					{include file='../form/field.tpl' field=$form_fields[5] form_fields_values=$form_fields_values}
                    {if $PS_STOCK_MANAGEMENT}
                    <div class="form-group">
						<div class="col-lg-3"></div>
						<div class="col-lg-9">
							<div class="warn alert alert-info">
								<p>{l s='If you leave empty this field module will use current stock (%s), you can also type ammount wich will be saved.' sprintf=[$quantity] mod='allegro'}</p>
							</div>
						</div>
					</div>
                    {/if}
				{/if}

				<hr />

				{foreach from=[29, 6, 7] item=id_field}
					{if isset($form_fields[$id_field])}
						{include file='../form/field.tpl' field=$form_fields[$id_field]}
					{/if}
				{/foreach}

                {* "Buy now" price *}
				{if isset($form_fields[8])}
					{include file='../form/field.tpl' field=$form_fields[8]}
					<div class="form-group">
						<div class="col-lg-3"></div>
						<div class="col-lg-9">
							<div class="warn alert alert-info">
                                {capture assign="priceConverted"}{convertPrice price=$price}{/capture}
								<p>{l s='If you leave empty this field module will use current price (%s), you can also type ammount wich will be saved.' sprintf=[$priceConverted] mod='allegro'}
                                    {if $ALLEGRO_PRICE_PC}
                                    <br><b>{l s='Price change is enabled: %s' sprintf=[$ALLEGRO_PRICE_PC] mod='allegro'}&#37;</b>
                                    {/if}
									<br>{l s='In case of auction without "Buy Now!" price set value "0".' mod='allegro'}
                                </p>
							</div>
						</div>
					</div>
				{/if}


				<hr />

				{* Duration *}
				{include file='../form/field.tpl' field=$form_fields[4]}

				<!-- Start time -->
				<div class="form-group attribs">
					<label class="control-label col-lg-3">{l s='Start time' mod='allegro'}</label>
					<div class="col-lg-2">
						<div class="input-group">
							<input type="text" class="datepicker form-control" value="{if isset($smarty.get.start_time)}{$smarty.get.start_time}{/if}" name="start_time" />
							<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
						</div>
					</div>
				</div>
				<script>
                {if !$is_15}
				$('.datepicker').datetimepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd',
					minDate: 0, // From now
					maxDate: "+1M", // Max 1 month
					// Define a custom regional settings in order to use PrestaShop translation tools
					currentText: '{l s='Now' mod='allegro'}',
					closeText: '{l s='Done' mod='allegro'}',
					ampm: false,
					timeFormat: 'hh:mm:ss tt',
					timeSuffix: '',
					timeOnlyTitle: '{l s='Pick time' mod='allegro'}',
					timeText: '{l s='Time' mod='allegro'}',
					hourText: '{l s='Hour' mod='allegro'}',
					minuteText: '{l s='Minute' mod='allegro'}',
                    onClose: function(dateText){
                        $('#toolbar-nav li a').each(function() {
                            $(this).attr('href', $(this).attr('href') + '&start_time='+dateText);
                        });
                    }
				});
                {else if}
                $('.datepicker').change(function(){
                    var dateInput = $(this);
                    $('#allegro_product_toolbar li a.toolbar_btn').each(function() {
                        $(this).attr('href', $(this).attr('href') + '&start_time='+dateInput.val());
                    });
                });
                {/if}
				</script>

				<hr />

				<!-- Shipping  -->

				<div class="form-group">
					<div class="col-lg-1"><span class="pull-right"></span></div>
					<label class="control-label col-lg-2">
						{l s='Allegro shipping' mod='allegro'}
					</label>
					{if count($shipping_pricings)}
					<div class="col-lg-3">
						<select name="id_allegro_shipping" id="id_allegro_shipping">
						<option value="0">{l s='-- Use default --' mod='allegro'}</option>
						{foreach from=$shipping_pricings item=shipping_pricing key=key}
							<option value="{$shipping_pricing.id_allegro_shipping|intval}" {if $shipping_pricing.id_allegro_shipping == $allegro_product->id_allegro_shipping}selected="selected"{/if}>{$shipping_pricing.name|escape:'html':'UTF-8'}</option>
						{/foreach}
						</select>
					</div>
					{else}
						<div class="col-lg-9">
							<div class="warn alert alert-warning">{l s='There is no shipping pricing' mod='allegro'} </div>
						</div>
					{/if}
				</div>

				<hr />

				<!-- Returns policy  -->
			    <div class="form-group">
			        <label class="control-label col-lg-3">{l s='Implied warranty' mod='allegro'}</label>
			        <div class="col-lg-{if $warranties}3{else}6{/if}">
			            {if $implied_warranties}
			            <select name="implied_warranty">
			                <option value="">{l s='- Use default -' mod='allegro'}</option>
			                {foreach from=$implied_warranties item=item key=key name=name}
			                    <option {if $allegro_product_account.implied_warranty == $item->id}selected="selected"{/if} value="{$item->id|escape:'html':'UTF-8'}">{$item->name|escape:'html':'UTF-8'}</option>
			                {/foreach}
			            </select>
			            {else}
			            <div class="alert alert-warning">
			                {l s='No implied warranties - first you must add one' mod='allegro'}
			            </div>
			            {/if}
			        </div>
			    </div>

			    <div class="form-group">
			        <label class="control-label col-lg-3">{l s='Return policy' mod='allegro'}</label>
			        <div class="col-lg-{if $warranties}3{else}6{/if}">
			            {if $return_policies}
			            <select name="return_policy">
			                <option value="">{l s='- Use default -' mod='allegro'}</option>
			                {foreach from=$return_policies item=item key=key name=name}
			                    <option {if $allegro_product_account.return_policy == $item->id}selected="selected"{/if} value="{$item->id|escape:'html':'UTF-8'}">{$item->name|escape:'html':'UTF-8'}</option>
			                {/foreach}
			            </select>
			            {else}
			            <div class="alert alert-warning">
			                {l s='No return policies - first you must add one' mod='allegro'}
			            </div>
			            {/if}
			        </div>
			    </div>

			    <div class="form-group">
			        <label class="control-label col-lg-3">{l s='Warranty' mod='allegro'}</label>
			        <div class="col-lg-{if $warranties}3{else}6{/if}">
			            {if $warranties}
			            <select name="warranty">
			                <option value="">{l s='- Use default -' mod='allegro'}</option>
			                {foreach from=$warranties item=item key=key name=name}
			                    <option {if $allegro_product_account.warranty == $item->id}selected="selected"{/if} value="{$item->id|escape:'html':'UTF-8'}">{$item->name|escape:'html':'UTF-8'}</option>
			                {/foreach}
			            </select>
			            {else}
			            <div class="alert alert-warning">
			                {l s='No warranties - first you must add one' mod='allegro'}
			            </div>
			            {/if}
			        </div>
			    </div>

				<hr>
				{* Additional services *}
			    <div class="form-group">
			        <label class="control-label col-lg-3">{l s='Additional services' mod='allegro'}</label>
			        <div class="col-lg-{if $additional_services}3{else}6{/if}">
			            {if $additional_services}
			            <select name="additional_services">
			                <option value="">{l s='- No additional services -' mod='allegro'}</option>
			                {foreach from=$additional_services item=item key=key name=name}
			                    <option {if $allegro_product_account.additional_services == $item->id}selected="selected"{/if} value="{$item->id|escape:'html':'UTF-8'}">{$item->name|escape:'html':'UTF-8'}</option>
			                {/foreach}
			            </select>
			            {else}
			            <div class="alert alert-warning">
			                {l s='No additional services - first you must add one' mod='allegro'}
			            </div>
			            {/if}
			        </div>
			    </div>

				{$footer_html}
			</div>
		</div>

		<!-- TAB - category -->
		<div id="product-tab-content-Category" class="product-tab-content" {if $key_tab != 'Category'}style="display: none"{/if}>
			<div class="panel product-tab">
				<h3 class="tab">{l s='Category' mod='allegro'}{$prev_next}</h3>
				<div class="form-group">
					<label for="reference" class="control-label col-lg-3">{l s='Category' mod='allegro'}</label>
					<div class="col-lg-9">
                        <div id="categoryContainer" {if $id_allegro_category}style="display: none"{/if}>
                            {include file='./../form/category_select.tpl'}
                        </div>
						{if $id_allegro_category}
							<p id="categoryPath" style="font-size: 13px; padding: 5px">
								{$allegro_category_path} ({$id_allegro_category})&nbsp;&nbsp;<a href="#" id="updateCategory"><i class="icon-pencil"></i> {l s='Change / remove' mod='allegro'}</a>
								{if $product_has_category}
                                {* Do not save category if come from mapping *}
                                <input type="hidden" name="field[2]" value="{$id_allegro_category|intval}" />
                                {/if}
							</p>
						{/if}
					</div>
				</div>

				<div id="features">
				{if $id_allegro_category}
					{foreach from=$form_fields item=field}
						{if $field.category}
							{include file='../form/field.tpl' field=$field}
						{/if}
					{/foreach}
				{else}
					<div class="warn alert alert-info col-lg-offset-3">{l s='To see available features select category above.' mod='allegro'}</div>
				{/if}

					<hr>

					<div class="warn alert alert-info col-lg-offset-3">
						<p>{l s='You can copy category and params from other product.' mod='allegro'}</p>
						<p>
							<label for="#clone">{l s='Enter product ID' mod='allegro'}:</label>
							<input type="text" name="clone_id_product" id="clone" class="form-controll" style="max-width: 100px; display: inline-block;">
							<span>{l s='and click save button.' mod='allegro'}</span>
						</p>
					</div>
				</div>

				{* Footer *}
				{$footer_html}
			</div>
		</div>

		<!-- TAB - Description -->
		<script>
		var iso = '{$iso_tiny_mce}';
		var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
		var ad = '{$ad}';

		$(document).ready(function(){
			tinySetup({ editor_selector:"autoload_rte" });
		});
		</script>

		<script>
		function tinySetup2(config) {
		    if(!config) {
		        config = { };
		    }

		    default_config = {
		        selector: '.rte',
		        plugins : 'code lists',
		        toolbar1: 'code | undo redo | formatselect | bold | bullist numlist | markers',
		        toolbar2: '',
		        valid_elements : 'h1,h2,p,b,ul,ol,li',
		        valid_children: '-h1[b],-h2[b],-ul[h1],-ol[h1],-li[h1],-ul[h2],-ol[h2],-li[h2],-li[p]',
		        block_formats: 'Paragraph=p;Header 1=h1;Header 2=h2',
		        menubar: false,
		        statusbar : false,
		        //remove_linebreaks: true,
		        force_br_newlines : false,
		        force_p_newlines : true,
		        forced_root_block : '',
		        branding: false,
		        min_height: 200,
		        formats: {
		            bold: { inline: 'b' },  
		        },
		        setup: function (editor) {
		            editor.on("change", function () {
		                nfAllegroEditor.propagateChanges(editor, false);
		            }).on("blur", function () {
		                nfAllegroEditor.propagateChanges(editor, true);
		            });

		            editor.addButton('markers', {
		              type: 'menubutton',
		              text: "{l s='Markers' mod='allegro'}",
		              icon: false,
		              menu: [{
		                text: "{l s='Auction title' mod='allegro'}",
		                onclick: function() {
		                    //editor.setContent('');
		                    editor.insertContent('<p>[auction_title]</p>');
		                }
		                }, {
		                    text: "{l s='Auction price' mod='allegro'}",
		                    onclick: function() {
		                        //editor.setContent('');
		                        editor.insertContent('<p>[auction_price]</p>');
		                    }
		                }, {
		                    text: "{l s='Product description' mod='allegro'}",
		                    onclick: function() {
		                        editor.setContent('');
		                        editor.insertContent('[product_description]');
		                    }
		                }, {
		                    text: "{l s='Product description short' mod='allegro'}",
		                    onclick: function() {
		                        editor.setContent('');
		                        editor.insertContent('[product_description_short]');
		                    }
		                }, {
		                    text: "{l s='Product features' mod='allegro'}",
		                    onclick: function() {
		                        editor.setContent('');
		                        editor.insertContent('[product_features]');
		                    }
		                }, {
		                    text: "{l s='Product weight' mod='allegro'}",
		                    onclick: function() {
		                        //editor.setContent('');
		                        editor.insertContent('<p>[product_weight]</p>');
		                    }
		                }, {
		                    text: "{l s='Manufacturer name' mod='allegro'}",
		                    onclick: function() {
		                        //editor.setContent('');
		                        editor.insertContent('<p>[manufacturer_name]</p>');
		                    }
		                }
		            ]
		            });
		        },
		    };

		    $.each(default_config, function(index, el) {
		        if (config[index] === undefined) {
		            config[index] = el;
		        }
		    });

		    tinyMCE.init(config);
		}

		$(document).ready(function() {
		    // Init allegro editor
		    nfAllegroEditor = $('#nf-editor')
		        .nfAllegroEditor({
		            default_text:   "{l s='Enter text here...' mod='allegro'}",
		            image_text:     "{l s='Image' mod='allegro'}",
		            add_row_text:   "{l s='Add row' mod='allegro'}",
		            hideInput:      true,
		        })
		        .show();
		});
		</script>


		<div id="product-tab-content-Description" class="product-tab-content" {if $key_tab != 'Description'}style="display: none"{/if}>
			<div class="panel product-tab">
				<h3 class="tab">{l s='Description' mod='allegro'}{$prev_next}</h3>

				{if !count($allegro_themes)}
					<div class="warn alert alert-warning">{l s='There is no active themes, you can add themes in "Allegro" > "Themes" tab.' mod='allegro'}</div>
				{/if}


				<div class="alert alert-info">
					{l s='Click "Preview" to display preview of offer description:' mod='allegro'}&nbsp;&nbsp;
					<a href="{$link->getAdminLink('AdminAllegroProduct')}&ajax=1&action=themePreview&id_allegro_product={$allegro_product->id|intval}" class="btn btn-default theme-preview">
						<i class="icon icon-picture"></i> {l s='Preview' mod='allegro'}
					</a>
				</div>

				{* Theme selector *}
				<div class="form-group">
					<div class="col-lg-1"><span class="pull-right"></span></div>
					<label class="control-label col-lg-2">
						{l s='Description generation' mod='allegro'}
					</label>

					<div class="col-lg-3">
						<select name="id_allegro_theme" id="id_allegro_theme">
							<option {if $allegro_product->id_allegro_theme == -1}selected="selected"{/if} value="-1">{l s='Create description here' mod='allegro'}</option>
						{if count($allegro_themes)}
							<optgroup label="{l s='Use theme' mod='allegro'}">
							{foreach from=$allegro_themes item=allegro_theme key=key}
								<option value="{$allegro_theme.id_allegro_theme|intval}" {if $allegro_theme.id_allegro_theme == $allegro_product->id_allegro_theme}selected="selected"{/if}>{$allegro_theme.name|escape:'html':'UTF-8'}</option>
							{/foreach}
							</optgroup>
							<optgroup label="{l s='Other' mod='allegro'}">
								<option {if $allegro_product->id_allegro_theme == 0}selected="selected"{/if} value="0">{l s='Use default theme' mod='allegro'}</option>
							</optgroup>
						{/if}
						</select>
					</div>
				</div>

				{* New description *}
				<div class="form-group nf-editor-group" {if $allegro_product->id_allegro_theme != -1}style="display: none;"{/if}>
					<label class="control-label col-lg-3">
						{l s='Offer description' mod='allegro'}
					</label>
					<div class="col-lg-9">
						{if $is_15}
							<div class="alert alert-info">{l s='Availables soon...' mod='allegro'}</div>
						{else}
						<div id="nf-editor" style="display: none;"></div>
						<textarea name="field[341]" id="content_html" cols="30" rows="10">
							{if !empty($form_fields_values[341].value)}{$form_fields_values[341].value}{else}[]{/if}
						</textarea>
						{/if}
					</div>
				</div>

				<hr>

				<!-- Description external -->
				<div class="form-group">
					<label class="control-label col-lg-3">
						{l s='External description' mod='allegro'}
					</label>
					<div class="col-lg-9">
						<textarea name="field[24]" class="autoload_rte" cols="60" rows="10">{$external_desc}</textarea>
						<p class="help-block">{l s='Here you can provide additional description which can be shown in theme.' mod='allegro'}</p>
					</div>
				</div>
			{* Footer *}
			{$footer_html}
			</div>
		</div>

		<!-- TAB - Images -->
		<script>
		$(document).ready(function(){
			$('table.images-table tr').click(function(e){
				if(e.target.nodeName == 'TD')
					$(this).find('input[type="radio"]').click();
			});
		});
		</script>
		<div id="product-tab-content-Images" class="product-tab-content" {if $key_tab != 'Images'}style="display: none"{/if}>
			<div class="panel product-tab">
				<h3 class="tab">{l s='Images' mod='allegro'}{$prev_next}</h3>
				<div class="warn alert alert-info" style="display:block">
					<p>{l s='Here you can manage your images - you can disable images, upload new images and select a cover of auction.' mod='allegro'}</p>
					<p>{l s='By default all product images are available in theme but you can disable some of them here.' mod='allegro'}</p>
				</div>
				<div class="form-group">
					<div class="col-lg-12">
						<table class="table images-table">
							<thead>
								<tr>
									<th>{l s='Product images' mod='allegro'}</th>
									<th class="text-center">{l s='Cover' mod='allegro'}</th>
									<th>{l s='Actions' mod='allegro'}</th>
								</tr>
							</thead>
							<tbody>
							{if count($product_images)}
								{foreach from=$product_images item=image}
								<tr>
									<td><img class="img-thumbnail" src="{$link->getImageLink('img', $image.id_image, $image_type)}" alt=""></td>
									<td class="text-center">
										<input type="radio" name="image_cover" value="shop:{$image.id_image|intval}" {if $allegro_product->image_cover == "shop:`$image.id_image`"}checked="checked"{/if} />
										<p><small>{if $cover_default == $image.id_image}({l s='product cover' mod='allegro'}){/if}</small></p>
									</td>
									<td>
										<input type="checkbox" id="images_excl_{$image.id_image|intval}" name="images_excl[]" value="{$image.id_image|intval}" {if $image.id_image|in_array:$images_excl}checked="checked"{/if} />
										<label for="images_excl_{$image.id_image|intval}">{l s='Disable' mod='allegro'}</label>
									</td>
								</tr>
								{/foreach}
							{else}
								<td class="list-empty" colspan="99">
									<div class="list-empty-msg">
										<i class="icon-warning-sign list-empty-icon"></i>
										{l s='No imges' mod='allegro'}
									</div>
								</td>
							{/if}
							</tbody>
							<thead>
								<tr>
									<th colspan="3">{l s='External images' mod='allegro'}</th>
								</tr>
							</thead>
							<tbody>
							{if count($allegro_images)}
								{foreach from=$allegro_images item=image}
								<tr>
									<td><img class="img-thumbnail" src="{$allegro_img_url}{$image.id_allegro_image|intval}-{$image_type|escape:'html':'UTF-8'}.jpg" alt=""></td>
									<td class="text-center">
										<input type="radio" name="image_cover" value="allegro:{$image.id_allegro_image|intval}" {if $allegro_product->image_cover == "allegro:`$image.id_allegro_image`"}checked="checked"{/if} />
									</td>
									<td><a href="{$currentIndex}&token={$token}&id_allegro_product={$smarty.get.id_allegro_product}&key_tab=Images&action=delete_thumb&id_allegro_image={$image.id_allegro_image|intval}" class="btn btn-default"><i class="icon-trash"></i> {l s='Delete image' mod='allegro'}</a></td>
								</tr>
								{/foreach}
							{else}
								<td class="list-empty" colspan="99">
									<div class="list-empty-msg">
										<i class="icon-warning-sign list-empty-icon"></i>
										{l s='No external imges' mod='allegro'}
									</div>
								</td>
							{/if}
							</tbody>
						</table>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3" >
						{l s='Upload images' mod='allegro'}
					</label>
					<div class="col-lg-3">
						<input type="file" multiple="multiple" name="images[]" style="display: none;" />
						<button class="btn btn-default" data-style="expand-right" data-size="s" type="button" onclick="$(this).prev().click();">
							<i class="icon-folder-open"></i> {l s='Select files...' mod='allegro'}
						</button>
					</div>
				</div>
			{* Footer *}
			{$footer_html}
			</div>
		</div>

		<!-- TAB - Additional -->
		<div id="product-tab-content-Additional" class="product-tab-content" {if $key_tab != 'Additional'}style="display: none"{/if}>
			<div class="panel product-tab">
				<h3 class="tab">{l s='Additional parameters' mod='allegro'}{$prev_next}</h3>

				{foreach from=[12, 13, 14, 340, 27, 15] item=id_field} {* TODO - make it configurable *}
					{if isset($form_fields[$id_field])}
						{include file='../form/field.tpl' field=$form_fields[$id_field]}
					{/if}
				{/foreach}

				{* Footer *}
				{$footer_html}
			</div>
		</div>

		<!-- TAB - Auctions -->
		<div id="product-tab-content-Auctions" class="product-tab-content" {if $key_tab != 'Auctions'}style="display: none"{/if}>
			<div class="panel product-tab">
				<h3 class="tab">{l s='Auctions' mod='allegro'}{$prev_next}</h3>

				<div class="warn alert alert-info" style="display:block">
					<p>{l s='Unable to finish auction just after creation, please wait 1-5 minutes to finish new auction otherwise you may see an error.' mod='allegro'}</p>
					<p>{l s='New auction appears after 1-5 minut after creation.' mod='allegro'}</p>
				</div>

				<table class="table">
					<thead>
						<tr>
							<th>{l s='ID' mod='allegro'}</th>
							<th>{l s='Title' mod='allegro'}</th>
							<th>{l s='Quantity' mod='allegro'}</th>
							<th>{l s='Creation date' mod='allegro'}</th>
							<th>{l s='Last update' mod='allegro'}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$auctions item=auction}
						<tr>
							<td><a href="{$auction.link|escape:'html':'UTF-8'}" target="_blank">{$auction.id_auction|floatval}</a></td>
							<td>{$auction.title|escape:'html':'UTF-8'}</td>
							<td>{$auction.quantity|intval}</td>
							<td>{$auction.date_add|escape:'html':'UTF-8'}</td>
							<td>{$auction.date_upd|escape:'html':'UTF-8'}</td>
							<td>
								<a href="{$current}&token={$token}&id_allegro_product={$allegro_product->id|intval}&updateallegro_product&submitFinishAuction&id_auction={$auction.id_auction|floatval}&key_tab=Auctions" class="btn btn-default">
									<i class="icon icon-trash"></i>
									{l s='Finish auction' mod='allegro'}
								</a>
							</td>
						</tr>
					{foreachelse}
						<tr>
							<td class="list-empty" colspan="99">
								<div class="list-empty-msg">
									<i class="icon-warning-sign list-empty-icon"></i>
									{l s='No auctions' mod='allegro'}
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			{* Footer *}
			{$footer_html}
			</div>
		</div>

		<!-- TAB - Sale manager -->
		<div id="product-tab-content-SaleManager" class="product-tab-content" {if $key_tab != 'SaleManager'}style="display: none"{/if}>
			<div class="panel product-tab">
				<h3 class="tab">{l s='Sale manager' mod='allegro'}{$prev_next}</h3>

				<div class="warn alert alert-info">
					<p>{l s='This feature will help you manage product sales in Allegro.' mod='allegro'}</p>
				</div>

				<div id="sale_manger_group">
                    {if $PS_STOCK_MANAGEMENT}
					<!-- Relist -->
					<div class="form-group">
						<div class="col-lg-1"><span class="pull-right"></span></div>
						<label class="control-label col-lg-2">
							{l s='Relist auctions' mod='allegro'}
						</label>
						<div class="col-lg-3">
                            {if 0 && count($accounts) == 1}
							<select name="relistAccountBox[]">
								<option value="0" {if $allegro_product->relist == 0}selected="selected"{/if}>{l s='No' mod='allegro'}</option>
								<option value="1" {if $allegro_product->relist == 1}selected="selected"{/if}>{l s='Yes' mod='allegro'}</option>
							</select>
                            {else}
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{l s='Select' mod='allegro'}</th>
                                        <th>{l s='Account' mod='allegro'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$accounts item=account}
                                    <tr>
                                        <td>
                                            <input name="relistAccountBox[]" value="{$account.id_allegro_account|intval}" {if $account.id_allegro_account|in_array:$relist_accounts_ids}checked="checked"{/if} type="checkbox">
                                        </td>
                                        <td>{$account.name|escape:'html':'UTF-8'}</td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                            {/if}
						</div>
					</div>
					<!-- Min. quantity -->
					<div class="form-group">
						<label class="control-label col-lg-3">
							<span>
								{l s='Min. relist quantity' mod='allegro'}
							</span>
						</label>
						<div class="col-lg-3">
							<input type="text" name="relist_min_qty" value="{$allegro_product->relist_min_qty|intval}" />
						</div>
					</div>

					<!-- Stock sync -->
                    <div class="form-group attribs">
    					<div class="col-lg-1"><span class="pull-right"></span></div>
    					<label class="control-label col-lg-2">
    						{l s='Stock sync' mod='allegro'}
    					</label>
    					<div class="col-lg-9">
    						<span class="switch prestashop-switch fixed-width-lg">
    							<input type="radio" name="stock_sync" id="stock_sync_on" value="1" {if $allegro_product->stock_sync&1}checked="checked"{/if}>
    							<label for="stock_sync_on" class="radioCheck">
    								{l s='Yes' mod='allegro'}
    							</label>
    							<input type="radio" name="stock_sync" id="stock_sync_off" value="0" {if !$allegro_product->stock_sync&1}checked="checked"{/if}>
    							<label for="stock_sync_off" class="radioCheck">
    								{l s='No' mod='allegro'}
    							</label>
    							<a class="slide-button btn"></a>
    						</span>
    					</div>
    				</div>

                    <!-- Price sync -->
					<div class="form-group attribs">
						<div class="col-lg-1"><span class="pull-right"></span></div>
						<label class="control-label col-lg-2">
							{l s='Price sync (in time stock update)' mod='allegro'}
						</label>
						<div class="col-lg-3">
                            <div class="radio">
                                <label for="price_sync_0"><input id="price_sync_0" name="price_sync" {if !$allegro_product->price_sync}checked="checked"{/if} type="radio" value="0"> {l s='No' mod='allegro'}</label>
                            </div>
                            <div class="radio">
                                <label for="price_sync_1"><input id="price_sync_1" name="price_sync" {if $allegro_product->price_sync == 1}checked="checked"{/if} type="radio" value="1"> {l s='Yes - up only' mod='allegro'}</label>
                            </div>
                            <div class="radio">
                                <label for="price_sync_2"><input id="price_sync_2" name="price_sync" {if $allegro_product->price_sync == 2}checked="checked"{/if} type="radio" value="2"> {l s='Yes - up and down' mod='allegro'}</label>
                            </div>
						</div>
					</div>
                    {/if}

				</div>

			{* Footer *}
			{$footer_html}
			</div>
		</div>

	</form>
</div>
