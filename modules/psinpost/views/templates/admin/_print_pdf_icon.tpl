<span class="btn-group-action">
	<span class="btn-group">
	{if $label}
		<a class="btn btn-default _blank" onclick="doDownload({$label})">
			<i class="icon-truck"></i>
		</a>
	{/if}
    {if $order->delivery_number}
        <a class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order={$order->id}">
            <i class="icon-file-text"></i>
        </a>
    {/if}
	</span>
</span>
