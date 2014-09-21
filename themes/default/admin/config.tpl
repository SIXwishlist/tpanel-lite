{display(success)}
	{if $success}
		Configuration saved successfully
	{else}
		Configuration has errors
	{end}
{end}
{{form->open()}}
<fieldset><legend>Web Host Configuration</legend>
	<div class="row">
		<label>Web Host Name</label>
		{{form->text('web_host_name')}}
	</div>
	<div class="row">
		<label>Free Web Space</label>
		{{form->text('free_space')}}
	</div>
	<div class="row">
		<label>Administrative Email</label>
		{{form->text('admin_email')}}
	</div>
	<div class="row">
		<label>Theme</label>
		{{form->text('theme')}}
	</div>
	<div class="row">
		<label>User Directory</label>
		{{form->text('user_dir')}}
	</div>
</fieldset>
<fieldset><legend>Database Configuration</legend>
	<div class="row">
		<label>Database Server</label>
		{{form->text('server')}}
	</div>
	<div class="row">
		<label>Username</label>
		{{form->text('username')}}
	</div>
	<div class="row">
		<label>Password</label>
		{{form->password('password')}}
	</div>
	<div class="row">
		<label>Database Name</label>
		{{form->text('database')}}
	</div>
	<div class="row">
		<label>Table Prefix</label>
		{{form->text('prefix')}}
	</div>
</fieldset>
<div class="row">
	{{form->submit('funcbtn1', ['value' => 'Save configuration'])}}
</div>
{{form->close()}}
