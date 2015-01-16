<?php
/**
 * Uninstallation File
 * Performs some extra tasks when uninstalling the component
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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

$ext = 'snippets';

// Delete plugin files
$folder = JPATH_PLUGINS . '/editors-xtd/' . $ext;
if (JFolder::exists($folder))
{
	JFolder::delete($folder);
}
$folder = JPATH_PLUGINS . '/system/' . $ext;
if (JFolder::exists($folder))
{
	JFolder::delete($folder);
}

// Delete plugin language files
$lang_folder = JPATH_ADMINISTRATOR . '/language';
$languages = JFolder::folders($lang_folder);
foreach ($languages as $lang)
{
	$file = $lang_folder . '/' . $lang . '/' . $lang . '.plg_editors-xtd_' . $ext . '.ini';
	if (JFile::exists($file))
	{
		JFile::delete($file);
	}
	$file = $lang_folder . '/' . $lang . '/' . $lang . '.plg_editors-xtd_' . $ext . '.sys.ini';
	if (JFile::exists($file))
	{
		JFile::delete($file);
	}
	$file = $lang_folder . '/' . $lang . '/' . $lang . '.plg_system_' . $ext . '.ini';
	if (JFile::exists($file))
	{
		JFile::delete($file);
	}
	$file = $lang_folder . '/' . $lang . '/' . $lang . '.plg_system_' . $ext . '.sys.ini';
	if (JFile::exists($file))
	{
		JFile::delete($file);
	}
}
