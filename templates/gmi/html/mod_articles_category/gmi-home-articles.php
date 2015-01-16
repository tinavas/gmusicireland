<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
 
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
	
	function stripTags($text)
	{
		$text = strip_tags($text);
		$text = trim($text);

		return $text;
	}
?>
	
<div class="gmi-home-articles<?php echo $moduleclass_sfx; ?>">
	<?php if ($grouped) : ?>
	<ul>
		<?php foreach ($list as $group_name => $group) : ?>
		<li>
			<ul>			
				<?php foreach ($group as $item) : ?>
					<?php $image = json_decode($item->images); ?>
					<li>
						<?php
						if ($module->showtitle) {
							echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><div>&nbsp;</div><div>" . $item->title . "</div></" . $header_tag . ">";
						}
						?>
						<?php if ($params->get('link_titles') == 1) : ?>
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php  if (isset($image->image_intro) and !empty($image->image_intro)) : ?>
							<img src="<?php echo $image->image_intro; ?>" />
						<?php endif; ?>	
								<?php echo $item->title; ?>
							</a>
						
						<?php else : ?>
							<?php echo $item->title; ?>
						<?php endif; ?>
						
						<?php if ($item->displayHits) : ?>
							<span class="mod-articles-category-hits">
								(<?php echo $item->displayHits; ?>)
							</span>
						<?php endif; ?>
	
						<?php if ($params->get('show_author')) : ?>
							<span class="mod-articles-category-writtenby">
								<?php echo $item->displayAuthorName; ?>
							</span>
						<?php endif;?>
	
						<?php if ($item->displayCategoryTitle) : ?>
							<span class="mod-articles-category-category">
								(<?php echo $item->displayCategoryTitle; ?>)
							</span>
						<?php endif; ?>
	
						<?php if ($item->displayDate) : ?>
							<span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
						<?php endif; ?>
	
						<?php if ($params->get('show_introtext')) : ?>
							<p class="mod-articles-category-introtext">
								<?php echo $item->displayIntrotext; ?>
							</p>
						<?php endif; ?>
	
						<?php if ($params->get('show_readmore')) : ?>
							<p class="mod-articles-category-readmore">
								<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
									<?php if ($item->params->get('access-view') == false) : ?>
										<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
									<?php elseif ($readmore = $item->alternative_readmore) : ?>
										<?php echo $readmore; ?>
										<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
											<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
												<?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>
											<?php endif; ?>
									<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
										<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
									<?php else : ?>
										<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
										<?php echo JHtml::_('string.truncate', ($item->title), $params->get('readmore_limit')); ?>
									<?php endif; ?>
								</a>
							</p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</li>
		<?php endforeach; ?>
	</ul>
	
	<?php else : ?>		
	
		<?php foreach ($list as $item) :?>
			<?php
				$image = json_decode($item->images);				
				// Get Category image
				$db=JFactory::getDbo();
				$catid = $item->catid;
				$db->setQuery('select params from #__categories where id=' . $catid . ' LIMIT 1');
				$category=$db->loadResult();
				$cat_params = json_decode($category);
				$cat_image = $cat_params->image;
			?>
			
			<?php if ($count == 1 || $count % 2 != 0 ) : ?>
				<div class="row-fluid">
			<?php endif; ?>			
			
			<article class="span6">
				<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
					
					<?php if ($module->showtitle) : ?>				
						<?php echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><div>&nbsp;</div><div>" . $item->title . "</div></" . $header_tag . ">"; ?>
					<?php endif; ?>			
					
					<div class="content-list-img">
						<?php if ($params->get('link_titles') == 1) : ?>
							<?php echo $item->title; ?>
							<?php  if (isset($image->image_intro) and !empty($image->image_intro)) : ?>
								<img src="<?php echo $image->image_intro; ?>" />
							<?php endif; ?>
						<?php else : ?>					
							<?php  if (isset($image->image_intro) and !empty($image->image_intro)) : ?>
								<img src="<?php echo $image->image_intro; ?>" />
							<?php endif; ?>
						<?php endif; ?>
						
						<?php if ($item->displayCategoryTitle) : ?>						
							<?php if (isset($cat_params->image) and !empty($cat_params->image)) : ?>
								<div class="mod-articles-category-categoryimage">							
									<img src="/gmusicireland/<?php echo $cat_image; ?>" />
								</div>
							<?php else : ?>
								<div class="mod-articles-category-category">
									<?php echo stripTags($item->displayCategoryTitle); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					
					<?php if ($item->displayHits) : ?>
						<span class="mod-articles-category-hits">
							(<?php echo $item->displayHits; ?>)
						</span>
					<?php endif; ?>
		
					<?php if ($params->get('show_author')) : ?>
						<span class="mod-articles-category-writtenby">
							<?php echo $item->displayAuthorName; ?>
						</span>
					<?php endif;?>

					<?php if ($item->displayDate) : ?>
						<span class="mod-articles-category-date">
							<?php echo $item->displayDate; ?>
						</span>
					<?php endif; ?>
		
					<?php if ($params->get('show_introtext')) : ?>
						<div class="mod-articles-category-introtext">
							<div class="introtext-symbol-small"><span>&nbsp;</span></div>
							<div class="introtext-short"><p><?php echo $item->displayIntrotext; ?></p></div>							
						</div>
					<?php endif; ?>
		
					<?php if ($params->get('show_readmore')) : ?>
						<p class="mod-articles-category-readmore">
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php if ($item->params->get('access-view') == false) : ?>
									<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
								<?php elseif ($readmore = $item->alternative_readmore) : ?>
									<?php echo $readmore; ?>
									<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
								<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
									<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
								<?php else : ?>
									<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
									<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
								<?php endif; ?>
							</a>
						</p>
					<?php endif; ?>
					
				</a>
			</article>			
			
			<?php if ($count % 2 == 0 ) : ?>
				</div>
			<?php endif; ?>
			
			<?php $count++; ?>
			
		<?php endforeach; ?>			    

		
		
	<?php endif; ?>
</div>