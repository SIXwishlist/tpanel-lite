{ display(errors) }
	<div class="padding-10">
	<div class="alert danger">
	{{ @errors }}
	</div>
	</div>
{ end }
{ display(warning) }
	<div class="padding-10">
	<div class="alert danger">
	WARNING: Your account will be removed when you press &quot;Remove Account&quot; again. This message is a warning to prevent your account from being erased in the event that &quot;Remove Account&quot; was made in error.
	</div>
	</div>
{ end }

<form method="post">
	<div class="row">
		<label>Username:</label>
		<div class="field">{{@config['username']}}</div>
	</div>
	
	<div class="row">
		<label>Full Name:</label>
		<div class="field">{{form->text('full_name')}}</div>
	</div>
	<div class="row">
		<label>Email:</label>
		<div class="field">{{form->text('email')}}</div>
	</div>
	<div class="row">
		<label>Web space:</label>
		<div class="field">{{@config['webspace']}} MB</div>
	</div>
	<div class="row">
		{{form->submit('funcbtn1', ['value' => 'Save Settings'])}}
		{{form->submit('funcbtn2', ['value' => 'Remove Account'])}}
	</div>
</form>
