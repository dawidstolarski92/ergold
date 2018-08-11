
{if isset($fps)}
<script type="text/javascript">
     var fps_loop={$fps.loop|intval};
     var fps_speed={$fps.speed|intval};
     var fps_pause={$fps.pause|intval};
     var fps_nav={$fps.nav|intval};
     var fps_navpos={$fps.navpos|intval};
     var fps_infinite={$fps.infinite|intval};
     var titles = [{if isset($fps.titles)}{foreach from=$fps.titles item=title}'{$title.title}',{/foreach}{/if}];
</script>
{/if}