<?php
/**
 * @package         Advanced Template Manager
 * @version         1.3.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
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
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$showthumbs = $this->config->show_thumbs;
$showcolors = $this->config->show_color;
if ($showcolors)
{
	require_once JPATH_LIBRARIES . '/joomla/form/fields/color.php';
	$colorfield = new JFormFieldColor;
	$script = "
		function setColor(id, el)
		{
			var f = document.getElementById('adminForm');
			f.setcolor.value = jQuery(el).val();
			listItemTask(id, 'styles.setcolor');
		}
	";
	JFactory::getDocument()->addScriptDeclaration($script);
}

JHtml::stylesheet('nnframework/style.min.css', false, true);

// Version check
require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
if ($this->config->show_update_notification)
{
	echo nnVersions::getInstance()->getMessage('advancedtemplates', '', '', 'component');
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_advancedtemplates'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<div class="clearfix"></div>

		<?php $cols = 8; ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="5">
						&#160;
					</th>
					<?php if ($showthumbs) : ?>
						<?php $cols++; ?>
						<th width="5">
							&#160;
						</th>
					<?php endif; ?>
					<?php if ($showcolors) : ?>
						<?php $cols++; ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'color', $listDirn, $listOrder, null, 'asc', 'ATP_COLOR', 'icon-color'); ?>
						</th>
					<?php endif; ?>
					<th width="5">
						&#160;
					</th>
					<th>
						<?php echo JHtml::_('searchtools.sort', 'COM_TEMPLATES_HEADING_STYLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'a.home', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JText::_('COM_TEMPLATES_HEADING_ASSIGNED'); ?>
					</th>
					<th width="10%" class="nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JCLIENT', 'a.client_id', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo $cols; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) :
					$canCreate = $user->authorise('core.create', 'com_templates');
					$canEdit = $user->authorise('core.edit', 'com_templates');
					$canChange = $user->authorise('core.edit.state', 'com_templates');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td width="1%" class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if ($showthumbs) : ?>
							<td class="center thumbnail-small">
								<?php echo JHtml::_('templates.thumb', $item->template, $item->client_id); ?>
							</td>
						<?php endif; ?>
						<?php if ($showcolors) : ?>
							<td class="center inlist">
								<?php
								$color = (isset($item->params->color) && $item->params->color) ? $color = str_replace('##', '#', $item->params->color) : 'none';
								$element = new SimpleXMLElement(
									'<field
										name="color_' . $i . '"
											type="color"
											control="simple"
											default=""
											colors="' . (isset($this->config->main_colors) ? $this->config->main_colors : '') . '"
											split="4"
											onchange="setColor(\'cb' . $i . '\', this)"
											/>'
								);
								$element->value = $color;
								$colorfield->setup($element, $color);
								echo $colorfield->__get('input');
								?>
							</td>
						<?php endif; ?>
						<td width="1%" class="center">
							<?php if ($this->preview && $item->client_id == '0') : ?>
								<a target="_blank" href="<?php echo JUri::root() . 'index.php?tp=1&templateStyle=' . (int) $item->id ?>" class="btn btn-micro">
									<i class="icon-eye-open hasTooltip" title="<?php echo JHtml::tooltipText(JText::_('COM_TEMPLATES_TEMPLATE_PREVIEW'), $item->title, 0); ?>"></i></a>
							<?php elseif ($item->client_id == '1') : ?>
								<i class="icon-eye-close disabled hasTooltip" title="<?php echo JHtml::tooltipText('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN'); ?>"></i>
							<?php
							else: ?>
								<i class="icon-eye-close disabled hasTooltip" title="<?php echo JHtml::tooltipText('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?>"></i>
							<?php endif; ?>
						</td>
						<td>
							<?php
							$title = $item->title;
							$title = str_ireplace($item->template . ' - ', '', $title);
							$title = '<span class="label label-info small">' . $item->template . '</span> ' . $this->escape($title);
							?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_advancedtemplates&task=style.edit&id=' . (int) $item->id); ?>">
									<?php echo $title; ?></a>
							<?php else : ?>
								<?php echo $title; ?>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php if ($item->home == '0' || $item->home == '1'): ?>
								<?php echo JHtml::_('jgrid.isdefault', $item->home != '0', $i, 'styles.', $canChange && $item->home != '1'); ?>
							<?php elseif ($canChange): ?>
								<a href="<?php echo JRoute::_('index.php?option=com_advancedtemplates&task=styles.unsetDefault&cid[]=' . $item->id . '&' . JSession::getFormToken() . '=1'); ?>">
									<?php echo JHtml::_('image', 'mod_languages/' . $item->image . '.gif', $item->language_title, array('title' => JText::sprintf('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title)), true); ?>
								</a>
							<?php
							else: ?>
								<?php echo JHtml::_('image', 'mod_languages/' . $item->image . '.gif', $item->language_title, array('title' => $item->language_title), true); ?>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php if ($item->assigned > 0) : ?>
								<i class="icon-ok tip hasTooltip" title="<?php echo JHtml::tooltipText(JText::plural('COM_TEMPLATES_ASSIGNED', $item->assigned), '', 0); ?>"></i>
							<?php else : ?>
								&#160;
							<?php endif; ?>
						</td>
						<td class="small">
							<?php echo $item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>
						</td>
						<td class="center">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="setcolor" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php if ($this->config->show_switch) : ?>
	<div style="text-align:right">
		<a href="<?php echo JRoute::_('index.php?option=com_templates&force=1'); ?>"><?php echo JText::_('ATP_SWITCH_TO_CORE'); ?></a>
	</div>
<?php endif; ?>
<?php
// PRO Check
require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';
echo nnLicenses::getInstance()->getMessage('ADVANCED_TEMPLATE_MANAGER', 0);

// Copyright
echo nnVersions::getInstance()->getCopyright('ADVANCED_TEMPLATE_MANAGER', '', 0, 'advancedtemplates', 'component');
