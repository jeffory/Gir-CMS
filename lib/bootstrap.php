<?php
/**
 * Bootstrap
 * 
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/
 
define('DS', DIRECTORY_SEPARATOR);
define('CMS_PATH', realpath('.'));

$cms = new CMSCore();

// Divide the url into parts
$url = $cms->parseURL(@$_GET['url']);
$cms->requestExt = $url['ext'];

// Render the content
if (@!in_array($url['slug'], $cms->dynamicPages))
{
	$pageContent = $cms->renderPage($url['slug']);
}
else
{
	$pageContent = $cms->renderPagePart($url['slug']);
}

// Display content using template
if (isset($cms->templates[$url['ext']]))
{
	if (isset($cms->templates[$url['ext']]['content-type']))
		header('Content-type: '. $cms->templates[$url['ext']]['content-type']);
	
	include($cms->layoutDir. DS. $cms->templates[$url['ext']]['template']);
}
else
{
	$cms->handleError("Template for specified extension \"{$url['ext']}\" does not exist.", 2);
}