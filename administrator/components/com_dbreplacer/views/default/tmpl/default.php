<?php
/**
 * DB Replacer Default View Template
 *
 * @package         DB Replacer
 * @version         3.3.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::_('jquery.framework');

/* SCRIPTS */
$alert = "nnDBReplacer.protectSpaces();form.task.value = 'replace';form.submit();";
if ($this->config->show_alert)
{
	$alert = "if ( confirm( '" . str_replace(array('<br />', "\n", "'"), array('\n', '\n', "\\'"), JText::_('DBR_ARE_YOU_REALLY_SURE')) . "' ) ) {" . $alert . "}";
}
$alert = "if ( confirm( '" . str_replace(array('<br />', "\n", "'"), array('\n', '\n', "\\'"), JText::_('NN_ARE_YOU_SURE')) . "' ) ) {" . $alert . "}";
$script = "
	function submitform( task )
	{
		var form = document.adminForm;
		try {
			form.onsubmit();
			}
		catch( e ) {}
		var form = document.adminForm;
		" . $alert . "
	}
	var DBR_root = '" . JURI::root() . "';
";
JFactory::getDocument()->addScriptDeclaration($script);
JFactory::getDocument()->addScriptVersion(JURI::root(true) . '/media/dbreplacer/js/script.min.js');
JFactory::getDocument()->addScriptVersion(JURI::root(true) . '/media/nnframework/js/script.min.js');

// Version check
require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
if ($this->config->show_update_notification)
{
	echo nnVersions::getInstance()->getMessage('dbreplacer', '', '', 'component');
}

$s = JRequest::getVar('search', '', 'default', 'none', 2);
$r = JRequest::getVar('replace', '', 'default', 'none', 2);
$s = str_replace('||space||', ' ', $s);
$r = str_replace('||space||', ' ', $r);
?>
	<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="controller" value="default" />
		<input type="hidden" name="task" value="" />

		<div class="row-fluid">
			<div class="span3">
				<div class="col dbr_select dbr_tables">
					<fieldset class="adminform">
						<legend><?php echo html_entity_decode(JText::_('DBR_TABLES')); ?></legend>
						<div id="dbr_tables"><?php echo $this->tables; ?></div>
					</fieldset>
				</div>
			</div>
			<div class="span3">
				<div class="col dbr_select dbr_columns">
					<fieldset class="adminform">
						<legend><?php echo html_entity_decode(JText::_('DBR_COLUMNS')); ?></legend>
						<div id="dbr_columns">
							<input type="hidden" name="columns"
								value="<?php echo implode(',', JFactory::getApplication()->input->get('columns', array(0), 'array')); ?>"
								class="dbr_element" />
						</div>
					</fieldset>
				</div>
			</div>
			<div class="span6">

				<div class="col dbr_select dbr_search">
					<fieldset class="adminform">
						<legend><?php echo html_entity_decode(JText::_('DBR_SEARCH')); ?></legend>
						<div style="clear:both;margin-bottom: 5px;">
							* = <?php echo JText::_('DBR_ALL'); ?> &nbsp; &nbsp;
							NULL = <?php echo JText::_('DBR_NULL'); ?>
						</div>

						<textarea name="search" class="dbr_element" cols="30" rows="3"><?php echo $s; ?></textarea>

						<div class="row-fluid">
							<div class="span4">
								<label for="dbr_case" class="checkbox">
									<input type="checkbox" value="1" name="case" id="dbr_case" class="dbr_element" <?php echo JFactory::getApplication()->input->getInt('case', 0) ? 'checked="checked"' : ''; ?>>
									<?php echo JText::_('DBR_CASE_SENSITIVE'); ?>
								</label>
							</div>
						</div>
					</fieldset>
				</div>

				<div style="clear:both;"></div>

				<div class="col dbr_select dbr_replace">
					<fieldset class="adminform">
						<legend><?php echo html_entity_decode(JText::_('DBR_REPLACE')); ?></legend>
						<textarea name="replace" class="dbr_element" cols="30" rows="3"><?php echo $r; ?></textarea>

						<div class="btn-group" id="dbr_submit">
							<a onclick="submitform();" class="btn btn-success">
								<span class="icon-shuffle"></span> <?php echo JText::_('DBR_REPLACE'); ?>
							</a>
						</div>
					</fieldset>
				</div>
			</div>

			<div style="clear:both;"></div>

			<div class="col dbr_select">
				<fieldset class="adminform">
					<legend><?php echo html_entity_decode(JText::_('DBR_PREVIEW')); ?></legend>
					<div id="dbr_rows"></div>
				</fieldset>
			</div>

			<div style="clear:both;"></div>
	</form>

<?php
// PRO Check
require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';
echo nnLicenses::getInstance()->getMessage('DB_REPLACER', 0);

// Copyright
echo nnVersions::getInstance()->getCopyright('DB_REPLACER', '', 0, 'dbreplacer', 'component');
