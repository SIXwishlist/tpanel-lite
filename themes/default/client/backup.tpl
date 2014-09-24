{ display(success1) }
	<div class="padding-10">
		<div class="alert success">
		Backup saved successfully
		</div>
	</div>
{ end }
{ display(success2) }
	<div class="padding-10">
		<div class="alert success">
		Backup restored successfully
		</div>
	</div>
{ end }

<div class="stats">
	Last backup: {{@date}}
</div>
<div class="padding-10">
<table class="backup-files">
<tr><th>File/Directory</th><th>Size</th></tr>
{foreach $files -> $f}
<tr><td>{{@f['name']}}</td><td>{{@filesize($f['size'])}}</td></tr>
{end}
</table>
</div>
<form method="post">
<div class="row">
	<input type="submit" name="funcbtn1" value="Backup" />
	<input type="submit" name="funcbtn2" value="Restore" />
</div>
</form>
