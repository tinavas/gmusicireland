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
	
<div id="list_carousel<?php echo $module->id; ?>" class="responsive list_carousel <?php echo $moduleclass_sfx? $params->get('moduleclass_sfx'):''; ?>">

	<ul id="foo4" style="width:<?php echo $moduleWidth.";"?>">
		<?php foreach ($list as $item) : ?>
		<li>
			<a class="item-link" target="<?php echo $openTarget; ?>" title="<?php echo $item->title; ?>" href="<?php echo $item->link;?>">
			
				<div class="list_carousel-item-container">				

					<!-- Thumbnail -->
					<?php if( $item->thumbnail && $align_image == "center" ): ?>
						<div class="list_carousel-center">					
							<img src="<?php echo $item->thumbnail; ?>" alt="<?php echo $item->title; ?>" title="<?php echo $item->title; ?>" />
							<span></span>
						</div>
					<?php endif ; ?>
					<div class="b">
						<!-- Author & Date -->
						<?php if( $showAuthor || $showDate ): ?>
							<div class="list_carousel-extra">
								<?php if( $showAuthor ): ?>
									<span class="list_carousel-author">
										<?php echo JText::sprintf('BT_CREATEDBY', JHtml::_('link',JRoute::_($item->authorLink),$item->author)); ?>
									</span>
								<?php endif; ?>
								<?php if( $showDate ): ?>
									<span class="list_carousel-date">
										<?php echo JText::sprintf('BT_CREATEDON', $item->date); ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<!-- Introtext -->
						<?php if( $show_intro ): ?>
							<div class="list_carousel-introtext">
								<?php echo $item->description; ?>
							</div>
						<?php endif; ?>
						<!-- Title -->
						<?php if( $showTitle ): ?>
							<div class="list_carousel-title">
								<?php echo $item->title_cut; ?>
							</div>
						<?php endif; ?>
						<!-- Category -->
						<?php if($show_category_name ): ?>				
							<div class="list_carousel-category">
								<?php echo $item->category_title; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>				
			</a>
		</li>		
		<!--end ul foreeach	-->
		<?php endforeach; ?>
	</ul>
	
	<div class="clearfix"></div>
	
	<?php if( $next_back ) : ?>
		<a id="prev2" class="prev" href="#"><div class="prev-arrow"></div></a>
		<a id="next2" class="next" href="#"><div class="next-arrow"></div></a>
	<?php endif; ?>
	
	<?php if( $butlet == 1 ) : ?>
		<div id="pager2" class="pager"></div>
	<?php endif; ?>
	
	<?php if( $butlet == 2 ) : ?>
		<div id="pager2" class="pager"></div>
	<?php endif; ?>
	
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
	// set responsive for mobile device
	if($moduleWidth=='auto'){
	$document->addStyleDeclaration(
		'
		@media screen and (max-width: 480px){.bt-cs .bt-row{width:100%!important;}}'
	);
	}
}
else
{ 
	echo '<div>No result...</div>'; 
}
?> 

<script type="text/javascript" language="javascript">

$(function() {
	var mod_width = $('#foo4').width();
	var num_items = <?php echo $itemsPerRow; ?>;
	var item_width = Math.round(mod_width / num_items) + 1;
	
	//	Responsive layout, resizing the items
	$('#foo4').carouFredSel({		
		auto: <?php	if( $auto_start == 0 ){ echo 'false'; } elseif( $auto_start == 1 ){	echo 'true'; } ?>,
		responsive: true,
		prev: '#prev2',
		next: '#next2',
		pagination: "#pager2",
		mousewheel: true,
		scroll: <?php echo $itemsPerRow; ?> -1,
		height: <?php if( $autoHeight == 0 && $moduleHeight != 'auto' ) { echo noPx( $moduleHeight ); } elseif( $autoHeight == 0 && $moduleHeight == 'auto' ){	echo "'auto'"; } elseif( $autoHeight == 1 ) { echo "'auto'"; } ?>,
		items: {
			width: item_width,
			//	height: '30%',	//	optionally resize item-height
			visible: {
				min: 2,
				max: 6
			}
		}
	});
});

</script>
