var Dialog = {};
Dialog.show = function (id, okCallback) {
	var dlg = jQuery('#'+id);
	dlg.css('width','400px').css('height','240px').css('margin-left','-200px').css('margin-top','-120px');
	
	// Init an overlay
	var ovl = dlg.parent('.overlay');
	ovl.show();
	
	// Init the OK button
	dlg.find('.ok-btn').click(function() {
		okCallback(dlg);
		ovl.hide()
		dlg.hide();
		return false;
	});
	
	// Init the cancel button
	dlg.find('.cancel-btn').click(function() {
		ovl.hide();
		dlg.hide();
		return false;
	});
	
	dlg.show();
};

jQuery(document).ready(function() {
	jQuery('#file-menu #delete').click(function() {
		if (confirm('Remove "'+jQuery('.file-check').first().val()+'"?'))
		{
			alert('DELETE');
		}
		return false;
	});
	
	jQuery('#file-menu #rename').click(function() {
		var name = prompt('Rename Name:', jQuery('.file-check').first().val());
		if (name !== null && name !== false)
		{
			alert('RENAME '+name);
		}
		return false;
	});
	
	jQuery('#file-menu #upload').click(function() {
		Dialog.show('upload-dialog', function (dlg) {
			jQuery('form#upload-form').submit();
		});
		return false;
	});
	jQuery('#file-menu #mkdir').click(function() {
		var dir = prompt('Directory Name:');
		if (dir !== null && dir !== false)
		{
			API.mkdir(dir);
		}
		return false;
	});
	
	jQuery('.commands #check-all').click(function () {
		jQuery('.file-table input[type="checkbox"].file-check').prop('checked', true);
		return false;
	});
	
	jQuery('.commands #check-none').click(function () {
		jQuery('.file-table input[type="checkbox"].file-check').prop('checked', false);
		return false;
	});
});
