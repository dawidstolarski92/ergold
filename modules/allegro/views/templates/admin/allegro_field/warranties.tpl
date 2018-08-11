<form action="" method="post" class="panel form-horizontal">
	<div class="panel-heading">{l s='Default warranties & return policies' mod='allegro'} ({$allegro_login|escape:'html':'UTF-8'})</div>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Implied warranty' mod='allegro'}</label>
        <div class="col-lg-{if $warranties}3{else}6{/if}">
            {if $implied_warranties}
            <select name="implied_warranty">
                <option value="">{l s='- None -' mod='allegro'}</option>
                {foreach from=$implied_warranties item=item key=key name=name}
                    <option {if $ALLEGRO_IMPLIED_WARRANTY == $item->id}selected="selected"{/if} value="{$item->id|escape:'html':'UTF-8'}">{$item->name|escape:'html':'UTF-8'}</option>
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
                <option value="">{l s='- None -' mod='allegro'}</option>
                {foreach from=$return_policies item=item key=key name=name}
                    <option {if $ALLEGRO_RETURN_POLICY == $item->id}selected="selected"{/if} value="{$item->id|escape:'html':'UTF-8'}">{$item->name|escape:'html':'UTF-8'}</option>
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
                <option value="">{l s='- None -' mod='allegro'}</option>
                {foreach from=$warranties item=item key=key name=name}
                    <option {if $ALLEGRO_WARRANTY == $item->id}selected="selected"{/if} value="{$item->id|escape:'html':'UTF-8'}">{$item->name|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
            {else}
            <div class="alert alert-warning">
                {l s='No warranties - first you must add one' mod='allegro'}
            </div>
            {/if}
        </div>
    </div>

    <!-- Footer -->
	<div class="panel-footer">
		<button type="submit" name="submitSaveWarranties" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='allegro'}</button>
	</div>
</form>