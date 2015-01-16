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
if($modal){JHTML::_('behavior.modal');}
$document = JFactory::getDocument();
if(count($list)>0){?>

<div id="list_carousel<?php echo $module->id; ?>" class="responsive list_carousel<?php echo $moduleclass_sfx? $params->get('moduleclass_sfx'):''; ?>">

	<?php 
		$add_style = "";
		if( trim($params->get('content_title')) ){
		$add_style= "border: 1px solid #CFCFCF;padding:10px 0px;";
		?>
		<h3 style="width:<?php echo $moduleWidth;?>">
			<?php if($params->get('content_link')) {?>
				<a href="<?php echo $params->get('content_link');?>"><span><?php echo $params->get('content_title') ?> </span></a>
			<?php } else { ?>
				<span><?php echo $params->get('content_title') ?> </span>                    
			<?php   }?>
		</h3>	
	<?php } ?>
	<ul id="foo4" style="border: 1px solid blue;width:<?php echo $moduleWidth.";".$add_style;?>">
		<?php foreach ($list as $item) : ?>
		<li>
			<?php if( $item->thumbnail && $align_image != "center"): ?>
				<a target="<?php echo $openTarget; ?>" class="bt-image-link<?php echo $modal? ' modal':''?>" title="<?php echo $item->title;?>" href="<?php echo $modal?$item->mainImage:$item->link;?>">
				  <img <?php echo $imgClass ?>  src="<?php echo $item->thumbnail; ?>" alt="<?php echo $item->title?>"  style="width:<?php echo $thumbWidth ;?>px; float:<?php echo $align_image;?>;margin-<?php echo $align_image=="left"? "right":"left";?>:5px" title="<?php echo $item->title?>" />
				</a> 
			<?php endif ; ?>
			
			<!-- Category -->
			<?php if($show_category_name ): ?>
				<?php if($show_category_name_as_link) : ?>
					<div class="list_carousel-category">
						<a class="bt-category" target="<?php echo $openTarget; ?>"
							title="<?php echo $item->category_title; ?>"
							href="<?php echo $item->categoryLink;?>"> <?php echo $item->category_title; ?>
						</a>
					</div>
				<?php else :?>
					<div class="list_carousel-category">
						<span class="list_carousel-category"> <?php echo $item->category_title; ?> </span>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<!-- Title -->
			<?php if( $showTitle ): ?>
				<div class="list_carousel-title">
					<a class="bt-title" target="<?php echo $openTarget; ?>"
						title="<?php echo $item->title; ?>"
						href="<?php echo $item->link;?>">
						<?php echo $item->title_cut; ?>
					</a>
				</div>
			<?php endif; ?>
			
			<!-- Thumbnail -->
			<?php if( $item->thumbnail && $align_image == "center" ): ?>
				<div class="list_carousel-center">
					<a target="<?php echo $openTarget; ?>"
						class="list_carousel-image-link<?php echo $modal? ' modal':''?>"
						title="<?php echo $item->title;?>" href="<?php echo $modal?$item->mainImage:$item->link;?>">
						<img <?php echo $imgClass ?> src="<?php echo $item->thumbnail; ?>" alt="<?php echo $item->title?>"  style="width:<?php #echo $thumbWidth ;?>px;" title="<?php echo $item->title?>" />
					</a>
				</div>
			<?php endif ; ?>
			
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

			<!-- Readmore -->
			<?php if( $showReadmore ) : ?>
			<p class="readmore">
				<a target="<?php echo $openTarget; ?>"
					title="<?php echo $item->title;?>"
					href="<?php echo $item->link;?>"> <?php echo JText::_('READ_MORE');?>
				</a>
			</p>
			<?php endif; ?>
		</li>		
		<!--end ul foreeach	-->
		<?php endforeach; ?>
	</ul>
	
	<div class="clearfix"></div>
	
	<?php if( $next_back ) : ?>
		<a id="prev2" class="prev" href="#">&lt;</a>
		<a id="next2" class="next" href="#">&gt;</a>
	<?php endif; ?>
	
	<?php if( $butlet == 1 ) : ?>
		<div id="pager2" class="pager"></div>
	<?php endif; ?>
	
	<?php if( $butlet == 2 ) : ?>
		<div id="pager2" class="pager"></div>
	<?php endif; ?>
	
</div>

<div class="clearfix"></div>
<div class="clearfix"></div>
<div class="clearfix"></div>
<div class="clearfix"></div>

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

<?php
function noPx($string){
	$pattern = '/px/';
	$replacement = '';
	$string =  preg_replace($pattern, $replacement, $string);
	return $string;
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
		scroll: <?php echo $itemsPerRow; ?>,
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
