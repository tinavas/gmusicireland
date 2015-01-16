<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

// We will get the view filter
$viewField = $data['view']->filterForm->getField('view');
?>
	<div class="js-stools-field-filter js-stools-view pull-left">
		<?php echo $viewField->input; ?>
	</div>
<?php
// Display the main joomla layout
echo JLayoutHelper::render('joomla.searchtools.default.bar', $data, null, array('component' => 'none'));
