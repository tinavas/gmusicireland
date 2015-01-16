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
	<ul id="gmi-carousel-all">
		<?php foreach ($list as $item) : ?>
		<li>
			<a class="item-link" target="<?php echo $openTarget; ?>" title="<?php echo $item->title; ?>" href="<?php echo $item->link;?>">
			
				<div class="list_carousel-item-container">				

					<div class="a">
					<!-- Thumbnail -->
					<?php if( $item->thumbnail && $align_image == "center" ): ?>
						<div class="list_carousel-center">					
							<img src="<?php echo $item->thumbnail; ?>" alt="<?php echo $item->title; ?>" title="<?php echo $item->title; ?>" />
							<span></span>
						</div>
					<?php endif ; ?>
					</div>
					
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
					</div><!-- /.b	-->				
				</div>				
			</a>
		</li>		
		<!--end ul foreeach	-->
		<?php endforeach; ?>
			</ul>
			<div class="clearfix"></div>
				<a id="prev-all" class="prev" href="#"><div class="prev-arrow"></div></a>
				<a id="next-all" class="next" href="#"><div class="next-arrow"></div></a>
				<!--div id="pager-all" class="pager"></div-->
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
</script-->
<?php 
}
else
{ 
	echo '<div>No result...</div>'; 
}
?>