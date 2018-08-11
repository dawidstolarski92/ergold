{*
* 2007-2015 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="tab-pane fade in active row" id="extra-99">
	<input class="commens_vars" type="hidden"
	data-productcomments_controller_url="{$productcomments_controller_url}"
	data-confirm_report_message="{l s='Are you sure that you want to report this comment?' mod='productcomments'}"
	data-secure_key="{$secure_key}"
	data-productcomments_url_rewrite="{$productcomments_url_rewriting_activated}"
	data-productcomment_added="{l s='Your comment has been added!' mod='productcomments'}"
	data-productcomment_added_moderation="{l s='Your comment has been submitted and will be available once approved by a moderator.' mod='productcomments'}"
	data-productcomment_title="{l s='New comment' mod='productcomments'}"
	data-productcomment_ok="{l s='Ok' mod='productcomments'}"
	data-moderation_active="{$moderation_active}"
	>
	<div id="product_comments" class="col-xs-12 col-sm-6 product_comments">
		<div class="section-indent">
		{if $comments}
			<div class="section-title flex-container">
			<h3>{$comments|count} {if ($comments|count > 1)}{l s='Reviews' mod='productcomments'}{else}{l s='Review' mod='productcomments'}{/if}</h3>
			{hook h='displayProductRating' product=$id_product_comment_form}
			</div>
			{foreach from=$comments item=comment}
				{if $comment.content}
				<div class="comment flex-container">
					<div class="customer-icon">
						<svg class="svgic svgic-account"><use xlink:href="#si-account"></use></svg>
					</div>
					<div class="comment_details">
						<div class="title-block-wrapper flex-container">
							<strong class="title_block">{$comment.title}</strong>
							<div class="flex-container"><svg class="svgic svgic-pk-star"><use xlink:href="#si-pk-star"></use></svg> <span>{$comment.grade}/5</span></div>
						</div>
						<p>{$comment.content|nl2br}</p>
						<ul class="hidden">
							{if $comment.total_advice > 0}
								<li>{l s='%1$d out of %2$d people found this review useful.' sprintf=[$comment.total_useful,$comment.total_advice] mod='productcomments'}</li>
							{/if}
							{if $customer.is_logged}
								{if !$comment.customer_advice}
								<li>{l s='Was this comment useful to you?' mod='productcomments'}<button class="usefulness_btn" data-is-usefull="1" data-id-product-comment="{$comment.id_product_comment}">{l s='yes' mod='productcomments'}</button><button class="usefulness_btn" data-is-usefull="0" data-id-product-comment="{$comment.id_product_comment}">{l s='no' mod='productcomments'}</button></li>
								{/if}
								{if !$comment.customer_report}
								<li><span class="report_btn" data-id-product-comment="{$comment.id_product_comment}">{l s='Report abuse' mod='productcomments'}</span></li>
								{/if}
							{/if}
						</ul>
						<div class="comment_author_infos">
							<span>{$comment.customer_name}</span>, <span>{dateFormat date=$comment.date_add full=0}</span>
						</div>
					</div>
				</div>
				{/if}
			{/foreach}
		{else}
			{if (!$too_early AND ($customer.is_logged OR $allow_guests))}
				<p>{l s='Be the first to write your review' mod='productcomments'} !</p>
			{else}
				<p>{l s='No customer reviews for the moment.' mod='productcomments'}</p>
			{/if}
		{/if}
		</div>
	</div>

	{if isset($product) && $product}
	<div id="new_comment_form" class="col-xs-12 col-sm-6 product_comments">
		<form id="id_new_comment_form" action="#">

			<div class="section-title"><h3>{l s='Write your review' mod='productcomments'}</h3></div>

			<div class="new_comment_form_content">
				<div id="new_comment_form_error" class="error" style="display:none;">
					<ul></ul>
				</div>
				{if $criterions|@count > 0}
					<div id="criterions_list" class="">
					{foreach from=$criterions item='criterion'}
						<div class="criterions_list_wrapper flex-container">
						<label>Ocena</label>
						<div class="star_content flex-container">
							{assign var=stars_num value=0}
							{while $stars_num++ < 5}
							  <input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="{$stars_num}" checked />
							{/while}
						</div>
						</div>
					{/foreach}
					</div>
				{/if}

				{if $allow_guests == true && !$customer.is_logged}
				<div class="new_comment_form_content__inputs flex-container">
				<input id="commentCustomerName" name="customer_name" type="text" value="" placeholder="{l s='Your Name' mod='productcomments'}" required />
				</div>
				{/if}
				<div class="new_comment_form_content__inputs flex-container">
				<input id="comment_title" name="title" type="text" value="" placeholder="{l s='Title' mod='productcomments'}" required />
				</div>

				<textarea name="content" placeholder="{l s='Your review' mod='productcomments'}" required></textarea>

				<div id="new_comment_form_footer">
					<input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}' />
					<button id="submitNewMessage" name="submitMessage" type="submit" class="btn">{l s='Send' mod='productcomments'}</button>
				</div>

			</div>
		</form><!-- /end new_comment_form_content -->
	</div>
	{/if}

</div>
