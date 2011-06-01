<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
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
	
	<link href="http://fonts.googleapis.com/css?family=PT+Serif|Amaranth" rel="stylesheet" type="text/css">
	<link href="<?php echo $cms->siteURL ?>/styles/prodoc.css?v=1" rel="stylesheet" type="text/css">
</head>
<body>
	<div id='container'>
		<header>
			<h1 id='top'><?php echo $cms->siteTitle ?></h1>
		</header>

		<article id="main" role="main">
			<?php echo $pageContent; ?>
		</article>
		
		<footer>
			Powered by Gir CMS by <a href="http://keithmcgahey.com">Keith McGahey</a>.
		</footer>
	</div>
	
	<script type="text/javascript">
		/*!
		* $script.js v1.3
		* https://github.com/ded/script.js
		* Copyright: @ded & @fat - Dustin Diaz, Jacob Thornton 2011
		* Follow our software http://twitter.com/dedfat
		* License: MIT
		*/
		!function(a,b,c){function t(a,c){var e=b.createElement("script"),f=j;e.onload=e.onerror=e[o]=function(){e[m]&&!/^c|loade/.test(e[m])||f||(e.onload=e[o]=null,f=1,c())},e.async=1,e.src=a,d.insertBefore(e,d.firstChild)}function q(a,b){p(a,function(a){return!b(a)})}var d=b.getElementsByTagName("head")[0],e={},f={},g={},h={},i="string",j=!1,k="push",l="DOMContentLoaded",m="readyState",n="addEventListener",o="onreadystatechange",p=function(a,b){for(var c=0,d=a.length;c<d;++c)if(!b(a[c]))return j;return 1};!b[m]&&b[n]&&(b[n](l,function r(){b.removeEventListener(l,r,j),b[m]="complete"},j),b[m]="loading");var s=function(a,b,d){function o(){if(!--m){e[l]=1,j&&j();for(var a in g)p(a.split("|"),n)&&!q(g[a],n)&&(g[a]=[])}}function n(a){return a.call?a():e[a]}a=a[k]?a:[a];var i=b&&b.call,j=i?b:d,l=i?a.join(""):b,m=a.length;c(function(){q(a,function(a){h[a]?(l&&(f[l]=1),o()):(h[a]=1,l&&(f[l]=1),t(s.path?s.path+a+".js":a,o))})},0);return s};s.get=t,s.ready=function(a,b,c){a=a[k]?a:[a];var d=[];!q(a,function(a){e[a]||d[k](a)})&&p(a,function(a){return e[a]})?b():!function(a){g[a]=g[a]||[],g[a][k](b),c&&c(d)}(a.join("|"));return s};var u=a.$script;s.noConflict=function(){a.$script=u;return this},typeof module!="undefined"&&module.exports?module.exports=s:a.$script=s}(this,document,setTimeout)
		
		$script("//www.google-analytics.com/ga.js", function(){
			var t = _gat._getTracker ("UA-23592342-1");
			t._setDomainName("keithmcgahey.com");
			//t._initData();
			t._trackPageview();
		});
		
		var dependencyList = {
			modernizr: 'scripts/libs/modernizr-1.7.min.js',
			jquery: 'scripts/libs/jquery-1.5.2.min.js',
		};
		
		$script('//ajax.cdnjs.com/ajax/libs/modernizr/1.7/modernizr-1.7.min.js', 'modernizr');
		$script('//ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js', 'jquery');
		
		$script.ready(['modernizr', 'jquery'], function() {
			$("a[href^=http]").each(function() {
				if(this.href.indexOf(location.hostname) == -1) {
					$(this).click(function(){window.open(this.href);return false;});
				}
			});
		// }, function(depsNotFound) {
		// 			// Load local dependancys if the online ones fail.
		// 			depsNotFound.forEach(function(dep) {
		// 				console.log(dep + ' could not be loaded, loading local copy.')
		// 				$script(dependencyList[dep], dep);
		// 			});
		});
		
		window.applicationCache.addEventListener('updateready', function(e) {
			if (window.applicationCache.status == window.applicationCache.UPDATEREADY) {
				window.applicationCache.swapCache();
				if (confirm('A new version of this site is available. Load it?')) {
					window.location.reload();
				}
			}
		}, false);
	</script>
	
	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg");</script>
	<![endif]-->
</body>
</html>