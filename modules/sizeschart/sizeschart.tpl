
<div id="short_description_blockm">

<table width="100%" border="0" id="sizes">
  <tr>
    <td align="left"><a href="{$module_dir}sizeschart2.php" class="{if $psversion > '1.5.3.0'}i{/if}frame"><img src="{$module_dir}icon.jpg" /></a></td>
      <td align="left"><div class = 'btn btn-primary sizeChart'><span><a href="{$module_dir}sizeschart2.php" class="{if $psversion > '1.5.3.0'}i{/if}frame">{l s='Click to open sizes chart' mod='sizeschart'}</a></span></div>          
</td>
  </tr>
</table>
</div>

 {if $psversion < "1.5.3.0"}
   <script type="text/javascript">$('a.iframe').fancybox();</script>
{else}

<script type="text/javascript">
		$(document).ready(function() {
            console.log('size chart works');
	        $("a.iframe").fancybox({
	            'type' : 'iframe',
	            'width':980,
	            'height':600
	        });
	    });
	</script>
    {/if}