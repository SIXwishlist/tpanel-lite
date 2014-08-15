<script type="text/javascript" src="{{ @theme('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ @theme('js/nicEdit.js') }}"></script>
<script type="text/javascript">
var WebEditor = {};

jQuery(document).ready(function () {
	WebEditor.content = null;
	WebEditor.editor = null;
	WebEditor.file = null;
	
	WebEditor.list = function (dir) {
		jQuery.post('{{ @url('api/files') }}', {dir:dir}, function (data) {
			jQuery('.files').children().remove();
		
			var tbl = jQuery('<table border="1"></table>');
			var files = data.files;
			if (dir != '/' && dir != '') {
				var row = jQuery('<tr><td><a href="#"><img src="{{ @theme('icons/webdir.png') }}" />..</a></td></tr>');
				row.find('a').click(function (dir) {
					return function () {
						WebEditor.list(dir+'/');
						return false;
					};
				}(dir.substring(0, dir.substring(0, dir.length-1).lastIndexOf('/'))));
				tbl.append(row);
			}
			for(var j=0;j<files.length;j++){
				// Append row for each file
				if (files[j].isdir)
				{
					// Display directory row
					var row = jQuery('<tr><td><a href="#"><img src="{{ @theme('icons/webdir.png') }}" />'+files[j].name+'</a></td></tr>');
					var onNavigate = function (dir, file) {
						return function () {
							WebEditor.list(dir+file+'/');
							return false;
						};
					};
					row.find('a').click(onNavigate(dir, files[j].name));
					tbl.append(row);
				}
				else
				{
					// Display file row
					if (files[j].name.substring(files[j].name.length-4, files[j].name.length).toLowerCase() == 'html')
					{
						var row = jQuery('<tr><td><a href="#"><img src="{{ @theme('icons/webfile.png') }}" />'+files[j].name+'</a></td></tr>');
						row.find('a').click(function (dir, file) {
							return function () {
								WebEditor.editFile(dir, file);
								return false;
							};
						}(dir, files[j].name));
						tbl.append(row);
					}
				}
			}
			jQuery('.files').append(tbl);
		});
	};
	
	WebEditor.editFile = function (dir, file) {
		if (dir.substring(dir.length-1,dir.length) != '/')
		{
			dir += '/';
		}
		
		jQuery.post('{{ @url('api/file/contents') }}', {'file': dir+file}, function (data) {
			if (WebEditor.editor != null)
			{
				WebEditor.closeEditor();
			}
			WebEditor.content = data.contents;
			WebEditor.file = dir+file;
			WebEditor.initEditor();
		});
	};
	
	WebEditor.closeEditor = function () {
		WebEditor.editor = null;
		WebEditor.content = null;
		jQuery('.html-editor-frame').remove();
	};
	
	WebEditor.initEditor = function () {
		var htmlEditorDiv = jQuery('<div class="html-editor-frame"><textarea id="html-editor" style="width:100%;height:350px"></textarea><button id="save">Save</button><button id="cancel">Cancel</button></div>');
		htmlEditorDiv.find('textarea').val(WebEditor.content);
		
		htmlEditorDiv.find('#save').click(function () {
			var rq = {'file':WebEditor.file, 'contents':nicEditors.findEditor('html-editor').getContent()};
			jQuery.post('{{ @url('api/file/save') }}', rq, function (data) {
				if (data.success)
				{
					alert('Successfully saved');
					WebEditor.closeEditor();
				}
				else
				{
					alert('Error saving file');
				}
			});
		});
		
		htmlEditorDiv.find('#cancel').click(function () {
			WebEditor.closeEditor();
		});
		
		jQuery('.files').append(htmlEditorDiv);
		
		WebEditor.editor = new nicEditor({iconsPath: '{{ @theme('icons/nicEditorIcons.gif') }}', fullPanel: true}).panelInstance('html-editor');
	};
	
	// Initialize
	WebEditor.list('/');
});
</script>

<div class="files">
		<!-- Placeholder -->
</div>