<div class="pk_popup_container" style="{if $pk_ppp.PK_POPUP_WIDTH}width:{$pk_ppp.PK_POPUP_WIDTH}px;{/if}{if $pk_ppp.PK_POPUP_HEIGHT}height:{$pk_ppp.PK_POPUP_HEIGHT}px;{/if}{if $pk_ppp.PK_POPUP_BG == 1}background-image: url({$urls.base_url}modules/pk_popup/{$popup_bg});{/if}">
    <!--noindex-->
    <svg style="display:none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <defs>
    <symbol id="si-close" viewBox="0 0 11 11">
    <path d="M0.225,10.774c0.296,0.297,0.777,0.297,1.073,0L5.5,6.572l4.201,4.202c0.297,0.297,0.776,0.297,1.073,0 s0.297-0.776,0-1.073L6.573,5.5l4.201-4.202c0.297-0.295,0.297-0.777,0-1.074c-0.297-0.295-0.776-0.295-1.073,0L5.5,4.427 L1.298,0.224c-0.296-0.295-0.777-0.295-1.073,0c-0.296,0.297-0.296,0.779,0,1.074L4.427,5.5L0.225,9.701 C-0.071,9.998-0.071,10.478,0.225,10.774z"/>
    </symbol>
    <symbol id="si-run" viewBox="0 0 19 19">
    <path d="M13.422,14.866l5.424-5.022c0.098-0.09,0.156-0.216,0.156-0.343c0-0.13-0.055-0.253-0.156-0.343 l-5.424-5.022c-0.207-0.191-0.539-0.191-0.746,0s-0.207,0.499,0,0.69l4.527,4.19H0.524c-0.293,0-0.526,0.218-0.526,0.488 s0.233,0.487,0.526,0.487h16.679l-4.527,4.191c-0.102,0.094-0.156,0.221-0.156,0.343c0,0.123,0.051,0.249,0.156,0.344 C12.883,15.054,13.219,15.054,13.422,14.866z"/>
    </symbol>
    </defs>
    </svg><!--/noindex-->
    <div class="hidden pk_popup" 
        data-width="{$pk_ppp.PK_POPUP_WIDTH}"
        data-height="{$pk_ppp.PK_POPUP_HEIGHT}"
        data-path="{$pk_ppp.PK_POPUP_PATH}"></div>
    <div class="pk_popup_flex">
        <div id="newsletter_block_popup" class="block">
            <div class="block_content">
                {if isset($msg) && $msg}<p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>{/if}
                <form action="{$link->getPageLink('index')|escape:'html'}" method="post">
                    
                    <div class="popup_text">
                        <h3>{l s='Be the first to now' mod='pk_popup'}</h3>
                        <div>{l s='Subscribe for the latest news &amp; get 15% off your first order.' mod='pk_popup'}</div>
                    </div>
                    
                    {if $pk_ppp.PK_POPUP_NEWSLETTER == 1}
                    <div class="relative-input relative">
                    <input class="inputNew" id="newsletter-input-popup" type="email" name="email" required="" value="" placeholder="{l s='your@email.com' mod='pk_popup'}" />
                    <button class="send-reqest"><svg class="svgic svgic-run"><use xlink:href="#si-run"></use></svg></button>
                    </div>
                    {/if}
                    <div class="send-response"></div>
                </form>
            </div>
        </div>
    </div>
</div>