function confirmSubmit(confirmMsg) {

  var agree=confirm(confirmMsg);

	if (agree) return true;

	return false;

}