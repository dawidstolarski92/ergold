<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Slides list' mod='pk_fullpageslider'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')}&configure=pk_fullpageslider&addSlide=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new' mod='pk_fullpageslider'}" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="slidesContent">
		<div id="slides">
			{foreach from=$slides item=slide}
				<div id="slides_{$slide.id_slide}" class="panel">
					<div class="row">
						<div class="col-lg-1">
							<span><i class="icon-arrows "></i></span>
						</div>
						<div class="col-md-3">
							<img src="{$image_baseurl}{$slide.image}" alt="{$slide.title}" class="img-thumbnail" />
						</div>
						<div class="col-md-8">
							<h4 class="pull-left">
								#{$slide.id_slide} - {$slide.title}
								{if $slide.is_shared}
									<div>
										<span class="label color_field pull-left" style="background-color:#108510;color:white;margin-top:5px;">
											{l s='Shared slide' mod='pk_fullpageslider'}
										</span>
									</div>
								{/if}
							</h4>
							<div class="btn-group-action pull-right">
								{$slide.status}

								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')}&configure=pk_fullpageslider&id_slide={$slide.id_slide}">
									<i class="icon-edit"></i>
									{l s='Edit' mod='pk_fullpageslider'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')}&configure=pk_fullpageslider&delete_id_slide={$slide.id_slide}">
									<i class="icon-trash"></i>
									{l s='Delete' mod='pk_fullpageslider'}
								</a>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</div>
