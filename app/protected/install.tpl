<html>
<head><title>tPanel Lite - Installation</title>
<link rel="stylesheet" href="{{ @url('themes/default/css/style.css') }}" />
</head>
<body>
	<div class="page">
	  <div class="header">
	    <a href="{{ @url('/install.php') }}"><h1><span class="blue">tPanel</span> <span class="green">Lite</span> <small>Installation</small></h1></a>
	  </div>
	  <div class="content">
		{display(error)}
		<div class="alert danger">
		  {{@error}}
		</div>
		{end}
		{display(success)}
		<div class="alert success">
		  {{@success}}
		</div>
		{end}
		<p>To install tPanel Lite, complete the setup form below and press &quot;Install&quot; to complete the installation.</p>
		<form method="post">
		{{content}}
		<div class="row">
			<input type="submit" value="Install" />
		</div>
		</form>
	</div>
	<div class="footer">
	  &copy; Copyright 2014 Data Components Software
	</div>
</body>
</html>