<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
// no direct access
defined('_JEXEC') or die;

/**
 * CWTAG file table
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cwtags
 * @since		1.6
 */
class CWtagsTableCWtagFiles extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__cwtags_files', 'id', $db);
	}
	
	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * @see		JTable::check
	 * @since	1.5
	 */
	function check()
	{
		//Kontrola
		//Zatim nic

		return true;
	}
	
	/**
	 * Overloaded store a row
	 *
	 * @param boolean $updateNulls True to update fields even if they are null.
	 */
	function store($updateNulls = false)
	{
		if (empty($this->id))
		{
			$user = JFactory::getUser();
			$date = JFactory::getDate();
			$this->created = $user->get('id');
			$this->created_time = $date->toSql();
			
			// Store the row
			parent::store($updateNulls);
		}
		else
		{
			// Get the old row
			$oldrow = JTable::getInstance('CwtagFiles', 'CWtagsTable');
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}

			// Store the new row
			$this->check();
			parent::store($updateNulls);
		}
		return count($this->getErrors())==0;
	}
}
