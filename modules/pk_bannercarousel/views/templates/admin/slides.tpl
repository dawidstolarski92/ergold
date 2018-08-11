{include file="{$minic.admin_tpl_path}messages.tpl" id='sortable'}
<div id="slides-list">
    <div id="slides-navigation">
        {if $slider.options.single == 0}
            <a href="#{$slider.lang.default.iso_code}_slides" class="navigation active">
                <img src="{$slider.lang.lang_dir}{$slider.lang.default.id_lang}.jpg" />
                {$slider.lang.default.name}
            </a>
        {else}
            {foreach from=$slider.lang.all item=lang}
                <a href="#{$lang.iso_code}_slides" class="navigation {if $lang.iso_code == $slider.lang.default.iso_code}active{/if}">
                    <img src="{$slider.lang.lang_dir}{$lang.id_lang}.jpg" />
                    {$lang.name}
                </a>
            {/foreach}    
        {/if}
    </div>
    <div class="slides-holder" style="width: {$slider.slides|count}00%;">
        {foreach name=languages from=$slider.slides key=iso item=lang}
            <ul id="{$iso}_slides" class="languages" style="width: {100/{$slider.slides|count}}%;">
                {foreach name=slides from=$lang item=slide}
                    <li id="order_{$slide.id_slide}h{$slide.id_order}" class="slide" >
                        <div class="slide-header {if $slide.active != 1}inactive{/if}">
                            <i class="orderer icon-align-justify"></i>
                            <span class="order">{if $slide.id_order le 9}0{/if}{$slide.id_order}</span>
                            <span class="title">{if $slide.title}{$slide.title}{else}{l s='Untitled slide' mod='pk_bannercarousel'}{/if}</span>
                            <span class="{if $slide.active == 1}active{else}deactivated{/if}"></span>
                            <span class="arrow"></span>
                        </div>
                        <div id="{$iso}_{$slide.id_order}" class="slide-body minic-container">
                            <form method="post" action="{$slider.postAction}" enctype="multipart/form-data">
                                <div class="minic-top">
                                    <h3>{l s='Editing slide' mod='pk_bannercarousel'}
                                        <a href="http://module.minic.ro/minic-slider-news/using-the-feedback-and-bug-report/" target="_blank" class="help">{l s='help & tips' mod='pk_bannercarousel'}</a>
                                    </h3>
                                    <div class="title input-holder">
                                        <input type="text" name="title" class="tooltip2" placeholder="{l s='The title of the slide'}" value="{$slide.title}" title="{l s='This will be the title on the slide.' mod='pk_bannercarousel'}" /> 
                                    </div>
                                    <div class="switch-holder">
                                        <div class="switch large {if $slide.active}active{else}inactive{/if}">
                                            <input type="radio" class="" name="isActive"  value="{$slide.active}" checked="true" />
                                        </div>
                                    </div>
                                    <a href="#{$iso}_{$slide.id_order}" class="minic-close">x</a>
                                </div>
                                <div class="minic-content">
                                    <div class="image-holder input-holder">                                        
                                        <label>{l s='Image' mod='pk_bannercarousel' mod='pk_bannercarousel'}</label>
                                        <img src="{$module_dir}uploads/{$slide.image}" />
                                        <p class="warning">Recommended width of image - 440px</p>
                                        <input type="file" name="newImage" class="file"/>
                                    </div>
                                    <div class="url input-holder">
                                      	<label>{l s='Url' mod='pk_bannercarousel'}: </label>
                                      	<input type="text" name="url" class="tooltip2" placeholder="{l s='Link of the slide'}" value="{$slide.url}" title="{l s='ex. http://myshop.com/promotions' mod='pk_bannercarousel'}" />  
                                        <span>{l s='Blank target' mod='pk_bannercarousel'}: </span>
                                        <input type="checkbox" name="target" class="tooltip2" value="1" {if $slide.target == 1}checked="true"{/if} title="{l s='Check this if you want to open the link in new window.' mod='pk_bannercarousel'}" />         
                                    </div>
                                    <div class="alt input-holder">
                                        <label>{l s='Image alt' mod='pk_bannercarousel'}: </label>
                                        <input type="text" name="alt" class="tooltip2" placeholder="{l s='An alternate text for the image'}" value="{$slide.alt}" title="{l s='The image alt, alternate text for the image' mod='pk_bannercarousel'}" />
                                    </div>
                                    <!--<div class="caption input-holder"> 
                                      	<label>{l s='Caption' mod='pk_bannercarousel'}: </label>
                                    	<textarea type="text" name="caption" class="tooltip2" cols="40" rows="6" title="{l s='Be carefull, too long text isnt good and HTML is not allowed.'}" placeholder="{l s='The slide text' mod='pk_bannercarousel'}">{$slide.caption}</textarea>
                                    </div>   -->
                                </div>  
                                <div class="minic-bottom">
                                    <input type="hidden" name="slideId" value="{$slide.id_slide}" />
                                    <input type="hidden" name="orderId" value="{$slide.id_order}" />
                                    <input type="hidden" name="slideIso" value="{$slide.lang_iso}" />
                                    <input type="hidden" name="oldImage" value="{$slide.image}" />    
                                    <input type="submit" name="deleteSlide" value="{l s='Delete' mod='pk_bannercarousel'}" id="delete-slide" class="button-large red disabled" />   
                                    <input type="submit" name="editSlide" value="{l s='Update' mod='pk_bannercarousel'}" id="update-slide" class="button-large green" />       
                                    <a href="#{$iso}_{$slide.id_order}" class="button-large grey minic-close">{l s='Close' mod='pk_bannercarousel'}</a>
                                </div>    
                            </form>
                        </div>  
                    </li>                 
              {/foreach}    
          </ul>               
      {/foreach}
    </div>  
</div>