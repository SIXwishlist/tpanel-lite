<script type="text/javascript" src="{{ @theme('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ @theme('js/filemanager.js') }}"></script>
<h4>/{{@dir}}</h4>
<ul class="button-group" id="file-menu">
	<li><a id="rename">Rename</a></li>
	<li><a id="delete">Delete</a></li>
	<li><a id="move">Move</a></li>
	<li><a id="copy">Copy</a></li>
	<li><a id="upload">Upload</a></li>
	<li><a id="mkdir">New Directory</a></li>
</ul>
<ul class="file-grid">
{if $dir !== ''}
	<li data-type="up">
		<a href="{{ @url('/files/'.$dir.'/..') }}"><img src="{{ @theme('icons/folder.png') }}" class="icon" /><div class="title">..</div></a>
	</li>
{end}
{foreach $files -> $file}
	{if $file['info']->isDir() }
		<li data-type="dir" data-filename="{{ @file['name'] }}"><a href="{{ @url('/files/'.$file['name']) }}"><img src="{{ @theme('icons/folder.png') }}" class="icon" /><div class="title">{{@file['name']}}</div></a></li>
	{else}
		<li data-type="file" data-filename="{{ @file['name'] }}"><img src="{{ @theme('icons/'.$file['icon'].'.png') }}" class="icon" /><div class="title">{{@file['name']}}</div></li>
	{end}
{end}
</ul>

<div class="stats">
	Usage: {{@usage}}
	Free: {{@free}}
	<div style="border:1px solid #000">
		<div style="background:-moz-linear-gradient(top,#DBF7FF,#81B6C4);height:27px;width:{{@usagePercent}}%;"> </div>
	</div>
</div>
