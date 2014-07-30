{{form->open()}}
<div class="row">
	<label>Username</label>
	{{form->text('username')}}
</div>
<div class="row">
	<label>Password</label>
	{{form->password('password_1')}}
</div>
<div class="row">
	<label>Password (again)</label>
	{{form->password('password_2')}}
</div>
<div class="row">
	<label>Email</label>
	{{form->text('email')}}
</div>
<div class="row">
	{{form->submit('funcbtn1', ['value' => 'Register'])}}
</div>
{{form->close()}}
