<form action="{$requestUri}" method="post" name="form1" id="module_form" class="defaultForm form-horizontal">
	<div class="panel" id="fieldset_0"> 
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Manage Testimonials' d='Modules.Testimonials.Admin'}</div>                
			<table id="box-table-a">
				<th>{l s='Select' mod='pk_testimonials'}</th> <!-- Select Column Header-->
				<th>{l s='Status' mod='pk_testimonials'}</th> <!-- Status Column Header-->
				<th>{l s='Name' mod='pk_testimonials'}</th> <!-- Name Column Header-->
				<th>{l s='Email' mod='pk_testimonials'}</th> <!-- Name Column Header-->
				<th>{l s='Date' mod='pk_testimonials'}</th> <!-- Date Column Header-->
				<th style="width:50%">{l s='Testimonial' mod='pk_testimonials'}</th> <!-- Testimonial  Column Header-->
				{if isset($testimonials)}			  
					{foreach from=$testimonials item=nr}				  
					{if $nr}
						<tr>
							<td> <!--Check Box -->
								<INPUT class="testimonialselect" TYPE=checkbox VALUE="{$nr.testimonial_id}" NAME="moderate[]">
							</td>
							<td> <!-- Status Column -->
								<span class="{(($nr.status|lower == "disabled") ? 'disabled' : 'enabled')}">{$nr.status}</span>
							</td>
							<td> <!-- Name Column -->
								{$nr.testimonial_submitter_name}
							</td>
							<td> <!-- Name Column -->
								{$nr.testimonial_submitter_email}
							</td>
							<td> <!-- Date Column -->
								{$nr.date_added|strip_tags|date_format:"%H:%M:%S on %B %e, %Y"}
							</td>
							<td> <!-- Testimonial Column -->
								<textarea style="width:100%; height:100px" name="testimonial_main_message_{$nr.testimonial_id}" > {$nr.testimonial_main_message} </textarea>
							</td>
						</tr>
						{/if}
					{/foreach}
				{else}
					<tr><td colspan="6">{l s='No Testimonials Yet' mod='pk_testimonials'}</td></tr>
				{/if}
			</table>
			<div class="panel-footer">
				<button type="submit" name="Disable" class="btn btn-default pull-right"><i class="process-icon-toggle-off"></i> {l s='Disable' mod='pk_testimonials'}</button>
				<button type="submit" name="Enable" class="btn btn-default pull-right"><i class="process-icon-toggle-on"></i> {l s='Enable' mod='pk_testimonials'}</button>
				<button type="submit" name="Delete" class="btn btn-default pull-right" onClick="return confirmSubmit('{l s='Okay to Delete this Testimonial(s)?' mod='pk_testimonials'}')"><i class="process-icon-delete"></i> {l s='Delete' mod='pk_testimonials'}</button>
				<button type="submit" name="Update" class="btn btn-default pull-right"><i class="process-icon-update"></i> {l s='Update' mod='pk_testimonials'}</button>
			</div>
		</div>
	</div>
</form>
