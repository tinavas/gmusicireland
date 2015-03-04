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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
$user = JFactory::getUser();

$script = "
Joomla.submitbutton = function(task)
{
	if (task == 'style.cancel' || document.formvalidator.isValid(document.id('style-form'))) {
		Joomla.submitform(task, document.getElementById('style-form'));
	}
};";
if (JFactory::getUser()->authorise('core.admin'))
{
	$script .= "
	jQuery(document).ready(function()
	{
		// add alert on remove assignment buttons
		jQuery('button.nn_remove_assignment').click(function()
		{
			if(confirm('" . str_replace('<br />', '\n', addslashes(JText::_('ATP_DISABLE_ASSIGNMENT'))) . "')) {
				jQuery('div#toolbar-options button').click();
			}
		});
	});";
}

JFactory::getDocument()->addScriptDeclaration($script);
JFactory::getDocument()->addScriptVersion(JURI::root(true) . '/media/nnframework/js/script.min.js');
JFactory::getDocument()->addScriptVersion(JURI::root(true) . '/media/nnframework/js/toggler.min.js');
?>

<form action="<?php echo JRoute::_('index.php?option=com_advancedtemplates&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="style-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS', true)); ?>

		<div class="row-fluid">
			<div class="span9">
				<h3>
					<?php echo JText::_($this->item->template); ?>
				</h3>
				<div class="info-labels">
					<span class="label hasTooltip" title="<?php echo JHtml::tooltipText('COM_TEMPLATES_FIELD_CLIENT_LABEL'); ?>">
						<?php echo $this->item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>
					</span>
				</div>
				<div>
					<p><?php echo JText::_($this->item->xml->description); ?></p>
					<?php
					$this->fieldset = 'description';
					$description = JLayoutHelper::render('joomla.edit.fieldset', $this);
					?>
					<?php if ($description) : ?>
						<p class="readmore">
							<a href="#" onclick="jQuery('.nav-tabs a[href=#description]').tab('show');">
								<?php echo JText::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
							</a>
						</p>
					<?php endif; ?>
				</div>
				<?php
				$this->fieldset = 'basic';
				$html = JLayoutHelper::render('joomla.edit.fieldset', $this);
				echo $html ? '<hr />' . $html : '';
				?>
			</div>
			<div class="span3">
				<?php
				// Set main fields.
				$this->fields = array(
					'home',
					'client_id',
					'template'
				);
				?>
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				<?php if ($this->config->show_color) : ?>
					<fieldset class="form-vertical">
						<div class="control-group">
							<div class="control-label">
								<label id="advancedparams_color-lbl" for="advancedparams_color" class="hasTooltip" title=""
									data-original-title="<strong><?php echo addslashes(JText::_('ATP_COLOR')); ?></strong><br /><?php echo addslashes(JText::_('ATP_COLOR_DESC')); ?>">
									<?php echo JText::_('ATP_COLOR'); ?>
								</label>
							</div>
							<div class="controls">
								<?php
								include_once(JPATH_LIBRARIES . '/joomla/form/fields/color.php');
								$colorfield = new JFormFieldColor;

								$color = (isset($this->item->advancedparams['color']) && $this->item->advancedparams['color']) ? str_replace('##', '#', $this->item->advancedparams['color']) : 'none';
								$element = new SimpleXMLElement(
									'<field
											name="advancedparams[color]"
											id="advancedparams_color"
											type="color"
											control="simple"
											default=""
											colors="' . (isset($this->config->main_colors) ? $this->config->main_colors : '') . '"
											split="4"
											/>'
								);
								$element->value = $color;
								$colorfield->setup($element, $color);
								echo $colorfield->__get('input');
								?>
							</div>
						</div>
					</fieldset>
			<?php endif; ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if ($description) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', JText::_('JGLOBAL_FIELDSET_DESCRIPTION', true)); ?>
			<?php echo $description; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php
		$this->fieldsets = array();
		$this->ignore_fieldsets = array('basic');
		echo JLayoutHelper::render('joomla.edit.params', $this);
		?>

		<?php if ($user->authorise('core.edit', 'com_menu') && $this->item->client_id == 0 && $this->canDo->get('core.edit.state')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'assignment', JText::_('ATP_ASSIGNMENTS', true)); ?>
			<?php echo $this->loadTemplate('assignment'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php if ($this->config->show_switch) : ?>
	<div style="text-align:right"><a href="<?php echo JRoute::_('index.php?option=com_templates&force=1&task=style.edit&id=' . (int) $this->item->id); ?>"><?php echo JText::_('ATP_SWITCH_TO_CORE'); ?></a></div>
<?php endif; ?>
