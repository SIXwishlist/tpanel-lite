{display(errors)}
	<div class="alert danger">
		{{@errors[0]}}
	</div>
{end}
<p>Please complete the registration form below:</p>
<div class="width-1-2 container-center">
	{{form->open()}}
	<div class="row">
		<label>Username</label>
		<div class="field">{{form->text('username')}}</div>
	</div>
	<div class="row">
		<label>Password</label>
		<div class="field">{{form->password('password_1')}}</div>
	</div>
	<div class="row">
		<label>Password (again)</label>
		<div class="field">{{form->password('password_2')}}</div>
	</div>
	<div class="row">
		<label>Full Name</label>
		<div class="field">{{form->text('full_name')}}</div>
	</div>
	<div class="row">
		<label>Email</label>
		<div class="field">{{form->text('email')}}</div>
	</div>
	<div class="row">
		<div class="field">{{form->submit('funcbtn1', ['value' => 'Register'])}}</div>
	</div>
	{{form->close()}}
</div>