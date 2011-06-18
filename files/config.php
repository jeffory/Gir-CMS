<?php
/**
 * Configuration
 * 
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

class cmsConfig
{
	var $siteTitle = 'Gir CMS';
	
	var $siteURL = 'http://localhost/gircms/';
	
	var $defaultTimezone = 'Australia/Brisbane';
	
	var $dbConfig = array(
			'driver' => 'PlaintextDatabase',
			'options' => array(
				'database' => 'files/pages',
				'extension' => 'txt'
				)
			);
	
	// Where the pages are stored.
	var $pagesDirectory = 'files/pages';
	
	var $pagesExtension = 'txt';
	
	// Where the dynamic (php) pages are stored.
	var $dynamicDirectory = 'files/snips';
	
	var $dynamicPages = array();
	
	var $debug = true;
	
	var $cacheEnabled = true;
	
	var $cacheDir = 'cache';
	
	var $layoutDir = 'files/layout';
	
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