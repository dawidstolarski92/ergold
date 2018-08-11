{function name="menu" nodes=[] depth=0}
  {strip}
    {if $nodes|count}
      <ul data-depth="{$depth}">
        {foreach from=$nodes item=node}
          <li class="{$node.type}{if $node.current} current{/if}">
            <a href="{$node.url}" {if $node.open_in_new_window} target="_blank" {/if}>{$node.label}</a>
            {if $node.image_urls|count}
              <div class="menu-images-container">
                {foreach from=$node.image_urls item=image_url}
                  <img src="{$image_url}">
                {/foreach}
              </div>
            {/if}
            {menu nodes=$node.children depth=$node.depth}
          </li>
        {/foreach}
      </ul>
    {/if}
  {/strip}
{/function}

<div class="ps_mainmenu">
  <h4 class="module-title"><span>{l s='Categories' d='Modules.Specials.Shop'}</span><svg class="svgic svgic-menu"><use xlink:href="#si-menu"></use></svg></h4>
  <div class="module-body">
    <div class="menu">
        {menu nodes=$menu.children}
    </div>
  </div>
</div>
