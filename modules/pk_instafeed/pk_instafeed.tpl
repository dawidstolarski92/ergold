{assign var='ilikes' value=''}
{assign var='icomments' value=''}
{assign var='icaption' value=''}
{assign var='ilink' value='{{link}}'}
{assign var='iimage' value='{{image}}'}

{if $pk_ig.PK_INSTA_LIKES}{assign var='ilikes' value='<span class=\'ig-likes ig-icon\'><svg class=\'svgic svgic-eye\'><use xlink:href=\'#si-heart\'></use></svg>{{likes}}</span>'}{/if}
{if $pk_ig.PK_INSTA_COMMENTS}{assign var='icomments' value='<span class=\'ig-comments ig-icon\'><svg class=\'svgic svgic-comment\'><use xlink:href=\'#si-comment\'></use></svg>{{comments}}</span>'}{/if}
{if $pk_ig.PK_INSTA_CAPTION}{assign var='icaption' value='<span class=\'ig-caption ellipsis\'>{{caption}}</span>'}{/if}

{assign var="template" value="<li><div class='ig-indent'><div class='ig-wrapper'><a target='_blank' rel='nofollow' class='ig-link relative oh' href='`$ilink`'><img src='`$iimage`' width='300' height='300' alt='insta-image' /><span class='ig-desc smooth02'>`$icaption``$ilikes``$icomments`</span></a></div></li>"}

<div class="instagram-feed homemodule block ig_{$pk_ig_suffix} {if $pk_ig.PK_INSTA_CAROUSEL == false} instalist{/if}{if $pk_ig.PK_INSTA_BACKGROUND == 1} instabg{else} noinstabg{/if}{if ($pk_ig.PK_INSTA_COLOR == true)} light-color{else} dark-color{/if}" {if $pk_ig.PK_INSTA_BACKGROUND == 1}style='background-image:url({$link->getMediaLink("`$module_dir`$insta_bg")});'{/if}>
	<div class="hidden pk_instafeed"
		data-apicode="{$pk_ig.PK_INSTA_API_CODE}"
		data-apisecret="{$pk_ig.PK_INSTA_API_SECRET}"
		data-at="{$pk_ig.PK_INSTA_AT}"
		data-apicallback="{$pk_ig.PK_INSTA_API_CALLBACK}"
		data-carousel="{$pk_ig.PK_INSTA_CAROUSEL}"
		data-contenttype="{$pk_ig.PK_INSTA_CONTENT_TYPE}"
		data-username="{$pk_ig.PK_INSTA_USERNAME}"
		data-userid="{$pk_ig.PK_INSTA_USERID}"
		data-sortby="{$pk_ig.PK_INSTA_SORTBY}"
		data-number="{$pk_ig.PK_INSTA_NUMBER}"
		data-numbervis="{$pk_ig.PK_INSTA_NUMBER_VIS}"
		data-hashtag="{$pk_ig.PK_INSTA_HASHTAG}"
		data-links="{$pk_ig.PK_INSTA_LINKS}"
		data-back="{$pk_ig.PK_INSTA_BACKGROUND}"
		data-auto="{$pk_ig.PK_INSTA_AUTOSCROLL}"
		data-template="{$template}"
		data-suffix="{$pk_ig_suffix}"> 
	</div> 
	<div class="container page-width oh">
		<div class="instafeed-container">
			<h4 class="module-title"><span>{l s='Instagram Feed' mod='pk_instafeed'}</span></h4>
			<ul id="instafeed_{$pk_ig_suffix}" class="instafeed_ul"></ul>
		</div>
	</div>
</div>