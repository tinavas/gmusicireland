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

<div id="Flexslidert" class="slider_content <?php echo $moduleclass_sfx; ?> flexslider">
	<ul class="slides">
	<?php foreach ($list as $item) : ?>
		<li>
			<article class="wgr">
				<div class="featured_thumb">
					<a class="first_A" target="<?php echo $openTarget; ?>" title="<?php echo $item->title; ?>" href="<?php echo $item->link;?>" rel="bookmark">
						<img src="<?php echo $item->thumbnail; ?>" alt="<?php echo $item->title; ?>" title="<?php echo $item->title; ?>" />
						<span class="thumb-icon small">
							<i class="<?php
								$cat_title = $item->category_title;
								switch ($cat_title) {
									case "Artists" : echo "fa fa-users"; break;
									case "News" : echo "fa fa-newspaper-o"; break;
									case "Shows" : echo "fa fa-video-camera fa-3x"; break;
									case "Videos" : echo "fa fa-play-circle"; break;
								}
								?>">
							</i>
						</span>						
					</a>
				</div>
				<!--div class="tt_review">
					<div class="tt_point">
						<div class="tt_val">9.3</div><span class="tt_title">Excelent</span>
					</div>
				</div-->
				<!-- Author & Date -->
				<?php if( $showAuthor || $showDate || $show_category_name || $show_intro || $showTitle ): ?>
					<div class="wgr-details">						
						<div class="wgr-introtext">
						<!-- Introtext -->
							<?php if( $show_intro || $showTitle ) : ?>							
								<?php if( $showTitle ): ?>
									<a target="<?php echo $openTarget; ?>" title="<?php echo $item->title; ?>" href="<?php echo $item->link;?>" rel="bookmark">
										<h3><?php echo $item->title_cut; ?></h3>
									</a>
								<?php endif; ?>
								<?php if( $show_intro ) : ?>
									<div class="bt-introtext">
										<?php echo $item->description; ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						</div>
						<?php if( $showAuthor || $showDate ): ?>
							<div class="details">
								<span class="s_category">
									<?php if( $showDate ): ?>
										<a class="date_c" href="#"><i class="icon-calendar mi"></i>&nbsp;<?php echo JText::sprintf('BT_CREATEDON', $item->date); ?></a>
									<?php endif; ?>
									<?php if( $showAuthor ): ?>								
										<a href="#"><i class="icon-user mi"></i><?php echo JText::sprintf('BT_CREATEDBY', JHtml::_('link',JRoute::_($item->authorLink),$item->author)); ?></a>								
									<?php endif; ?>
								</span>
							</div>
						<?php endif; ?>
						<?php if($show_category_name ): ?>
							<span class="more_meta">
								<a target="<?php echo $openTarget; ?>"
							title="<?php echo $item->category_title; ?>"
							href="<?php echo $item->categoryLink;?>"><!--i class="icon-message mi"></i--><?php echo $item->category_title; ?></a>
							</span>								
						<?php endif; ?>
					</div>
				<?php endif; ?>	
			</article>
		</li>
	<?php endforeach; ?>
	</ul>
	<!--end ul foreeach	-->		
</div>
<!--end bt-container -->
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