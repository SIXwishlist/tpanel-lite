<?php

/**
 * Mail
 *
 * Generates an e-mail based on user-provided information and sends an email
 * via PHP's built-in "mail" function.
 */

namespace Base;

class Mail
{
	const TEXT = 0;
	const HTML = 1;
	
	protected $subject;
	protected $boundary;
	protected $headers;
	protected $from;
	protected $reply_to;
	protected $body;
	protected $cc;
	protected $bcc;
	protected $to;
	protected $attachments;
	protected $mime;
	protected $generated_header;
	
	// Constructor
	function __construct ()
	{
		$this->subject = null;
		$this->headers = array();
		$this->from = null;
		$this->reply_to = null;
		$this->body = array();
		$this->cc = array();
		$this->bcc = array();
		$this->to = array();
		$this->attachments = array();
		$this->boundary = null;
		$this->mime = array(self::TEXT => 'text/plain', self::HTML => 'text/html');
		$this->generated_header = null;
	}
	
	// Returns the MIME for a file type
	protected function get_mime ($file)
	{
		$f = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($f, $file);
		finfo_close($f);
		return $mime;
	}
	
	// Returns the generated boundary
	protected function get_boundary ($type)
	{
		if ($this->boundary === null)
		{
			$this->boundary = sprintf('appframe-%%s-%s', md5(date('r', time()+mt_rand(-100,100))));
		}
		return sprintf($this->boundary, $type);
	}
	
	// Generates headers
	protected function get_headers ()
	{
		// Avoid redoing the whole thing over
		if ($this->generated_header !== null)
		{
			return $this->generated_header;
		}
		
		$body = array();
		if ($this->from !== null)
		{
			$body[] = sprintf('From: %s', $this->from);
		}
		if ($this->reply_to !== null)
		{
			$body[] = sprintf('Reply-To: %s', $this->reply_to);
		}
		if ($this->cc !== null)
		{
			$body[] = sprintf('Cc: %s', implode(', ', $this->cc));
		}
		if ($this->bcc !== null)
		{
			$body[] = sprintf('Bcc: %s', implode(', ', $this->bcc));
		}
		
		if (count($this->attachments) > 0)
		{
			$body[] = sprintf('Content-Type: multipart/mixed; boundary="%s"', $this->get_boundary('mixed'));
			$body[] = sprintf('--%s', $this->get_boundary('mixed'));
			$body[] = '';
		}
		
		// Explicitly define multiple types
		if (count($this->body) > 1)
		{
			$body[] = sprintf('Content-Type: multipart/alternative; boundary="%s"', $this->get_boundary('alt'));
		}
		
		// Add bodies
		foreach ($this->body as $type => $content)
		{
			if (count($this->body) > 1)
			{
				$body[] = sprintf('--%s', $this->get_boundary('alt'));
			}
			$body[] = sprintf('Content-Type: %s; charset="iso-8859-1"', $this->mime[$type]);
			$body[] = 'Content-Transfer-Encoding: 7bit';
			$body[] = '';
			$body[] = $content;
			$body[] = '';
		}
		
		// Signal end of boundary
		if (count($this->body) > 1)
		{
			$body[] = sprintf('--%s--', $this->get_boundary('alt'));
		}
		
		// Add attachments
		if (count($this->attachments) > 0)
		{
			foreach ($this->attachments as $file => $as_file)
			{
				$body[] = '';
				$body[] = sprintf('--%s', $this->get_boundary('mixed'));
				$body[] = sprintf('Content-Type: %s; name="%s"', $this->get_mime($file), $as_file === null ? basename($file) : $as_file);
				$body[] = 'Content-Transfer-Encoding: base64'; 
				$body[] = 'Content-Disposition: attachment';
				$body[] = '';
				$body[] = chunk_split(base64_encode(file_get_contents($file)));
			}
			$body[] = sprintf('--%s--', $this->get_boundary('mixed'));
		}
		
		$body[] = '';
		
		$this->generated_header = implode("\r\n", $body);
		return $this->generated_header;
	}
	
	// Adds an additional header
	function header ($line)
	{
		array_push($this->headers, $line);
	}
	
	// Sends an email
	function send ()
	{
		$args = func_get_args();
		if (count($args) > 1)
		{
			foreach ($args as $email)
			{
				if (!$this->send($email))
				{
					return false;
				}
			}
			return true;
		}
		elseif (count($args) == 1)
		{
			if (is_array($args[0]))
			{
				foreach ($args[0] as $email)
				{
					if (!$this->send($email))
					{
						return false;
					}
				}
				return true;
			}
			else
			{
				// Force boundary to regenerate on each send
				$this->boundary = null;
				return mail($args[0], $this->subject, '', $this->get_headers());
			}
		}
		else
		{
			return $this->send($this->to);
		}
	}
	
	// Adds content to the body along with a specified type
	function body ($content, $type = self::TEXT)
	{
		$this->generated_headers = null;
		$this->body[$type] = $content;
		return $this;
	}
	
	// Sets the subject
	function subject ($title)
	{
		$this->generated_headers = null;
		$this->subject = $title;
		return $this;
	}
	
	// Specifies the from address
	function from ($address)
	{
		$this->generated_headers = null;
		$this->from = $address;
		return $this;
	}
	
	// Carbon-copies an address
	function cc ()
	{
		$this->generated_headers = null;
		$this->cc = func_get_args();
		return $this;
	}
	
	// Send to...
	function to ()
	{
		$this->generated_headers = null;
		$this->to = func_get_args();
		return $this;
	}
	
	// Blind carbon-copy
	function bcc ()
	{
		$this->generated_headers = null;
		$this->bcc = func_get_args();
		return $this;
	}
	
	// Adds an attachment
	function attachment ($file, $as_filename = null)
	{
		$this->generated_headers = null;
		$this->attachments[$file] = $as_filename;
		return $this;
	}
}