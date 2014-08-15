<script type="text/javascript" src="{{ @theme('js/jquery.js') }}"></script>
<script type="text/javascript">
var WebEditor = {};

jQuery(document).ready(function () {
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
		alert(dir+file);
	};
	
	WebEditor.list('/');
});
</script>

<div class="files">
		<!-- Placeholder -->
</div>