<!doctype html>
<html>
  <head>
    <title>tPanel Lite - {{ @title }}</title>
    <link rel="stylesheet" href="{{ @theme('css/style.css') }}" />
  </head>
  <body>
    <div class="page">
	  <div class="header">
	    <a href="{{ @url('/admin/') }}"><h1><span class="blue">tPanel</span> <span class="green">Lite</span></h1></a>
	  </div>
	  <div class="content">
	    { if $this->hasFlash() }
			<div class="alert success">
				{{ @flash: }}
			</div>
		{ end }
	    {{ content: }}
	  </div>
	</div>
	<div class="footer">
	  &copy; Copyright 2014 Data Components Software
	</div>
  </body>
</html>
