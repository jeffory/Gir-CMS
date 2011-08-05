<?php
/**
 * Initial functions
 *
 * Start the benchmarking and include all the files we need for the base.
 * 
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

$startTime = @microtime(true);		// for benchmarking

chdir('..');
require('files/config.php');
require('lib/core.php');
require('lib/bootstrap.php');