<?php
/**
*Jw Player Module : mod_playerjr
* @version     SVN:$Id$
* @package     Mod_Playerjr
* @subpackage  install.script.php
* @author      "Joomlarulez" 
* @copyright   (C) 2009 - 2014 www.joomlarulez.com
* @license     Limited http://www.gnu.org/licenses/gpl.html
* @final 3.11.0
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * JW Player Module Script file
 *
 * @package  Mod_Playerjr
 * @since    3.0.0
 */
class Mod_PlayerjrInstallerScript
{
	/**
	* method to run after an install/update/uninstall method
	* 
	* @param   object  $type  	The object to observe
	* @param   class   $parent  	Class calling this method
	* 
	* @return void
	*/
	function postflight($type, $parent)
	{

		// Re define DS constant for J3.0 +
		if (!(defined('DS')))
		{
			define('DS', DIRECTORY_SEPARATOR);
		}

		// Rename manifest file
		$path = $parent->getParent()->getPath('extension_root');
		if (JFile::exists($path . "/mod_playerjr.j25.xml"))
		{
				if (JFile::exists($path . "/mod_playerjr.xml"))
				{
					JFile::delete($path . "/mod_playerjr.xml");
				}
				JFile::move($path . "/mod_playerjr.j25.xml", $path . "/mod_playerjr.xml");
		}

		// Set crossdomain.xml file if need
		if (!(JFile::exists(JPATH_ROOT . DS . "crossdomain.xml")))
		{
			JFile::copy($path . "/library/crossdomain.html", JPATH_ROOT . DS . "crossdomain.xml");
		}

		// Delete temporary crossdomain.html file
		JFile::delete($path . "/library/crossdomain.html");
	}
}
