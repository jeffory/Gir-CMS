<!doctype html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
	<?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false): ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php endif; ?>
	<title><?php echo $cms->siteTitle ?></title>
	
	<meta name="HandheldFriendly" content="true">
	<meta name="viewport" content="width=device-width, height=device-height, user-scalable=no">
	
	<?php if (file_exists('extras/favicon.ico')): ?>
	<link rel="shortcut icon" href="<?php echo $cms->siteURL ?>/favicon.ico">
	<?php endif ?>
	<?php if (file_exists('extras/apple-touch-icon.png')): ?>
	<link rel="apple-touch-icon" href="<?php echo $cms->siteURL ?>/apple-touch-icon.png">
	<?php endif ?>
	
	<link href="//fonts.googleapis.com/css?family=PT+Serif|Amaranth" rel="stylesheet" type="text/css">
	<link href="<?php echo $cms->siteURL ?>/styles/prodoc.css" rel="stylesheet" type="text/css">
	<?php if ($cms->debug): ?><link href="<?php echo $cms->siteURL ?>/styles/debug.css" rel="stylesheet" type="text/css"><?php endif; ?>
	
	<script src="//ajax.cdnjs.com/ajax/libs/modernizr/1.7/modernizr-1.7.min.js" type="text/javascript"></script>
</head>
<body>
	<div id='container'>
		<header role='banner'>
			<h1 id='top'><?php echo $cms->siteTitle ?></h1>
		</header>
		
		<article role='main'>
			<?php echo $pageContent; ?>
		</article>
		
		<footer role='contentinfo'>
			Powered by <a href="http://c0d.in/gir-cms">Gir CMS</a>.
		</footer>
	</div>
	
	<script type="text/javascript">
		<?php if ($contents = file_get_contents('files/scripts/myscripts.js')) echo $contents ?>
	</script>
	
	<!--[if lt IE 7 ]>
	<script src="scripts/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg");</script>
	<![endif]-->
	<?php if ($cms->debug) echo $cms->showDebug() ?>
</body>
</html><?php echo @microtime(true) ? '<!-- '. round(microtime(true) - $startTime, 4). 's -->': null; ?>