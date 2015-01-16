<?php
/**
 * Plugin Helper File
 *
 * @package         Cache Cleaner
 * @version         3.4.3
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('plg_system_cachecleaner');

/**
 * Plugin that cleans cache
 */
class plgSystemCacheCleanerHelper
{
	var $message = '';
	var $show_message = false;
	var $error = false;
	var $size = 0;
	var $ignore_folders = array();

	public function __construct(&$params)
	{
		$this->params = $params;
	}

	function clean()
	{
		if (!$type = $this->getCleanType())
		{
			return;
		}

		// Load language for messaging
		NNFrameworkFunctions::loadLanguage('mod_cachecleaner');

		$this->cleanCache($type);

		if (JFactory::getApplication()->input->getInt('break'))
		{
			echo (!$this->error ? '+' : '') . $this->message;
			die;
		}

		if ($this->show_message && $this->message)
		{
			JFactory::getApplication()->enqueueMessage($this->message, ($this->error ? 'error' : 'message'));
		}
	}

	function getCleanType()
	{
		$cleancache = JFactory::getApplication()->input->getString('cleancache');

		// Clean via url
		if ($cleancache != '')
		{
			// Return blank if on frontend and no secret url key is given
			if (JFactory::getApplication()->isSite() && ($cleancache == '' || $cleancache != $this->params->frontend_secret))
			{
				return '';
			}

			$this->show_message = true;

			return 'clean';
		}

		// Clean via save task
		if ($this->passTask())
		{
			return 'save';
		}


		return '';
	}

	function passTask()
	{
		if (!$task = JFactory::getApplication()->input->get('task'))
		{
			return false;
		}

		$task = explode('.', $task, 2);
		$task = isset($task['1']) ? $task['1'] : $task['0'];
		if (strpos($task, 'save') === 0)
		{
			$task = 'save';
		}

		$tasks = array_diff(array_map('trim', explode(',', $this->params->auto_save_tasks)), array(''));

		if (empty($tasks) || !in_array($task, $tasks))
		{
			return false;
		}

		if (JFactory::getApplication()->isAdmin() && $this->params->auto_save_admin)
		{
			$this->show_message = $this->params->auto_save_admin_msg;

			return true;
		}

		if (JFactory::getApplication()->isSite() && $this->params->auto_save_front)
		{
			$this->show_message = $this->params->auto_save_front_msg;

			return true;
		}

		return false;
	}


	function cleanCache($type = 'clean')
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// remove all folders and files in cache folder
		$this->emptyCacheFolders();


		// Folders
		if ($type == 'clean'
			|| ($type == 'save' && $this->params->auto_save_folders)
		)
		{
			$this->emptyFolders();
		}


		if ($this->params->purge)
		{
			$this->purgeCache();
		}

		if ($this->params->purge_updates)
		{
			$this->purgeUpdateCache();
		}


		

		if ($this->error)
		{
			$this->message = JText::_('CC_NOT_ALL_CACHE_COULD_BE_REMOVED');

			return;
		}

		$this->message = JText::_('CC_CACHE_CLEANED');

		if ($this->params->show_size && $this->size)
		{
			$this->message .= ' (' . $this->getSize() . ')';
		}
	}

	function getIgnoreFolders()
	{
		if (empty($this->params->ignore_folders))
		{
			return array();
		}

		if (!empty($this->ignore_folders))
		{
			return $this->ignore_folders;
		}

		$ignore_folders = explode("\n", str_replace('\n', "\n", $this->params->ignore_folders));
		foreach ($ignore_folders as &$folder)
		{
			if (trim($folder) == '')
			{
				continue;
			}
			$folder = rtrim(str_replace('\\', '/', trim($folder)), '/');
			$folder = str_replace('//', '/', JPATH_SITE . '/' . $folder);
		}

		return $ignore_folders;
	}

	function emptyCacheFolders()
	{
		$this->emptyFolder(JPATH_SITE . '/cache');
		$this->emptyFolder(JPATH_ADMINISTRATOR . '/cache');
	}


	function purgeCache()
	{
		$cache = JFactory::getCache();
		$cache->gc();
	}


	function emptyFolders()
	{
		// Empty tmp folder
		if ($this->params->clean_tmp)
		{
			$this->emptyFolder(JPATH_SITE . '/tmp');
		}

	}

	function emptyFolder($path)
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$size = 0;

		if (JFolder::exists($path))
		{
			if ($this->params->show_size)
			{
				$size = $this->getFolderSize($path);
			}

			// remove folders
			$folders = JFolder::folders($path);
			foreach ($folders as $folder)
			{
				$f = $path . '/' . $folder;
				if (!in_array($f, $this->getIgnoreFolders()) && @opendir($path . '/' . $folder))
				{
					if ($this->isIgnoredParent($f))
					{
						$this->emptyFolder($f);
					}
					else
					{
						if (!JFolder::delete($path . '/' . $folder))
						{
							$this->error = 1;
						}
						if ($folder == 'com_zoo')
						{
							JFolder::create($path . '/' . $folder);
						}
					}
				}
			}

			// remove files
			$files = JFolder::files($path);
			foreach ($files as $file)
			{
				if ($file != 'index.html' && !in_array($path . '/' . $file, $this->getIgnoreFolders()))
				{
					if (!JFile::delete($path . '/' . $file))
					{
						$this->error = 1;
					}
				}
			}

			if ($this->params->show_size)
			{
				$size -= $this->getFolderSize($path);
			}
		}

		if ($this->params->show_size)
		{
			$this->size += $size;
		}
	}

	/*
	 * Check if folder is a parent path of something in the ignore list
	 */
	function isIgnoredParent($path)
	{
		$check = $path . '/';
		$len = strlen($check);

		foreach ($this->getIgnoreFolders() as $ignore_folder)
		{
			if (substr($ignore_folder, 0, $len) == $check)
			{
				return true;
			}
		}

		return false;
	}

	function getFolderSize($path)
	{
		jimport('joomla.filesystem.file');

		if (JFile::exists($path))
		{
			return @filesize($path);
		}

		jimport('joomla.filesystem.folder');
		if (!JFolder::exists($path) || !(@opendir($path)))
		{
			return 0;
		}

		$size = 0;
		foreach (JFolder::files($path) as $file)
		{
			$size += @filesize($path . '/' . $file);
		}

		foreach (JFolder::folders($path) as $folder)
		{
			if (!@opendir($path . '/' . $folder))
			{
				continue;
			}

			$size += $this->getFolderSize($path . '/' . $folder);
		}

		return $size;
	}

	function purgeUpdateCache()
	{
		$db = JFactory::getDBO();
		$db->setQuery('TRUNCATE TABLE #__updates');
		if (!$db->execute())
		{
			return;
		}

		// Reset the last update check timestamp
		$query = $db->getQuery(true)
			->update('#__update_sites')
			->set('last_check_timestamp = ' . $db->quote(0));
		$db->setQuery($query);
		$db->execute();
	}


	function getSize()
	{
		if ($this->size >= 1048576)
		{
			// Return in MBs
			return (round($this->size / 1048576 * 100) / 100) . 'MB';
		}

		// Return in KBs
		return (round($this->size / 1024 * 100) / 100) . 'KB';
	}
}
