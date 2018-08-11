{if $slides|@count != 0}
  {if $page.page_name == 'index'}
  <!-- Banners Carousel -->
    <div class="banners_carousel">
      <div class="page-width">
      <div class="hidden pk_bannercarousel" 
            data-num="{if $minicSlider.options.startSlide != ''}{$minicSlider.options.startSlide}{else}2{/if}"
            data-animationspeed="{if $minicSlider.options.speed != ''}{$minicSlider.options.speed}{else}500{/if}"
            data-autoplay="{if $minicSlider.options.manual != ''}{$minicSlider.options.manual}{else}0{/if}"
            data-autoplayspeed="{if $minicSlider.options.pause != ''}{$minicSlider.options.pause}{else}3000{/if}"
            data-pauseonhover="{if $minicSlider.options.hover != ''}{$minicSlider.options.hover}{else}1{/if}"
            data-showbuttons="{if $minicSlider.options.buttons != ''}{$minicSlider.options.buttons}{else}0{/if}"
            data-pref="ban-top"></div>
      <div class="banners_carousel-container">
        <div id="banners_carousel" class="banners_carousel_top theme-default{if $minicSlider.options.thumbnail == 1 and $minicSlider.options.control != 0} controlnav-thumbs{/if}">   
          <ul id="sliderCarousel" class="slides bannersCarousel sliderCarousel_top">
            {foreach from=$slides item=image name=singleimage}
            <li class="dib">
                <div class="banners_carousel_wrap">
                {if $image.url != ''}<a href="{$image.url}" {if $image.target == 1}target="_blank"{/if}>{/if}
                  <img src="{$minicSlider.path.images}{$image.image}" class="slider_image" 
                        alt="{if $image.alt}{$image.alt}{/if}" />
                {if $image.url != ''}</a>{/if}
                </div>
            </li>
            {/foreach}
          </ul>
        </div> 
      </div>
      </div>
  </div>
  <!-- End of Banners Carousel -->
  {/if}
{/if}