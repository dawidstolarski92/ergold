{if $pkts.footer_bottom_social == 1}
<div id="socialnetworks">
<ul class="socialnetworks_menu dib">
{foreach from=$soc item=s key=name}
<li class="{$name} dib"><a rel="nofollow" class="smooth02 icon-{$name}" title="{$name}" target="_blank" href="{$s}"><svg class="dib svgic svgic-{$name}"><use xlink:href="#si-{$name}"></use></svg></a></li>
{/foreach}
</ul>
</div>
{/if}
{if $pkts.footer_bottom_pcards == 1}
<div id="payment-icons">
<ul class="dib">
{foreach from=$pay item=s key=name}
<li class="{$name}"><img src="{$module_dir}/{$name}.png" alt="" /></li>
{/foreach}
</ul>
</div>
{/if}