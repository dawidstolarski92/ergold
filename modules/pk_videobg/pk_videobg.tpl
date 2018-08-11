<div id="pk_videobg" class="homemodule load-animate" data-videoid="{$opts.pk_videobg_yt_code}">
	<div class="page_width">
		<div class="videobg-indent">
			<h4 class="main_color lmromandemi">{$opts.pk_videobg_title}</h4>
			<h6 class="lmromandemi">{$opts.pk_videobg_subtitle}</h4>
			<div class="videobg_text">{$opts.pk_videobg_text}</div>
			<a class="button dib" href="{$opts.pk_videobg_url}">{l s='Take a look' d='Modules.VideoBg.Shop'}</a>
		</div>
	</div>
	<div id="videobgWrapper" {if $opts.pk_videobg_local == true}class="localtrue"{/if}>
		{if $opts.pk_videobg_local == false}
		<!--<iframe id="videobg" class="ytplayer" width="100%" height="400" src="{$opts.pk_videobg_link}" muted="muted"></iframe>-->
		<div id="player"></div>
		{else}
		<video autoplay="autoplay" loop="loop" controls="controls" tabindex="0" muted>
		 <source src="{$opts.pk_videobg_link}" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
		  Video tag not supported. Download the video <a href="movie.webm">here</a>.
		 <video>
		{/if}
	</div>
</div>