--
-- Database query file
-- For uninstallation
--
-- @package         Advanced Template Manager
-- @version         1.1.7
--
-- @author          Peter van Westen <peter@nonumber.nl>
-- @link            http://www.nonumber.nl
-- @copyright       Copyright © 2014 NoNumber All Rights Reserved
-- @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
--

DELETE FROM `#__extensions`
WHERE `type` = 'plugin' AND `element` = 'advancedtemplates';
