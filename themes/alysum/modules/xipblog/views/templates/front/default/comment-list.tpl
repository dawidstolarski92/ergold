{if ($xipblog_commets|count > 0)}
<div class="comments_area" id="comments">
    <h4 class="comments_title module-title">
    	<span>{$xipblog_commets|count}&nbsp;{if ($xipblog_commets|count == 1)}{l s='comment' mod='xipblog'}{else}{l s='comments' mod='xipblog'}{/if}</span>
    </h4>
    <ol class="comment_list">
		{foreach from=$xipblog_commets item=xipblog_commet}
        <li class="comment" id="comment_{$xipblog_commet.id_xip_comments}">
            <article class="comment_body">
				<div class="comment_author vcard">
				    <svg class="svgic"><use xlink:href="#si-account"></use></svg>
				</div>
				<div class="comment_content">
					<div class="comment_meta_title">
						{$xipblog_commet.subject}
					</div>
					<div class="comment_content_bottom">
						{$xipblog_commet.content}
					</div>
					<div class="comment_meta">
					    <div class="comment_meta_author">
					    	{$xipblog_commet.name},
					    </div>
					    <div class="comment_meta_date">
					    	<time datetime="2016-03-07T04:33:23+00:00">
					    	    {$xipblog_commet.created|date_format:"%e %B, %Y"}
					    	</time>
					    </div>
					    <div class="reply">
					        <a class="comment-reply-link" href="#" onclick='return addComment.moveForm( "div-comment-3", "3", "respond", "38" )' rel="nofollow">
					            Reply
					        </a>
					    </div>
					</div>
				</div>
            </article>
        </li>
		{/foreach}
    </ol>
</div>
{/if}