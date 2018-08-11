<!-- Block testimonial module -->
<div id="block_testimonials_column" class="oh">
	<div class="testimonials-wrapper">
		<h4 class="module-title"><span>{l s='Testimonials' mod='pk_testimonials'}</span></h4>
		<div class="module-body">
	    <ul class="testimonials-list testimonials-{$hookn}" data-hook="{$hookn}">
			{if isset($testims)}		  
			{foreach from=$testims item=nr}	
			<li class="testimonial">
				<div class="indent">
					<div class="testimonial-body">
						<div class="item-wrapper">
			    			<div class="testimonial-title">{$nr.testimonial_title nofilter}</div>
			    			<div class="testimonial-message">{$nr.testimonial_main_message|truncate:150:'...'}</div>	
			    		</div>
		    			<div class="bott"></div>
		    		</div>
		    		<div class="flex-container">
			    		<div class="testimonial-avatar">
			    			{$nr.avatar nofilter}
			    		</div>
			    		<div class="testimonial-author">
			    			<div>{$nr.testimonial_submitter_name}</div>
			    			<h6>{$nr.date_added}</h6>
			    		</div>
			    	</div>
			    </div>
		    </li>
			{/foreach}
			{/if}
	    </ul>
	    </div>
    </div>
</div>
<!-- /Block testimonial module -->