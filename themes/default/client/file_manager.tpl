<h4>/{{@dir}}</h4>
<ul class="file-grid">
{if $dir !== ''}
	<li>
		<a href="{{ @url('/files/'.$dir.'/..') }}"><img src="{{ @theme('icons/folder.png') }}" class="icon" /><div class="title">..</div></a>
	</li>
{end}
{foreach $files -> $file}
	{if $file['info']->isDir() }
		<li><a href="{{ @url('/files/'.$file['name']) }}"><img src="{{ @theme('icons/folder.png') }}" class="icon" /><div class="title">{{@file['name']}}</div></a></li>
	{else}
		<li><img src="{{ @theme('icons/'.$file['icon'].'.png') }}" class="icon" /><div class="title">{{@file['name']}}</div></li>
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
