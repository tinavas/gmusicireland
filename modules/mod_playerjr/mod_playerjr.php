<?php
/**
*Jw Player Module : mod_playerjr
* @version     SVN:$Id$
* @package     Mod_Playerjr
* @subpackage  mod_playerjr.php
* @author      "Joomlarulez" 
* @copyright   (C) 2009 - 2014 www.joomlarulez.com
* @license     Limited http://www.gnu.org/licenses/gpl.html
* @final 3.11.0
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Re define DS constant for J3.0 +
if (!(defined('DS')))
{
	define('DS', DIRECTORY_SEPARATOR);
}

// Include the syndicate functions only once
require_once dirname(__FILE__) . DS . 'helper.php';

$params = Modplayerjr_Helper::getParams($params);

// Joomla version
$jversion = new JVersion;
$jversion = $jversion->RELEASE;

// Module Parameters
$document = JFactory::getDocument();

// Get pathway
$jwplayer_pathway  = JURI::root() . "modules/mod_playerjr/";
$jwplayer_pathway_DS = JPATH_BASE . DS . 'modules' . DS . 'mod_playerjr' . DS;

$jwplayeradvanced_pathway  = JURI::root() . "media/jwadvanced/";
$jwplayeradvanced_pathway_DS  = JPATH_BASE . DS . 'media' . DS . 'jwadvanced' . DS;


// Module ID
$jwplayerclasspl_sfx = $module->id;

// Flash Install
$moduleflash_sfx = $params->get('PlaylistFlashinstall');
if ($moduleflash_sfx == '1')
{
	$is_jwplayer_flash = "You must have <a href=\"http://get.adobe.com/flashplayer\">the Adobe Flash Player</a> installed to view this player.";
}
else
{
	$is_jwplayer_flash = "";
}

// Link Parameters
$is_jwplayer_playlist_joomlarulezlink = $params->get('Playlistjoomlarulezlink');

// Set array flashvars
$is_jwplayer_flashvars = array();

// Playlist url
$is_jwplayer_playlist_file = $params->get('mod_plfile');

// Set source mode
$is_jwplayer_playlist_select = $params->get('mod_plselect');

if ($is_jwplayer_playlist_select == '1')
{
	$is_jwplayer_playlisttype = "file";
}
elseif ($is_jwplayer_playlist_select == '0')
{
	$is_jwplayer_playlisttype = "playlist";

	// Toutube manipulation
	if (strpos($is_jwplayer_playlist_file, "youtube.com"))
	{

		// Replace youtube playlist with gdata youtube playlist
		$is_jwplayer_playlist_file = str_replace("http://www.youtube.com/playlist?list=PL", "http://gdata.youtube.com/feeds/api/playlists/", $is_jwplayer_playlist_file);

		if (!(strpos($is_jwplayer_playlist_file, "?alt=rss")))
		{

			// Add ?alt=rss for youtube playlist
			$is_jwplayer_playlist_file = $is_jwplayer_playlist_file . "?alt=rss";
		}
	}
}
else
{
	$is_jwplayer_playlisttype = "file";
}

// Set final source
$is_jwplayer_flashvars[$is_jwplayer_playlisttype] = $is_jwplayer_playlist_file;

// Set  flashwars with no default mode
$count = 3;
$index1 = 'height';
$index_defaut1 = '270';
$index2 = 'width';
$index_defaut2 = '480';
for ($i = 1; $i < $count; $i++)
{
	$is_jwplayer_flashvars[${'index' . $i}] = $params->get("Playlist" . ${'index' . $i}, ${'index_defaut' . $i});
}

// Set primary
$is_jwplayer_flashvars["primary"] = "flash";

// Set  simple flaswars
$count = 7;
$index1 = 'stretching';
$index_defaut1 = 'uniform';
$index2 = 'image';
$index_defaut2 = '';

// Set  true false flaswars
$index3 = 'mute';
$index_defaut3 = '0';
$index4 = 'repeat';
$index_defaut4 = '0';
$index5 = 'autostart';
$index_defaut5 = '0';
$index6 = 'controls';
$index_defaut6 = '1';

for ($i = 1; $i < $count; $i++)
{
	$is_jwplayer_playlist_var = $params->get("Playlist" . ${'index' . $i}, ${'index_defaut' . $i});
	if ($is_jwplayer_playlist_var != '' && $is_jwplayer_playlist_var != ${'index_defaut' . $i})
	{
		if ($i > 2)
		{
			$is_jwplayer_flashvars[${'index' . $i}] = $is_jwplayer_playlist_var ? 'true' : 'false';
		}
		else
		{
			$is_jwplayer_flashvars[${'index' . $i}] = $is_jwplayer_playlist_var;
		}
	}
}

// Set listbar.size
$is_jwplayer_flashvars["listbar.size"] = $params->get("Playlistplaylistsize", "180");

// Set listbar.position
$is_jwplayer_flashvars["listbar.position"] = $params->get("Playlistplaylist", "none");

// Set block  flashvars for JW 6.x
$count = 2;
$index1 = 'listbar';
$keys = array_keys($is_jwplayer_flashvars);
foreach ($keys as $key)
{
	for ($i = 1; $i < $count; $i++)
	{
		if (strpos($key, ${'index' . $i}) !== false)
		{
			$subkey_block = preg_replace("#" . ${'index' . $i} . ".#", "", $key);
			$is_jwplayer_flashvars[${'index' . $i}][$subkey_block] = $is_jwplayer_flashvars[$key];
			unset($is_jwplayer_flashvars[$key]);
		}
	}
}

require JModuleHelper::getLayoutPath('mod_playerjr');
