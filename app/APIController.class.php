<?php

namespace App;
use Base\MVC\Controller;
use Base\App;

class APIController extends Controller
{
	// Username of the current user
	protected $username;
	// User ID of the current user
	protected $userId;
	
	/**
	 * Initialization for the API Controller before the request is passed to a
	 * controller method.  If the user is not logged in, he/she is redirected
	 * to the login page.
	 *
	 * @param request the Request object of the current HTTP request
	 * @param view the HTTP response in the form of a View
	 */
	function init ($request, $view)
	{
		if (!App::Auth('Client')->enabled())
		{
			App::Session('Redirect')->set('url', $request->URL());
			App::flash('You must be logged in to use this API');
			App::redirect('@LoginController::client');
		}
		else
		{
			$this->username = App::Auth('Client')->get('username');
			$this->userId = App::Auth('Client')->get('userId');
			
			// Initialize models
			$this->FileSystem->setUser($this->username);
			$this->User->setUser($this->userId);
		}
	}
	
	// List files in a directory
	function listFiles ($request, $view)
	{
		$dir = $request->post('dir', '');
		
		$view->files = $this->FileSystem->listFiles($dir);
		$view->renderAsJSON();
	}
	
	protected function canSaveFile ($size)
	{
		return $this->User->availableSpace() == 0 || ($this->FileSystem->used() + $size <= $this->User->availableSpace());
	}
	
	// Upload a file
	function uploadFile ($request, $view)
	{
		$dir = $request->post('dir');
		
		$fileInfo = $this->FileSystem->getUploadMeta($request);
		if (!$this->canSaveFile($fileInfo['size']))
		{
			$view->success = false;
			$view->error = 'File exceeds web space limit';
		}
		else
		{
			$view->success = $this->FileSystem->upload($dir, $request);
		}
		$view->renderAsJSON();
	}
	
	// Retrieve file metadata
	function getFileMeta ($request, $view)
	{
		$file = $request->post('file');
		
		$view->file = $this->FileSystem->getFileMeta($file);
		$view->renderAsJSON();
	}
	
	// Retrieve a file's contents
	function getFileContents ($request, $view)
	{
		$file = $request->post('file');
		
		// NOTE: Base64 encoded (need 'atob' to decode)
		$view->contents = $this->FileSystem->getContents($file);
		$view->renderAsJSON();
	}
	
	// Save a file's contents
	function saveFile ($request, $view)
	{
		$file = $request->post('file');
		$contents = $request->post('contents');
		
		if ($this->canSaveFile(mb_strlen($contents)))
		{
			$view->success = $this->FileSystem->saveFile($file, $contents);
		}
		else
		{
			$view->success = false;
		}
		$view->renderAsJSON();
	}
	
	// Rename a file or directory
	function renameFile ($request, $view)
	{
		$file = $request->post('src', null);
		$newFile = $request->post('dest', null);
		
		$view->success = $this->FileSystem->renameFile($file, $newFile);
		$view->renderAsJSON();
	}
	
	// Move a file or directory
	function moveFile ($request, $view)
	{
		$source = $request->post('src', null);
		$destination = $request->post('dest', null);
		
		$view->success = $this->FileSystem->moveFile($source, $destination);
		$view->renderAsJSON();
	}
	
	// Copy a file or directory
	function copyFile ($request, $view)
	{
		$source = $request->post('src', null);
		$destination = $request->post('dest', null);
		
		// Check file size limitations
		$info = $this->FileSystem->getFileMeta($source);
		if ($info === false || !$this->canSaveFile($info['size']))
		{
			$view->success = false;
		}
		else
		{
			$view->success = $this->FileSystem->copyFile($source, $destination);		
		}
		$view->renderAsJSON();
	}
	
	// Delete a file or directory
	function deleteFile ($request, $view)
	{
		$file = $request->post('file');
		
		$view->success = $this->FileSystem->delete($file);
		$view->renderAsJSON();
	}
	
	// Create a blank file
	function newFile ($request, $view)
	{
		$file = $request->post('file');
		
		$view->success = $this->FileSystem->touch($file);
		$view->renderAsJSON();
	}
	
	// Create a new directory
	function newDir ($request, $view)
	{
		$dir = $request->post('dir');
		
		$view->success = $this->FileSystem->mkdir($dir);
		$view->renderAsJSON();
	}
}
