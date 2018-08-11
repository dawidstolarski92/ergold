{extends file="helpers/list/list_footer.tpl"}

{block name=after}
<div id="allegro_mass_create" class="panel {if $is_15}is_15{/if}">
	<div class="panel-heading">{l s='Mass create auctions' mod='allegro'} (<a href="#" onclick="$('#caBox').toggle(); return false;">{l s='show / hide' mod='allegro'}</a>)</div>
	<div class="row" id="caBox" style="display: none;">
		<div class="col-lg-offset-4 col-lg-4">
			<div class="progress">
				<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
			</div>
            <div class="alert alert-info">{l s='Select products and click "Create auctions" button.' mod='allegro'}</div>
			<hr />
			<div id="log"></div>
		</div>
		<div class="col-lg-4">
			<button type="submit" name="submit" class="btn btn-default ajax-create-auction" onclick="createAuctionStart(); return false;">
				<i class="icon-legal"></i> {l s='Create auctions' mod='allegro'}
			</button>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
    // Switch status
	$('.covered').die().live('click', function(e) {
		e.preventDefault();

		$(this).toggleClass('icon-check-sign icon-check-empty');
		$(this).toggleClass('covered covered'); // <--

		$.ajax({
			type: 'POST',
		  	url: 'index.php?controller=AdminAllegroProduct&token={$token}&ajax=1&action=toggle'+$(this).data('action'),
		  	data: { id_allegro_product: $(this).data('id') }
		}).done(function( msg ) {
		    showSuccessMessage('{l s='Status updated successfully' mod='allegro'}');
		});
	});
});

products_ids = [];
products_index = 0;
is_running = false;
function createAuctionRec() {
	$.ajax({
		type: 'POST',
		dataType: 'json',
	  	url: 'index.php?controller=AdminAllegroProduct&token={$token}&ajax=1&action=createAuction',
	  	data: { id_allegro_product: products_ids[products_index]}
	}).done(function( msg ) {
		// Add info to log
		$('#log').append('#'+msg['id']+' '+msg['msg']+'<br />');

		// Update progressbar
	    updateProgressbar();

	    // Continue process
	    if(typeof products_ids[products_index+1] != 'undefined') {
	    	products_index++;
	    	createAuctionRec()
	    } else {
	    	createAuctionFinish();
	    }
	});
}

function createAuctionStart() {
    products_ids = [];
    $('input[name="allegro_productBox[]"]:checked').each(function() {
       products_ids.push(this.value);
    });

	if(products_ids.length > 0) {
		$('#log').text('');
		if(!is_running) {
			is_running = true;
			createAuctionRec();
			$('.progress-bar').css('width', '0%').attr('aria-valuenow', 0).text('');
			$('.ajax-create-auction i').removeClass('icon-legal').addClass('icon-refresh icon-spin');
		}
	}
	else
		alert('{l s='Select at least one product' mod='allegro'}');
}

function createAuctionFinish() {
	products_index=0;
	is_running=false;
	$('.ajax-create-auction i').addClass('icon-legal').removeClass('icon-refresh icon-spin');
	$('.progress-bar').addClass('progress-bar-success');
}

function updateProgressbar() {
	var progress = 100/(products_ids.length/(products_index+1));
	$('.progress-bar').css('width', progress+'%').attr('aria-valuenow', progress).text(Math.round(progress)+'% {l s='Complete' mod='allegro'} '+'('+(products_index+1)+'/'+products_ids.length+')');
}
</script>
{/block}
