<?php
/**
* 2016 Revolution Slider
*
*  @author    SmatDataSoft <support@smartdatasoft.com>
*  @copyright 2016 SmatDataSoft
*  @license   private
*  @version   5.1.3
*  International Registered Trademark & Property of SmatDataSoft
*/

// preview slide
?>

	<div id="dialog_preview" class="dialog_preview" title="Preview Slide" style="display:none;">

		<iframe id="frame_preview" name="frame_preview" style="<?php echo RevGlobalObject::getVar('iframeStyle')?>"></iframe>

	</div>

	

	<form id="form_preview_slide" name="form_preview_slide" action="" target="frame_preview" method="post">

		<input type="hidden" name="client_action" value="preview_slide">

		<input type="hidden" name="data" value="" id="preview_slide_data">

		<input type="hidden" id="preview_slide_nonce" name="nonce" value="">

	</form>

