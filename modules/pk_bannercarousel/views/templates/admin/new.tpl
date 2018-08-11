<div id="new" class="minic-container">
    <form id="form-new" class="" method="post" action="{$slider.postAction}" enctype="multipart/form-data">
        <div class="minic-top">
            <h3>{l s='New slide' mod='pk_bannercarousel'}
                <a href="http://module.minic.ro/minic-slider-news/using-the-feedback-and-bug-report/" target="_blank" class="help">{l s='help & tips' mod='pk_bannercarousel'}</a>
            </h3>
            <a href="#new" class="minic-close">x</a>
        </div>
        <div class="minic-content">
            <div class="title input-holder">
                <label>{l s='Title' mod='pk_bannercarousel'}</label>
                <input type="text" name="title" class="tooltip2" placeholder="{l s='The title of the slide'}" title="{l s='This will be the title on the slide.' mod='pk_bannercarousel'}" /> 
            </div>
            <div class="url input-holder">
                <label>{l s='Url' mod='pk_bannercarousel'}</label>
                <input type="text" name="url" class="tooltip2" placeholder="{l s='Link of the slide'}" title="{l s='ex. http://myshop.com/promotions' mod='pk_bannercarousel'}" />  
                <span>{l s='Blank target' mod='pk_bannercarousel'}</span>
                <input type="checkbox" name="target" class="tooltip2" placeholder="1" title="{l s='Check this if you want to open the link in new window.' mod='pk_bannercarousel'}" />         
            </div>
            <div class="image input-holder">
                <label>{l s='Image' mod='pk_bannercarousel'}</label>
                <input type="file" name="image" id="image-chooser" class="tooltip2" title="{l s='Choose an image, only .jpg, .png, .gif are allowed.' mod='pk_bannercarousel'}" /><br/><br/>
                <p class="warning">Recommended width of image - 440px</p>
            </div>
            <div class="imageName input-holder">
                <label>{l s='Image name' mod='pk_bannercarousel'}</label>
                <input type="text" name="imageName" class="tooltip2" placeholder="{l s='Image name'}" title="{l s='Optional! The name of the uploaded image without extension. The white spaces will be replaces with underscore ( _ )' mod='pk_bannercarousel'}" />           
            </div>
            {if $slider.options.single == 1}
            <div class="input-holder language">
                <label>{l s='Language' mod='pk_bannercarousel'}</label>
                <select name="language" class="" title="{l s='The language of the slide.' mod='pk_bannercarousel'}">
                    {foreach from=$slider.lang.all item=lang}
                        <option value="{$lang.id_lang}" {if $lang.id_lang == $slider.lang.default.id_lang}selected="selected"{/if}>{$lang.name}</option>
                    {/foreach}
                </select>
            </div>
            {/if}
            <div class="alt input-holder">
                <label>{l s='Image alt' mod='pk_bannercarousel'}</label>
                <input type="text" name="alt" class="tooltip2" placeholder="{l s='An alternate text for the image'}" title="{l s='The image alt, alternate text for the image' mod='pk_bannercarousel'}" />
            </div>
            <!--<div class="caption input-holder"> 
                <label>{l s='Caption' mod='pk_bannercarousel'}</label>
                <textarea type="text" name="caption" cols=40 rows=6 class="tooltip2" title="{l s='Be carefull, too long text isnt good and FULL HTML is allowed.'}" placeholder="{l s='The slide text' mod='pk_bannercarousel'}"></textarea>                      
            </div>-->
            
        </div>
        <div class="minic-bottom">
            <input type="submit" name="submitNewSlide" value="{l s='Add Slide' mod='pk_bannercarousel'}" class="green button-large" />
            {if $slider.options.single == 0}
                <input type="hidden" name="language" value="{$slider.lang.default.id_lang}" />
            {/if}
            <a href="#new" class="minic-close button-large lgrey">{l s='Close' mod='miicslider' mod='pk_bannercarousel'}</a>
        </div>
    </form>
</div>
