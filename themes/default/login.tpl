<h2>Login</h2>
{ display(message) }
	<div style="color:#220000;background:#ffdddd;text-align:center">
		{{ @message }}
	</div>
{ end }
<p>Please enter your username and password below to login:</p>
<form method="post">
<label>Username:</label>
{{ username }}
<label>Password:</label>
{{ password }}
{{ submit }}
</form>
