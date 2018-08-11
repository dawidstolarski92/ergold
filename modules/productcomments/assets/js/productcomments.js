$(function() {

	var src = $('.commens_vars'),
		productcomments_controller_url = src.data('productcomments_controller_url'),
		confirm_report_message = src.data('confirm_report_message'),
		secure_key = src.data('secure_key'),
		productcomments_url_rewrite = src.data('productcomments_url_rewrite'),
		productcomment_added = src.data('productcomment_added'),
		productcomment_added_moderation = src.data('productcomment_added_moderation'),
		productcomment_title = src.data('productcomment_title'),
		productcomment_ok = src.data('productcomment_ok'),
		moderation_active = src.data('moderation_active');


	$('input.star').rating();
	$('.auto-submit-star').rating();

	$('.open-comment-form').fancybox({
		'hideOnContentClick': false
	});

	$('button.usefulness_btn').click(function() {
		var id_product_comment = $(this).data('id-product-comment');
		var is_usefull = $(this).data('is-usefull');
		var parent = $(this).parent();

		$.ajax({
			url: productcomments_controller_url + '?rand=' + new Date().getTime(),
			data: {
				id_product_comment: id_product_comment,
				action: 'comment_is_usefull',
				value: is_usefull
			},
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			success: function(result){
				parent.fadeOut('slow', function() {
					parent.remove();
				});
			}
		});
	});

	$('span.report_btn').click(function() {
		if (confirm(confirm_report_message))
		{
			var idProductComment = $(this).data('id-product-comment');
			var parent = $(this).parent();

			$.ajax({
				url: productcomments_controller_url + '?rand=' + new Date().getTime(),
				data: {
					id_product_comment: idProductComment,
					action: 'report_abuse'
				},
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				success: function(result){
					parent.fadeOut('slow', function() {
						parent.remove();
					});
				}
			});
		}
	});

	$('#submitNewMessage').click(function(e) {
		// Kill default behaviour
		e.preventDefault();

		// Form element
        url_options = '?';
        if (!productcomments_url_rewrite)
            url_options = '&';

		$.ajax({
			url: productcomments_controller_url + url_options + 'action=add_comment&secure_key=' + secure_key + '&rand=' + new Date().getTime(),
			data: $('#id_new_comment_form').serialize(),
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			dataType: "json",
			success: function(data){
				if (data.result)
				{
					$.fancybox.close();
                    var buttons = {};
                    buttons[productcomment_ok] = "productcommentRefreshPage";
                    fancyChooseBox(moderation_active ? productcomment_added_moderation : productcomment_added, productcomment_title, buttons);
				}
				else
				{
					$('#new_comment_form_error ul').html('');
					$.each(data.errors, function(index, value) {
						$('#new_comment_form_error ul').append('<li>'+value+'</li>');
					});
					$('#new_comment_form_error').slideDown('slow');
				}
			}
		});
		return false;
	});
});

function productcommentRefreshPage() {
    window.location.reload();
}
/**
 * Display a MessageBox
 * @param {string} msg
 * @param {string} title (optional)
 */
function fancyMsgBox(msg, title)
{
    if (title) msg = "<h2>" + title + "</h2><p>" + msg + "</p>";
    msg += "<br/><p class=\"submit\" style=\"text-align:right; padding-bottom: 0\"><input class=\"button\" type=\"button\" value=\"OK\" onclick=\"$.fancybox.close();\" /></p>";
	if(!!$.prototype.fancybox)
    	$.fancybox( msg, {'autoDimensions': false, 'autoSize': false, 'width': 500, 'height': 'auto', 'openEffect': 'none', 'closeEffect': 'none'} );
}

/**
 * Display a messageDialog with different buttons including a callback for each one
 * @param {string} question
 * @param {mixed} title Optional title for the dialog box. Send false if you don't want any title
 * @param {object} buttons Associative array containg a list of {buttonCaption: callbackFunctionName, ...}. Use an empty space instead of function name for no callback
 * @param {mixed} otherParams Optional data sent to the callback function
 */
function fancyChooseBox(question, title, buttons, otherParams)
{
    var msg, funcName, action;
	msg = '';
    if (title)
		msg = "<h2>" + title + "</h2><p>" + question + "</p>";
    msg += "<br/><p class=\"submit\" style=\"text-align:right; padding-bottom: 0\">";
    var i = 0;
    for (var caption in buttons) {
        if (!buttons.hasOwnProperty(caption)) continue;
        funcName = buttons[caption];
        if (typeof otherParams == 'undefined') otherParams = 0;
        otherParams = escape(JSON.stringify(otherParams));
        action = funcName ? "$.fancybox.close();window['" + funcName + "'](JSON.parse(unescape('" + otherParams + "')), " + i + ")" : "$.fancybox.close()";
	  msg += '<button type="submit" class="button btn-default button-medium" style="margin-right: 5px;" value="true" onclick="' + action + '" >';
	  msg += '<span>' + caption + '</span></button>'
        i++;
    }
    msg += "</p>";
	if(!!$.prototype.fancybox)
    	$.fancybox(msg, {'autoDimensions': false, 'width': 500, 'height': 'auto', 'openEffect': 'none', 'closeEffect': 'none'});
}