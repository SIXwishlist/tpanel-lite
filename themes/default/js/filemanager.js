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

jQuery(document).ready(function() {
	jQuery('#file-menu #delete').click(function() {
		if (confirm('Remove "'+jQuery('.file-check:checked').first().val()+'"?'))
		{
			jQuery.post(FileManager.baseURI+'/api/file/delete', {'file':FileManager.dir+'/'+jQuery('.file-check:checked').first().val()}, function (data) {
				if (data.success === true)
				{
					window.location = FileManager.URI;
				}
				else
				{
					alert('ERROR: '+data.error);
				}
			});
		}
		return false;
	});
	
	jQuery('#file-menu #rename').click(function() {
		var name = prompt('Rename Name:', jQuery('.file-check:checked').first().val());
		if (name !== null && name !== false)
		{
			jQuery.post(FileManager.baseURI+'/api/file/rename', {'src':FileManager.dir+'/'+jQuery('.file-check:checked').first().val(), 'dest':FileManager.dir+'/'+name}, function (data) {
				if (data.success === true)
				{
					window.location = FileManager.URI;
				}
				else
				{
					alert('ERROR: '+data.error);
				}
			});
		}
		return false;
	});
	
	jQuery('#file-menu #upload').click(function() {
		Dialog.show('upload-dialog', function (dlg) {
			var data = new FormData();
			data.append('upload', jQuery('#upload-file').get(0).files[0]);
			jQuery.ajax({
				url: FileManager.baseURI+'/api/file/upload?dir='+escape(FileManager.dir),
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				type: 'POST',
				success: function(data){
					if (data.success === true)
					{
						window.location = FileManager.URI;
					}
					else
					{
						alert('ERROR: '+data.error);
					}
				}
			});
		});
		return false;
	});
	jQuery('#file-menu #mkdir').click(function() {
		var dir = prompt('Directory Name:');
		if (dir !== null && dir !== false)
		{
			jQuery.post(FileManager.baseURI+'/api/directory/new', {'dir':FileManager.dir+'/'+dir}, function (data) {
				if (data.success === true)
				{
					window.location = FileManager.URI;
				}
				else
				{
					alert('ERROR: '+data.error);
				}
			});
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
