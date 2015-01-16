<?php
/**
 * @package         Advanced Template Manager
 * @version         1.1.7
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('predefinedlist');

/**
 * Selection field for template filter
 */
class JFormFieldTemplate extends JFormFieldPredefinedList
{
	/**
	 * The form field type.
	 */
	protected $type = 'Template';

	protected function getOptions()
	{
		$model = new AdvancedTemplatesModelStyles;
		$client_id = $model->getState('filter.client_id', '*');
		$client_id = $client_id == '' ? '*' : $client_id;

		return array_merge(JFormFieldList::getOptions(), AdvancedTemplatesHelper::getTemplateOptions($client_id));
	}
}
