<script type="text/javascript">
    $(document).ready(function() {

        // Sortable
        $("ul.languages").sortable({
            opacity: 0.6,
            cursor: 'move',
            handle: '.orderer',
            update: function(event, ui) {
                var list = $(this);
                var number;
                var response;
                $.getJSON(
                    "{$slider.sortUrl}", 
                    {ldelim}slides: $(this).sortable("serialize"){rdelim}, 
                    function(response){
                        if(response.success == "true"){
                            showResponse($("#sortable"), "{l s='Saved successfull' mod='pk_awshowcaseslider'}", 'conf');
                            var i = 1;
                            list.children("li").each(function(){
                                number = i;
                                if(i < 10){ 
                                    number = "0"+i; 
                                }
                                $(this).find(".order").text(number);
                                i++;
                            });
                        }else{
                            showResponse($("#sortable"), "{l s='Something went wrong, please refresh the page and try again' mod='pk_awshowcaseslider'}", 'error'); 
                        }
                  }
                );
            }
        });         
    });
</script>
		

<div id="minic">
    {if $error}
        {include file="{$minic.admin_tpl_path}messages.tpl" id="main" text=$error class='error'}
    {/if}
    {if $confirmation}
        {include file="{$minic.admin_tpl_path}messages.tpl" id="main" text=$confirmation class='success'}
    {/if}
    <div class="header">
        <div id="navigation">
            <a href="#new" id="new-button" class="minic-open">{l s='Add New' mod='pk_awshowcaseslider'}</a>
            <a href="#options" id="options-button" class="minic-open">{l s='Options' mod='pk_awshowcaseslider'}</a>
            <div class="clearfix"></div>
        </div>
    </div>
    <!-- Options -->
    {include file="{$minic.admin_tpl_path}options.tpl"}
    <!-- New -->
    {include file="{$minic.admin_tpl_path}new.tpl"}
    <!-- Slides -->
    {include file="{$minic.admin_tpl_path}slides.tpl"}
</div>