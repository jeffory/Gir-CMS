<?php
/**
 * Gir-CMS Core
 *
 * The heart of the CMS, the CMS should be able to function simply with this
 * class, a config and a bootstrap file.
 *
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

class CMSCore extends cmsConfig
{
	/**
	 * Placeholder for the loaded database
	 *
	 * @var object
	 **/
	var $db;
	
	/**
	 * Setup timezones, config, cache and database(s).
	 *
	 * @return void
	 **/
	function __construct()
	{
		if (isset($this->defaultTimezone))
		{
			@date_default_timezone_set($this->defaultTimezone);
		}
		
		// Check the pages directory is avaliable
		if (is_dir(realpath($this->pagesDir)))
		{
			$this->pagesDir = realpath($this->pagesDir);
		}
		else
		{
			$this->handleError("Page directory '{$this->pagesDir}' not found, CMS can't continue.", 2);
		}
		
		// Check the cache settings
		if ($this->cacheEnabled === true && empty($this->cacheDir))
		{
			$this->cacheDir = sys_get_temp_dir();
		}
		elseif($this->cacheEnabled === true && !is_dir(realpath($this->cacheDir)))
		{
			$this->handleError("Cache folder \"{$this->cacheDir}\" doesn't exist, check 'cacheDir' setting.", 0);
			$this->cacheDir = sys_get_temp_dir();
		}
		elseif ($this->cacheEnabled === true)
		{
			$this->cacheDir = realpath($this->cacheDir);
		}
		
		// Load a database
		if (isset($this->dbConfig))
		{
			if ($this->db = $this->loadClass($this->dbConfig['driver']))
			{
				$this->db->options = $this->dbConfig['options'];
			}
			else
			{
				$this->handleError("Database couldn't be loaded.", 2);
			}
		}
	}
	
	/**
	 * Load the class and return it.
	 * 
	 * @return object	newly loaded class
	 * @access public
	 **/
	public function loadClass($className, $classFile = null)
	{
		if (!isset($classFile))
			$classFile = 'lib/'. $this->underscore($className). '.php';
		
		if (require $classFile)
		{
			return new $className;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Returns the given camelCasedWord as an underscored_word.
	 *
	 * @param string $camelCasedWord Camel-cased word to be "underscorized"
	 * @return string Underscore-syntaxed version of the $camelCasedWord
	 * @access public
	 **/
	public static function underscore($camelCasedWord) {
		$result = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
		
		return $result;
	}
	
	/**
	 * renderPage
	 *
	 * @return string	rendered content
	 * @access public
	 **/
	public function renderPage($slug = '', $parseMarkdown = true)
	{
		if (!empty($slug))
		{
			$this->pageRequest = $slug;
			
			if (isset($this->pages[$this->pageRequest]))
			{
				$cacheFile = realpath($this->cacheDir). DS. sha1($this->pageRequest. @$this->ext);
				
				if ($this->cacheEnabled === true && file_exists($cacheFile) && (filemtime($this->pages[$slug]['file']) < filemtime($cacheFile)))
				{
					$content = file_get_contents($cacheFile);
				}
				else
				{
					// Markdown?
					if ($parseMarkdown == true)
					{
						$content = $this->renderContent(file_get_contents($this->pages[$slug]['file']));
						
						if ($this->cacheEnabled === true)
						{
							if (is_writable($this->cacheDir))
							{
								file_put_contents($cacheFile, $content);
							}
							else
							{
								$this->handleError("File \"{$cacheFile}\" not writable, check 'cacheDir' setting.", 0);
							}
						}
					}
					else
					{
						$content = file_get_contents($this->pages[$slug]['file']);
					}
				}	
			}
			else
			{
				if (isset($this->pages['404']))
				{
					return $this->renderPage('404');
				}
				else
				{
					return $this->handleError("Page \"{$slug}\" not found", 1);
				}
			}
		}
		return $content;
	}
	
	/**
	 * Render some content
	 *
	 * @return string	rendered page content
	 * @access public
	 **/
	public function renderContent($content)
	{
		$markdown = $this->loadClass('MarkdownRenderer');
		
		$content = MarkdownRenderer::renderContent($content);
		
		return $content;
	}
	
	/**
	 * Run a PHP snippet and return the result
	 *
	 * @return string	rendered/eval'd code
	 * @access public
	 **/
	public function renderPagePart($partName)
	{
		$file = $this->dynamicDir. DS. $partName. '.php';
		
		if (file_exists($file))
		{
			// Run the file, return the output
			ob_start();
			include $file;
			$content = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			return $this->handleError("Page \"{$partName}\" not found", 1);
		}
		
		return $content;
	}
	
	/**
	 * URL parsing
	 * 
	 * @var string		the raw url
	 * @return array	the slug and extension
	 * @access public
	 **/
	public function parseURL($url)
	{
		if (!isset($_GET['url']))
		{
			$ret['slug'] = 'index';
			$ret['ext'] = 'html';
		}
		else
		{
			preg_match_all('/(.*?)(\.|\/|$)(.*?)/is', $url, $matches);
			
			$ret['slug'] = $matches[1][0];
			if (isset($this->templates[$matches[1][1]])) {
				$ret['ext'] = $matches[1][1];
			}
			else
			{
				$ret['ext'] = 'html';
			}
		}
		
		if ($ret['ext'] == 'html')
		{
			$this->url = $ret['slug'];
		} else {
			$this->url = $ret['slug']. '.'. $ret['ext'];
		}
		
		return $ret;
	}
	
	/**
	 * Error handling function
	 * 
	 * @var string		the error message
	 * @var integer		error level, 0: a warning, 1: a standard, 2: a fatal error
	 * @return string	an error is returned if the error level is less then two
	 * @access public
	 **/
	public function handleError($message, $errorLevel = 0)
	{
		$logFile = "files/errors.log";
		$fh = fopen($logFile, 'a');
		
		switch ($errorLevel) {
			case 0:
				$newError = "Warning:\t". date('r'). "\t\t". $message;
			break;
			
			case 1:
				$newError = "Error:\t\t". date('r'). "\t\t". $message;
			break;
			
			case 2:
				$newError = "Fatal Error:\t". date('r'). "\t\t". $message;
			break;
		}
		
		// Log the client's hostname in case someone's tampering with the site.
		fwrite($fh, $newError. "\t\t\t". gethostbyaddr($this->getClientIP()). "\n");
		fclose($fh);
		
		if ($errorLevel == 2)
		{
			die('<b>Error:</b> '. $message);
		}
		
		if ($errorLevel > 0)
		{
			return '<b>Error:</b> '. $message;
		}
	}
	
	/**
	 * Attempt to get the clients IP even if they are behind some sort of proxy
	 * 
	 * @return string	the clients IP address
	 * @access public
	 **/
	public static function getClientIP()
	{
		// Check HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR for an IP otherwise fallback to REMOTE_ADDR
		return !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : 
			!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 
				$_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * Return any important debugging information
	 * 
	 * @return string	debugging info
	 * @access public
	 **/
	public function showDebug()
	{
		$debugData = '<div id="debug"><h2>Debug</h2>';
		$debugData .= '<h3>Log:</h3>';
		$debugData .= '<h3>Included files:</h3><ul>';
		
		foreach(get_included_files() as $file)
		{
			if (defined('CMS_PATH'))
			{
				$debugData .= '<li>'. str_replace(CMS_PATH. '\\', '', $file). '</li>';
			}
			else
			{
				$debugData .= '<li>'. $file. '</li>';
			}
		}
		
		$debugData .= '</ul><br>';
		$debugData .= print_r($this, true);
		$debugData .= '</div>';
		
		return $debugData;
	}
}