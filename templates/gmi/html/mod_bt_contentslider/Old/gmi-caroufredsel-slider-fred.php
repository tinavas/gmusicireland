<?php
/**
 * @package 	mod_bt_contentslider - BT ContentSlider Module
 * @version		1.4
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Module Class
$module_class = htmlspecialchars($params->get('moduleclass_sfx'));

// Module Parameters
$gmi_params = json_decode($module->params);  

// Article count set in (Filtering Options -> Count)
$article_count = $params->get('count');
$count = 1;

// Header tag set in the admin panel for that module (Advanced -> Header Tag)
// If not set 'H3' will be assigned.
if (isset($gmi_params->header_tag)) {  
	$header_tag = htmlspecialchars($gmi_params->header_tag);  
} else {  
	$header_tag = "h3";  
}

// Header class set in the admin panel for that module (Advanced -> Header Class)	
if (isset($gmi_params->header_class)) {  
	$header_class = htmlspecialchars($gmi_params->header_class);  
} else {  
	$header_class = "";  
}


if($modal){JHTML::_('behavior.modal');}
$document = JFactory::getDocument();
if(count($list)>0){?>

<!-- MODULE TITLE -->
<?php if ($module->showtitle) : ?>				
	<?php echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><div>&nbsp;</div><div>" . $module->title . "</div></" . $header_tag . ">"; ?>
<?php endif; ?>
	
<div class="list_carousel responsive">
			<ul id="foo4">
				<li>
					<div>header</div>
					<div>thumbnali</div>
					<div>desc</div><div>header</div>
					<div>thumbnali</div>
					<div>desc</div>
				</li>
				<li>a</li>
				<li>r</li>
				<li>o</li>
				<li>u</li>
				<li>F</li>
				<li>r</li>
				<li>e</li>
				<li>d</li>
				<li>S</li>
				<li>e</li>
				<li>l</li>
				<li> </li>
			</ul>
			<div class="clearfix"></div>
				<a id="prev2" class="prev" href="#">&lt;</a>
				<a id="next2" class="next" href="#">&gt;</a>
				<div id="pager2" class="pager"></div>
		</div>		

<!--end bt-container -->
<div style="clear: both;"></div>
<!--script type="text/javascript">	
	if(typeof(btcModuleIds)=='undefined'){var btcModuleIds = new Array();var btcModuleOpts = new Array();}
	btcModuleIds.push(<?php echo $module->id; ?>);
	btcModuleOpts.push({
			slideEasing : '<?php echo $slideEasing; ?>',
			fadeEasing : '<?php echo $slideEasing; ?>',
			effect: '<?php echo $effect; ?>',
			preloadImage: '<?php echo $preloadImg; ?>',
			generatePagination: <?php echo $paging ?>,
			play: <?php echo $play; ?>,						
			hoverPause: <?php echo $hoverPause; ?>,	
			slideSpeed : <?php echo $duration; ?>,
			autoHeight:<?php echo $autoHeight ?>,
			fadeSpeed : <?php echo $fadeSpeed ?>,
			equalHeight:<?php echo $equalHeight; ?>,
			width: <?php echo $moduleWidth=='auto'? "'auto'":$params->get( 'module_width', 0 ); ?>,
			height: <?php echo $moduleHeight=='auto'? "'auto'":$params->get( 'module_height', 0 ); ?>,
			pause: 100,
			preload: true,
			paginationClass: '<?php echo $butlet==1 ? 'bt_handles': 'bt_handles_num' ?>',
			generateNextPrev:false,
			prependPagination:true,
			touchScreen:<?php echo $touchScreen ?>
	});
</script--><!-- include jQuery + carouFredSel plugin -->

<?php 
	// set position for bullet
	if($butlet) {
		$nav_top = (-1)*(int)$params->get( 'navigation_top', 0 );
		$nav_right = (-1)*(int)$params->get( 'navigation_right', 0 )+5;
		if(trim($params->get('content_title'))) $nav_top += 13;
		$document->addStyleDeclaration(
			$modid . ' ' . ($butlet == 1 ? '.bt_handles' : '.bt_handles_num') . '{'.
				'top: ' . $nav_top .'px !important;'.
				'right: ' . $nav_right . 'px !important'.
			'}'
		);

	}
	
}
else
{ 
	echo '<div>No result...</div>'; 
}
?> 

<!-- fire plugin onDocumentReady -->
<script type="text/javascript" language="javascript">
$(function() {
	//	Responsive layout, resizing the items
	$('#foo4').carouFredSel({
		auto: true,
		width: '100%',
		responsive: true,
		prev: '#prev2',
		next: '#next2',
		pagination: "#pager2",
		mousewheel: true,
		scroll: 2,
		items: {
			width: 200,
			//	height: '30%',	//	optionally resize item-height
			visible: {
				min: 2,
				max: 6
			}
		}
	});
});

</script>
