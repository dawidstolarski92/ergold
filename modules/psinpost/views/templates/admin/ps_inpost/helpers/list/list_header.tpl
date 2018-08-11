{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
<script>
    var inpost_pdf_uri = '{$inpost_pdf_uri|escape:'htmlall':'UTF-8'}';
    var inpost_token = '{$inpost_token|escape:'htmlall':'UTF-8'}';
        
    function doDownload(id_label) {
        link = inpost_pdf_uri + '?printLabel=true&id_label=' + id_label + '&token=' + encodeURIComponent(inpost_token);
        ifr = window.document.getElementById('inpost_down');
        ifr.src = link;
        return true;
    }
    
</script>
<iframe id="inpost_down" style="display: none;"></iframe>

{if isset($updateOrderStatus_mode) && $updateOrderStatus_mode}
	<div class="panel">
		<div class="panel-heading">
			{l s='Choose an order status'}
		</div>
		<form action="{$REQUEST_URI}" method="post">
			<div class="radio">
				<label for="id_order_state">
					<select id="id_order_state" name="id_order_state">
{foreach from=$order_statuses item=order_status_name key=id_order_state}
						<option value="{$id_order_state|intval}">{$order_status_name|escape}</option>
{/foreach}
					</select>
				</label>
			</div>
{foreach $POST as $key => $value}
	{if is_array($value)}
		{foreach $value as $val}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}[]" value="{$val|escape:'html':'UTF-8'}" />
		{/foreach}
	{elseif strtolower($key) != 'id_order_state'}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}" />

	{/if}
{/foreach}
			<div class="panel-footer">
				<button type="submit" name="cancel" class="btn btn-default">
					<i class="icon-remove"></i>
					{l s='Cancel'}
				</button>
				<button type="submit" class="btn btn-default" name="submitUpdateOrderStatus">
					<i class="icon-check"></i>
					{l s='Update Order Status'}
				</button>
			</div>
		</form>
	</div>
{/if}

{if isset($editLabels_mode) && $editLabels_mode}
    {if count($errors)}
        <div class="error alert alert-danger">
            {if count($errors) == 1}
                {$errors[0]|escape:'htmlall':'UTF-8'}
            {else}
                {$errors|count} {l s='errors' mod='psinpost'}
                <br/>
                <ol>
                    {foreach $errors as $error}
                        <li>{$error|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                </ol>
            {/if}
        </div>
    {/if}
    <form action="{$REQUEST_URI}" method="post">
    {foreach from=$ids item=id key=i}
        <div class="panel">
            <div class="panel-heading">
                Order #{$id|intval} / {$refs.$id}
                <input type="hidden" id="orderBox_{$id|intval}" name="orderBox[]" value="{$id|intval}">
            </div>
            <div class="form-group" id="inpost_form1">
                <label for="inpost_form_kwota" class="control-label col-lg-3">Kwota pobrania</label>
                <div class="input-group col-lg-5">
                    <input type="text" id="inpost_form_kwota_{$id|intval}" name="inpost_form_kwota_{$id|intval}" autocomplete="off" onchange="this.value = this.value.replace(/,/g, '.');" value="{$val_kwoty.$id|escape:'htmlall':'UTF-8'}" maxlength="12" size="12">
                </div>
            </div>
            <div class="form-group" id="inpost_form2">
                <label for="inpost_form_adres" class="control-label col-lg-3">Adres</label>
                <div class="input-group col-lg-5">
                    <input type="text" id="inpost_form_adres_{$id|intval}" name="inpost_form_adres_{$id|intval}" value="{$val_adresy.$id|escape:'htmlall':'UTF-8'}" maxlength="30" size="60">
                </div>
            </div>
            <div class="form-group" id="inpost_form3">
                <label for="inpost_form_kod" class="control-label col-lg-3">Kod</label>
                <div class="input-group col-lg-5">
                    <input type="text" id="inpost_form_kod_{$id|intval}" name="inpost_form_kod_{$id|intval}" value="{$val_kody.$id|escape:'htmlall':'UTF-8'}" maxlength="6" size="6">
                </div>
            </div>
            <div class="form-group" id="inpost_form4">
                <label for="inpost_form_miasto" class="control-label col-lg-3">Miasto</label>
                <div class="input-group col-lg-5">
                    <input type="text" id="inpost_form_miasto_{$id|intval}" name="inpost_form_miasto_{$id|intval}" value="{$val_miasta.$id|escape:'htmlall':'UTF-8'}" maxlength="20" size="20">
                </div>
            </div>
            <div class="form-group" id="inpost_form5">
                <label for="inpost_form_uwagi" class="control-label col-lg-3">Uwagi</label>
                <div class="input-group col-lg-5">
                    <textarea id="inpost_form_uwagi_{$id|intval}" name="inpost_form_uwagi_{$id|intval}">{$val_uwagi.$id|escape:'htmlall':'UTF-8'}</textarea>
                </div>
            </div>
            {if isset($val_kwoty.$id) && $val_kwoty.$id == 0}
            <div class="form-group" id="inpost_form6">
                <label for="inpost_form_ubezp" class="control-label col-lg-3">Ubezpieczenie</label>
                <div class="input-group col-lg-5">
                    <input type="text" id="inpost_form_ubezp_{$id|intval}" name="inpost_form_ubezp_{$id|intval}" value="{$val_ubez.$id|escape:'htmlall':'UTF-8'}" maxlength="10" size="10">
                </div>
            </div>
            {else}
                <input type="hidden" id="inpost_form_ubezp_{$id|intval}" name="inpost_form_ubezp_{$id|intval}" value="0">
            {/if}
        </div>
    {/foreach}
    <div class="panel">
        <div class="panel-footer">
            <button type="submit" name="cancel" class="btn btn-default">
                <i class="icon-remove"></i>
                {l s='Cancel'}
            </button>
            <button type="submit" class="btn btn-default" name="submitEditLabels">
                <i class="icon-check"></i>
                Utwórz etykiety
            </button>
        </div>
    </div>
    </form>
{/if}

{if isset($printLabels_mode) && $printLabels_mode}
<iframe id="inpost_down_bulk" style="display: none;"></iframe>
<script>
    link = inpost_pdf_uri + '?printLabel=true&bulk=true&id_label={$ids|escape:'htmlall':'UTF-8'}&token=' + encodeURIComponent(inpost_token);
    ifr = window.document.getElementById('inpost_down_bulk');
    ifr.src = link;    
</script>
{/if}

{if isset($printSlips_mode) && $printSlips_mode}
<iframe id="inpost_down_bulk" style="display: none;"></iframe>
<script>
    link = inpost_pdf_uri + '?printSlip=true&bulk=true&ids={$ids|escape:'htmlall':'UTF-8'}&token=' + encodeURIComponent(inpost_token);
    ifr = window.document.getElementById('inpost_down_bulk');
    ifr.src = link;    
</script>
{/if}

{if isset($createLabels_mode) && $createLabels_mode}
    {if count($errors)}
        <div class="error alert alert-danger">
            {if count($errors) == 1}
                {$errors[0]|escape:'htmlall':'UTF-8'}
            {else}
                {$errors|count} {l s='errors' mod='psinpost'}
                <br/>
                <ol>
                    {foreach $errors as $error}
                        <li>{$error|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                </ol>
            {/if}
        </div>
    {/if}
{/if}

{/block}