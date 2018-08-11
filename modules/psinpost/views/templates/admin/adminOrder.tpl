<br />
<script>
    var inpost_ajax_uri = '{$inpost_ajax_uri|escape:'htmlall':'UTF-8'}';
    var inpost_token = '{$inpost_token|escape:'htmlall':'UTF-8'}';
    var inpost_pdf_uri = '{$smarty.const._INPOST_PDF_URI_|escape:'htmlall':'UTF-8'}';
    var inpost_id_label = '{$id_label|escape:'htmlall':'UTF-8'}';
    var mpak = 0;
</script>

<iframe id="inpost_down" style="display: none;"></iframe>

<div class="row">
	<div class="col-lg-7">
		<div class="panel" id="inpost">
			<div class="panel-heading">
                <i class="icon-truck"></i>
                Wysyłka Inpost
			</div>

			<div id="inpost_shipment_creation"{if isset($smarty.get.scrollToShipment)} class="displayed-element"{/if}>
				<div id="inpost_msg_container">{if isset($errors) && $errors}{include file=$smarty.const._PS_MODULE_DIR_|cat:'psinpost/views/templates/admin/errors.tpl'}{/if}</div>

				<div class="row">
					<div id="ipost_actions" class="form-horizontal">
						<div class="col-lg-4">
                            <table cellspacing="0" cellpadding="10" class="table">
                                <tbody>
                                    <tr>
							             <td><button class="btn btn-default pull-right" id="inpost_create_label" type="button"><i class="process-icon-save"></i> {l s='Utwórz i drukuj etykietę' mod='psinpost'}</button></td>
                                         <td><button class="btn btn-default pull-right" id="inpost_edit_label" type="button"><i class="process-icon-save"></i> {l s='Edytuj i drukuj etykietę' mod='psinpost'}</button></td>
							             <td><button class="btn btn-default pull-right" id="inpost_print_label" type="button"{if $id_label}{else} style="display: none;"{/if}><i class="process-icon-save"></i> {l s='Drukuj etykietę' mod='psinpost'}</button></td>
							        </tr>
							    </tbody>
							</table>
						</div>
					</div>
				</div>
				
				<hr/>
	            
	            <form id="inpost_form">			
				<div class="row form-wrapper" id="inpost_form_div">
				    <div class="form-group" id="inpost_form1">
                        <label for="inpost_form_kwota" class="control-label col-lg-3">Kwota pobrania</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_kwota" name="inpost_form_kwota" autocomplete="off" onchange="this.value = this.value.replace(/,/g, '.');" value="{$inpost_kwota|escape:'htmlall':'UTF-8'}" maxlength="12" size="12">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form2">
                        <label for="inpost_form_adres" class="control-label col-lg-3">Adres</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_adres" name="inpost_form_adres" value="{$inpost_adres|escape:'htmlall':'UTF-8'}" maxlength="30" size="60">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form3">
                        <label for="inpost_form_kod" class="control-label col-lg-3">Kod</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_kod" name="inpost_form_kod" value="{$inpost_kod|escape:'htmlall':'UTF-8'}" maxlength="6" size="6">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form4">
                        <label for="inpost_form_miasto" class="control-label col-lg-3">Miasto</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_miasto" name="inpost_form_miasto" value="{$inpost_miasto|escape:'htmlall':'UTF-8'}" maxlength="20" size="20">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form5">
                        <label for="inpost_form_uwagi" class="control-label col-lg-3">Uwagi</label>
                        <div class="input-group col-lg-5">
                            <textarea id="inpost_form_uwagi" name="inpost_form_uwagi">{$inpost_uwagi|escape:'htmlall':'UTF-8'}</textarea>
                        </div>
                    </div>
                    {if isset($inpost_kwota) && $inpost_kwota == 0}
                    <div class="form-group" id="inpost_form6">
                        <label for="inpost_form_ubezp" class="control-label col-lg-3">Ubezpieczenie</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_ubezp" name="inpost_form_ubezp" value="{$inpost_ubez|escape:'htmlall':'UTF-8'}" maxlength="10" size="10">
                        </div>
                    </div>
                    {else}
                        <input type="hidden" id="inpost_form_ubezp" name="inpost_form_ubezp" value="0">
                    {/if}
                    <div class="form-group" id="inpost_form7">
                        <label for="inpost_form_waga" class="control-label col-lg-3">Waga</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_waga" name="inpost_form_waga" value="{$inpost_waga|escape:'htmlall':'UTF-8'}" maxlength="10" size="10">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form8">
                        <label for="inpost_form_wys" class="control-label col-lg-3">Wysokość</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_wys" name="inpost_form_wys" value="{$inpost_wys|escape:'htmlall':'UTF-8'}" maxlength="10" size="10">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form9">
                        <label for="inpost_form_dlug" class="control-label col-lg-3">Długość</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_dlug" name="inpost_form_dlug" value="{$inpost_dlug|escape:'htmlall':'UTF-8'}" maxlength="10" size="10">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form10">
                        <label for="inpost_form_szer" class="control-label col-lg-3">Szerokość</label>
                        <div class="input-group col-lg-5">
                            <input type="text" id="inpost_form_szer" name="inpost_form_szer" value="{$inpost_szer|escape:'htmlall':'UTF-8'}" maxlength="10" size="10">
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form11">
                        <label for="inpost_form_szer" class="control-label col-lg-3">NST</label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" id="inpost_form_nst" name="inpost_form_nst" value="1" {$inpost_nst|escape:'htmlall':'UTF-8'}>
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form12">
                        <label for="inpost_form_mail" class="control-label col-lg-3">Mail</label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" id="inpost_form_mail" name="inpost_form_mail" value="1" {$inpost_mail|escape:'htmlall':'UTF-8'}>
                        </div>
                    </div>
                    <div class="form-group" id="inpost_form13">
                        <label for="inpost_form_sms" class="control-label col-lg-3">SMS</label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" id="inpost_form_sms" name="inpost_form_sms" value="1" {$inpost_sms|escape:'htmlall':'UTF-8'}>
                        </div>
                    </div>
                    <div id="ipost_actions" class="form-horizontal">
                        <div class="col-lg-4">
                            <table cellspacing="0" cellpadding="10" class="table">
                                <tbody>
                                    <tr>
                                         <td><button class="btn btn-default pull-right" id="inpost_editcreate_label" type="button"><i class="process-icon-save"></i> {l s='Drukuj etykietę' mod='psinpost'}</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				
			</div>
			</form>

			<div id="inpost-status-panel" class="panel-group">
				<div class="panel">
					<div id="inpost-status">
						<div class="panel-body">
							<table cellspacing="0" cellpadding="10" class="table">
								<thead>
									<tr>
										<th width="200"><span class="title_box">{l s='Akcja' mod='psinpost'}</span></th>
										<th width="50"><span class="title_box">{l s='Status' mod='psinpost'}</span></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{l s='Etykieta utworzona' mod='psinpost'}</td>
										<td id="inpost_status">{if $id_label}{l s='Tak' mod='psinpost'}{else}{l s='Nie' mod='psinpost'}{/if}</td>
									</tr>
                                    <tr>
                                        <td>{l s='Link śledzenia' mod='psinpost'}</td>
                                        <td id="inpost_track_url">{$inpost_track_url}</td>
                                    </tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
