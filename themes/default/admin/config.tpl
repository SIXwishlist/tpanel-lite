{display(success)}
	{if $success}
		<div class="alert success">Configuration saved successfully</div>
	{else}
		<div class="alert danger">Configuration has errors</div>
	{end}
{end}
{{form->open()}}
<fieldset><legend>Web Host Configuration</legend>
	<div class="row">
		<label>Web Host Name</label>
		<div class="field">{{form->text('web_host_name')}}</div>
	</div>
	<div class="row">
		<label>Free Web Space</label>
		<div class="field">{{form->text('free_space')}}</div>
	</div>
	<div class="row">
		<label>Administrative Email</label>
		<div class="field">{{form->text('admin_email')}}</div>
	</div>
	<div class="row">
		<label>Theme</label>
		<div class="field">{{form->text('theme')}}</div>
	</div>
	<div class="row">
		<label>User Directory</label>
		<div class="field">{{form->text('user_dir')}}</div>
	</div>
	<div class="row">
		<label>User URL</label>
		<div class="field">{{form->text('user_url')}}</div>
	</div>
</fieldset>
<fieldset><legend>Database Configuration</legend>
	<div class="row">
		<label>Database Server</label>
		<div class="field">{{form->text('server')}}</div>
	</div>
	<div class="row">
		<label>Username</label>
		<div class="field">{{form->text('username')}}</div>
	</div>
	<div class="row">
		<label>Password</label>
		<div class="field">{{form->password('password')}}</div>
	</div>
	<div class="row">
		<label>Database Name</label>
		<div class="field">{{form->text('database')}}</div>
	</div>
	<div class="row">
		<label>Table Prefix</label>
		<div class="field">{{form->text('prefix')}}</div>
	</div>
</fieldset>
<div class="row">
	{{form->submit('funcbtn1', ['value' => 'Save configuration'])}}
</div>
{{form->close()}}
