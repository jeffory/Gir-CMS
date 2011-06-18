<?php
/**
 * JSON template
 *
 * Simple JSON format.
 *
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

$page = array(
		'title' => $siteTitle,
		'content' => $pageContent
	);

echo json_encode($page);