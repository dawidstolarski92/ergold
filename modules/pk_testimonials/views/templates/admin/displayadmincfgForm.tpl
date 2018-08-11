<form action="{$requestUri}" method="post" id="testimonialCfg" enctype="multipart/form-data" id="module_form" class="defaultForm form-horizontal">
  <div class="panel" id="fieldset_0"> 
  <div class="panel-heading"><i class="icon-cogs"></i> {l s='Configuration' d='Modules.Testimonials.Admin'}</div>                
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='Use ReCaptcha Anti Spam' d='Modules.Testimonials.Admin'}</label>
        <div class="col-lg-6">
    				<input type="radio" name="reCaptcha" id="recaptcha_on" value="1" {if $recaptcha eq 1}checked="yes" {/if}/>
    				<label class="t" for="recaptcha_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="radio" name="reCaptcha" id="recaptcha_off" value="0" {if $recaptcha eq 0}checked="yes" {/if} />
    				<label class="t" for="recaptcha_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />
          </label>
        </div>  
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3"></label>
        <strong class="col-lg-6">{l s='Create a reCAPTCHA key here: ' mod='pk_testimonials'}<a href="https://www.google.com/recaptcha">https://www.google.com/recaptcha</a></strong>
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='ReCaptcha Public Key' mod='pk_testimonials'}</label>
        <div class="col-lg-6">
          <input type="text" name="recaptchaPub" value="{$recaptchaPub}" />
        </div>
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='ReCaptcha Private Key' mod='pk_testimonials'}</label>
        <div class="col-lg-6">
          <input type="text" name="recaptchaPriv" value="{$recaptchaPriv}" />
        </div>
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='# of testimonials per page' mod='pk_testimonials'}</label>
        <div class="col-lg-6">
          <input type="text" name="perPage" value="{$recaptchaPerpage}" />
        </div>
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='# of testimonials in column' mod='pk_testimonials'}</label>
        <div class="col-lg-6">
          <input type="text" name="perBlock" value="{$recaptchaPerBlock}" />
        </div>
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='Show background Image' mod='pk_testimonials'}</label>
        <div class="col-lg-6">
          <input type="radio" name="displayImage" id="displayImage_on" value="1" {if $displayImage eq 1}checked="yes" {/if}/>
          <label class="t" for="displayImage_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="displayImage" id="displayImage_off" value="0" {if $displayImage eq 0}checked="yes" {/if} />
          <label class="t" for="displayImage_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
        </div>
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='Upload Background Image' mod='pk_testimonials'}</label>
        <div class="col-lg-6">
          <input type="file" name="testimonialsbg" id="testimonialsbg">
        </div>
      </div>
    </div>
    <div class="form-wrapper">
      <div class="form-group">
        <label class="control-label col-lg-3">{l s='Recommended dimension is' mod='pk_testimonials'}: 1920x800px</label>
        <div class="col-lg-6">
          <img src="{$testimonial_bg}" alt="" style="width:300px; height: auto;" />
        </div>
      </div>
    </div>
    <div class="panel-footer">
        <button type="submit" value="1" id="submitConfig" name="submitConfig" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='pk_testimonials'}</button>
    </div>
</div>
</form>
<div class="defaultForm form-horizontal">
  <div class="panel" id="fieldset_0"> 
    <div class="panel-heading"><i class="icon-cogs"></i> {l s='Backup Testimonials' mod='pk_testimonials'}</div>                
    <p>{l s='Use this to create backup of your testimonials in a CSV File. This will create a file called backup.csv in this /modules/pk_testimonials directory' mod='pk_testimonials'}</p>
    <form id="backupform" action="{$requestUri}" method="post" name="backupform" >
      {if $backupfileExists >0}
        <span style="font-weight:bold"><a href="{$base_dir}modules/pk_testimonials/backup.csv" >{l s='Download Existing Backup' mod='pk_testimonials'}</a></span>
      {/if}
      <div class="panel-footer">
          <button type="submit" value="1" id="Backup" name="Backup" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='BackUp' mod='pk_testimonials'}</button>
      </div>
    </form>
  </div>
</div>