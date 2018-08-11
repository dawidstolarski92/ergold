<div class="post_format_items post_gallery {if isset($class) && $class}{$class}{/if}">
{if isset($gallery_lists) && $gallery_lists}
{foreach from=$gallery_lists item=galleryimg}
	<div class="post_gallery_img item">
		<div class="post-thumb" style="background-image:url({$galleryimg.$imagesize})"></div>
	</div>
{/foreach}
{/if}
</div>