<?php
/**
 * Purge SEF URLs page
 * Empty the SEF URL databas table
 *
 * @package         Better Preview
 * @version         3.3.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

if (!JFactory::getApplication()->isAdmin())
{
	die;
}

// need to set the user agent, to prevent breaking when debugging is switched on
$_SERVER['HTTP_USER_AGENT'] = '';

$db = JFactory::getDBO();

$query = $db->getQuery(true)
	->delete('#__betterpreview_sefs');
$db->setQuery($query);
$db->execute();

if (isset($_SERVER['HTTP_REFERER']))
{
	JFactory::getApplication()->redirect($_SERVER['HTTP_REFERER'], JText::_('BP_PURGED'), 'message');
}
else
{
	die(JText::_('BP_PURGED'));
}
