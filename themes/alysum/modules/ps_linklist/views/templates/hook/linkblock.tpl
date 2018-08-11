{foreach $linkBlocks as $linkBlock}
<div class="link-block">
  <h4>{$linkBlock.title}</h4>
  <ul>
    {foreach $linkBlock.links as $link}
      <li>
        <a id="{$link.id}-{$linkBlock.id}" class="{$link.class}" href="{$link.url}" title="{$link.description}">{$link.title}</a>
      </li>
    {/foreach}
  </ul>
</div>
{/foreach}