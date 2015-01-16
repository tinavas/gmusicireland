<?php
/**
 * Ajax load page
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

if (JFactory::getApplication()->isSite())
{
	die();
}

$class = new DBReplacer;
echo $class->render();
die;

class DBReplacer
{
	function render()
	{
		$this->db = JFactory::getDBO();

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$this->config = $parameters->getComponentParams('com_dbreplacer');

		$field = JFactory::getApplication()->input->get('field', 'table');
		$p = JFactory::getApplication()->input->get('params', array(), 'array');
		$params = new stdClass;
		$params->table = '';
		$params->columns = array();
		foreach ($p as $key => $val)
		{
			$params->{$key} = $val;
		}


		$this->params = $params;

		switch ($field)
		{
			case 'rows':
				return $this->renderRows();
				break;
			case 'columns':
			default:
				return $this->renderColumns();
				break;
		}
	}

	function renderColumns()
	{
		$table = $this->params->table;
		$selected = $this->implodeParams($this->params->columns);

		$options = array();
		if ($table)
		{
			$cols = $this->getColumns();
			foreach ($cols as $col)
			{
				$options[] = JHTML::_('select.option', $col, $col, 'value', 'text', 0);
			}
		}

		$html = '<strong>' . $this->params->table . '</strong><br />';
		$html .= JHTML::_('select.genericlist', $options, 'columns[]', 'multiple="multiple" size="20" class="dbr_element"', 'value', 'text', $selected, 'paramscolumns');

		return $html;
	}

	function getColumns()
	{
		$query = 'SHOW COLUMNS FROM `' . trim($this->params->table) . '`';
		$this->db->setQuery($query);
		$columns = $this->db->loadColumn();

		return $columns;
	}

	function renderRows()
	{
		// Load plugin language
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('com_dbreplacer');

		$max = 50;

		$html = array();
		if ($this->params->table)
		{
			$columns = $this->implodeParams($this->params->columns);
			$search = str_replace('||space||', ' ', $this->params->search);
			$replace = str_replace('||space||', ' ', $this->params->replace);
			$s1 = '|' . md5('<SEARCH TAG>') . '|';
			$s2 = '|' . md5('</SEARCH TAG>') . '|';
			$r1 = '|' . md5('<REPLACE TAG>') . '|';
			$r2 = '|' . md5('</REPLACE TAG>') . '|';

			$cols = $this->getColumns();
			$rows = $this->getRows($max);
			if (count($rows) > $max - 1)
			{
				$html[] = '<p>' . JText::sprintf('DBR_MAXIMUM_ROW_COUNT_REACHED', $max) . '</p>';
			}

			$html[] = '<table class="table table-striped">';
			$html[] = '<thead><tr>';
			foreach ($cols as $col)
			{
				$class = '';
				if (!in_array($col, $columns))
				{
					$class = 'ghosted';
				}
				$html[] = '<th class="' . $class . '">' . $col . '</th>';
			}
			$html[] = '</tr></thead>';
			if ($rows && !empty($rows))
			{
				$html[] = '<tbody>';
				foreach ($rows as $row)
				{
					$html[] = '<tr>';
					foreach ($cols as $col)
					{
						$class = '';
						$val = $row->$col;
						if (!in_array($col, $columns))
						{
							$class = 'ghosted';
							if ($val == '' || $val === null || $val == '0000-00-00')
							{
								if ($val === null)
								{
									$val = 'NULL';
								}
								$val = '<span class="null">' . $val . '</span>';
							}
							else
							{
								$val = preg_replace('#^((.*?\n){4}).*?$#si', '\1...', $val);
								if (strlen($val) > 300)
								{
									$val = substr($val, 0, 300) . '...';
								}
								$val = htmlentities($val, ENT_COMPAT, 'utf-8');
							}
						}
						else
						{
							if ($search == 'NULL')
							{
								if ($val == '' || $val === null || $val == '0000-00-00')
								{
									if ($val === null)
									{
										$val = 'NULL';
									}
									$val = '<span class="search_string"><span class="null">' . $val . '</span></span><span class="replace_string">' . $replace . '</span>';
								}
								else
								{
									$val = preg_replace('#^((.*?\n){4}).*?$#si', '\1...', $val);
									if (strlen($val) > 300)
									{
										$val = substr($val, 0, 300) . '...';
									}
									$val = htmlentities($val, ENT_COMPAT, 'utf-8');
								}
							}
							else if ($search == '*')
							{
								$val = '<span class="search_string"><span class="null">*</span></span><span class="replace_string">' . $replace . '</span>';
							}
							else
							{
								if ($val === null)
								{
									$val = '<span class="null">NULL</span>';
								}
								else
								{
									$match = 0;
									if ($search != '')
									{
										$s = preg_quote($search, '#');
										$s = '#' . $s . '#s';
										if ($this->params->case != 1)
										{
											$s .= 'i';
										}
										$r = $s1 . '\0' . $s2 . $r1 . $replace . $r2;

										$match = @preg_match($s, $val);
									}

									if ($match)
									{
										$class = 'has_search';
										$val = preg_replace($s, $r, $val);
										$val = htmlentities($val, ENT_COMPAT, 'utf-8');
										$val = str_replace(' ', '&nbsp;', $val);
										$val = str_replace($s1, '<span class="search_string">', str_replace($s2, '</span>', $val));
										$val = str_replace($r1, '<span class="replace_string">', str_replace($r2, '</span>', $val));
									}
									else
									{
										$val = preg_replace('#^((.*?\n){4}).*?$#si', '\1...', $val);
										if (strlen($val) > 300)
										{
											$val = substr($val, 0, 300) . '...';
										}
										$val = htmlentities($val, ENT_COMPAT, 'utf-8');
									}

									if ($val == '0000-00-00')
									{
										$val = '<span class="null">' . $val . '</span>';
									}
								}
							}
						}
						$val = nl2br($val);
						$html[] = '<td class="db_value ' . $class . '">' . $val . '</td>';
					}
					$html[] = '</tr>';
				}
				$html[] = '</tbody>';
			}
			$html[] = '</table>';
		}

		return implode("\n", $html);
	}

	function getRows($max = 100)
	{
		$table = $this->params->table;
		$columns = $this->params->columns;

		$s = str_replace('||space||', ' ', $this->params->search);

		$where = '';
		if ($columns)
		{
			$likes = array();
			if ($s != '')
			{
				if ($s == 'NULL')
				{
					$likes[] = 'IS NULL';
					$likes[] = '= ""';
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
						if ($this->params->case == 1)
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
				$columns = $this->implodeParams($columns);
				$where = array();
				foreach ($columns as $column)
				{
					foreach ($likes as $like)
					{
						$where[] = '`' . trim($column) . '` ' . $like;
					}
				}
				$where = ' WHERE ( ' . implode(' OR ', $where) . ' )';
			}
		}

		$query = 'SELECT * FROM `' . trim($table) . '`'
			. $where
			. ' LIMIT ' . (int) $max;
		$this->db->setQuery($query);

		return $this->db->loadObjectList();
	}

	function implodeParams($params)
	{
		if (is_array($params))
		{
			return $params;
		}
		$params = explode(',', $params);
		$p = array();
		foreach ($params as $param)
		{
			if (trim($param) != '')
			{
				$p[] = trim($param);
			}
		}

		return array_unique($p);
	}
}
