var Dialog = {};
Dialog.show = function (id, okCallback) {
	var dlg = jQuery('#'+id);
	dlg.css('width','400px').css('height','240px').css('margin-left','-200px').css('margin-top','-120px');
	
	// Init an overlay
	var ovl = dlg.parent('.overlay');
	ovl.show();
	
	// Init the OK button
	dlg.find('.ok-btn').off().click(function() {
		okCallback(dlg);
		ovl.hide()
		dlg.hide();
		return false;
	});
	
	// Init the cancel button
	dlg.find('.cancel-btn').off().click(function() {
		ovl.hide();
		dlg.hide();
		return false;
	});
	
	dlg.show();
};