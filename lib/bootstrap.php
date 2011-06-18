<?php
	define('DS', DIRECTORY_SEPARATOR);
	require('lib'. DS. 'core.php');
	$cms = new cms();
	
	// divide the url into parts
	$url = $cms->parseURL(@$_GET['url']);
	
	// render the content
	if (@!in_array($url['slug'], $cms->dynamicPages))
	{
		if (isset($cms->templates[$url['ext']]['markdown']) && $cms->templates[$url['ext']]['markdown'] === true)
		{
			$pageContent = $cms->renderPage($url['slug']);
		} else {
			$pageContent = $cms->renderPage($url['slug'], false);
		}
		
	} else {
		$pageContent = $cms->renderPagePart($url['slug']);
	}
	
	// display content using template
	if (isset($cms->templates[$url['ext']]))
	{
		if (isset($cms->templates[$url['ext']]['content-type']))
		{
			header('Content-type: '. $cms->templates[$url['ext']]['content-type']);
		}
		
		include($cms->layoutDir. DS. $cms->templates[$url['ext']]['template']);
	} else {
		$cms->handleError("Template for specified extension \"{$url['ext']}\" does not exist.", 2);
	}