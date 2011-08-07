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
	
	var $siteURL = 'http://localhost/gircms';
	
	var $debug = true;
	
	var $defaultTimezone = 'Australia/Brisbane';
	
	var $dbConfig = array(
			'driver' => 'Plaintext',
			'options' => array(
				'database' => 'files/pages',
				'extension' => 'txt'
				)
			);
	
	// Where the dynamic (php) pages are stored.
	var $dynamicDir = 'files/snips';
	
	var $dynamicPages = array(
		'test'
		);
	
	var $cacheEnabled = true;
	
	var $cacheDir = 'cache';
	
	var $layoutDir = 'files/layout';
	
	var $logDir = 'files';

	var $templates = array(
		'html' => array(
			'template' => 'html.php',
			'content-type' => 'text/html',
			'renderer' => 'Markdown'
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