<h4>/{{@dir}}</h4>
<table width="100%">
<tr><th>Name</th><th>Size</th></tr>
{if $dir !== ''}
	<tr><td><a href="{{ @url('/files/'.$dir.'/..') }}">..</a></td><td>-</td></tr>
{end}
{foreach $files -> $file}
	{if $file['info']->isDir() }
	<tr><td><a href="{{ @url('/files/'.$file['name']) }}">{{@file['name']}}</a></td><td>{{@file['size']}}</td></tr>
	{else}
	<tr><td>{{@file['name']}}</td><td>{{@file['size']}}</td></tr>
	{end}
{end}
</table>

<div class="stats">
	Usage: {{@usage}}
	Free: {{@free}}
	<div style="border:1px solid #000">
		<div style="background:-moz-linear-gradient(top,#DBF7FF,#81B6C4);height:27px;width:{{@usagePercent}}%;"> </div>
	</div>
</div>
