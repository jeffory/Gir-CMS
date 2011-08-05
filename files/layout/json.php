<?php
/**
 * JSON template
 *
 * Simple JSON format.
 *
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

// header('Cache-Control: no-cache, must-revalidate');
// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

$page = array(
		'title' => $siteTitle,
		'content' => $pageContent
	);

echo json_encode($page);