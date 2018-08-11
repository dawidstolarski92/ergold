{assign var=stars_num value=5}
{assign var=stars_num_tmp value=$stars_num}
<div class="star_content">
	<div class="max-rating star-container">
		{while $stars_num_tmp-- > 0}
		  <svg class="svgic svgic-pk-star"><use xlink:href="#si-pk-star"></use></svg>
		{/while}
	</div>
	<div class="star-rating" style="width:{(100/$stars_num)*$averageTotal}%" data-average="{$averageTotal}">
		<div class="cut-stars star-container">
		{while $stars_num-- > 0}
		  <svg class="svgic svgic-pk-star"><use xlink:href="#si-pk-star"></use></svg>
		{/while}
		</div>
	</div>
</div>
{if isset($show_link) && $show_link == true}
<div class="add-review">
	<a class="open-comment-form" href="#new_comment_form">{l s='Add Review' mod='productcomments'}</a>
</div>
{/if}