{if $slides|@count != 0}

    {if $minicSlider.options.front == 1 && $page.page_name != 'index'}
        <!-- aw_Slider -->
    {else}

        {if $minicSlider.options.current != ''}
            {assign var=aw_mode value=$minicSlider.options.current}
        {else}
            {assign var=aw_mode value=fade}
        {/if}
        {if $minicSlider.options.speed != ''}
            {assign var=aw_speed value=$minicSlider.options.speed}
        {else}
            {assign var=aw_speed value=1000}
        {/if}
        {if $minicSlider.options.manual == 1}
            {assign var=aw_auto value="true"}
        {else}
            {assign var=aw_auto value="false"}
        {/if}
        {if $minicSlider.options.pause != ''}
            {assign var=aw_pause value=$minicSlider.options.pause}
        {else}
            {assign var=aw_pause value=3500}
        {/if}
        {if $minicSlider.options.random == 1}
            {assign var=aw_random value="true"}
        {else}
            {assign var=aw_random value="false"}
        {/if}
        {if $minicSlider.options.buttons == 1}
            {assign var=aw_buttons value="true"}
        {else}
            {assign var=aw_buttons value="false"}
        {/if}
        {if $minicSlider.options.control == 1}
            {assign var=aw_control value="true"}
        {else}
            {assign var=aw_control value="false"}
        {/if}
        {if $minicSlider.options.hover == 1}
            {assign var=aw_hover value="true"}
        {else}
            {assign var=aw_hover value="false"}
        {/if}

    
        <div id="aw_slider_container" class="load-animate homemodule">  
            <div class="hidden pk_awshowcaseslider" 
                data-mode="{$aw_mode}"
                data-speed="{$aw_speed}"
                data-auto="{$aw_auto}"
                data-pause="{$aw_pause}"
                data-random="{$aw_random}"
                data-buttons="{$aw_buttons}"
                data-controls="{$aw_control}"
                data-hover="{$aw_hover}"
            ></div>
            <ul id="aw_slider">
                {foreach from=$slides item=image name=singleimage}
                    <li {if $smarty.foreach.singleimage.index != 0}class="inactive"{/if}>
                    {if ($image.video == 0)}
                        {if $image.url != ''}<a href="{$image.url}" {if $image.target == 1}target="_blank"{/if}>{/if}
                            <img src="{$minicSlider.path.images}{$image.image}" class="slider_image" alt="{if $image.alt}{$image.alt}{/if}"/>
                        {if $image.url != ''}</a>{/if}
                        <div class="aw_slide-text">
                            {if $image.title != ''}<h3>{$image.title}</h3>{/if}
                            {if $image.caption != ''}<div>{$image.caption nofilter}</div>{/if}
                        </div>
                        <div class="showcase-tooltips">
                            {foreach from=$coordinates item=coord}
                                {if ($image.id_lang == $coord.id_lang) && ($image.id_shop == $coord.id_shop) && ($image.id_slide == $coord.id_slide)}                                
                                    <a href="{if $coord.point_type == 'product'}{$link->getProductLink($coord.id_product, $coord.product_link_rewrite)}{else}#{/if}" coords="{$coord.coordinateX*2+13}, {$coord.coordinateY*2+13}" class="showcase-plus-anchor main_bg" style="top:{$coord.coordinateY*2}px; left:{$coord.coordinateX*2}px">
                                        <div class="point_container">
                                            {if $coord.point_type == "product"}
                                            <img src="{$coord.product_image_link}" alt="{$coord.product_name}" />
                                            <span class="aw_pName">{$coord.product_name}</span>
                                            <span class="aw_price price">{$coord.price}</span>
                                            {/if}
                                            {if ($coord.point_type == "text") && ($coord.point_text != NULL)}
                                                <span class="aw_text">{$coord.point_text}</span>
                                            {/if}
                                        </div>
                                    </a>                                
                                {/if}
                            {/foreach}      
                        </div>
                    {else}
                        <iframe class="videoframe" src="//www.youtube.com/embed/{$image.video_url}" frameborder="0" allowfullscreen></iframe>                        
                    {/if}                   
                    </li>
                {/foreach}                
            </ul>
        </div> 
        {/if}
    {/if}