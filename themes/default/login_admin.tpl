<h2>Admin Login</h2>
{ display(message) }
	<div class="alert danger">
		{{ @message }}
	</div>
{ end }
<p>Please enter your username and password below to login:</p>
<form method="post" class="form width-1-2 container-center">
<div class="row">
	<label>Username:</label>
	<div class="field">{{ username }}</div>
</div>
<div class="row">
	<label>Password:</label>
	<div class="field">{{ password }}</div>
</div>
<div class="row">
	<div class="field">{{ submit }}</div>
</div>
</form>
