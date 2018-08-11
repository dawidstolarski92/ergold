{if count($errors)}
	<div class="bootstrap">
		<div class="error alert alert-danger">
			{if count($errors) == 1}
				{$errors[0]|escape:'htmlall':'UTF-8'}
			{else}
				{$errors|count} {l s='errors' mod='psinpost'}
				<br/>
				<ol>
					{foreach $errors as $error}
						<li>{$error|escape:'htmlall':'UTF-8'}</li>
					{/foreach}
				</ol>
			{/if}
		</div>
	</div>
{/if}