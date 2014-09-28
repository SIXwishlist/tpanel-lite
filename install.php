<?php

// TODO: Includes

use Base\Setup;

$setup = new Setup();

$setup->before(function($setup) {
	// Check directory permissions
	if (!$setup->dirIsWritable('.'))
	{
		$setup->error('Main directory is not writable');
	}
});

$setup->after(function($setup) {
	// Delete install.php
	$setup->dissolve();
});

$setup->validate(function($setup, $data) {
	// TODO: Validate datasets
});

$setup->beforeSubmit(function($setup) {
	// TODO: Check database settings
});

$setup->submit(function($setup) {
	// TODO: Set file contents
	$setup->fileSet('tpanel-conf', '');
	$setup->fileSet('db-conf', '');
	
	// TODO: Create database
	
	// Create directories
	$setup->dirCreate('users', 0644);
	$setup->dirCreate('data', 0644);
	$setup->dirCreate('data/backups', 0644);
	$setup->dirCreate('data/databases', 0644);
	$setup->dirCreate('data/emails', 0644);
	
	$setup->fileCreate('.htaccess', 'main-htaccess');
	$setup->fileCreate('data/.htaccess', 'data-htaccess');
	$setup->fileCreate('users/.htaccess', 'users-htaccess');
	$setup->fileCreate('data/tpanel.conf', 'tpanel-conf');
	$setup->fileCreate('data/databases/main.conf', 'db-conf');
	$setup->fileCreate('data/emails/activate.tpl', 'email');
	
	// Create user account
	$user = new User();
	// username, password, email, full_name
	$userData = Arr::filter($setup->post(), ['username', 'password', 'email', 'full_name']);
	$userData['webspace'] = $setup->postValue('free_space');
	$userData['user_level'] = 2;
	if ($user->createFromAdmin($userData))
	{
		$setup->success('Admin account created successfully');
	}
	else
	{
		$setup->error('Cannot create admin account - please check your directory permissions');
	}
});

$setup->init(function($setup) {
	$defaultPath = Path::local('users');
	$defaultUrl = Path::web('users', $setup->getRequest());
	
	// Form fields
	$setup->beginGroup('General Setup');
		$setup->textField('Web Host Name', 'web_host_name');
		$setup->textField('Default Web Space in MB (0 = unlimited)', 'free_space', 50);
		$setup->textField('Admin Email Address', 'admin_email');
	$setup->endGroup();
	$setup->beginGroup('Path Settings');
		$setup->textField('User Path', 'user_dir', $defaultPath);
		$setup->textField('User URL', 'user_url', $defaultUrl);
	$setup->endGroup();
	$setup->beginGroup('Database Settings');
		$setup->textField('Server', 'server', 'localhost');
		$setup->textField('Username', 'username');
		$setup->passwordField('Password', 'password');
		$setup->textField('Database', 'database');
		$setup->textField('Prefix', 'prefix');
	$setup->endGroup();
});

$htfile1 = <<<EOD
Options +FollowSymLinks
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteRule ^index\.php$ - [L]
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^(.+)$ index.php/$1
</IfModule>
EOD;

$htfile2 = <<<EOD
deny from all
EOD;

$htfile3 = <<<EOD
RewriteEngine Off
SetHandler none
Options -ExecCGI
php_flag engine off
RemoveHandler .cgi .inc .jsp .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo .shtml
EOD;

$emailTpl = <<<EOD
Hello {{@full_name}},

Thank you for registering for free web hosting.  Below is the activation link:

{{base_url}}/activate/{{username}}/{{activation_code}}

EOD;

$setup->fileSet('main-htaccess', $htfile1);
$setup->fileSet('data-htaccess', $htfile2);
$setup->fileSet('users-htaccess', $htfile3);
$setup->fileSet('email', $emailTpl);

$setup->run();