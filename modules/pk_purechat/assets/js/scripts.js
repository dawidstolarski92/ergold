$(document).ready(function(){

	(function () { 
		var purechat = $('.pk_purechat').data('options'),
			done = false,
			script = document.createElement('script');
			
		script.async = true;
		script.type = 'text/javascript';
		script.src = 'https://app.purechat.com/VisitorWidget/WidgetScript';
		document.getElementsByTagName('HEAD').item(0).appendChild(script);
		script.onreadystatechange = script.onload = function (e) {
			if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
				var w = new PCWidget({ c: purechat, f: true });
				done = true;
			}
		};
	})();

});