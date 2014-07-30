{ display(success) }
	{{ @success }}
{ end }
<table border="1">
{foreach $files -> $f}
<tr><td>{{@f['name']}}</td><td>{{@f['size']}}</td></tr>
{end}
</table>
<form method="post">
<input type="submit" name="funcbtn1" value="Backup" />
<input type="submit" name="funcbtn2" value="Restore" />
</form>
<div class="stats">
	Last update: {{@date}}
</div>
