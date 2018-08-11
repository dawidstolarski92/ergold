<!-- Block testimonial module -->
<div id="block_testimonials" class="homemodule{if isset($displayImage) && $displayImage == 0} no-bg{/if}">
	<div class="hidden pk_testimonials" {if isset($hookn)}data-hookn="{$hookn}"{/if} data-page="{$page.page_name}"></div> 
	<div class="testimonials-bg"{if isset($displayImage) && ($displayImage == 1) && isset($testimonial_bg)} style="background-image:url('{$testimonial_bg}');"{/if}>
		<div class="page-width">
			<div class="testimonials-wrapper">
			<h4 class="module-title"><span><a href="{$link->getModuleLink('pk_testimonials', 'testimonials')}" class="testimonial-blocktitle lmromandemi">{l s='Testimonials' mod='pk_testimonials'}</a></span></h4>
		    <ul id="{if isset($hookn)}{$hookn}{/if}testimonials">
				{if isset($testims)}		  
				{foreach from=$testims item=nr}	
				<li class="testimonial">
					<div class="indent">
						<div class="testimonial-body">
							<div class="item-wrapper">
				    			<div class="testimonial-title{if ($nr.testimonial_title == "“")} nt main_color{/if}">{$nr.testimonial_title}</div>
				    			<div class="testimonial-message">{$nr.testimonial_main_message|truncate:250}</div>	
				    		</div>
			    			<div class="bott"></div>
			    		</div>
			    		<div class="testimonial-avatar">{$nr.avatar}</div>	    			
			    		<div class="testimonial-author lmroman">{$nr.testimonial_submitter_name}, {$nr.date_added}</div>
				    </div>
			    </li>
				{/foreach}
				{/if}
		    </ul>
		    </div>
	    </div>
    </div>
</div>
<!-- /Block testimonial module -->