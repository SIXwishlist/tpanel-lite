jQuery(document).ready(function() {
	FileManager.fileList = null;
	
	jQuery('#file-menu #delete').click(function() {
		var files = jQuery('.file-check:checked').map(function (num, item) {
			return jQuery(item).val();
		});
		if (files.size() > 0 && confirm(files.size() > 1 ? 'Remove selected files?' : 'Remove "'+files[0]+'"?'))
		{
			FileManager.fileList = jQuery.makeArray(files);
			FileManager.deleteFile();
		}
		return false;
	});
	
	FileManager.deleteFile = function () {
		jQuery.post(FileManager.baseURI+'/api/file/delete', {'file':FileManager.dir+'/'+FileManager.fileList.pop()}, function (data) {
			if (!data.success === true)
			{
				alert('ERROR: '+data.error);
			}
			else if (FileManager.fileList.length > 0)
			{
				FileManager.deleteFile();
			}
			else
			{
				alert('File(s) removed successfully');
				window.location = FileManager.URI;
			}
		});
	};
	
	jQuery('#file-menu #rename').click(function() {
		if (jQuery('.file-check:checked').size() < 1) return false;
		
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
						console.log(data);
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
