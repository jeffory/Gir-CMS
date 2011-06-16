<?php
	require('shared/markdown.php');
	require('shared/smartypants.php');
	
	require('files/config.php');
	
	class cms extends cmsConfig
	{
		var $pages = array();
		var $debugInfo;
		
		var $url;
		var $pageRequest;
		
		
		function __construct()
		{
			$this->addDebug(getcwd());
			
			if (isset($cms->defaultTimezone))
				@date_default_timezone_set($cms->defaultTimezone);
			
			// Check the pages directory
			if (is_dir(realpath($this->pagesDirectory)))
			{
				$this->pagesDirectory = realpath($this->pagesDirectory);
			}
			else
			{
				$this->handleError("Page directory '{$this->pagesDirectory}' not found, CMS can't continue.", 2);
			}
			
			// Check the log file
			if (is_writable($this->logFile) || is_writable(dirname($this->logFile)))
			{
				if (!file_exists($this->logFile))
				{
					$fh = fopen($this->logFile, 'w');
					fwrite($fh, "# Gir CMS error log\n\n");
					fclose($fh);
				}
			}
			else
			{
				$this->handleError("Log file '{$this->logFile}' is not writable, CMS can't continue.", 2);
			}
			
			// Check the cache settings
			if ($this->cacheEnabled === true && empty($this->cacheDir))
			{
				$this->cacheDir = sys_get_temp_dir();
			}
			elseif($this->cacheEnabled === true && is_dir(realpath($this->cacheDir)))
			{
				$this->cacheDir = realpath($this->cacheDir);
			}
			elseif ($this->cacheEnabled === true)
			{
				$this->handleError("Cache folder \"{$this->cacheDir}\" doesn't exist, check 'cacheDir' setting.", 0);
				$this->cacheDir = sys_get_temp_dir();
			}
			
			// Initilize
			$this->listPages($this->pagesDirectory);
			
			// Load additional helpers
			foreach ($this->loadHelpers as $file => $varName)
			{
				include $file. '.php';
				$varName = $file;
			}
		}
		
		function listPages($pagesDirectory)
		{
			foreach (scandir($pagesDirectory) as $pageFile)
			{
				if ($this->checkPage($pageFile) && !(substr(basename($pageFile), 0, 1) == '.'))
				{
					if (is_dir($pagesDirectory. $pageFile))
					{
						$this->listPages($pagesDirectory. $pageFile);
					}
					else
					{
						$pageSlug = $this->getPageSlug($pageFile);
					
						$this->pages[$pageSlug]['title'] = $this->getPageTitle($pageFile);
						$this->pages[$pageSlug]['file'] = $pagesDirectory. DS. $pageFile;
						$this->pages[$pageSlug]['public'] = $this->isPublic($pagesDirectory. DS. $pageFile);
					}
				}
			}
		}
		
		function stripFilename($filename)
		{
			// Returns just the filename without extension.
			return preg_replace('/\.[^.]*$/', '', basename($filename));
		}
		
		function getPageSlug($filename)
		{
			// Replace anything that isn't text/numbers into dashes
			$slug = preg_replace('/[^a-z0-9-]/', '-', strtolower($this->stripFilename($filename)));
			// Remove excessive dashes
			$slug = ltrim(preg_replace('/-+/', "-", $slug), '-');
			
			return $slug;
		}
		
		function getPageTitle($filename)
		{
			// Remove the file extension and replace underscores from the filename
			return trim(htmlentities(ucwords(str_replace("_", " ", $this->stripFilename($filename)))));
		}
		
		function checkPage($filename)
		{
			// TODO: Add blacklist/whitelist filtering function... hidden pages? mmm
			return true;
		}
		
		function isPublic($filename)
		{
			return !(substr(basename($filename), 0, 1) == '_');
		}
		
		function renderPage($slug = '', $parseMarkdown = true)
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
		
		function renderContent($content)
		{
			@$renderer = $this->templates[$this->url['ext']]['renderer'];
			
			if (isset($renderer) && @in_array($renderer, array_keys($this->renderers)))
			{
				if (file_exists('lib/'. $this->renderers[$renderer]['file']))
				{
					// TODO: Implement renderers, class is loading, nothing else however.
					include('lib/'. $this->renderers[$renderer]['file']);
				}
				else
				{
					return $this->handleError("Renderer \"{$renderer}\" couldn't be found, check the config", 2);
				}
			}
			else
			{
				return $this->handleError("Renderer \"{$renderer}\" is invalid, check the config", 2);
			}
			
			return $content;
		}
		
		function renderPagePart($partName)
		{
			$file = $this->dynamicDirectory. DS. $partName. '.php';
			
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
		
		function parseURL($url)
		{
			if (!isset($_GET['url']))
			{
				$this->url['slug'] = 'index';
				$this->url['ext'] = 'html';
			}
			else
			{
				preg_match_all('/(.*?)(\.|\/|$)(.*?)/is', $url, $matches);
				$this->url['slug'] = $matches[1][0];
				
				if (isset($this->templates[$matches[1][1]])) {
					$this->url['ext'] = $matches[1][1];
				}
				else
				{
					$this->url['ext'] = 'html';
				}
			}
			
			return $this->url;
		}
		
		function handleError($message, $errorLevel = 0)
		{
			// $errorLevel,
			// 0 - A warning: log to file, don't display.
			// 1 - An error: display it to the user, however don't halt the script.
			// 2 - Fatal error: halt the script.
			
			$fh = fopen($this->logFile, 'a');
			
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
		
		function getClientIP()
		{
			// Check HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR for an IP otherwise fallback to REMOTE_ADDR
			return !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : 
				!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 
					$_SERVER['REMOTE_ADDR'];
		}
		
		function addDebug($line)
		{
			$this->debugInfo .= $line. "<br>";
		}
		
		function printDebug()
		{
			echo "<span class='big'><strong>Debug</strong></span><br><br>";
			echo $this->debugInfo. '<br><br>';
			echo '<strong>Files loaded:</strong>'. '<br><br>';
			
			echo '<ul>';
			
			foreach (get_included_files() as $file)
			{
				echo '<li>'. str_replace(getcwd(). DS, '', $file). '</li>';
			}
			
			echo '</ul><br>';
			htmlentities(print_r($this));
		}
	}