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

/**
 * View class for a list of cwtags.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_cwtags
 * @since       1.6
 */
class CWtagsViewOrders extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		JHTML::stylesheet('administrator/components/com_cwtags/assets/cwtags.css' );  
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		//require_once JPATH_COMPONENT . '/models/fields/cwtagclient.php';
		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/cwtags.php';

		$canDo = CWtagsHelper::getActions();
		$user = JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_CWTAGS_MANAGER_CWTAGS'), 'cwtags.png');
    /*
		JToolBarHelper::addNew('order.add');

		if (($canDo->get('core.edit')))
		{
			JToolBarHelper::editList('order.edit');
		}
    */
		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				//JToolBarHelper::divider();
				JToolBarHelper::publish('orders.publish', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::unpublish('orders.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}
      /*
			if ($this->state->get('filter.state') != -1)
			{
				JToolBarHelper::divider();
				if ($this->state->get('filter.state') != 2)
				{
					JToolBarHelper::archiveList('orders.archive');
				}
				elseif ($this->state->get('filter.state') == 2)
				{
					JToolBarHelper::unarchiveList('orders.publish');
				}
			}*/
		}
    JToolBarHelper::divider();
		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::checkin('orders.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'orders.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('cwtags.trash');
      if ($canDo->get('core.delete')){
    	 JToolBarHelper::deleteList('', 'orders.delete', 'JTOOLBAR_DELETE');
      }
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_cwtags');
			JToolBarHelper::divider();
		}
		JToolBarHelper::help('JHELP_COMPONENTS_CWTAGS_CWTAGS');
	}
}
