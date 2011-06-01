<?php
	// Yeah our config, this might look messy, but only to you... :P
	
	class cmsConfig
	{
		var $siteTitle = 'Gir CMS';
		
		var $siteURL = 'http://localhost/gircms/';
		
		var $defaultTimezone = 'Australia/Brisbane';
		
		// Where the pages are stored.
		var $pagesDirectory = 'files/pages';
		
		// A list of pages that the CMS can access, if left blank all pages are viewable.
		var $pageWhitelist = array();
		
		// A list of pages that the CMS CANNOT access.
		var $pageBlacklist = array();
		
		// Where the dynamic (php) pages are stored.
		var $dynamicDirectory = 'snips';
		
		var $dynamicPages = array(
			'setup'
			);
		
		// A list of tags that we will allow to be used in content.
		var $allowedTags =  '<a><br><b><h1><h2><h3><strong><u><em><small><big><i><img><li>
			<ol><ul><p><dd><dt><dl><del><pre><br><blockquote><code><abbr><acronym><cite>
			<dfn><q><sup><sub><kbd><samp><var><hr><table><tr><td><th><thead><tbody><tfoot>';
		
		var $pagesExtension = 'txt';
		
		var $cacheEnabled = true;
		
		var $cacheDir = 'cache';
		
		var $layoutDir = 'layout';
		
		var $templates = array(
			'html' => array(
				'template' => 'default.php',
				'content-type' => 'text/html',
				'markdown' => true
				),
			'txt' => array(
				'template' => 'txt.php',
				'content-type' => 'text/plain'
				)
			);
	}
?>