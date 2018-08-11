{block name='section'}
<div class="form-group form">
	<div class="control-pilot">
		{if $section.code == 1}
		<textarea name="{$section.name}" class="ace-editor hidden" data-editor="editor" data-mode="css" data-theme="monokai" rows="30" cols="10">{$section.current}</textarea>
		<pre id="editor" class="ace-editor-area">{$section.current}</pre>
		{else}
		<label class="control-label col-lg-4 text-left">{$section.label}</label>
		<div class="col-lg-8 control-pilot">
			<textarea name="{$section.name}" rows="10" cols="10">{$section.current}</textarea>
		</div>
		{/if}
	</div>
</div>
{if $section.code == 1}
<script>
    var editor = ace.edit("editor");
    var textarea = $('textarea[name="{$section.name}"]');

    editor.setTheme("ace/theme/github");
    editor.session.setMode("ace/mode/css");
	editor.getSession().on('change', function(){
	  textarea.val(editor.getSession().getValue());
	});
</script>
{/if}
{/block}