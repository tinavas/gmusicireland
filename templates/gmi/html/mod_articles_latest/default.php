<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php
	$news_count = $params->get('count');
	$count = 0;
?>

<?php foreach ($list as $item) :  ?>
	<?php
		$count++;
		$images = json_decode($item->images);
		$news_link = $item->link;
		$news_introtext = substr($item->introtext, 0, 117);
		$news_title = $item->title;		
	?>
	<li>		
		<a href="<?php echo $news_link; ?>">
			<?php if (isset($images->image_intro) and !empty($images->image_intro)) : ?>
				<img src="<?php echo $images->image_intro; ?>" />
			<?php endif; ?>		
			<?php echo $news_introtext.'...';?>
			<span class="publish-date"><?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC3')); ?></span>
		</a>		
	</li>
	<?php if ($count != $news_count) : ?>
		<hr />
	<?php endif ?>
	
<?php endforeach; ?>
</ul>
<?php
$zone = "news-readmore";
$modules =& JModuleHelper::getModules($zone);
foreach ($modules as $module){
	echo JModuleHelper::renderModule($module);
}
?>

<!--
images{
	"image_intro":"",
	"float_intro":"",
	"image_intro_alt":"",
	"image_intro_caption":"",
	"image_fulltext":"",
	"float_fulltext":"",
	"image_fulltext_alt":"",
	"image_fulltext_caption":""
}



json_decode()->http://www.php.net/manual/en/function.json-decode.php. It converts that string into a key-value array. So all the data gets reachable:
$pictures = json_decode('
				{
				"image_intro":"images\/sampledata\/articles\/special_guest_chris.jpg",
				"float_intro":"",
				"image_intro_alt":"Special Guest - Chris Itaire",
				"image_intro_caption":"",
				"image_fulltext":"images\/sampledata\/articles\/special_guest_chris.jpg",
				"float_fulltext":"",
				"image_fulltext_alt":"",
				"image_fulltext_caption":""
				}',
			true);

echo $pictures['image_intro']; //gives images/sampledata/articles/special_guest_chris.jpg

<span itemprop="name">
				<?php #echo $news_title;  ?>				
			</span>
-->