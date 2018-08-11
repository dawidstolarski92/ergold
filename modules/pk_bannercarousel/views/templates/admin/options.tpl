<div id="options" class="minic-container">
    <form id="form-option" class="" method="post" action="{$slider.postAction}">
        <div class="minic-top">
            <h3>{l s='Options' mod='pk_bannercarousel'}</h3>
            <a href="#options" class="minic-close">x</a>
            <br class="clearfix" />
        </div>
        <div class="minic-content">
            <!-- Animation type -->
            
            <!--<h3 style="width:100%;{if $slider.options.random == 1} display:none{/if}">{l s='Animation type' mod='pk_bannercarousel'}</h3>
            <div class="select" {if $slider.options.random == 1}style="display:none"{/if}>      
                <div class="used">
                    <label><i class="icon-info-sign tooltip2" title="{l s='These are the animation effects, choose one or more and click to the Add button.'}"></i>{l s='Unused effects' mod='pk_bannercarousel'}</label>
                    <select multiple="multiple" id="select1" name="nivo_effect[]" >
                        {foreach from=$slider.options.effect item=effect}
                            <option>{$effect}</option>
                        {/foreach}
                    </select>   
                    <input name="left2right" value="{l s='Add'}" type="button" id="add" class="button-small green tooltip2" title="{l s='Click to add effect' mod='pk_bannercarousel'}">
                </div> 
                <div class="unused">
                    <label><i class="icon-info-sign tooltip2" title="{l s='These are the used animation effects, you can select and remove them, if its empty then all will be used ( random ).'}"></i>{l s='Used effects' mod='pk_bannercarousel'}</label>
                    <select multiple="multiple" id="select2" name="nivo_current[]" >
                        {foreach from=$slider.options.current item=current}
                            <option>{$current}</option>
                        {/foreach}
                    </select>   
                    <input name="right2left" value="{l s='Remove'}" type="button" id="remove" class="button-small grey tooltip2" title="{l s='Click to remove effect' mod='pk_bannercarousel'}">
                </div> 
            </div>    
            <div class="clearfix"></div>
            <br/><br/><br/>
             Slice and Box animation          
            <h3 {if $slider.options.random == 1}style="display:none"{/if}>{l s='Slice and Box animation configuration' mod='pk_bannercarousel'}</h3>
            <div class="input-holder" {if $slider.options.random == 1}style="display:none"{/if}>
                <label>{l s='Slices' mod='pk_bannercarousel'}: </label>
                <input type="text" name="slices" value="{$slider.options.slices}" class="tooltip2" title="{l s='The number of Slices for Slice animation' mod='pk_bannercarousel'}">
            </div>
            <div class="input-holder" {if $slider.options.random == 1}style="display:none"{/if}>
                <label>{l s='BoxCols' mod='pk_bannercarousel'}: </label>
                <input type="text" name="cols" value="{$slider.options.cols}" class="tooltip2" title="{l s='The number of Cols for Box animations' mod='pk_bannercarousel'}">
            </div>
            <div class="input-holder" {if $slider.options.random == 1}style="display:none"{/if}>
                <label>{l s='BoxRows' mod='pk_bannercarousel'}: </label>
                <input type="text" name="rows" value="{$slider.options.rows}" class="tooltip2" title="{l s='The number of Rows for Box animations' mod='pk_bannercarousel'}">
            </div>
            
             Animation configuration -->
            <h3>{l s='Animation configuration' mod='pk_bannercarousel'}</h3>
            <div class="input-holder">
                <label>{l s='Speed' mod='pk_bannercarousel'}: </label>
                <input type="text" name="speed" value="{$slider.options.speed}" class="tooltip2" title="{l s='Slide transition speed in miliseconds (default is 0.5 sec)' mod='pk_bannercarousel'}">                    
            </div>    
            <div class="input-holder">
                <label>{l s='Pause Time' mod='pk_bannercarousel'}: </label>
                <input type="text" name="pause" value="{$slider.options.pause}" class="tooltip2" title="{l s='How long each slide will be shown in miliseconds (default is 3 sec)' mod='pk_bannercarousel'}">
            </div>
            <div class="input-holder">
                <label>{l s='Visible baners' mod='pk_bannercarousel'}: </label>
                <input type="text" name="startSlide" value="{$slider.options.startSlide}" class="tooltip2" title="{l s='The number of slides which will be visible in carousel' mod='pk_bannercarousel'}">
            </div>
            <!--<h3>{l s='Width and Height configuration' mod='pk_bannercarousel'}</h3>
            <div class="input-holder">
                <label>{l s='Slider width' mod='pk_bannercarousel'}: </label>
                <input type="text" name="width" value="{$slider.options.width}" class="tooltip2" title="{l s='If you want to fix the width of the slider than fill this out.' mod='pk_bannercarousel'}">
            </div>
            <div class="input-holder">
                <label>{l s='Slider height' mod='pk_bannercarousel'}: </label>
                <input type="text" name="height" value="{$slider.options.height}" class="tooltip2" title="{l s='If you want to fix the height of the slider than fill this out.' mod='pk_bannercarousel'}">
            </div>-->
            <div class="right">
                <!--<h3>{l s='Other options' mod='pk_bannercarousel'}</h3>
                <div class="switch-holder">
                    <label><i class="icon-info-sign tooltip2" title="{l s='Turn it ON if you want to show banners as carousel.'}"></i>{l s='Carousel View' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.random}active{else}inactive{/if}">
                        <input type="radio" id="r-random" class="" name="random"  value="{$slider.options.random}" checked="true" />
                    </div>
                </div>-->
                <div class="switch-holder">
                    <label><i class="icon-info-sign tooltip2" title="{l s='Pause the slider on mouse hover.'}"></i>{l s='Pause on Mouse Hover' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.hover}active{else}inactive{/if}">
                        <input type="radio" id="r-hover" class="" name="hover"  value="{$slider.options.hover}" checked="true" />
                    </div>
                </div>
                <div class="switch-holder">
                    <label><i class="icon-info-sign tooltip2" title="{l s='Turn it ON if you want the slider to auto slide.'}"></i>{l s='Auto slide' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.manual}active{else}inactive{/if}">
                        <input type="radio" id="r-manual" class="" name="manual"  value="{$slider.options.manual}" checked="true" />
                    </div>
                </div>
                <div class="switch-holder">
                    <label><i class="icon-info-sign tooltip2" title="{l s='If you want previous and next buttons on the two side of the slider, then turn this on.'}"></i>{l s='Prev/Next button' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.buttons}active{else}inactive{/if}">
                        <input type="radio" id="r-buttons" class="" name="buttons"  value="{$slider.options.buttons}" checked="true" />
                    </div>
                </div><!--
                <div class="switch-holder">
                    <label><i class="icon-info-sign tooltip2" title="{l s='This controls the navigation, by default these are the litle dots under the slider.'}"></i>{l s='Control' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.control}active{else}inactive{/if}">
                        <input type="radio" id="r-control" class="" name="control"  value="{$slider.options.control}" checked="true" />
                    </div>
                </div>
                <div class="switch-holder" {if $slider.options.random == 1}style="display:none"{/if}>
                    <label><i class="icon-info-sign tooltip2" title="{l s='Turn it on if you want thumbnails in the place of the ( control ) litle dots.'}"></i>{l s='Thumbnails' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.thumbnail}active{else}inactive{/if}">
                        <input type="radio" id="r-thumbnail" class="" name="thumbnail"  value="{$slider.options.thumbnail}" checked="true" />
                    </div>
                </div>                -->
                <div class="switch-holder">
                    <label><i class="icon-info-sign tooltip2" title="{l s='Turn on if you want to use different slides for different languages, otherwise the default language slides will be used for all the languages.'}"></i>{l s='I need multilanguage' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.single}active{else}inactive{/if}">
                        <input type="radio" id="r-single" class="" name="single"  value="{$slider.options.single}" checked="true" />
                    </div>
                </div>
                <!--<div class="switch-holder">
                    <label><i class="icon-info-sign tooltip2" title="{l s='Turn on if you want to use the Minic Slider only on the home page.'}"></i>{l s='Home only' mod='pk_bannercarousel'}: </label>
                    <div class="switch small {if $slider.options.front}active{else}inactive{/if}">
                        <input type="radio" id="r-front" class="" name="front"  value="{$slider.options.front}" checked="true" />
                    </div>
                </div> -->
            </div> 
        </div>
        <div class="minic-bottom">
            <input type="submit" name="submitMinicOptions" value="{l s='Save' mod='pk_bannercarousel'}" id="submitOptions" class="button-large green" />
            <a href="#options" class="minic-close button-large lgrey">{l s='Close' mod='pk_bannercarousel'}</a>
        </div>
    </form>
</div>