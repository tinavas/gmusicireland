<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * CWtag controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_cwtags
 * @since       1.6
 */
class CWtagsControllerCWtag extends JControllerForm
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_CWTAGS_CWTAG';

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
  
  public function save()   
	{
    
    // Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('cwtag');
    $table          = $model->getTable();
    $data           = JRequest::getVar('jform', array(), 'post', 'array');
    $checkin        = property_exists($table, 'checked_out');
    $context        = "com_cwtags.edit.cwtag";
    $task           = $this->getTask();
    $this->_option = "com_cwtags"; 
    $tmpl           = JRequest::getString('tmpl');
    $layout         = JRequest::getString('layout', 'edit');
    $append         = '';		
    
    //Zkontrolujeme zda jeste muzem vlozit cenu
		/*$canAddOffer = OffersHelper::getCanAddItem(JRequest::getInt('id'));
		if ($canAddOffer == FALSE) {
			// Redirect back to the add screen.
			$app->enqueueMessage(JText::sprintf('COM_CWTAGS_INSERTOFFER_NOTIME'),'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&id='.JRequest::getInt('id').(is_numeric(JRequest::getVar('Itemid')) ? '&Itemid='.JRequest::getInt('Itemid') : ''), false));
			// Flush the data from the session.
			$app->setUserState('cwtags.cwtag.data', null);
			return false;
		}
    */
    
    // Get the data from the form POST
		$requestData = JRequest::getVar('jform', array(), 'post', 'array');
		// Get the data from the form FILES
    $requestFiles = JRequest::getVar('jform', array(), 'files', 'array');

		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		$requestData = array_merge($requestData, $requestFiles['name']);

		$data = $model->validate($form, $requestData);
		
		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			//$requestData = array_merge($requestData, $requestFiles['name']);
			// Save the data in the session.
			$app->setUserState('com_cwtags.cwtag.data', $data);

			// Redirect back to the add screen.
			$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&layout=edit&id='.JRequest::getInt('id')));
			return false;
		}
		
		//Pokud se nahraval soubor, tak jej zkontrolujeme
		$fieldName = 'file';
		if($requestFiles['name'][$fieldName]!="") {
			
			//any errors the server registered on uploading
			$fileError = $requestFiles['error'][$fieldName];
			if ($fileError > 0) 
			{
					switch ($fileError) 
					{
					case 1:
						$app->enqueueMessage(JText::_('COM_CWTAGS_INSERTOFFER_FILEERROR1'),'warning');
						break;
					case 2:
						$app->enqueueMessage(JText::_('COM_CWTAGS_INSERTOFFER_FILEERROR2'),'warning');
						break;
					case 3:
						$app->enqueueMessage(JText::_('COM_CWTAGS_INSERTOFFER_FILEERROR3'),'warning');
						break;
					case 4:
						$app->enqueueMessage(JText::_('COM_CWTAGS_INSERTOFFER_FILEERROR4'),'warning');
						break;
					}
					$app->setUserState('com_cwtags.cwtag.data', $data);
					$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&layout=edit&id='.JRequest::getInt('id')));
					return;
			}
			//check for filesize
			$fileSize = $requestFiles['size'][$fieldName];
			if($fileSize > 2000000)
			{
				$app->setUserState('com_cwtags.cwtag.data', $data);
				$app->enqueueMessage(JText::sprintf('COM_CWTAGS_INSERTOFFER_FILEERROR_SIZE', JHtml::_('number.bytes', $fileSize, 'auto', 1)),'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&layout=edit&id='.JRequest::getInt('id')));
				return;
			}
			//check the file extension is ok - opet
			$fileName = $requestFiles['name'][$fieldName];
			$uploadedFileNameParts = explode('.',$fileName);
			$uploadedFileExtension = array_pop($uploadedFileNameParts);
			$validFileExts = explode(',', 'jpeg,jpg,zip,doc,docx,pdf,zip,rar,png');
			//assume the extension is false until we know its ok
			$extOk = false;
			//go through every ok extension, if the ok extension matches the file extension (case insensitive)
			//then the file extension is ok
			foreach($validFileExts as $key => $value)
			{
					if( preg_match("/$value/i", $uploadedFileExtension ) )
					{
							$extOk = true;
					}
			}
			if ($extOk == false) 
			{
				$app->setUserState('com_cwtags.cwtag.data', $data);
				$app->enqueueMessage(JText::sprintf('COM_CWTAGS_INSERTOFFER_FILEERROR_EXT'),'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&layout=edit&id='.JRequest::getInt('id')));
				return;
			}
			//the name of the file in PHP's temp directory that we are going to move to our folder
			$fileTemp = $requestFiles['tmp_name'][$fieldName];
			//Zkratime jej na 220 znaku
			if (strlen($fileName) > 220) {
				$fileName = substr($fileName,0,strlen($fileName)-strlen($uploadedFileExtension)-1);
				$fileName = substr($fileName,0,220-strlen($uploadedFileExtension)-1);
			} else {
				$fileName = substr($fileName,0,strlen($fileName)-strlen($uploadedFileExtension)-1);
			}
			//lose any special characters in the filename
			$fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $fileName);
			//Pridame razitko data a casu
			$Datum = new DateTime;
			$fileName = $Datum->format('Y-m-d-H-i-s')."-".$fileName.'.'.$uploadedFileExtension;
		}
    Else {
      $itemid = $app->getUserState('com_cwtags.edit.cwtag.id');
      $db = JFactory::getDbo();
      // Get the data from the form POST
		  $requestF = JRequest::getVar('file', array(), 'post', 'array');
      if($itemid[0] > 0){
        $data['file'] = $requestF['name'];
      }
    }
		
		// Now update the loaded data to the database via a function in the model
		$data['checked_out'] = 0;
    $data['checked_out_time'] = '0000-00-00 00:00:00';
    $insitem = $model->save($data);

    //Id vytvorene polozky
    $item = $model->getItem(); 
    $id = $item->get('id'); 

            /*
                // Save succeeded, check-in the record.
            if ($checkin && !$model->checkin($id))
            {
                    // Check-in failed, go back to the record and display a notice.
                    $message = JText::sprintf('JError_Checkin_saved', $model->getError());
                    $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_view_item.$append, $message, 'error');
                    return false;
            }*/
                    
		if($requestFiles['name'][$fieldName]!="") {
			$insfiles = $model->savefiles($fileName,$fileSize,$fileTemp,$id);
		}

		// Check for errors.
		if ($insitem === false) {
			// Save the data in the session.
			$app->setUserState('com_cwtags.cwtag.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('COM_CWTAGS_INSERTOFFER_SAVE_UNSUCCESS', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&layout=edit&id='.$id));
			return false;
		} else {
			$app->enqueueMessage(JText::sprintf('COM_CWTAGS_INSERTOFFER_SAVE_SUCCESS', $model->getError()));
			//Zkontrolujeme zda nejsme pred koncem aukce, pripadne rovnou pridame cas navic
				//Zjistime nastaveni komponenty
				$params = JComponentHelper::getParams('com_cwtags');
				$timeBeforeEnd = (int)$params->get('timeBeforeEnd');
				$timeToAdd = (int)$params->get('timeToAdd');
				if ($timeBeforeEnd > 0 && $timeToAdd > 0) {
					$tTest = OffersHelper::AddTimeToEnd(JRequest::getInt('id'), $timeBeforeEnd, $timeToAdd);
				}
			//$this->setRedirect(JRoute::_('index.php?option=com_auctions&view=cwtag&id='.JRequest::getInt('id').(is_numeric(JRequest::getVar('Itemid')) ? '&Itemid='.JRequest::getVar('Itemid') : ''), false));
		}
		if($requestFiles['name'][$fieldName]!="") {
			// Check for errors.
			if ($insfiles === false) {
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&layout=edit&id='.$id));
				return false;
			} else {
				$app->enqueueMessage(JText::sprintf('COM_CWTAGS_INSERTOFFERFILE_SAVE_SUCCESS', $model->getError()));
			}
		}

		//$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtag&layout=edit&id='.$id.(is_numeric(JRequest::getVar('Itemid')) ? '&Itemid='.JRequest::getVar('Itemid') : ''), false));
		
		// Flush the data from the session.
		$app->setUserState('com_cwtags.cwtag.data', null);

            // Redirect the user and adjust session state based on the chosen task.
            switch ($task)
            {
                    case 'apply':
                            // Set the record data in the session.
                            $app->setUserState($context.'.id',              $model->getState($this->_context.'.id'));
                            $app->setUserState($context.'.data',    null);
                            // Redirect back to the edit screen.
                            $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view=cwtag&layout=edit&id='.$id, false));
                            break;
     
                    case 'save2new':
                            // Clear the record id and data from the session.
                            $app->setUserState($context.'.id', null);
                            $app->setUserState($context.'.data', null);
     
                            // Redirect back to the edit screen.
                            $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view=cwtag&layout=edit', false));
                            break;
     
                    default:
                            // Clear the record id and data from the session.
                            $app->setUserState($context.'.id', null);
                            $app->setUserState($context.'.data', null);
     
                            // Redirect to the list screen.
                            $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view=cwtags', false));
                            break;
            }    
    return true;
	}   
   /*public function save()
    {       
            // Check for request forgeries.
            JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));
     
            // Initialise variables.
            $app            = JFactory::getApplication();
            $model          = $this->getModel();
            $table          = $model->getTable();
            $data           = JRequest::getVar('jform', array(), 'post', 'array');
            $checkin        = property_exists($table, 'checked_out');
            $context        = "com_cwtags.edit.cwtag";
            $task           = $this->getTask();
            $this->_option = "com_cwtags"; 
            $tmpl           = JRequest::getString('tmpl');
            $layout         = JRequest::getString('layout', 'edit');
            $append         = '';


            // Setup redirect info.
            if ($tmpl) {
                    $append .= '&tmpl='.$tmpl;
            }
            if ($layout) {
                    $append .= '&layout='.$layout;
            }
     
            // The save2copy task needs to be handled slightly differently.
            if ($task == 'save2copy')
            {
                    // Check-in the original row.
                    if ($checkin  && !$model->checkin($data[$key]))
                    {
                            // Check-in failed, go back to the item and display a notice.
                            $message = JText::sprintf('JError_Checkin_saved', $model->getError());
                            $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_view_item.$append, $message, 'error');
                            return false;
                    }
     
                    // Reset the ID and then treat the request as for Apply.
                    $data['id']     = 0;
                    $task           = 'apply';
            }
    
            // Access check.
            
            if (!$this->_allowSave($data)) {
                    $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view='.$this->_view_items, false));
                    return JError::raiseWarning(403, 'JError_Save_not_permitted');
            }
            
            // Validate the posted data.
            $form   = &$model->getForm();
            if (!$form)
            {
                    JError::raiseError(500, $model->getError());
                    return false;
            }

            $data   = $model->validate($form, $data);
    
            // Check for validation errors.
            if ($data === false)
            { 
                    // Get the validation messages.
                    $errors = $model->getErrors();
     
                    // Push up to three validation messages out to the user.
                    for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
                    {
                            if (JError::isError($errors[$i])) {
                                    $app->enqueueMessage($errors[$i]->getMessage(), 'notice');
                            }
                            else {
                                    $app->enqueueMessage($errors[$i], 'notice');
                            }
                    }
     
                    // Save the data in the session.
                    $app->setUserState($context.'.data', $data);
     
                    // Redirect back to the edit screen.
                    $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view='.$this->_view_item.$append, false));
                    return false;
            }
            
            // Attempt to save the data.
            if (!$model->save($data))
            {
                    // Save the data in the session.
                    $app->setUserState($context.'.data', $data);
     
                    // Redirect back to the edit screen.
                    $this->setMessage(JText::sprintf('JError_Save_failed', $model->getError()), 'notice');
                    $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view='.$this->_view_item.$append, false));
                    return false;
            }
        		// priloha
            
            if($requestFiles['name'][$fieldName]!="") {
        			$insfiles = $model->savefiles($fileName,$fileSize,$fileTemp);
        		}            

            // Save succeeded, check-in the record.
            if ($checkin && !$model->checkin($data[$key]))
            {
                    // Check-in failed, go back to the record and display a notice.
                    $message = JText::sprintf('JError_Checkin_saved', $model->getError());
                    $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_view_item.$append, $message, 'error');
                    return false;
            }
     
            $this->setMessage(JText::_('JCONTROLLER_SAVE_SUCCESS'));
     
            // Redirect the user and adjust session state based on the chosen task.
            switch ($task)
            {
                    case 'apply':
                            // Set the record data in the session.
                            $app->setUserState($context.'.id',              $model->getState($this->_context.'.id'));
                            $app->setUserState($context.'.data',    null);
     
                            // Redirect back to the edit screen.
                            $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view='.$this->_view_item.$append, false));
                            break;
     
                    case 'save2new':
                            // Clear the record id and data from the session.
                            $app->setUserState($context.'.id', null);
                            $app->setUserState($context.'.data', null);
     
                            // Redirect back to the edit screen.
                            $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view='.$this->_view_item.$append, false));
                            break;
     
                    default:
                            // Clear the record id and data from the session.
                            $app->setUserState($context.'.id', null);
                            $app->setUserState($context.'.data', null);
     
                            // Redirect to the list screen.
                            $this->setRedirect(JRoute::_('index.php?option='.$this->_option.'&view='.$this->_view_list, false));
                            break;
            }
     
            return true;
    }*/ 
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId)
		{
			// If the category has been passed in the URL check it.
			$allow	= $user->authorise('core.create', $this->option . '.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absence of better information, revert to the component permissions.
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;

		if ($recordId)
		{
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId)
		{
			// The category has been set. Check the category permissions.
			return $user->authorise('core.edit', $this->option . '.category.' . $categoryId);
		}
		else
		{
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   string  $model  The model
	 *
	 * @return	boolean  True on success.
	 *
	 * @since	2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel('CWtag', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_cwtags&view=cwtags' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

}
