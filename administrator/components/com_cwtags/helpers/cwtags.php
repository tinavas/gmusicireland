<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die;

/**
 * CWtags component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cwtags
 * @since		1.6
 */
class CWtagsHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_CWTAGS_SUBMENU_CWTAGS'),
			'index.php?option=com_cwtags&view=cwtags',
			$vName == 'cwtags'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_CWTAGS_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_cwtags',
			$vName == 'categories'
		);
		if ($vName=='categories') {
			JToolBarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_cwtags')),
				'cwtags-categories');
		} /*
		JSubMenuHelper::addEntry(
			JText::_('COM_CWTAGS_SUBMENU_ORDERS'),
			'index.php?option=com_cwtags&view=orders',
			$vName == 'orders'
		);*/

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($categoryId)) {
			$assetName = 'com_cwtags';
		} else {
			$assetName = 'com_cwtags.category.'.(int) $categoryId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * @return	boolean
	 * @since	1.6
	 */
	public static function updateReset()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();
		$now = JFactory::getDate();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__cwtags');
		$query->where("'".$now."' >= ".$db->quoteName('reset'));
		$query->where($db->quoteName('reset').' != '.$db->quote($nullDate).' AND '.$db->quoteName('reset').'!=NULL');
		$query->where('('.$db->quoteName('checked_out').' = 0 OR '.$db->quoteName('checked_out').' = '.(int) $db->Quote($user->id).')');
		$db->setQuery((string)$query);
		$rows = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

		foreach ($rows as $row) {
			$purchase_type = $row->purchase_type;

			if ($purchase_type < 0 && $row->cid) {
				$client = JTable::getInstance('Client', 'CWtagsTable');
				$client->load($row->cid);
				$purchase_type = $client->purchase_type;
			}

			if ($purchase_type < 0) {
				$params = JComponentHelper::getParams('com_cwtags');
				$purchase_type = $params->get('purchase_type');
			}

			switch($purchase_type) {
				case 1:
					$reset = $nullDate;
					break;
				case 2:
					$date = JFactory::getDate('+1 year '.date('Y-m-d', strtotime('now')));
					$reset = $db->Quote($date->toSql());
					break;
				case 3:
					$date = JFactory::getDate('+1 month '.date('Y-m-d', strtotime('now')));
					$reset = $db->Quote($date->toSql());
					break;
				case 4:
					$date = JFactory::getDate('+7 day '.date('Y-m-d', strtotime('now')));
					$reset = $db->Quote($date->toSql());
					break;
				case 5:
					$date = JFactory::getDate('+1 day '.date('Y-m-d', strtotime('now')));
					$reset = $db->Quote($date->toSql());
					break;
			}

			// Update the row ordering field.
			$query->clear();
			$query->update($db->quoteName('#__cwtags'));
			$query->set($db->quoteName('reset').' = '.$db->quote($reset));
			$query->set($db->quoteName('impmade').' = '.$db->quote(0));
			$query->set($db->quoteName('clicks').' = '.$db->quote(0));
			$query->where($db->quoteName('id').' = '.$db->quote($row->id));
			$db->setQuery((string)$query);
			$db->query();

			// Check for a database error.
			if ($db->getErrorNum()) {
				JError::raiseWarning(500, $db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	public static function getClientOptions()
	{
		// Initialize variables.
		$options = array();

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

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_CWTAGS_NO_CLIENT')));

		return $options;
	}
  /**
   * Method for generating download link for item
	 *
	 * @param	int	item ID
	 *
	 * @return	string    
   */ 
  public function getFileLink($item_id)
  { 
    $db = JFactory::getDbo();
    
    //get item
    $query = "SELECT * FROM #__cwtags AS i LEFT JOIN #__cwtags_files AS f ON f.event_id=i.id WHERE i.id = ".$item_id;
    $db->setQuery($query);
    $file = $db->loadObject();
    //print_r($file);
    
    return $file->filename_real;
  }
}
