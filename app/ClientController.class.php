<?php

/**
 * ClientController
 *
 * Provides access to the client-side control panel in tPanel.
 */

namespace App;
use Base\App;
use Base\IO\File;
use Base\Form;
use Base\Filter;

class ClientController extends ClientBase
{
	/**
	 * Initializes one or more models passed as string arguments.
	 *
	 * @params The keywords (as strings) used for initializing models.
	 */
	protected function initModel ()
	{
		$args = func_get_args();
		foreach ($args as $model)
		{
			switch ($model)
			{
				case 'user':
					$this->User->setUser($this->userId);
					break;
				case 'file':
					$this->FileSystem->setUser($this->username);
					break;
				case 'backup':
					$this->Backup->setFileSystem($this->FileSystem);
					$this->Backup->setUser($this->userId);
			}
		}
	}
	
	/**
	 * Returns the free space available to the user as a percentage.
	 * @NOTE: This requires initModel to be run on "user" and "file" first.
	 */
	protected function getFreeSpacePercent ()
	{
		if ($this->User->availableSpace() === 0)
		{
			// Unlimited space
			return 0;
		}
		else
		{
			return floor($this->FileSystem->used() / $this->User->availableSpace() * 100);
		}
	}
	
	/**
	 * Displays the "Home" screen of the client-side control panel.
	 */
	function home ($request, $view)
	{
		$view->title = 'Home';
		$this->initModel('user', 'file');
		
		$view->freeSpacePercent = $this->getFreeSpacePercent();
		$view->render('home');
	}
	
	/**
	 * Displays a listing of files for a specified user directory.
	 */
	function fileManager ($request, $view)
	{
		$view->title = 'File Manager';
		$this->initModel('user', 'file');
		$dir = $request->param('dir', '');
		
		$view->files = $this->FileSystem->listFiles($dir);
		$view->usagePercent = $this->getFreeSpacePercent();
		$view->usage = Filter::fileSize($this->FileSystem->used());
		if ($this->User->availableSpace() === 0)
		{
			$view->free = 'Unlimited';
		}
		else
		{
			$view->free = Filter::fileSize($this->User->availableSpace() - $this->FileSystem->used());
		}
		$view->dir = $dir;
		
		$view->render('file_manager');
	}
	
	/**
	 * Renders a pie chart for displaying directory usages in the user's 
	 * web directory.
	 */
	function diskUsage ($request, $view)
	{
		$view->title = 'Space Usage';
		$this->initModel('user', 'file');
		
		// NOTE: Use Chart.js Pie chart for displaying directory usages
		$view->usageData = $this->FileSystem->usageAsJSON();
		$view->usage = Filter::fileSize($this->FileSystem->used());
		if ($this->User->availableSpace() == 0)
		{
			$view->available = 'Unlimited';
		}
		else
		{
			$view->available = Filter::fileSize($this->User->availableSpace());
		}
		
		$view->render('disk_usage');
	}
	
	/**
	 * Handles account settings for the client.
	 */
	function settings ($request, $view)
	{
		$view->title = 'Account Settings';
		$this->initModel('user');
		
		$confirmLevel = App::Session('Client')->get('deleteConfirm', 0);
		
		// Display a warning to the user if they click "Remove"
		if ($confirmLevel === 1)
		{
			$view->warning = true;
		}
		
		if ($request->isPost())
		{
			if ($request->posted('funcbtn1'))
			{
				// Save settings
				
				// Validate name, email (and password)
				$v = $this->User->validateUpdate($request->postArray());
				if ($v->success() && $this->User->update($request->postArray()))
				{
					App::flash('Settings updated successfully!');
				}
				else
				{
					$view->errors = $v->errors();
				}
			}
			elseif ($request->posted('funcbtn2'))
			{
				// Remove account
				if ($confirmLevel === 0)
				{
					App::Session('Client')->set('deleteConfirm', 1);
					$confirmLevel = 1;
				}
				elseif ($confirmLevel === 1)
				{
					// Confirmed by user
					App::Session('Client')->delete('deleteConfirm');
					$this->initModel('file', 'backup');
					
					$this->Backup->destroy();
					$this->FileSystem->destroy();
					$this->User->remove();
					App::Auth('Client')->disable();
					App::flash('Account removed successfully.');
					App::redirect('@LoginController::client');
				}
			}
		}
		$config = $this->User->getConfig();
		$view->config = $config;
		
		$form = new Form($request);
		$form->assign($config);
		
		$view->form = $form;
		$view->render('settings');
	}
	
	/**
	 * Serves up a simple file manager with WYSIWYG HTML editor pane.
	 */
	function webEditor ($request, $view)
	{
		$view->title = 'Web Editor';
		// NOTE: This uses the APIController heavily for listing files, editing, and submitting
		$view->render('html_editor');
	}
	
	/**
	 * Displays a list of files in a backup and allows the user to backup 
	 * his/her website or restore the backup.
	 */
	function backupPage ($request, $view)
	{
		$view->title = 'Backups';
		$this->initModel('user', 'backup');
		
		if ($request->isPost())
		{
			if ($request->posted('funcbtn1'))
			{
				// Backup the site
				$view->success1 = $this->Backup->backup();
			}
			elseif ($request->posted('funcbtn2'))
			{
				// Restore the backup
				$view->success2 = $this->Backup->restore();
			}
		}
		
		$view->files = $this->Backup->listFiles();
		$view->date = $this->Backup->getDate();
		$view->render('backup');
	}
	
	/**
	 * Disables authentication and logs the user out of the client-side
	 * control panel.
	 */
	function logout ($request, $view)
	{
		App::Auth('Client')->disable();
		App::flash('Successfully logged out');
		App::redirect('@LoginController::client');
	}
}
