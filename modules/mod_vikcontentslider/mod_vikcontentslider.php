<?php  
/**------------------------------------------------------------------------
 * mod_VikContentSlider
 * ------------------------------------------------------------------------
 * author    Valentina Arras - Extensionsforjoomla.com
 * copyright Copyright (C) 2014 extensionsforjoomla.com. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.extensionsforjoomla.com
 * Technical Support:  templates@extensionsforjoomla.com
 * ------------------------------------------------------------------------
*/

defined('_JEXEC') or die('Restricted Area'); 
//jimport( 'joomla.methods' );
//JHTML::_('behavior.mootools');

$document = & JFactory :: getDocument();
JHtml::_('stylesheet', JURI::root().'modules/mod_vikcontentslider/src/mod_vikcontentslider.css', false, true, false, false);
if(intval($params->get('loadjq')) == 1 ) {
	JHtml::_('jquery.framework', true, true);
	JHtml::_('script', JURI::root().'modules/mod_vikcontentslider/src/jquery.js', false, true, false, false);
}
JHtml::_('script', JURI::root().'modules/mod_vikcontentslider/src/effects.js', false, true, false, false);
//JHtml::_('script', JURI::root().'modules/mod_vikcontentslider/src/jquery.bxslider.min.js', false, true, false, false);
JHtml::_('script', JURI::root().'modules/mod_vikcontentslider/src/modernizr.custom.js', false, true, false, false);

$timeback = $params->get('timebackground');
$css_speed = ".vikcs-slide-fromright  .vikcs-img-bckground { 
-webkit-animation: fromRightAnim2 ".$timeback."s ease-in 0s both;
-moz-animation: fromRightAnim2 ".$timeback."s ease-in 0s both;
-o-animation: fromRightAnim2 ".$timeback."s ease-in 0s both;
-ms-animation: fromRightAnim2 ".$timeback."s ease-in 0s both;
animation: fromRightAnim2 ".$timeback."s ease-in 0s both;}";

$document->addStyleDeclaration($css_speed);

$viksliderid = rand(1, 17);
$width = $params->get('width');
$height = $params->get('height');
$wwidth = (!empty($width) ? "width=\"".$width."\"" : "");
$wheight = (!empty($height) ? "height=\"".$height."\"" : "");
$stwidth = (!empty($width) ? "width: ".$width.(substr($width, -1) != "%" ? "px" : "%")."; " : "");
$stheight = (!empty($height) ? "height: ".$height.(substr($height, -1) != "%" ? "px" : "%")."; " : "");

$autoplay = $params->get('autoplay');
$interval = $params->get('interval');
$navigation = $params->get('navigation');
$dotsnav = $params->get('dotsnav');
$readmtext = $params->get('readmoretext');
$textbackgr = $params->get('textbackground');
$wnext = "";
$wprev = "";


if (intval($navigation) == 1) {
	$navenable=true;
}
if (intval($autoplay) == 1) {
	$autoplaygo='1';
} else {
	$autoplaygo='0';
}
if (intval($textbackgr) == 1) {
	$textbackval=" bckgr-text";
}

/*
$decl="jQuery.noConflict();\n";
$decl.="jQuery(document).ready(function(){		
		jQuery('#vikcs-slider').cslider({
			autoplay : $autoplaygo,
			interval : $interval
		});";
$decl.="var altezza = jQuery('.vikcs-img-bckground').height();
		jQuery('.vikcs-slider').css('height', altezza);
		jQuery(window).resize(function() {
			var altezza = jQuery('.vikcs-img-bckground').height();
			jQuery('.vikcs-slider').css('height', altezza);
		});
		";

if (intval($dotsnav) == 0) {
	$decl.="\n"."jQuery('.vikcs-slide-dots').addClass('dothide');";
} 
$decl.="\n"."});";

$document->addScriptDeclaration($decl);
*/

//$css_string =".vikcs-slider {height: ".$height."px;}";
//$document->addStyleDeclaration($css_string);

$first_height = 0;

for($v = 1; $v <= 10; $v++) {
	$getslide = $params->get('vikcontentsliderback_'.$v);
	$getreadmore = $params->get('readmore_'.$v);
	$titleslide = $params->get('title_'.$v);
	$textslide = $params->get('text_'.$v);
	$getimgslideid = $params->get('vikcontentsliderimg_'.$v);
	if (!empty($getimgslideid) && file_exists('./images/vikcslider/'.$getimgslideid)) {
		$getimgon = "<img class=\"vikcs-img\" src=\"".JURI::root().'images/vikcslider/'.$getimgslideid."\" border=\"0\"/>";
	} else {
		$getimgon = "";
	}
	if (!empty($getslide) && file_exists('./images/vikcslider/'.$getslide)) {
		$img_size = @getimagesize('./images/vikcslider/'.$getslide);
		if( $img_size ) {
			if (!empty($getreadmore)) {
				$compslide="<div id=\"vikcs-container\"><img class=\"vikcs-img-bckground\" src=".JURI::root().'images/vikcslider/'.$getslide." /><div class=\"vikcs-texts\">";
				if(!empty($titleslide)) {
					$compslide.="<h2>".$titleslide."</h2>";
				}
				if(!empty($textslide)) {
					$compslide.="<p>".$textslide."</p>";
				}	
				$compslide.="<span class=\"vikcs-link\"><a href=\"".$getreadmore."\">".$readmtext."</a></span>".$getimgon."</div></div>";
				$arrslide[]=$compslide;
			} else {
				$compslide="<div id=\"vikcs-container\"><img class=\"vikcs-img-bckground\" src=".JURI::root().'images/vikcslider/'.$getslide." /><div class=\"vikcs-texts\">";
				if(!empty($titleslide)) {
					$compslide.="<h2>".$titleslide."</h2>";
				}
				if(!empty($textslide)) {
					$compslide.="<p>".$textslide."</p>";
				}	
				$compslide.=$getimgon."</div></div>";
				$arrslide[]=$compslide;
			}

			if( $first_height == 0 ) {
				$first_height = $img_size[1]; // height
			}
		} 
			
	}
} 

?>

<div id="vikcs-slider" class="vikcs-slider<?php echo $textbackval; ?>">
    <?php
    if (is_array($arrslide)) {
		foreach($arrslide as $vsl) {
			echo "<div class=\"vikcs-slide\">";
			echo $vsl;
			echo "</div>";
		}
	}
   ?>

	<?php
	if ($navigation) { ?>
	<nav class="vikcs-slide-arrows">
		<span class="vikcs-slide-arrows-prev"></span>
		<span class="vikcs-slide-arrows-next"></span>
	</nav>
	<?php } ?>
</div>

<script>
jQuery.noConflict();
jQuery(document).ready(function(){		
	jQuery('#vikcs-slider').cslider({
		autoplay : <?php echo $autoplaygo; ?>,
		interval : <?php echo $interval; ?>
	});

	var altezza = jQuery('.vikcs-img-bckground').height();
	if( altezza > 0 ) {
		jQuery('.vikcs-slider').css('height', altezza);
	}

	// for SAFARI reload action
	jQuery('.vikcs-img-bckground').first().on('load', function(e){
		var altezza = jQuery('.vikcs-img-bckground').height();
		jQuery('.vikcs-slider').css('height', altezza);
	});

	jQuery(window).resize(function() {
		var altezza = jQuery('.vikcs-img-bckground').height();
		jQuery('.vikcs-slider').css('height', altezza);
	});

});
</script>
	