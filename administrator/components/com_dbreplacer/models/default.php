<?php
/**
 * DB Replacer Default Model
 *
 * @package         DB Replacer
 * @version         3.2.0
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * DB Replacer Default Model
 */
class DBReplacerModelDefault extends JModelLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to replace in the database
	 *
	 * @access public
	 * @return integer
	 */
	public function replace(&$params)
	{
		if (!empty($params->columns))
		{
			$where = '';
			$s = str_replace('||space||', ' ', $params->search);
			$r = str_replace('||space||', ' ', $params->replace);

			$likes = array();
			if ($s != '')
			{
				if ($s == 'NULL')
				{
					$likes[] = '= ""';
					$likes[] = 'IS NULL';
				}
				else if ($s == '*')
				{
					$likes[] = ' != \'something it would never be!!!\'';
				}
				else
				{
					$dbs = $s;
						// escape slashes
						$dbs = str_replace('\\', '\\\\\\\\', $dbs);
						// escape single quotes
						$dbs = str_replace('\'', '\\\'', $dbs);
						// escape the underscores
						$dbs = str_replace('_', '\_', $dbs);
						$dbs = '%' . $dbs . '%';
						if ($params->case == 1)
						{
							$likes[] = 'LIKE BINARY \'' . $dbs . '\'';
						}
						else
						{
							$likes[] = 'LIKE \'' . $dbs . '\'';
						}
				}
			}
			if (!empty($likes))
			{
				$where = array();
				foreach ($params->columns as $column)
				{
					foreach ($likes as $like)
					{
						$where[] = '`' . trim($column) . '` ' . $like;
					}
				}
				$where = ' WHERE ( ' . implode(' OR ', $where) . ' )';
			}


			$query = 'SELECT * FROM `' . trim(preg_replace('#^__#', $this->_db->getPrefix(), $params->table)) . '`'
				. $where
				. ' LIMIT ' . (int) $params->max;
			$this->_db->setQuery($query);

			$rows = $this->_db->loadObjectList();

			$count = 0;
			foreach ($rows as $row)
			{
				$set = array();
				$where = array();
				foreach ($row as $key => $val)
				{
					if ($val != '' && $val !== null && $val != '0000-00-00')
					{
						$where[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($val);
					}
					if (in_array($key, $params->columns))
					{
						if ($s == 'NULL')
						{
							if ($val == '' || $val === null || $val == '0000-00-00')
							{
								$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($r);
							}
						}
						else if ($s == '*')
						{
							$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($r);
						}
						else
						{
							$dbs = preg_quote($s, '#');
							$dbs = '#' . $dbs . '#s';
							if ($params->case != 1)
							{
								$dbs .= 'i';
							}

							$match = @preg_match($dbs, $val);
							if ($match)
							{
								$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote(preg_replace($dbs, $r, $val));
							}
						}
					}
				}

				if (!empty($set) && !empty($where))
				{
					$where = ' WHERE ( ' . implode(' AND ', $where) . ' )';

					$query = 'UPDATE `' . trim(preg_replace('#^__#', $this->_db->getPrefix(), $params->table)) . '`'
						. ' SET ' . implode(', ', $set)
						. $where
						. ' LIMIT 1';
					$this->_db->setQuery($query);
					if (!$this->_db->execute())
					{
						JFactory::getApplication()->enqueueMessage(JText::_('???'), 'error');
					}
					else
					{
						$count++;
					}
				}
			}

			if (!$count)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('DBR_NO_ROWS_UPDATED'), 'notice');
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('DBR_ROWS_UPDATED', $count), 'message');
			}
		}

		return;
	}
}
