<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_cwtags')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller = JControllerLegacy::getInstance('Cwtags');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect(); 