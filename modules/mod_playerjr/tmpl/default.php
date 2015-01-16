<?php
/**
*Jw Player Module : mod_playerjr
* @version     SVN:$Id$
* @package     Mod_Playerjr
* @subpackage  default.php
* @author      "Joomlarulez" 
* @copyright   (C) 2009 - 2014 www.joomlarulez.com
* @license     Limited http://www.gnu.org/licenses/gpl.html
* @final 3.11.0
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Check if the user have player file
if ($is_jwplayer_playlist_file != '')
{

// Construct the Global parameter for the playlist
echo
"<div align=\"center\">";

	// Set JS
	$document->addScript($jwplayeradvanced_pathway . "player/6/jwplayer.js");
	echo
	"
	<div id='jwplayer" . $jwplayerclasspl_sfx . "'>" . $is_jwplayer_flash . "</div>";

	// Load JWEmbedderConfig framework
	include JPATH_BASE . DS . 'modules' . DS . 'mod_playerjr' . DS . 'includes' . DS . 'framework' . DS . 'JWEmbedderConfig.php';
	echo
	"
</div>";

// Valid or not the joomlarulezlink
if ($is_jwplayer_playlist_joomlarulezlink == '1')
{
echo
"
<div align=\"center\">
	Powered By: <a class=\"blank\" href=\"http://www.joomlarulez.com\" target=\"_blank\">http://www.joomlarulez.com</a>
</div>
";
}
}
