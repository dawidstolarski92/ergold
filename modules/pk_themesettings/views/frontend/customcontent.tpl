{*
 * 2007-2013 PrestaShop 
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
  * @category   Promokit modules
 * @package    Promokit Module
 * @author    Marek Mnishek <marek@promokit.eu>
 * @site    
 * @copyright  Copyright 2013 Promokit Co. (http://promokit.eu) 
 *}
{if (isset($pk_custom_tab) && ($pk_custom_tab != ""))}
<section data-section="12" class="page-product-box">
	<div class="rte">
	{$pk_custom_tab}
	</div>
</section>
{/if}
{if (isset($pk_video_id) && ($pk_video_id != ""))}
<section data-section="13" class="page-product-box">
	<div class="rte">
		<!--[if !IE]> -->
		<div class="videoWrapper"><iframe id="ytplayer" type="text/html" width="420" height="270" src="https://www.youtube.com/embed/{$pk_video_id}" frameborder="0"></iframe></div>
		<!-- <![endif]-->
		<!--[if gt IE 8]>
		<div class="videoWrapper"><iframe id="ytplayer" type="text/html" width="420" height="270" src="http://www.youtube.com/embed/{$pk_video_id}" frameborder="0"></iframe></div>
		<![endif]-->
		<!--[if lte IE 8]>
		<object id="ytplayer" width="420" height="270"><param name="movie" value="https://www.youtube-nocookie.com/v/{$pk_video_id}?hl=en_US&amp;version=3&amp;rel=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="https://www.youtube-nocookie.com/v/{$pk_video_id}?hl=en_US&amp;version=3&amp;rel=0" type="application/x-shockwave-flash" width="420" height="270" allowscriptaccess="always" allowfullscreen="true"></embed></object>		    
		<![endif]-->  
		</div>
</section>
{/if}