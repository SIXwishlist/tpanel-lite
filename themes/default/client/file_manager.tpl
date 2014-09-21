<script type="text/javascript" src="{{ @theme('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ @theme('js/dialog.js') }}"></script>
<script type="text/javascript" src="{{ @theme('js/filemanager.js') }}"></script>
<script type="text/javascript">
	var FileManager = {};
	FileManager.dir = '{{ @dir }}';
	FileManager.baseURI = '{{ @url('/') }}';
	FileManager.URI = '{{ @url('files/'.$dir) }}';
</script>
<h4>/{{@dir}}</h4>
<ul class="button-group" id="file-menu">
	<li><a id="rename">Rename</a></li>
	<li><a id="delete">Delete</a></li>
	<li><a id="upload">Upload</a></li>
	<li><a id="mkdir">New Directory</a></li>
</ul>
<div class="overlay">
	<div class="dialog" id="upload-dialog">
		<h2>Upload a file</h2>
		<p>Please select the file below and press &quot;Upload&quot; to continue.</p>
		<div class="row">
			<label>Filename:</label>
			<div class="field">
				<input type="file" name="upload" id="upload-file" />
			</div>
		</div>
		<div class="row">
			<button class="ok-btn">Upload</button>
			<button class="cancel-btn">Cancel</button>
		</div>
	</div>
</div>
<table class="file-table">
	<tr>
		<th>&nbsp;</th>
		<th>Filename</th>
		<th>Size</th>
		<th>Last Modified</th>
	</tr>
{if $dir !== ''}
	<tr>
		<td>&nbsp;</td>
		<td>
			<a href="{{ @url('/files/'.$dir.'/..') }}"><img src="{{ @theme('icons/icon_dir.png') }}" class="icon" />..</a>
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
{end}
{foreach $files -> $file}
	{if $file['info']->isDir() }
		<tr>
			<td><input type="checkbox" class="file-check" name="file[]" value="{{ @file['name'] }}" /></td>
			<td><a href="{{ @url('/files/'.$dir.'/'.$file['name']) }}"><img src="{{ @theme('icons/icon_dir.png') }}" class="icon" />{{@file['name']}}</a></td>
			<td>-</td>
			<td>{{ @date('Y-m-d H:i:s', $file['info']->getMTime()) }}</td>
		</tr>
	{else}
		<tr>
			<td><input type="checkbox" class="file-check" name="file[]" value="{{ @file['name'] }}" /></td>
			<td><a href="{{ @url('/files/'.$dir.'/'.$file['name']) }}"><img src="{{ @theme('icons/icon_file.png') }}" class="icon" />{{@file['name']}}</a></td>
			<td>{{ @filesize($file['size']) }}</td>
			<td>{{ @date('Y-m-d H:i:s', $file['info']->getMTime()) }}</td>
		</tr>
	{end}
{end}
</table>
<div class="commands">
	Check: <a href="#" id="check-all">All</a> | <a href="#" id="check-none">None</a>
</div>
<div class="stats">
	Usage: {{@usage}}
	Free: {{@free}}
	<div class="progress-bar">
		<div class="progress blue" style="width:{{@usagePercent}}%;"> </div>
	</div>
</div>