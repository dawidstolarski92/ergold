<br />
<script>
    var inpost_ajax_uri = '{$inpost_ajax_uri|escape:'htmlall':'UTF-8'}';
    var inpost_token = '{$inpost_token|escape:'htmlall':'UTF-8'}';
    var inpost_pdf_uri = '{$smarty.const._INPOST_PDF_URI_|escape:'htmlall':'UTF-8'}';
    var inpost_img_uri = '{$smarty.const._INPOST_IMG_URI_|escape:'htmlall':'UTF-8'}';
    var inpost_id_label = '{$id_label|escape:'htmlall':'UTF-8'}';
    var form_waga = '{$inpost_waga|escape:'htmlall':'UTF-8'}';
    var form_wys = '{$inpost_wys|escape:'htmlall':'UTF-8'}';
    var form_dlug = '{$inpost_dlug|escape:'htmlall':'UTF-8'}';
    var form_szer = '{$inpost_szer|escape:'htmlall':'UTF-8'}';
    var form_nst = '{$inpost_nst|escape:'htmlall':'UTF-8'}';
    var mpak = 1;
</script>

<iframe id="inpost_down" style="display: none;"></iframe>

<fieldset id="inpost">
			<legend>
			    <img src="../img/admin/delivery.gif">
                Wysyłka Inpost
			</legend>

			<div id="inpost_shipment_creation"{if isset($smarty.get.scrollToShipment)} class="displayed-element"{/if}>
				<div id="inpost_msg_container">{if isset($errors) && $errors}{include file=$smarty.const._PS_MODULE_DIR_|cat:'psinpost/views/templates/admin/errors.tpl'}{/if}</div>

				<div class="row">
					<div id="ipost_actions" class="form-horizontal">
						<div class="col-lg-4">
                            <table cellspacing="0" cellpadding="10" class="table">
                                <tbody>
                                    <tr>
							             <td><button class="button" id="inpost_create_label" type="button"><i class="process-icon-save"></i> {l s='Utwórz i drukuj etykietę' mod='psinpost'}</button></td>
                                         <td><button class="button" id="inpost_edit_label" type="button"><i class="process-icon-save"></i> {l s='Edytuj i drukuj etykietę' mod='psinpost'}</button></td>
							             <td><button class="button" id="inpost_print_label" type="button"{if $id_label}{else} style="display: none;"{/if}><i class="process-icon-save"></i> {l s='Drukuj etykietę' mod='psinpost'}</button></td>
							        </tr>
							    </tbody>
							</table>
						</div>
					</div>
				</div>
				
				<hr/>
                
                <form id="inpost_form">     
                <div class="row" id="inpost_form_div">
                    <div class="col-lg-6">
                    <h3 class="title-top">Dane etykiety</h3>
                <table>
                    <tr id="inpost_form1">
                        <td>Kwota pobrania</td>
                        <td>
                            <input type="text" id="inpost_form_kwota" name="inpost_form_kwota" autocomplete="off" onchange="this.value = this.value.replace(/,/g, '.');" value="{$inpost_kwota|escape:'htmlall':'UTF-8'}" maxlength="12" size="12">
                        </td>
                    </tr>
                    <tr id="inpost_form2">
                        <td>Adres</td>
                        <td>
                            <input type="text" id="inpost_form_adres" name="inpost_form_adres" value="{$inpost_adres|escape:'htmlall':'UTF-8'}" maxlength="30" size="60">
                        </td>
                    </tr>
                    <tr id="inpost_form3">
                        <td>Kod</td>
                        <td>
                            <input type="text" id="inpost_form_kod" name="inpost_form_kod" value="{$inpost_kod|escape:'htmlall':'UTF-8'}" maxlength="6" size="6">
                        </td>
                    </tr>
                    <tr id="inpost_form4">
                        <td>Miasto</td>
                        <td>
                            <input type="text" id="inpost_form_miasto" name="inpost_form_miasto" value="{$inpost_miasto|escape:'htmlall':'UTF-8'}" maxlength="20" size="20">
                        </td>
                    </tr>
                    <tr id="inpost_form5">
                        <td>Uwagi</td>
                        <td>
                            <textarea id="inpost_form_uwagi" name="inpost_form_uwagi">{$inpost_uwagi|escape:'htmlall':'UTF-8'}</textarea>
                        </td>
                    </tr>
                    {if isset($inpost_kwota) && $inpost_kwota == 0}
                    <tr id="inpost_form6">
                        <td>Ubezpieczenie</td>
                        <td>
                            <input type="text" id="inpost_form_ubezp" name="inpost_form_ubezp" value="{$inpost_ubez|escape:'htmlall':'UTF-8'}" maxlength="10" size="10">
                        </td>
                    </tr>
                    {else}
                        <input type="hidden" id="inpost_form_ubezp" name="inpost_form_ubezp" value="0">
                    {/if}
                    <tr id="inpost_form7">
                        <td>Mail</td>
                        <td>
                            <input type="checkbox" id="inpost_form_mail" name="inpost_form_mail" value="1" {$inpost_mail|escape:'htmlall':'UTF-8'}>
                        </td>
                    </tr>
                    <tr id="inpost_form8">
                        <td>SMS</td>
                        <td>
                            <input type="checkbox" id="inpost_form_sms" name="inpost_form_sms" value="1" {$inpost_sms|escape:'htmlall':'UTF-8'}>
                        </td>
                    </tr>
                    <tr id="ipost_actions">
                        <td><button class="button" id="inpost_editcreate_label" type="button"><i class="process-icon-save"></i> {l s='Drukuj etykietę' mod='psinpost'}</button></td>
                        <td></td>
                    </tr>
                </table>
                    </div>

                    <div class="col-lg-6">
                    <h3 class="title-top">Paczki</h3>
                    
                    <div class="table-responsive">
                        <table id="inpost_paczki" class="table">
                            <thead>
                                <tr>
                                    <th>Waga</th>
                                    <th>Wysokość</th>
                                    <th>Długość</th>
                                    <th>Szerokość</th>
                                    <th>NST</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <br/>
                    <button id="inpost_paczki_add" class="button" type="button">
                        <img src="{$smarty.const._INPOST_IMG_URI_|escape:'htmlall':'UTF-8'}/add_16.png"/>
                        Dodaj
                    </button>
                </div>              
            </div>
			</div>

            <br/>

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
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
</fieldset>
