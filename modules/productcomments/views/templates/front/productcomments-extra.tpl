{if (!$content_only && (($nbComments == 0 && $too_early == false && ($logged || $allow_guests)) || ($nbComments != 0)))}
<div id="product_comments_block_extra">
	{if $nbComments != 0}
	<div class="comments_note">
		<span>{l s='Average grade' mod='productcomments'}&nbsp</span>
		<div class="star_content clearfix">
		{section name="i" start=0 loop=5 step=1}
			{if $averageTotal le $smarty.section.i.index}
				<div class="star"></div>
			{else}
				<div class="star star_on"></div>
			{/if}
		{/section}
		</div>
	</div>
	{/if}
	<div class="comments_advices">
		{if $nbComments != 0}
		<a href="#idTab5">{l s='Read user reviews' mod='productcomments'} ({$nbComments})</a><br/>
		{/if}
		{if ($too_early == false AND ($logged OR $allow_guests))}
		<a class="open-comment-form" href="#new_comment_form">{l s='Write your review' mod='productcomments'}</a>
		{/if}
	</div>
</div>
{/if}
<!--  /Module ProductComments -->
