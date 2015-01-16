<?php
/**
 * List Model
 *
 * @package         Snippets
 * @version         3.5.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * List Model
 */
class SnippetsModelList extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'ordering', 'a.ordering',
				'state', 'a.published',
				'alias', 'a.alias',
				'name', 'a.name',
				'description', 'a.description',
				'id', 'a.id',
			);
		}

		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$this->parameters = NNParameters::getInstance();

		parent::__construct($config);
	}

	/**
	 * @var        string    The prefix to use with controller messages.
	 */
	protected $text_prefix = 'NN';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.ordering', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param    string    A prefix for the store id.
	 *
	 * @return    string    A store id.
	 */
	protected function getStoreId($id = '', $getall = 0)
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $getall;

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select(
				$this->getState(
					'list.select',
					'a.*'
				)
			)
			->from('#__snippets AS a');

		// Filter by published state
		$state = $this->getState('filter.state');
		if (is_numeric($state))
		{
			$query->where('a.published = ' . ( int ) $state);
		}
		else if ($state === '')
		{
			$query->where('( a.published IN ( 0,1,2 ) )');
		}

		// Filter the list over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . ( int ) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where(
					'( `name` LIKE ' . $search .
					' OR `alias` LIKE ' . $search .
					' OR `description` LIKE ' . $search .
					' OR `content` LIKE ' . $search . ' )'
				);
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.ordering');
		$orderDirn = $this->state->get('list.direction');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	public function getItems($getall = 0)
	{
		// Get a storage key.
		$store = $this->getStoreId('', $getall);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();
		if ($getall)
		{
			$query->clear('order')->order('a.published asc, a.ordering asc');
			$this->_db->setQuery($query);
			$items = $this->_db->loadObjectList('alias');
		}
		else
		{
			$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		}

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		foreach ($items as $i => $item)
		{
			$isini = ((substr($item->params, 0, 1) != '{') && (substr($item->params, -1, 1) != '}'));
			$params = $this->parameters->getParams($item->params, JPATH_ADMINISTRATOR . '/components/com_snippets/item_params.xml');
			foreach ($params as $key => $val)
			{
				if (!isset($item->$key) && !is_object($val))
				{
					$items[$i]->$key = $val;
				}
			}
			unset($items[$i]->params);

			if ($isini)
			{
				foreach ($items[$i] as $key => $val)
				{
					if (is_string($val))
					{
						$items[$i]->$key = stripslashes($val);
					}
				}
			}
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $items;
	}

	/**
	 * Import Method
	 * Import the selected items specified by id
	 * and set Redirection to the list of items
	 */
	function import($model)
	{
		$file = JRequest::getVar('file', '', 'files', 'array');

		if (!is_array($file) || !isset($file['name']))
		{
			$msg = JText::_('SNP_PLEASE_CHOOSE_A_VALID_FILE');
			JFactory::getApplication()->redirect('index.php?option=com_snippets&view=list&layout=import', $msg);
		}

		$ext = explode(".", $file['name']);

		if ($ext[count($ext) - 1] != 'snbak')
		{
			$msg = JText::_('SNP_PLEASE_CHOOSE_A_VALID_FILE');
			JFactory::getApplication()->redirect('index.php?option=com_snippets&view=list&layout=import', $msg);
		}

		jimport('joomla.filesystem.file');
		$publish_all = JFactory::getApplication()->input->getInt('publish_all', 0);

		$data = file_get_contents($file['tmp_name']);
		$data = explode('<SN_ITEM_START>', $data);

		$items = array();
		foreach ($data as $data_item)
		{
			$data_item = trim(str_replace('<SN_ITEM_END>', '', $data_item));
			if ($data_item)
			{
				$data_item_keyvals = explode('<SN_KEY>', $data_item);
				$item = array();
				foreach ($data_item_keyvals as $data_item_keyval)
				{
					$data_item_keyval = trim(str_replace('<SN_END>', '', $data_item_keyval));
					if ($data_item_keyval)
					{
						$data_item_keyval = explode('<SN_VAL>', $data_item_keyval);
						$item[$data_item_keyval['0']] = (isset($data_item_keyval['1'])) ? $data_item_keyval['1'] : '';
					}
				}
				$item['id'] = 0;
				if ($publish_all == 0)
				{
					unset($item['published']);
				}
				else if ($publish_all == 1)
				{
					$item['published'] = 1;
				}
				$items[] = $item;
			}
		}

		$msg = JText::_('Items saved');

		foreach ($items as $item)
		{
			$saved = $model->save($item);
			if ($saved != 1)
			{
				$msg = JText::_('Error Saving Item') . ' ( ' . $saved . ' )';
			}
		}

		JFactory::getApplication()->redirect('index.php?option=com_snippets&view=list', $msg);
	}

	/**
	 * Export Method
	 * Export the selected items specified by id
	 */
	function export($ids)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('s.*')
			->from('#__snippets as s')
			->where('s.id IN ( ' . implode(', ', $ids) . ' )');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$string = '';
		foreach ($rows as $row)
		{
			unset($row->id);
			unset($row->checked_out);
			unset($row->checked_out_time);
			$string .= '<SN_ITEM_START>' . "\n";
			foreach ($row as $key => $val)
			{
				$string .= '	<SN_KEY>' . $key;
				$string .= '<SN_VAL>' . $val;
				$string .= '<SN_END>' . "\n";
			}
			$string .= '<SN_ITEM_END>' . "\n\n";
		}

		$filename = 'Snippets Item';
		if (count($rows) == 1)
		{
			$filename .= ' (' . preg_replace('#(.*?)_*$#', '\1', str_replace('__', '_', preg_replace('#[^a-z0-9_-]#', '_', strtolower(html_entity_decode($rows['0']->name))))) . ')';
		}
		else
		{
			$filename .= 's';
		}

		// SET DOCUMENT HEADER
		if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "Opera";
		}
		elseif (preg_match('#MSIE ([0-9].[0-9]{1,2})#', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "IE";
		}
		else
		{
			$UserBrowser = '';
		}
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
		@ob_end_clean();
		ob_start();

		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

		if ($UserBrowser == 'IE')
		{
			header('Content-Disposition: inline; filename="' . $filename . '.snbak"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $filename . '.snbak"');
			header('Pragma: no-cache');
		}

		// PRINT STRING
		echo $string;
		die;
	}

	/**
	 * Copy Method
	 * Copy all items specified by array cid
	 * and set Redirection to the list of items
	 */
	function copy($ids, $model)
	{
		foreach ($ids as $id)
		{
			$model->copy($id);
		}

		$msg = JText::sprintf('Items copied', count($ids));
		JFactory::getApplication()->redirect('index.php?option=com_snippets&view=list', $msg);
	}
}
