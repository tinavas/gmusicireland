<?php
/**
 * @package         Advanced Template Manager
 * @version         1.3.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('predefinedlist');

/**
 * Selection field for template filter
 */
class JFormFieldClientID extends JFormFieldPredefinedList
{
	/**
	 * The form field type.
	 */
	protected $type = 'ClientID';

	protected function getOptions()
	{
		return array_merge(JFormFieldList::getOptions(), AdvancedTemplatesHelper::getClientOptions());
	}
}
