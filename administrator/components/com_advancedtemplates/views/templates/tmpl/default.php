<?php
/**
 * @package         Advanced Template Manager
 * @version         1.1.7
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

// Version check
require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
if ($this->config->show_update_notification)
{
	echo NNVersions::getInstance()->getMessage('advancedtemplates', '', '', 'component');
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_advancedtemplates'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<div class="clearfix"></div>

		<table class="table table-striped" id="template-mgr">
			<thead>
				<tr>
					<th width="226" class="col1template hidden-phone">
						&#160;
					</th>
					<th width="5">
						&#160;
					</th>
					<th>
						<?php echo JHtml::_('searchtools.sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JHtml::_('searchtools.sort', 'JCLIENT', 'a.client_id', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('JVERSION'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('JDATE'); ?>
					</th>
					<th width="25%">
						<?php echo JText::_('JAUTHOR'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center hidden-phone">
							<?php echo JHtml::_('templates.thumb', $item->element, $item->client_id); ?>
						</td>
						<td width="1%" class="center">
							<?php if ($this->preview && $item->client_id == '0') : ?>
								<a target="_blank" href="<?php echo JUri::root() . 'index.php?tp=1&template=' . $item->element; ?>" class="btn btn-micro">
									<i class="icon-eye-open hasTooltip" title="<?php echo JHtml::tooltipText(JText::_('COM_TEMPLATES_TEMPLATE_PREVIEW'), $item->name, 0); ?>"></i></a>
							<?php elseif ($item->client_id == '1') : ?>
								<i class="icon-eye-close disabled hasTooltip" title="<?php echo JHtml::tooltipText('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN'); ?>"></i>
							<?php
							else: ?>
								<i class="icon-eye-close disabled hasTooltip" title="<?php echo JHtml::tooltipText('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?>"></i>
							<?php endif; ?>
						</td>
						<td class="template-name">
							<a href="<?php echo JRoute::_('index.php?option=com_advancedtemplates&view=template&id=' . (int) $item->extension_id . '&file=' . $this->file); ?>">
								<?php echo $item->name; ?>
							</a>
						</td>
						<td class="small center">
							<?php echo $item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>
						</td>
						<td class="small center">
							<?php echo $this->escape($item->xmldata->get('version')); ?>
						</td>
						<td class="small center">
							<?php echo $this->escape($item->xmldata->get('creationDate')); ?>
						</td>
						<td>
							<?php if ($author = $item->xmldata->get('author')) : ?>
								<p><?php echo $this->escape($author); ?></p>
							<?php else : ?>
								&mdash;
							<?php endif; ?>
							<?php if ($email = $item->xmldata->get('authorEmail')) : ?>
								<p><?php echo $this->escape($email); ?></p>
							<?php endif; ?>
							<?php if ($url = $item->xmldata->get('authorUrl')) : ?>
								<p><a href="<?php echo $this->escape($url); ?>">
										<?php echo $this->escape($url); ?></a></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php if ($this->config->show_switch) : ?>
	<div style="text-align:right">
		<a href="<?php echo JRoute::_('index.php?option=com_templates&force=1'); ?>"><?php echo JText::_('AMM_SWITCH_TO_CORE'); ?></a>
	</div>
<?php endif; ?>
<?php
// PRO Check
require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';
echo NNLicenses::getInstance()->getMessage('ADVANCED_TEMPLATE_MANAGER', 0);

// Copyright
echo NNVersions::getInstance()->getCopyright('ADVANCED_TEMPLATE_MANAGER', '', 10307, 'advancedtemplates', 'component');
