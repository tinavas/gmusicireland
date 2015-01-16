<?php
/**
*Jw Player Module : mod_playerjr
* @version     SVN:$Id$
* @package     Mod_Playerjr
* @subpackage  helper.php
* @author      "Joomlarulez" 
* @copyright   (C) 2009 - 2014 www.joomlarulez.com
* @license     Limited http://www.gnu.org/licenses/gpl.html
* @final 3.11.0
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class Modplayerjr_Helper
{
	/**
	 * @param   array &$params  An object containing the module parameters
	 *
	 * @access public
	 */
	public static function getParams(&$params)
	{

		// Module Parameters
		$params->def('PlaylistFlashinstall', '1');
		$params->def('Playlistjoomlarulezlink', '1');
		$params->def('Playlistplaylist', 'none');
		$params->def('Playlistcontrols', '1');
		$params->def('Playlistplaylistsize', '180');
		$params->def('Playlistheight', '270');
		$params->def('Playlistwidth', '480');
		$params->def('Playlistautostart', '0');
		$params->def('Playlistrepeat', '0');
		$params->def('Playliststretching', 'uniform');
		$params->def('Playlistmute', '0');
		$params->def('Playliststreamer', '');
		$params->def('Playlistimage', '');

		// Playlist Parameter
		$params->def('mod_plfile', 'http://www.joomlarulez.com/images/stories/playlist/big_buck_bunny/big_buck_bunny.xml');
		$params->def('mod_plselect', '0');

		return $params;
	}
}
