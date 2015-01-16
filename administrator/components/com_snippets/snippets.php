<?php
/**
 * Main Admin file
 *
 * @package         Snippets
 * @version         3.5.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_snippets'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JFactory::getLanguage()->load('com_snippets', JPATH_ADMINISTRATOR);

jimport('joomla.filesystem.file');

// return if NoNumber Framework plugin is not installed
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	$msg = JText::_('SNP_NONUMBER_FRAMEWORK_NOT_INSTALLED')
		. ' ' . JText::sprintf('SNP_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_SNIPPETS'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');
	return;
}

// give notice if NoNumber Framework plugin is not enabled
$nnep = JPluginHelper::getPlugin('system', 'nnframework');
if (!isset($nnep->name))
{
	$msg = JText::_('SNP_NONUMBER_FRAMEWORK_NOT_ENABLED')
		. ' ' . JText::sprintf('SNP_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_SNIPPETS'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// load the NoNumber Framework language file
JFactory::getLanguage()->load('plg_system_nnframework', JPATH_ADMINISTRATOR);

// Dependency
require_once JPATH_PLUGINS . '/system/nnframework/fields/dependency.php';
nnFieldDependency::setMessage('/plugins/editors-xtd/snippets/snippets.php', 'SNP_THE_EDITOR_BUTTON_PLUGIN');
nnFieldDependency::setMessage('/plugins/system/snippets/snippets.php', 'SNP_THE_SYSTEM_PLUGIN');

$controller = JControllerLegacy::getInstance('Snippets');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
