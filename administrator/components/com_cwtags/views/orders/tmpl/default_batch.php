<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_CWTAGS_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_CWTAGS_BATCH_TIP'); ?></p>
	<?php echo JHtml::_('batch.language');?>

	<?php if ($published >= 0) : ?>
		<?php echo JHtml::_('batch.item', 'com_cwtags');?>
	<?php endif; ?>

	<button type="submit" onclick="Joomla.submitbutton('cwtag.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-client-id').value='';document.id('batch-language-id').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
