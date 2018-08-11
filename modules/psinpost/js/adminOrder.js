
	$(document).ready(function(){
		$('#inpost_form_div').slideUp();
		if(mpak) addPack(false);

		$("#inpost_paczki_add").click(function() {
			addPack(true);
		});

		$('#inpost_edit_label').live('click', function() {
			$('#inpost_form_div').slideDown();

		});

		if(mpak) {
			$('#inpost_editcreate_label').live('click', function() {
				$('#ajax_running').slideDown();
				$('#inpost_msg_container').slideUp().html('');
				var jpak = new Array();
				for(var i = 0; i < paczki.length; i++) {
					if(paczki[i] == 1) {
						var num = i + 1;
						var pak = {
							"waga": $('#inpost_form_waga_' + num).val(),
							"dlug": $('#inpost_form_dlug_' + num).val(),
							"szer": $('#inpost_form_szer_' + num).val(),
							"wys": $('#inpost_form_wys_' + num).val(),
							"nst": ($('#inpost_form_nst_' + num).prop("checked") ? '1' : '0')
						}
						jpak.push(pak);
					}
				}
			
		        $.ajax({
		            type: "POST",
		            async: true,
		            url: inpost_ajax_uri + '?json=1',
		            dataType: "json",
		            global: false,
		            contentType: "application/json; charset=utf-8",
		            data: JSON.stringify({
		            	"createLabel": true,
		            	"ajax": true,
		            	"token": inpost_token,
		            	"id_order": id_order,
		            	"inpost_kwota": $('#inpost_form_kwota').val(),
		            	"inpost_adres": $('#inpost_form_adres').val(),
		            	"inpost_kod": $('#inpost_form_kod').val(),
		            	"inpost_miasto": $('#inpost_form_miasto').val(),
		            	"inpost_uwagi": $('#inpost_form_uwagi').val(),
		            	"inpost_ubezp": $('#inpost_form_ubezp').val(),
		            	"inpost_mail": ($('#inpost_form_mail').prop("checked") ? '1' : '0'),
		            	"inpost_sms": ($('#inpost_form_sms').prop("checked") ? '1' : '0'),
		            	"paczki": jpak
		            }),
		            success: function(resp)
		            {
						if (resp.error)
						{
							$('#inpost_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
							$.scrollTo('#inpost', 400, { offset: { top: -100 }});
						}
						else
						{
							inpost_id_label = resp.id_inpost;
							doDownload(inpost_id_label);
							$('#inpost_print_label').show();
							$('#inpost_status').html('Tak');
							$('#inpost_track_url').html(resp.tracking);
							$('#inpost_form_div').slideUp();
						}

		                $('#ajax_running').slideUp();
		            },
		            error: function(jqXHR, textStatus, errorThrown)
		            {
		            	if(jqXHR.status == 0) alert("Nieprawidłowa domena");
		                $('#ajax_running').slideUp();
		            }
		        });
			});		
		}
		else {
			$('#inpost_editcreate_label').live('click', function() {
				$('#ajax_running').slideDown();
				$('#inpost_msg_container').slideUp().html('');

		        $.ajax({
		            type: "POST",
		            async: true,
		            url: inpost_ajax_uri,
		            dataType: "json",
		            global: false,
		            data: "createLabel=true&ajax=true&token=" + encodeURIComponent(inpost_token) + "&id_order=" + encodeURIComponent(id_order) + "&inpost_kwota=" + $('#inpost_form_kwota').val() + "&inpost_adres=" + $('#inpost_form_adres').val() + "&inpost_kod=" + $('#inpost_form_kod').val() + "&inpost_miasto=" + $('#inpost_form_miasto').val() + "&inpost_uwagi=" + $('#inpost_form_uwagi').val() + "&inpost_ubezp=" + $('#inpost_form_ubezp').val() + "&inpost_waga=" + $('#inpost_form_waga').val() + "&inpost_dlug=" + $('#inpost_form_dlug').val() + "&inpost_szer=" + $('#inpost_form_szer').val() + "&inpost_wys=" + $('#inpost_form_wys').val() + "&inpost_nst=" + ($('#inpost_form_nst').prop("checked") ? '1' : '0') + "&inpost_mail=" + ($('#inpost_form_mail').prop("checked") ? '1' : '0') + "&inpost_sms=" + ($('#inpost_form_sms').prop("checked") ? '1' : '0'),
		            success: function(resp)
		            {
						if (resp.error)
						{
							$('#inpost_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
							$.scrollTo('#inpost', 400, { offset: { top: -100 }});
						}
						else
						{
							inpost_id_label = resp.id_inpost;
							doDownload(inpost_id_label);
							$('#inpost_print_label').show();
							$('#inpost_status').html('Tak');
							$('#inpost_track_url').html(resp.tracking);
							$('#inpost_form_div').slideUp();
						}

		                $('#ajax_running').slideUp();
		            },
		            error: function(jqXHR, textStatus, errorThrown)
		            {
		            	if(jqXHR.status == 0) alert("Nieprawidłowa domena");
		                $('#ajax_running').slideUp();
		            }
		        });
			});		
		}

		$('#inpost_create_label').live('click', function() {
			$('#ajax_running').slideDown();
			$('#inpost_msg_container').slideUp().html('');
			
			$.ajax({
	            type: "POST",
	            async: true,
	            url: inpost_ajax_uri,
	            dataType: "json",
	            global: false,
	            data: "createLabel=true&ajax=true&token=" + encodeURIComponent(inpost_token) + "&id_order=" + encodeURIComponent(id_order) + "&inpost_kwota=undefined&inpost_adres=undefined&inpost_kod=undefined&inpost_miasto=undefined&inpost_uwagi=undefined&inpost_ubezp=undefined&inpost_waga=undefined&inpost_dlug=undefined&inpost_szer=undefined&inpost_wys=undefined&inpost_nst=undefined&inpost_mail=undefined&inpost_sms=undefined",
	            success: function(resp)
	            {
					if (resp.error)
					{
						$('#inpost_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
						$.scrollTo('#inpost', 400, { offset: { top: -100 }});
					}
					else
					{
						inpost_id_label = resp.id_inpost;
						doDownload(inpost_id_label);
						$('#inpost_print_label').show();
						$('#inpost_status').html('Tak');
						$('#inpost_track_url').html(resp.tracking);
					}

	                $('#ajax_running').slideUp();
	            },
	            error: function(jqXHR, textStatus, errorThrown)
	            {
	            	if(jqXHR.status == 0) alert("Nieprawidłowa domena");
	                $('#ajax_running').slideUp();
	            }
	        });
		});

		$('#inpost_print_label').live('click', function() {
			$('#inpost_msg_container').slideUp().html('');
			doDownload(inpost_id_label);
		});

	});
	

function doDownload(id_label) {
	link = inpost_pdf_uri + '?printLabel=true&id_label=' + id_label + '&token=' + encodeURIComponent(inpost_token);
	ifr = window.document.getElementById('inpost_down');
	ifr.src = link;
	return true;
}

var paczki = new Array();
function addPack(slide) {
	var lpacz = 0;
	for(var i = 0; i < paczki.length; i++) if(paczki[i]) lpacz++;
	if(lpacz >= 10) {
		alert("Limit wynosi 10 paczek");
		return;
	}
	var num = paczki.length + 1;
	paczki.push(1);
    var td1 = '<td><input type="text" id="inpost_form_waga_' + num + '" name="inpost_form_waga" value="' + form_waga + '" maxlength="5" size="5"></td>';
    var td2 = '<td><input type="text" id="inpost_form_wys_' + num + '" name="inpost_form_wys" value="' + form_wys + '" maxlength="6" size="6"></td>';
    var td3 = '<td><input type="text" id="inpost_form_dlug_' + num + '" name="inpost_form_dlug" value="' + form_dlug + '" maxlength="6" size="6"></td>';
    var td4 = '<td><input type="text" id="inpost_form_szer_' + num + '" name="inpost_form_szer" value="' + form_szer + '" maxlength="6" size="6"></td>';
    var td5 = '<td><input type="checkbox" id="inpost_form_nst_' + num + '" name="inpost_form_nst" value="1" ' + form_nst + '></td>';
    var td6 = '<td><img src="' + inpost_img_uri + '/delete_16.png" class="inpost_paczki_del" style="cursor: pointer;"/><div class="inpost_paczki_num" style="display: none;">' + num + '</div></td>';

	$('#inpost_paczki tbody').append('<tr id="inpost_paczki_tr_' + num + '" style="display: none;">' + td1 + td2 + td3 + td4 + td5 + td6 + '</tr>');
	if(slide) {
		$("#inpost_paczki_tr_" + num).slideDown();
	}
	else {
		$("#inpost_paczki_tr_" + num).show();
	}
	$(".inpost_paczki_del").click(function() {
		var lpacz = 0;
		for(var i = 0; i < paczki.length; i++) if(paczki[i]) lpacz++;
		if(lpacz <= 1) return;
		var num = $(".inpost_paczki_num", $(this).parent()).html();
		paczki[num - 1] = 0;
		lpacz--;
		$("#inpost_paczki_tr_" + num).slideUp();
	});
}
