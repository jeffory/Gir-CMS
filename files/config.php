<?php
	class cmsConfig
	{
		var $siteTitle = 'Gir CMS';
		
		var $siteURL = 'http://localhost/gircms/';
		
		var $debug = true;
		
		// Useful if the server is located elsewhere, mainly used for logging.
		var $defaultTimezone = 'Australia/Brisbane';
		
		var $logFile = 'files/errors.log';
		
		// Where the pages are stored.
		var $pagesDirectory = 'files/pages';
		
		// A list of pages that the CMS can access, if left blank all pages are viewable.
		var $pageWhitelist = array();
		
		// A list of pages that the CMS CANNOT access.
		var $pageBlacklist = array();
		
		// Where the dynamic (php) pages are stored.
		var $dynamicDirectory = 'files/snips';
		
		var $dynamicPages = array(
			'setup'
			);
		
		var $pagesExtension = 'md';
		
		var $cacheEnabled = false;
		
		var $cacheDir = 'cache';
		
		var $layoutDir = 'files/layout';
		
		var $loadHelpers = array(
			'HTML_Helper' => 'html_helper'
			);
		
		var $renderers = array(
			'markdown' => array(
				'class' => 'Markdown',
				'file' => 'markdown.php'
				),
			);
		
		var $templates = array(
			'html' => array(
				'template' => 'html.php',
				'content-type' => 'text/html',
				'renderer' => 'markdown'
				),
			'txt' => array(
				'template' => 'txt.php',
				'content-type' => 'text/plain'
				),
			'json' => array(
				'template' => 'json.php',
				'content-type' => 'application/json'
				)
			);
	}