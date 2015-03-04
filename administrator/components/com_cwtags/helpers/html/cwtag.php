<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('JPATH_BASE') or die;

/**
 * CWtag HTML class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_cwtags
 * @since       2.5
 */
abstract class JHtmlCWtag
{
	/**
	 * Display a batch widget for the client selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public static function clients()
	{
		// Create the batch selector to change the client on a selection list.
		$lines = array(
			'<label id="batch-client-lbl" for="batch-client" class="hasTip" title="'.JText::_('COM_CWTAGS_BATCH_CLIENT_LABEL').'::'.JText::_('COM_CWTAGS_BATCH_CLIENT_LABEL_DESC').'">',
			JText::_('COM_CWTAGS_BATCH_CLIENT_LABEL'),
			'</label>',
			'<select name="batch[client_id]" class="inputbox" id="batch-client-id">',
			'<option value="">'.JText::_('COM_CWTAGS_BATCH_CLIENT_NOCHANGE').'</option>',
			'<option value="0">'.JText::_('COM_CWTAGS_NO_CLIENT').'</option>',
			JHtml::_('select.options', self::clientlist(), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public static function clientlist()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id As value, name As text');
		$query->from('#__cwtag_clients AS a');
		$query->order('a.name');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}
}
