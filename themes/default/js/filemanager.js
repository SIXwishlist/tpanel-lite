var FileManager = {dir:null,selectFunc:null};

FileManager.init = function (dir) {
	FileManager.dir = dir;
};

FileManager.select = function (callback) {
	FileManager.selectFunc = callback;
	FileManager.selectFiles(true);
	return false;
};

FileManager.selectFiles = function(on) {
	if (on===true) {
		jQuery('.file-grid > li, .file-grid > li:not(.up) > a').click(function() {
			jQuery(this).toggleClass('selected');
			if (FileManager.selectFunc !== null)
			{
				FileManager.selectFunc(jQuery(this).data('filename'));
				FileManager.selectFiles(false);
				jQuery(this).toggleClass('selected');
			}
			return false;
		});
	} else {
		jQuery('.file-grid li').off();
	}
};

FileManager.renameFile = function (filename) {
	prompt('Filename:', filename);
};

jQuery(document).ready(function() {
	jQuery('#file-menu #rename').click(function() {
		jQuery(this).parent('li').toggleClass('selected');
		return FileManager.select(FileManager.renameFile);
	});
	jQuery('#file-menu #delete').click(function() {
		alert('delete');
	});
	jQuery('#file-menu #upload').click(function() {
		alert('upload');
	});
	jQuery('#file-menu #mkdir').click(function() {
		alert('mkdir');
	});
});
