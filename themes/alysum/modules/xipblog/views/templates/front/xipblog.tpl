<div class="home_blog_post_area {$xipbdp_designlayout} {$hookName} small-preview">
	<div class="home_blog_post page-width">
		<div class="page_title_area">
			{if isset($xipbdp_title)}
				<h4 class="module-title"><span>{$xipbdp_title}</span></h4>
			{/if}
		</div>
		<div class="row home_blog_post_inner carousel">
		{if (isset($xipblogposts) && !empty($xipblogposts))}
			{foreach from=$xipblogposts item=xipblgpst}
				<article class="blog_post col-xs-12 col-sm-4">
					<div class="blog_post_content">
						<div class="blog_post_content_top">
							<div class="post_thumbnail">
								{if $xipblgpst.post_format == 'video'}
									{assign var="postvideos" value=','|explode:$xipblgpst.video}
									{if $postvideos|@count > 1 }
										{include file="module:xipblog/views/templates/front/post-video.tpl" videos=$postvideos width='570' height="316" class="carousel"}
									{else}
										{include file="module:xipblog/views/templates/front/post-video.tpl" videos=$postvideos width='570' height="316" class=""}
									{/if}
								{elseif $xipblgpst.post_format == 'audio'}
									{assign var="postaudio" value=','|explode:$xipblgpst.audio}
									{if $postaudio|@count > 1 }
										{include file="module:xipblog/views/templates/front/post-audio.tpl" audios=$postaudio width='570' height="316" class="carousel"}
									{else}
										{include file="module:xipblog/views/templates/front/post-audio.tpl" audios=$postaudio width='570' height="316" class=""}
									{/if}
								{elseif $xipblgpst.post_format == 'gallery'}
									{if $xipblgpst.gallery_lists|@count > 1 }
										{include file="module:xipblog/views/templates/front/post-gallery.tpl" gallery=$xipblgpst.gallery_lists imagesize="home_default" class="carousel"}
									{else}
										{include file="module:xipblog/views/templates/front/post-gallery.tpl" gallery=$xipblgpst.gallery_lists imagesize="home_default" class=""}
									{/if}
								{else}
									<div class="post-thumb" style="background-image:url({$xipblgpst.post_img_medium})"></div>
									<div class="blog_mask">
										<div class="blog_mask_content">
											<a class="thumbnail_lightbox" href="{$xipblgpst.post_img_large}">
												<i class="icon_plus"></i>
											</a>
										</div>
									</div>
								{/if}
							</div>
						</div>
						<div class="blog_post_content_bottom">

							<div class="post_meta clearfix">
								<p class="meta_author" style="display:none">
									{l s='Posted by' mod='xipblog'}
									<span>{$xipblgpst.post_author_arr.firstname} {$xipblgpst.post_author_arr.lastname}</span>
								</p>
								<h6 class="meta_date">
									{$xipblgpst.post_date|date_format:"%b %d, %Y"}
								</h6>
								
								<p class="meta_category" style="display:none">
									{l s='IN' mod='xipblog'}
									<a href="{$xipblgpst.category_default_arr.link}">{$xipblgpst.category_default_arr.name}</a>
								</p>
							</div>

							<div class="post_title text-uppercase"><a href="{$xipblgpst.link}">{$xipblgpst.post_title}</a></div>
							
							<div class="post_content">
								{if isset($xipblgpst.post_excerpt) && !empty($xipblgpst.post_excerpt)}
									<p>{$xipblgpst.post_excerpt|truncate:130:' ...'}</p>
								{else}
									<p>{$xipblgpst.post_content|truncate:130:' ...'}</p>
								{/if}
							</div>
							<div class="content_more">
								<a class="read_more btn" href="{$xipblgpst.link}">{l s='Read more' mod='xipblog'}</a>
							</div>
						</div>
					</div>
				</article>
			{/foreach}
		{else}
			<p>{l s='No Blog Post Found' mod='xipblog'}</p>
		{/if}
		</div>
	</div>
</div>