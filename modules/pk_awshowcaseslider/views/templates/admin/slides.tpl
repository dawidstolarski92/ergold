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
                            <span class="title">{if $slide.title}{$slide.title}{else}{l s='Untitled slide' mod='pk_awshowcaseslider'}{/if}</span>
                            <span class="{if $slide.active == 1}active{else}deactivated{/if}"></span>
                            <span class="arrow"></span>
                        </div>
                        <div id="{$iso}_{$slide.id_order}" class="slide-body minic-container">
                            <form method="post" action="{$slider.postAction}" enctype="multipart/form-data">
                                <div class="minic-top">
                                    <h3>{l s='Editing slide' mod='pk_awshowcaseslider'}
                                    </h3>
                                    <div class="title input-holder">
                                        <input type="text" name="title" class="tooltip2" placeholder="{l s='The title of the slide'}" value="{$slide.title}" title="{l s='This will be the title on the slide.' mod='pk_awshowcaseslider'}" /> 
                                    </div>
                                    <div class="switch-holder">
                                        <div class="switch large {if $slide.active}active{else}inactive{/if}">
                                            <input type="radio" class="" name="isActive"  value="{$slide.active}" checked="true" />
                                        </div>
                                    </div>
                                    <a href="#{$iso}_{$slide.id_order}" class="minic-close">x</a>
                                </div>
                                
                                <div class="minic-content">
                                    <div class="image-holder input-holder" data-idimg="{$slide.id_slide}" data-idshop="{$slide.id_shop}" data-idlang="{$slide.id_lang}">
                                        <label>{l s='Show video in this slide' mod='pk_awshowcaseslider'}</label>
                                        <div class="switch-holder">
                                            <div class="video_swt small {if $slide.video}active{else}inactive{/if}">
                                                <input type="radio" class="" name="video" value="{$slide.video}" checked="true" />
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                        <br/><br/>
                                        <div class="video-section {if $slide.video}display{else}hidden{/if}">
                                            <div class="url input-holder">
                                                <label>{l s='Youtube Video Code' mod='pk_awshowcaseslider'}: </label>
                                                <input type="text" name="video_url" class="tooltip2" placeholder="{l s='Link to video'}" value="{$slide.video_url}" title="{l s='ex. from url youtube.com/watch?v=wkTgr1zuRQ4 you need this wkTgr1zuRQ4' mod='pk_awshowcaseslider'}" />        
                                            </div>
                                        </div>
                                        <div class="image-section {if $slide.video}hidden{else}display{/if}">
                                            <label>{l s='Image' mod='pk_awshowcaseslider'}</label>
                                            <div class="imgWrapper">                                            
                                                <img src="{$module_dir}uploads/{$slide.image}" class="mainImg" />
                                                {assign var="counter" value=1}{assign var="counter2" value=1}
                                                {foreach from=$slider.coordinates.$iso item=coords name=coords}
                                                {if ($coords.id_slide == $slide.id_slide) AND ($coords.id_lang == $slide.id_lang) AND ($coords.id_shop == $slide.id_shop)}
                                                <div class="point_label" data-pointid="{$coords.id_coord}" style="top:{$coords.coordinateY}px; left:{$coords.coordinateX}px;">{$counter++}</div>
                                                {/if}
                                                {/foreach}
                                            </div>                                        
                                            <input type="file" name="newImage" class="file"/>
                                            <div class="coordinates-holder">
                                                {assign var="counter" value=1}
                                                {foreach from=$slider.coordinates.$iso item=coords name=coords}
                                                {if ($coords.id_slide == $slide.id_slide) AND ($coords.id_lang == $slide.id_lang) AND ($coords.id_shop == $slide.id_shop)}                                                
                                                    <div class="points" data-pointid="{$coords.id_coord}">
                                                        <div class="manage"><span class='num'>{$counter++}</span><span class="dot">.</span><input {if ($coords.point_type == "text")}checked="checked"{/if} type='radio' class='tooltip_type' name='t_{$coords.id_coord}' id='text' val='text' /><label for="text">Text</label><input {if ($coords.point_type == "product")}checked="checked"{/if} class='tooltip_type' val='product' type='radio' name='t_{$coords.id_coord}' /><label for="product">Product</label>
                                                        </div>                                                    
                                                        <span class="remove-point">&times;</span>
                                                        
                                                        <textarea data-pid='{$coords.id_coord}' class='point_text{if ($coords.point_type == "product") OR ($coords.point_type == NULL)} hidden{/if}'>{$coords.point_text}</textarea>
                                                        <a href='#' class='text-submit point_text{if ($coords.point_type == "product") OR ($coords.point_type == NULL)} hidden{/if}' onclick='return false'>Save</a>
                                                        <div class='productContainer{if ($coords.point_type == "text") OR ($coords.point_type == NULL)} hidden{/if}'>
                                                            <input type='text' data-pid='{$coords.id_coord}' value='{$coords.product_name}' class='product_autocomplete' placeholder='Start to type the name of product' />
                                                            <img src="{$coords.product_image_link}" class="prodImg" />
                                                        </div>
                                                        
                                                    </div>                                               
                                                {/if}
                                                {/foreach}                                       
                                            </div>
                                        </div>
                                    </div>
                                    <div class="url img-info input-holder {if $slide.video}hidden{else}display{/if}">
                                      	<label>{l s='Url' mod='pk_awshowcaseslider'}: </label>
                                      	<input type="text" name="url" class="tooltip2" placeholder="{l s='Link of the slide'}" value="{$slide.url}" title="{l s='ex. http://myshop.com/promotions' mod='pk_awshowcaseslider'}" />  
                                        <span>{l s='Blank target' mod='pk_awshowcaseslider'}: </span>
                                        <input type="checkbox" name="target" class="tooltip2" value="1" {if $slide.target == 1}checked="true"{/if} title="{l s='Check this if you want to open the link in new window.' mod='pk_awshowcaseslider'}" />         
                                    </div>
                                    <div class="alt img-info input-holder {if $slide.video}hidden{else}display{/if}">
                                        <label>{l s='Image alt' mod='pk_awshowcaseslider'}: </label>
                                        <input type="text" name="alt" class="tooltip2" placeholder="{l s='An alternate text for the image'}" value="{$slide.alt}" title="{l s='The image alt, alternate text for the image' mod='pk_awshowcaseslider'}" />
                                    </div>
                                    <div class="caption img-info input-holder {if $slide.video}hidden{else}display{/if}"> 
                                      	<label>{l s='Caption' mod='pk_awshowcaseslider'}: </label>
                                    	<textarea type="text" name="caption" class="tooltip2" cols="40" rows="6" title="{l s='Be carefull, too long text isnt good and HTML is not allowed.'}" placeholder="{l s='The slide text' mod='pk_awshowcaseslider'}">{$slide.caption}</textarea>
                                    </div>
                                </div>  
                                <div class="minic-bottom">
                                    <input type="hidden" name="slideId" value="{$slide.id_slide}" />
                                    <input type="hidden" name="orderId" value="{$slide.id_order}" />
                                    <input type="hidden" name="slideIso" value="{$slide.lang_iso}" />
                                    <input type="hidden" name="oldImage" value="{$slide.image}" />    
                                    <input type="submit" name="deleteSlide" value="{l s='Delete' mod='pk_awshowcaseslider'}" id="delete-slide" class="button-large red disabled" />   
                                    <input type="submit" name="editSlide" value="{l s='Update' mod='pk_awshowcaseslider'}" id="update-slide" class="button-large green" />       
                                    <a href="#{$iso}_{$slide.id_order}" class="button-large grey minic-close">{l s='Close' mod='pk_awshowcaseslider'}</a>
                                </div>    
                            </form>
                        </div>  
                    </li>                 
              {/foreach}    
          </ul>               
      {/foreach}
    </div>  
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.slides-holder li').each(function( index, value ) { // add suggestion when there are no points of interest
        var points = $(value).find('.point_label');
        var pointsContainer = $(value).find('.imgWrapper');
        if ($(points).size() == 0) {
            $(pointsContainer).append('<div class="doit"><i>&larr;</i><span>Click to the image to add a point of interest</span></div>');
        }
    });
    $(".imgWrapper").click(function(e){ // add new point of interest
        var obj = $(this);
        var d = new Date();
        var imgID = obj.closest('.image-holder').data("idimg");
        var idshop = obj.closest('.image-holder').data("idshop");
        var idlang = obj.closest('.image-holder').data("idlang"); 
        var randID = d.getTime();
        var parentOffset = $(this).offset(); 
        var relX = parseInt(e.pageX - parentOffset.left-8);
        var relY = parseInt(e.pageY - parentOffset.top-8);   
        var pointsNumber = obj.find('.point_label').size();
        obj.find('.doit').remove();
       $.ajax({
            type: 'POST',
            url: '{$slider.rootpath}coordinates.php',
            data: 'action=add&id_slide='+imgID+'&idcoord='+randID+'&coordX='+relX+'&coordY='+relY+'&idlang='+idlang+'&idshop='+idshop,
            success: function(result){
              if (result == '0')
              {
                console.log('no data');
              } else {
                obj.append("<div class='point_label' data-pointid='"+randID+"' style='top:"+relY+"px; left:"+relX+"px;'>"+(pointsNumber+1)+"</div>")
                obj.closest('.image-holder').find(".coordinates-holder").append("<div class='points' data-pointid='"+randID+"'><div class='manage'><span class='num'>"+(pointsNumber+1)+"</span><span class='dot'>.</span><input type='radio' class='tooltip_type' name='t_"+randID+"' val='text' id='text' /><label for='text'>Text</label><input class='tooltip_type' val='product' type='radio' name='t_"+randID+"' /><label for='product'>Product</label></div><span class='remove-point'>&times;</span><textarea data-pid='"+randID+"' class='point_text hidden'></textarea><a href='#' class='text-submit point_text hidden' onclick='return false'>Save</a><div class='productContainer hidden'><input type='text' data-pid='"+randID+"' value='' class='product_autocomplete' placeholder='Start to type the name of product' /></div></div>");             
              }
            }
        });
    });
    $(".remove-point").live('click', function(e){ // remove point of interest
        var obj = $(this);
        var imgID = obj.closest('.image-holder').data("idimg");
        var idshop = obj.closest('.image-holder').data("idshop");
        var idlang = obj.closest('.image-holder').data("idlang");
        var removepointid = obj.parent().data("pointid");
        var removepointnum = obj.parent().find('.num').text();
        obj.closest('.coordinates-holder').find('.points').each(function( index, value ) {
            if (removepointnum < (index+1)) {
                var num = $(this).find('.num').text();
                $(this).find('.num').text(num-1);
            }
        });
        obj.closest('.image-holder').find(".imgWrapper").find(".point_label").each(function( index, value ) {
            if (removepointnum < (index+1)) {
                var num = $(this).text();
                $(this).text(num-1);
            }
        });

        $.ajax({
            type: 'POST',
            url: '{$slider.rootpath}coordinates.php',
            data: 'action=remove&id_slide='+imgID+'&idcoord='+removepointid+'&idlang='+idlang+'&idshop='+idshop,
            success: function(result){
              if (result == '0')
              {
                console.log('no data');
              } else { 
                obj.closest('.image-holder').find('[data-pointid="'+removepointid+'"]').remove(); 
              }
            }
        });
    });
    
    $(".coordinates-holder").live("click", function(e){ // add product to point of interest
        var obj = $(this);        
        var imgID = obj.closest('.image-holder').data("idimg");
        var idshop = obj.closest('.image-holder').data("idshop");
        var idlang = obj.closest('.image-holder').data("idlang");          
        $(this).find('.product_autocomplete').autocomplete(
            'ajax_products_list.php', {
                minChars: 3,
                autoFill: true,
                max:20,
                matchContains: true,
                mustMatch:true,
                scroll:false,
                cacheLength:0,
                extraParams: {
                    excludeIds: -1
                },
                formatItem: function(item) {
                    return item[1]+' - '+item[0];
                }
            }).result(function(event, data, formatted) {                
                if (data == null)
                return false;                
                var productId = data[1];
                var productName = data[0]; 
                var idcoord = $(this).data("pid");                                       
                $.ajax({
                    type: 'POST',
                    url: '{$slider.rootpath}coordinates.php',
                    data: 'action=addProduct&id_slide='+imgID+'&idprod='+productId+'&idlang='+idlang+'&idshop='+idshop+'&idcoord='+idcoord,
                    success: function(result){
                      if (result == '0')
                      {
                        console.log('no data');
                      } else {                 
                        var product = JSON.parse(result);                                
                        $('.coordinates-holder').find("[data-pointid='" + idcoord + "']").find('.prodImg').remove();
                        $('.coordinates-holder').find("[data-pointid='" + idcoord + "']").find('.productContainer').append('<img src='+product.url+' alt=\'\' class="prodImg" />');
                        //obj.closest('.image-holder').find('[data-pointid="'+removepointid+'"]').remove(); 
                      }
                    }
                });
            });            
    });    
    $(".tooltip_type").live('click', function(){ // switcher between "text" and "product"
        if ($(this).attr("val") == "text") {
            $(this).closest('.points').find(".point_text").removeClass('hidden');     
            $(this).closest('.points').find('.productContainer').addClass('hidden');
        }
        if ($(this).attr("val") == "product") {
            $(this).closest('.points').find(".productContainer").removeClass('hidden');     
            $(this).closest('.points').find('.point_text').addClass('hidden');
        }
    });
    $(".text-submit").live('click', function(){ // save text
        var obj = $(this);        
        var imgID = obj.closest('.image-holder').data("idimg");
        var idshop = obj.closest('.image-holder').data("idshop");
        var idlang = obj.closest('.image-holder').data("idlang"); 
        var idcoord = obj.closest('.points').data("pointid");
        var text = obj.parent().find(".point_text").val();
         $.ajax({
            type: 'POST',
            url: '{$slider.rootpath}coordinates.php',
            data: 'action=addText&id_slide='+imgID+'&idlang='+idlang+'&idshop='+idshop+'&idcoord='+idcoord+'&text='+text,
            success: function(result){
              if (result == '0')
              {
                console.log('no data');
              } else { 
                obj.parent().find('.point_text').unbind('change');
                obj.text("Saved").css({ "color": "#fff", 'background-color': '#aaa' })
              }
            }
        });
    });   
    $("textarea.point_text").live("keyup", function(event, ui) { // button changer
        $(this).parent().find('.text-submit').text("Save").css({ "color": "#222", 'background-color': '#ccc' });
    });

});
</script>