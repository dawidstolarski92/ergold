{if $page.page_name == 'index'}
<!-- Module Full Page Slider -->
    {if isset($fps_slides)}
		{if isset($fps_slides.0) && isset($fps_slides.0.sizes.1)}{capture name='height'}{$fps_slides.0.sizes.1}{/capture}{/if}
		<div id="fullpageslider">
			{foreach from=$fps_slides item=slide}
				{if $slide.active}
					<div class="section" id="section{$slide.id_slide}">
						<div class="section-content">
							<img src="{$urls.base_url}modules/pk_fullpageslider/images/{$slide.image}" {if isset($slide.size.mainimg) && $slide.size.mainimg} {$slide.size.mainimg}{else} width="100%" height="100%"{/if} alt="{$slide.legend}" class="main_image" />
							<div class="slide-content animated {$slide.text_animation}">
								{if isset($slide.description) && trim($slide.description) != ''}
									<div class="fsp-description">
										{$slide.description nofilter}
										<a href="{$slide.url}" class="button btn" title="{$slide.legend}">{l s='Discover' d='pk_fullpageslider'}</a>
									</div>
								{/if}
							</div>
							{if isset($slide.subimage01) || isset($slide.subimage02) || isset($slide.subimage03)}
							<div class="slide-subimages">
								{if isset($slide.subimage01) && ($slide.subimage01_state == 1)}
								<img src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`pk_fullpageslider/images/`$slide.subimage01`")}" class="subimage01 subimage {if isset($slide.subimage01_animation)}animated {$slide.subimage01_animation}{/if}"{if isset($slide.size.subimage01) && $slide.size.subimage01} {$slide.size.subimage01}{else} width="100%" height="100%"{/if} alt="{$slide.legend}">
								{/if}
								{if isset($slide.subimage02) && ($slide.subimage02_state == 1)}
								<img src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`pk_fullpageslider/images/`$slide.subimage02`")}" class="subimage02 subimage {if isset($slide.subimage02_animation)}animated {$slide.subimage02_animation}{/if}"{if isset($slide.size.subimage02) && $slide.size.subimage02} {$slide.size.subimage02}{else} width="100%" height="100%"{/if} alt="{$slide.legend}">
								{/if}
								{if isset($slide.subimage03) && ($slide.subimage03_state == 1)}
								<img src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`pk_fullpageslider/images/`$slide.subimage03`")}" class="subimage03 subimage {if isset($slide.subimage03_animation)}animated {$slide.subimage03_animation}{/if}"{if isset($slide.size.subimage03) && $slide.size.subimage03} {$slide.size.subimage03}{else} width="100%" height="100%"{/if} alt="{$slide.legend}">
								{/if}
							</div>
							{/if}
						</div>
					</div>
				{/if}
			{/foreach}
		</div>
	{/if}
<!-- /Module Full page Slider -->
{/if}