$(document).ready(function() {
	var yes = $("label[for='FPS_NAVPOS_on']");
	var no = $("label[for='FPS_NAVPOS_off']");
	$(yes).text("left");
	$(no).text("right");

	var yes = $("label[for='text_align_on']");
	var no = $("label[for='text_align_off']");
	$(yes).text("left");
	$(no).text("right");
});