<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of cwtag records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cwtags
 * @since		1.6
 */
class CWtagsModelOrders extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'userid', 'a.userid',
				'account', 'a.account',
				'state', 'a.state',
				'created', 'a.created',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from($db->quoteName('#__cwtags_orders').' AS a');
		//$query->where('a.access IN ('.$groups.')');

		// Join over the users for the author and modified_by names.
		$query->select("ua.name AS author,ua.username");

		$query->join('LEFT', '#__users AS ua ON ua.id = a.userid');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.userid = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(author LIKE '.$search.' OR a.account LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'ordering');
		$orderDirn	= $this->state->get('list.direction', 'ASC');
		if ($orderCol == 'ordering' || $orderCol == 'category_title') {
			$orderCol = 'c.title '.$orderDirn.', a.ordering';
		}

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Order', $prefix = 'CWtagsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_cwtags');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	public function getOrderItems($order_id)
	{
		$user	= JFactory::getUser();

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the categories.
		$query->select('*');
		$query->from($db->quoteName('#__cwtags_orders_items').' AS i');
		
		$query->where('i.order_id = '.$order_id);

    $db->setQuery($query);
    $orderitems = $db->loadObjectList();
    return $orderitems;
	}
} 