var Dialog = {};
Dialog.show = function (id, okCallback) {
	var dlg = jQuery('#'+id);
	dlg.css('width','400px').css('margin-left','-200px');
	
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
	
	dlg.show().css('margin-top','-'+(dlg.height()/2)+'px');
};

var Form = {};
Form.init = function (id, data) {
	for (var k in data) {
		var item = jQuery('#'+id+' #'+k);
		if (item.length > 0)
		{
			var tag = item.get(0).nodeName.toLowerCase();
			if (tag == 'input' || tag == 'select')
			{
				item.val(data[k]);
			}
			else
			{
				item.html(data[k]);
			}
		}
	}
};