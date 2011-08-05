<?php
/**
 * Plain text database
 *
 * A simple database using a very simple folder based layout.
 *
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

class PlaintextDatabase
{
	/**
	 * pages folder location
	 *
	 * @var string
	 **/
	var $database;
	
	/**
	 * list of pages
	 *
	 * @var array
	 **/
	var $pages = array();
	
	/**
	 * page extension
	 *
	 * @var string
	 **/
	var $extension = array();
	
	
	/**
	 * Initialize database
	 *
	 * @return void
	 **/
	function __construct()
	{
		// Check the pages directory is available
		if (!is_dir(realpath($this->database)))
			$parent->handleError("Page directory '{$this->database}' not found, CMS can't continue.", 2);
	}	
	
	/**
	 * Returns an array with the pages and their properties.
	 *
	 * @param string $pagesDirectory 
	 * @return array
	 * @access public
	 **/
	public function listPages()
	{
		$pagesDirectory = $this->database;
		
		foreach (scandir($pagesDirectory) as $pageFile)
		{
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
					$this->pages[$pageSlug]['file'] = realpath($pagesDirectory. DS. $pageFile);
					$this->pages[$pageSlug]['public'] = $this->isPublic($pagesDirectory. DS. $pageFile);
				}
			}
		}
		
		return $this->pages;
	}
	
	/**
	 * Returns a page slug from a filename.
	 *
	 * @param string	filename
	 * @return string	slug
	 * @access public
	 **/
	public function getPageSlug($filename)
	{
		// Replace anything that isn't text/numbers into dashes
		$slug = preg_replace('/[^a-z0-9-]/', '-', strtolower($this->stripFilename($filename)));
		// Remove excessive dashes
		$slug = ltrim(preg_replace('/-+/', "-", $slug), '-');
		
		return $slug;
	}
	
	public function getPageTitle($filename)
	{
		// Remove the file extension and replace underscores from the filename
		return trim(htmlentities(ucwords(str_replace("_", " ", $this->stripFilename($filename)))));
	}
	
	private function checkPage($filename)
	{
		// TODO: Add blacklist/whitelist filtering function... hidden pages? mmm
		return true;
	}
	
	private function isPublic($filename)
	{
		return !(substr(basename($filename), 0, 1) == '_');
	}
	
	private function stripFilename($filename)
	{
		// Returns just the filename without extension.
		return preg_replace('/\.[^.]*$/', '', basename($filename));
	}
}