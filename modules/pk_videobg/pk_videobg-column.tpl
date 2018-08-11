<div id="pk_videobg" class="pk_videobg_column">
	<h4 class="module-title"><span>{l s='Featured Video' d='Modules.Pk_Videobg.Shop'}</span></h4>
	<div id="videobgWrapper" {if $opts.pk_videobg_local == true}class="localtrue"{/if}>
		{if $opts.pk_videobg_local == false}
		<iframe width="100%" height="200" src="{$opts.pk_videobg_link}"></iframe>
		{else}
		<video autoplay="autoplay" loop="loop" controls="controls" tabindex="0" muted>
		 <source src="{$opts.pk_videobg_link}" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
		</video>
		{/if}
	</div>
</div>