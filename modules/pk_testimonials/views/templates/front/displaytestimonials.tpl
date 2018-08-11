<!-- Block testimonial module -->
<div id="block_testimonials" class="homemodule">
	<h1 class="page-heading">
        {l s='Testimonials' mod='pk_testimonials'}
    </h1>
	<div id="testimonials-list">
		{if isset($testimonials)}				  
			{foreach from=$testimonials item=nr}				  
				{if $nr}
				<div class="testimonial">
					<strong class="testimonialhead">{$nr.testimonial_title}</strong>
					<div id="text">
						<div class="testimonialbody">						
							{$nr.testimonial_main_message}
						</div>
					</div>
					
					<ul>
						<li><strong>{l s='Submitted By:' mod='pk_testimonials'}</strong> {$nr.testimonial_submitter_name}</li>
						<li><strong>{l s='Submitted Date:' mod='pk_testimonials'}</strong> {$nr.date_added|strip_tags}</li>
					</ul>					
				</div>
				{/if}
			{/foreach}
		{else}
			<h1>{l s='No Testimonials Yet!' mod='pk_testimonials'}</h1>
		{/if}
	</div>
	<div id="paginationTop">
		{if $currentpage > 1}
			<a href='{$link->getModuleLink("pk_testimonials", "testimonials")}?currentpage=1'>{l s='Last' mod='pk_testimonials'}</a>
			{* show < link to go back to 1 page *}
			<a href='{$link->getModuleLink("pk_testimonials", "testimonials")}?currentpage={$prevpage}'>{l s='Previous' mod='pk_testimonials'}</a>			
		{/if}		  
		[{$currentpage}]
		{if $currentpage != $totalpages}	    
			<a href='{$link->getModuleLink("pk_testimonials", "testimonials")}?currentpage={$nextpage}'>{l s='Next' mod='pk_testimonials'}</a>
			<a href='{$link->getModuleLink("pk_testimonials", "testimonials")}?currentpage={$totalpages}'>{l s='Last' mod='pk_testimonials'}</a>
		{/if}
	</div>
	<div class="addblocktestimonial">
		<a class="button addblocktestimonial" href="{$link->getModuleLink('pk_testimonials', 'addtestimonial')}">{l s='Write Testimonial' mod='pk_testimonials'}</a>
	</div>
</div> <!-- /end paginationTop div -->
<!-- /Block testimonial module -->