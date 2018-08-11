<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<title>{if !empty($fileds_list[1])}{$fileds_list[1]|escape:'html':'UTF-8'}{/if}</title>

	{* Includes *}
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

	<style>
		{* @todo *}
		.container * {
			font-family: "Open Sans", sans-serif;
			color: #444444;
		}

		.container img {
    		margin: 0 auto;
			/* Bootsrtap .img-responsive */
			max-width: 100%;
			height: auto;
			display:block;
		}
	</style>
</head>
<body>
	{* Header *}
	{* @todo *}

	{* Content *}
	{if !empty($fileds_list[24])}
		{$fileds_list[24]}
	{elseif !empty($fileds_list[341]) && count($fileds_list[341]|@json_decode)}
		<div class="container">
		{$json=$fileds_list[341]|@json_decode}
		{foreach from=$json->sections item=section key=keys}
			<div class="row">
			{foreach from=$section->items item=item key=keyi}
				<div class="col-md-{if count($section->items) > 1}6{else}12{/if}">
					{if $item->type == 'TEXT'}
						{$item->content}
					{else}
						{* base_64 *}
						<img src="data:png;base64,{$item->url}" />
					{/if}
				</div>
			{/foreach}
			</div>
		{/foreach}
		</div>
	{else}
		<h1 class="text-center">{l s='Description is empty - add some sections and try again.' mod='allegro'}</h1>
	{/if}
</body>
</html>