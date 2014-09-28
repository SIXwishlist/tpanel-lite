<?php

// TODO: Includes

use Base\Setup;
use Base\Template;

$setup = new Setup();

$setup->before(function($setup) {
	// Check directory permissions
	if (!$setup->dirIsWritable('.'))
	{
		$setup->error('Main directory is not writable');
	}
});

$setup->afterSubmit(function($setup) {
	// Delete install.php
	$setup->dissolve();
});

$setup->validate(function($setup, $data) {
	// TODO: Validate datasets
	$setup->validate->
});

$setup->beforeSubmit(function($setup) {
	// Check database settings
	$db = [
			'server' => $setup->postValue('db_server'),
			'user' => $setup->postValue('db_username'),
			'pass' => $setup->postValue('db_password'),
			'db' => $setup->postValue('db_database')
		];
		
	$dbSql = <<<EOD
CREATE TABLE IF NOT EXISTS `{{prefix}}_backups` (
  `user_id` int(11) NOT NULL,
  `backup_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `backup_file_hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{{prefix}}_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'SHA1 hashing',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `webspace` int(11) NOT NULL DEFAULT '0',
  `user_level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = unactivated, 1 = client, 2 = admin',
  `activation_code` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`,`email`),
  KEY `password` (`password`,`activation_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;

	$dbTpl = Template::compile($dbSql);
	$dbTpl->prefix = $setup->postValue('db_prefix');
	
	$setup->dbSettings('main', $db);
	if (!$setup->dbConnect('main'))
	{
		$setup->error('Cannot connect to database');
	}
	
	$setup->contentSet('main-sql', $dbTpl->_render());
});

$setup->submit(function($setup) {
	// TODO: Set file contents
	$setup->fileSet('tpanel-conf', '');
	$setup->fileSet('db-conf', '');
	
	// Create database
	$setup->dbCreate('main', 'main-sql');
	
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
		$setup->textField('Server', 'db_server', 'localhost');
		$setup->textField('Username', 'db_username');
		$setup->passwordField('Password', 'db_password');
		$setup->textField('Database', 'db_database');
		$setup->textField('Prefix', 'db_prefix');
	$setup->endGroup();
	
	$setup->beginGroup('Admin Account');
		$setup->textField('Username', 'admin_user', 'admin');
		$setup->passwordField('Password', 'admin_pass');
		$setup->textField('Database', 'db_database');
		$setup->textField('Prefix', 'db_prefix');
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

$setup->contentSet('main-htaccess', $htfile1);
$setup->contentSet('data-htaccess', $htfile2);
$setup->contentSet('users-htaccess', $htfile3);
$setup->contentSet('email', $emailTpl);

$setup->run();