<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JLoader::register('CWtagsHelper', JPATH_COMPONENT.'/helpers/cwtags.php');

/**
 * View to edit a cwtag.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cwtags
 * @since		1.5
 */
class CWtagsViewCWtag extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		JHTML::stylesheet('administrator/components/com_cwtags/assets/cwtags.css' );
		  
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
    
    $jversion = new JVersion();
    if(substr($jversion->getShortVersion(),0,1) == '3'){ parent::display('j3'); }
    else { parent::display($tpl); }
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= CWtagsHelper::getActions($this->item->catid,0);

		JToolBarHelper::title($isNew ? JText::_('COM_CWTAGS_MANAGER_CWTAG_NEW') : JText::_('COM_CWTAGS_MANAGER_CWTAG_EDIT'), 'cwtags.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_cwtags', 'core.create')) > 0)) {
			//JToolBarHelper::apply('cwtag.apply');
			JToolBarHelper::save('cwtag.save');

			if ($canDo->get('core.create')) {
				JToolBarHelper::save2new('cwtag.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolBarHelper::save2copy('cwtag.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('cwtag.cancel');
		}
		else {
			JToolBarHelper::cancel('cwtag.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_CWTAGS_CWTAGS_EDIT');
	}
}
