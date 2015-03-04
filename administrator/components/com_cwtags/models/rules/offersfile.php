<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import Joomla formrule library
jimport('joomla.form.formrule');
// pomocnika
require_once JPATH_COMPONENT . '/helpers/offers.php';

class JFormRuleOffersFile extends JFormRule
{
	/**
	 * Method to test for a valid color in hexadecimal.
	 *
	 * @param   SimpleXMLElement  &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value     The form field value to validate.
	 * @param   string            $group     The field name group control value. This acts as as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 * @param   object            &$input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   object            &$form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   11.2
	 * @throws  JException on invalid rule.
	 */
	
	public function test(&$element, $value, $group = null, &$input = null, &$form = null) {

    if (empty($value))
		{
			return true;
		}
		
		$value = (string) $value;
		$allowed_file_types = array('zip','jpg','jpeg','pdf','doc','docx','png','rar');
		
		foreach ($allowed_file_types as $extension) {
			$results = substr($value, -strlen($extension));
			if ($results == $extension) {
				return true;
			}
		}
		return new JException(JText::sprintf('COM_CWTAGS_INSERTOFFER_FIELD_FILE_DESC2', JText::_((string) $element['label'])), 1, E_WARNING);
	}
}
?>