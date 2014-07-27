{ display(errors) }
	{{ @errors }}
{ end }
{display(warning)}
<h1>{{@warning}}</h1>
{end}

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
		{{form->submit('funcbtn1', ['value' => 'Save settings'])}}
	</div>
</form>
