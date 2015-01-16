<?php
/**
*Jw Player Module : mod_playerjr
* @version     SVN:$Id$
* @package     Mod_Playerjr
* @subpackage  JWEmbedderConfig.php
* @author      "Joomlarulez" 
* @copyright   (C) 2009 - 2014 www.joomlarulez.com
* @license     Limited http://www.gnu.org/licenses/gpl.html
* @final 3.11.0
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

			echo
			"
			<script type='text/javascript'>
				jwplayer('jwplayer" . $jwplayerclasspl_sfx . "').setup({";

				// Set flashvars
				$i = 1;
				while (list($key, $value) = each($is_jwplayer_flashvars))
				{
				if ($i > 1)
				{
				echo
				",";
				}

				// Load sub array flashvars if need
				if (is_array($value))
				{
				echo
				"
				'" . $key . "': {";
					$i2 = 1;
					while (list ($key2, $val) = each($value))
					{
					if ($key2 != "" && $val != "")
					{
					if ($i2 > 1)
					{
					echo
					",";
					}
					echo
					"
					'" . $key2 . "': '" . $val . "'";
					$i2++;
					}
					}
					reset($value);
				echo
				"
				}";
				}
				else
				{
				echo
				"
				'" . $key . "': '" . $value . "'";
				}
				$i++;
				}
				reset($is_jwplayer_flashvars);
			echo
			"
				});
			</script>
			";
