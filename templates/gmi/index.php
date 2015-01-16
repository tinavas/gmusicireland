<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScript('templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');
$doc->addStyleSheet('templates/' . $this->template . '/css/header.css');
//$doc->addStyleSheet('templates/' . $this->template . '/css/breadcrumb.css');
$doc->addStyleSheet('templates/' . $this->template . '/css/footer.css');

// Add Javascript
//$doc->addScript('templates/' . $this->template . '/js/caroufredsel620/jquery-1.8.2.min.js');
//$doc->addScript('templates/' . $this->template . '/js/caroufredsel620/jquery.carouFredSel-6.2.0-packed.js');
//$doc->addScript('templates/' . $this->template . '/js/caroufredsel620/helper-plugins/jquery.mousewheel.min.js');
//$doc->addScript('templates/' . $this->template . '/js/caroufredsel620/helper-plugins/jquery.touchSwipe.min.js');
//$doc->addScript('templates/' . $this->template . '/js/caroufredsel620/helper-plugins/jquery.transit.min.js');
//$doc->addScript('templates/' . $this->template . '/js/caroufredsel620/helper-plugins/jquery.ba-throttle-debounce.min.js');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span6";
}
elseif ($this->countModules('position-7') && !$this->countModules('position-8'))
{
	$span = "span8";
}
elseif (!$this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span8";
}
else
{
	$span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle')) . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}
?>
<?php
function noPx($string){
	$pattern = '/px/';
	$replacement = '';
	$string =  preg_replace($pattern, $replacement, $string);
	return $string;
}
?>
<?php
// Include the FBCounts.php file.
require_once("FBCounts.php");
require_once('TwitterAPIExchange.php'); //get it from https://github.com/J7mbo/twitter-api-php
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	
	<?php // Use of Google Font ?>
	<?php if ($this->params->get('googleFont')) : ?>
		<link href='//fonts.googleapis.com/css?family=<?php echo $this->params->get('googleFontName'); ?>' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Oswald:700,400,300' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700italic,700,400italic,300italic' rel='stylesheet' type='text/css'>
		<style type="text/css">
			h1,h2,h3,h4,h5,h6,.site-title{
				font-family: '<?php echo str_replace('+', ' ', $this->params->get('googleFontName')); ?>', sans-serif;
			}
		</style>
	<?php endif; ?>
	<link rel='stylesheet' href='templates/gmi/css/icons.css' type='text/css' media='all' />
	<link href="templates/gmi/css/font-awesome.min.css" rel="stylesheet">
	<!-- include jQuery + carouFredSel plugin -->
	<script type="text/javascript" language="javascript" src="templates/gmi/js/caroufredsel620/jquery.carouFredSel-6.2.0-packed.js"></script>
	<!-- optionally include helper plugins -->
	<script type="text/javascript" language="javascript" src="templates/gmi/js/caroufredsel620/helper-plugins/jquery.mousewheel.min.js"></script>
	<script type="text/javascript" language="javascript" src="templates/gmi/js/caroufredsel620/helper-plugins/jquery.touchSwipe.min.js"></script>
	<script type="text/javascript" language="javascript" src="templates/gmi/js/caroufredsel620/helper-plugins/jquery.transit.min.js"></script>
	<script type="text/javascript" language="javascript" src="templates/gmi/js/caroufredsel620/helper-plugins/jquery.ba-throttle-debounce.min.js"></script>
	
	<?php // Template color ?>
	<?php if ($this->params->get('templateColor')) : ?>
	<style type="text/css">
		body.site
		{
			border-top: 3px solid <?php echo $this->params->get('templateColor'); ?>;
			background-color: <?php echo $this->params->get('templateBackgroundColor'); ?>
		}
		a
		{
			color: <?php echo $this->params->get('templateColor'); ?>;
		}
		.navbar-inner, .nav-list > .active > a, .nav-list > .active > a:hover, .dropdown-menu li > a:hover, .dropdown-menu .active > a, .dropdown-menu .active > a:hover, .nav-pills > .active > a, .nav-pills > .active > a:hover,
		.btn-primary
		{
			background: <?php echo $this->params->get('templateColor'); ?>;
		}
		.navbar-inner
		{
			-moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
			-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
			box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
		}
	</style>
	<?php endif; ?>
	<!--[if !IE 7]>
		<style type="text/css">
			#wrap {display:table;height:100%}
		</style>
	<![endif]-->
	<!--[if lt IE 9]>
		<script src="<?php echo $this->baseurl; ?>/media/jui/js/html5.js"></script>
	<![endif]-->
	<script>
	function getClass(element, startsWith) {
		var result = undefined;
		$(element.attr('class').split(' ')).each(function() {
			if (this.indexOf(startsWith) > -1) result = this;
		});
		return result.toString();
	}
	$(document).ready(function(){
		var p = $('div.socialmedia div');
		if(p.hasClass("showtitle")){
			var c = $('div.showtitle');var facebook = 'icon-facebook'
			$('[class*=icon-].showtitle').each(function() {
				var className = getClass( $(this), 'icon-' );
				$("div.showtitle a").addClass("footer-sociallinks");
				switch(className){
					case 'icon-facebook' :		
						$("div.icon-facebook.showtitle a").text("Facebook");
						break;
					case 'icon-twitter' : 			
						$("div.icon-twitter.showtitle a").text("Twitter");
						break;
					case 'icon-gplus' : 			
						$("div.icon-gplus.showtitle a").text("Google+");
						break;
					case 'icon-rss' : 			
						$("div.icon-rss.showtitle a").html("Latest&nbsp;Videos");
						break;
				}
			});				
		}
	});
	</script>
</head>

<body class="site-custom <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	. ($params->get('fluidContainer') ? ' fluid' : '');
?>"style="padding: 0; margin: 0;">
	<!-- #Wrap -->
	<div id="wrap">
		<!-- Header -->
		<header class="header header-social-media">
			<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
				<div class="pull-right">
					<jdoc:include type="modules" name="header-socialmedia" style="none" />
				</div>
			</div>
		</header>		
		<header class="header header-menu" role="banner">		
			<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
				<a class="brand pull-left" href="<?php echo $this->baseurl; ?>">
					<?php echo $logo; ?>
					<?php if ($this->params->get('sitedescription')) : ?>
						<?php echo '<div class="site-description">' . htmlspecialchars($this->params->get('sitedescription')) . '</div>'; ?>
					<?php endif; ?>
				</a>			
				<div class="header-inner clearfix">
					<?php if ($this->countModules('position-1')) : ?>
						<nav class="navigation pull-right" role="navigation">
							<jdoc:include type="modules" name="position-1" style="none" />
							<jdoc:include type="modules" name="header-search" style="none" />
						</nav>
					<?php endif; ?>
					
				</div>
				<div class="header-search pull-right">
					
				</div>
			</div>			
		</header>
		
		<!-- /Header -->

		<!-- Begin Banner Top -->
		<jdoc:include type="modules" name="banner-top" style="banner" />
		<!-- End Banner Top -->
		
		<!-- Body -->
		<div class="body">
			<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">		
				<div class="row-fluid">
					<?php if ($this->countModules('position-8')) : ?>
						<!-- Begin Sidebar -->
						<div id="sidebar" class="span4">
							<div class="sidebar-nav">
								<jdoc:include type="modules" name="position-8" style="xhtml" />
							</div>
						</div>
						<!-- End Sidebar -->
					<?php endif; ?>
					<main id="content" role="main" class="<?php echo $span; ?>">
						<!-- Begin Content -->
						<jdoc:include type="modules" name="position-3" style="none" />
						<jdoc:include type="message" />
						<jdoc:include type="component" />
						<!-- End Content -->
					</main>
					<?php if ($this->countModules('position-7')) : ?>
						<div id="aside" class="span4">
							<!-- Begin Right Sidebar -->
							<jdoc:include type="modules" name="position-7" style="well" />
							<!-- End Right Sidebar -->
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<!-- /Body -->
		
		<!-- Begin Banner Bottom -->
		<jdoc:include type="modules" name="banner-bottom" style="banner" />
		<!-- End Banner Bottom -->			
		
	</div>
	<!-- /#Wrap -->
	
	<!-- Footer -->
	<?php require('footer.php'); ?>
	<!--/Footer -->
	<jdoc:include type="modules" name="debug" style="none" />

	<script type="text/javascript" language="javascript">
	$( function() {	
		$('#gmi-carousel-all').carouFredSel({
			responsive: true,
			//circular: false,
			//infinite: false,
			scroll: 1,
			prev: '#prev-all',
			next: '#next-all',
			pagination: "#pager-all",
			mousewheel: true,
			height: 'variable',
			items: {
				width: 400,
				height: 'variable',
			//	height: '30%',	//	optionally resize item-height
				visible: {
					min: 4,
					max: 6
				}
			}
		});
		$('#gmi-carousel-music').carouFredSel({
			responsive: true,
			//circular: false,
			//infinite: false,
			scroll: 1,
			prev: '#prev-music',
			next: '#next-music',
			pagination: "#pager-music",
			mousewheel: true,
			height: 'variable',
			items: {
				width: 400,
				height: 'variable',
			//	height: '30%',	//	optionally resize item-height
				visible: {
					min: 4,
					max: 6
				}
			}
		});	
		$('#gmi-carousel-shows').carouFredSel({
			responsive: true,
			//circular: false,
			//infinite: false,
			scroll: 1,
			prev: '#prev-shows',
			next: '#next-shows',
			pagination: "#pager-shows",
			mousewheel: true,
			height: 'variable',
			items: {
				width: 400,
				height: 'variable',
			//	height: '30%',	//	optionally resize item-height
				visible: {
					min: 4,
					max: 6
				}
			}
		});
		$('#gmi-carousel-videos').carouFredSel({
			responsive: true,
			//circular: false,
			//infinite: false,
			scroll: 1,
			prev: '#prev-videos',
			next: '#next-videos',
			pagination: "#pager-videos",
			mousewheel: true,
			height: 'variable',
			items: {
				width: 400,
				height: 'variable',
			//	height: '30%',	//	optionally resize item-height
				visible: {
					min: 4,
					max: 6
				}
			}
		});	
	});
	</script>
	<!-- Home - Main Slider - Scripts -->
	<script type='text/javascript' src='templates/gmi/js/seven/seven.min.js'></script>
	<script type='text/javascript' src='templates/gmi/js/seven/jquery.fitvids.js'></script>
	<script type='text/javascript' src='templates/gmi/js/seven/custom.js'></script>
</body>
</html>
