<?php
	require('sanitizer.php');
	require('markdown.php');
	require('smartypants.php');
	
	require('../files/config.php');
	
	class cms extends cmsConfig
	{
		var $pages = array();
		
		function __construct()
		{
			chdir('..');
			
			if (isset($cms->defaultTimezone))
			{
				@date_default_timezone_set($cms->defaultTimezone);
			}
			
			// Check the config
			if (!is_dir(realpath($this->pagesDirectory)))
			{
				$this->handleError("Page directory '{$this->pagesDirectory}' not found, CMS can't continue.", 2);
			}
			else
			{
				$this->pagesDirectory = realpath($this->pagesDirectory);
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
			
			// Initilize
			$this->listPages($this->pagesDirectory);
		}
		
		function listPages($pagesDirectory)
		{
			foreach (scandir($pagesDirectory) as $pageFile) {
				
				if ($this->checkPage($pageFile) && $pageFile !== '.' && $pageFile !== '..')
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
					if (isset($this->pages['-404']))
					{
						return $this->renderPage('-404');
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
			$markdownParser = new MarkdownExtra_Parser;
			$htmlSanitizer = new HTML_Sanitizer;
			
			$renderFixes = array(
						'/^(#+)/m' => '$1#',	// Take the headings down a step, H1 is the main website heading :)
						'/<(\/|)s>/m' => '<$1del>',
					);
			
			foreach ($renderFixes as $pattern => $replacement)
			{
				$content = preg_replace($pattern, $replacement, $content);
			}
				
			$content = $markdownParser->transform($content);
			$content = $htmlSanitizer->sanitize($content);
			$content = SmartyPants($content);
			
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
		
		function handleError($message, $errorLevel = 0)
		{
			// $errorLevel,
			// 0: A warning, log to file, don't display.
			// 1: An error, display it to the user, however don't halt the script.
			// 2: Fatal error, halt the script.
			
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
		
		function getClientIP()
		{
			// Check HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR for an IP otherwise fallback to REMOTE_ADDR
			return !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : 
				!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 
					$_SERVER['REMOTE_ADDR'];
		}
	}