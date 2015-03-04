<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * CWtag model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_cwtags
 * @since       1.6
 */
class CWtagsModelOrder extends JModelAdmin
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_CWTAGS_CWTAG';

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array   $commands   An array of commands to perform.
	 * @param   array   $pks        An array of item ids.
	 * @param   array   $contexts   An array of item contexts.
	 *
	 * @return	boolean	 Returns true on success, false on failure.
	 *
	 * @since	2.5
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['category_id']))
		{
			$cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands['category_id'], $pks, $contexts);
				if (is_array($result))
				{
					$pks = $result;
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands['category_id'], $pks, $contexts))
			{
				return false;
			}
			$done = true;
		}

		if (strlen($commands['client_id']) > 0)
		{
			if (!$this->batchClient($commands['client_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}


	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return;
			}
			$user = JFactory::getUser();

			if (!empty($record->catid))
			{
				return $user->authorise('core.delete', 'com_cwtags.category.' . (int) $record->catid);
			}
			else
			{
				return parent::canDelete($record);
			}
		}
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check against the category.
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_cwtags.orders.' . (int) $record->catid);
		}
		// Default to component settings if category not known.
		else
		{
			return parent::canEditState($record);
		}
	}

	/**
	 * Returns a JTable object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate. [optional]
	 * @param   string  $prefix  A prefix for the table class name. [optional]
	 * @param   array   $config  Configuration array for model. [optional]
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Order', $prefix = 'CWtagsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form. [optional]
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cwtags.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Determine correct permissions to check.
		if ($this->getState('cwtag.id'))
		{
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('sticky', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('sticky', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_cwtags.edit.order.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('cwtag.id') == 0)
			{
				$app = JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_cwtags.orders.filter.category_id')));
			}
		}

		return $data;
	}

	/**
	 * Method to stick records.
	 *
	 * @param   array    &$pks   The ids of the items to publish.
	 * @param   integer  $value  The value of the published state
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	function stick(&$pks, $value = 1)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$table = $this->getTable();
		$pks = (array) $pks;

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->stick($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = '. (int) $table->catid;
		$condition[] = 'state >= 0';
		return $condition;
	}

	/**
	 * Method to get the inquiry form data.
	 *
	 * The base form data is loaded and then an tag is fired
	 * for users plugins to extend the data.
	 *
	 * @return	mixed		Data object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		if ($this->data === null) {

			$this->data	= new stdClass();
			$app	= JFactory::getApplication();
			$params	= JComponentHelper::getParams('com_cwtags');

			// Override the base user data with any data in the session.
			$temp = (array)$app->getUserState('com_cwtags.order.data', array());
			foreach ($temp as $k => $v) {
				$this->data->$k = $v;
			}

      /*
			// Get the dispatcher and load the inquiries plugins.
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('cwtag');

			// Trigger the data preparation tag.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_cwtags.cwtag', $this->data));

			// Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true)) {
				$this->setError($dispatcher->getError());
				$this->data = false;
			}
      */
		}

		return $this->data;
	}
	
	function getRealIpAddr() {
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
		  $ip=$_SERVER['HTTP_CLIENT_IP']; // share internet
		} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; // pass from proxy
		} else {
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
  
	/**
	 * Method to add/save the file atachment of an item.
	 * @param   string  $fileName  Name of file.
	 * @param   string  $fileSize  Size of file.
	 * @param   string  $fileTemp  Location of tempfile.
	 * @return  boolean  True on success.
	 * @since   1.6
	 */
	public function savefiles($fileName,$fileSize,$fileTemp,$id)
	{
		
		$app	= JFactory::getApplication();
		// Initialise the table
		$data = (array)$this->getData();
		
		//Pripravime data
		$datafile = array();
		$datafile['event_id'] = $id;
		//$datafile['offers_price_id'] = $this->LastID;
		$datafile['filename'] = sprintf('%1$010d',(int)$datafile['event_id']).'-'.(string)$fileName;
		$datafile['filename_real'] = JPATH_SITE.DS.'files'.DS.'cwtag'.DS.$datafile['filename'];
		$datafile['filesize'] = (int)$fileSize;
		$datafile['ip'] = $this->getRealIpAddr();
		$datafile['dns'] = gethostbyaddr($datafile['ip']);
		
		//Presuneme soubor do noveho umisteni
		if(!JFile::upload($fileTemp, $datafile['filename_real'])) 
		{
				echo JText::_('COM_CWTAGS_INSERTOFFER_FILEERROR_MOVE');
				return false;
		}
		
		$tableEventsFiles = parent::getTable('CwtagFiles', 'CWtagsTable');
		//Zjistime zda uz je vlozena nabidka
		//$tableEventsFiles->id = $datafile['offers_id'];

		//Vzdy budeme vkladat novy zaznam
		if (!$tableEventsFiles->bind($datafile)) {
			$app->enqueueMessage(JText::sprintf('COM_CWTAGS_INSERTOFFERFILE_BIND_FAILED', $tableEventsFiles->getError()), 'warning');
			return false;
		}
		if (!$tableEventsFiles->check()) {
			$app->enqueueMessage($tableEventsFiles->getError(), 'warning');
			return false;
		}
		// Store the data.
		if (!$tableEventsFiles->store()) {
			$app->enqueueMessage(JText::sprintf('COM_CWTAGS_INSERTOFFERFILE_SAVE_UNSUCCESS', $tableEventsFiles->getError()), 'warning');
			return false;
		}
		
    //smazat fyzicky puvodni soubor k teto item id
    $db = JFactory::getDbo();
    $query = "SELECT * FROM #__cwtags_files WHERE event_id = ".$datafile['event_id']." AND filename_real != '".$datafile['filename_real']."' AND filename != '".$datafile['filename']."'";
    $db->setQuery($query);
    $res = $db->loadObjectList();
    jimport( 'joomla.filesystem.file' );
    foreach($res as $row){
      JFile::delete($row->filename_real);
    }
    $query = "DELETE FROM #__cwtags_files WHERE event_id = ".$datafile['event_id']." AND filename_real != '".$datafile['filename_real']."' AND filename != '".$datafile['filename']."'";
    $db->setQuery($query);
    $db->query();
       
		return true;
	}

  	// CUSTOM
	function getItemParams($id,$param){
    $db			= JFactory::getDBO();
    
		$where 		= array();
		$where[]	= 'id = '.$id;
/*		// Filter by language
		if ($this->getState('filter.language')) {
			$where[] =  'language IN ('.$db->Quote(JFactory::getLanguage()->getTag()).','.$db->Quote('*').')';
		}  */
		$where		= (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		$query = 'SELECT params'
			.' FROM #__cwgamebook_items'
			. $where;

		$db->setQuery($query);
		$result = $db->loadObject();

		// Convert parameter fields to objects.
    $params = new JParameter($result->params);
    $myParam = $params->get($param);

//		$registry = new JRegistry;
//		$registry->loadString($result->params);
//		$params = clone $this->getState('params');
/*       
		echo "<pre>";print_r($params);echo "</pre>";
		echo $myParam;
		exit;
*/		
    return $myParam;
	}
}
