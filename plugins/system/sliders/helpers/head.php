<?php
/**
 * Plugin Helper File: Head
 *
 * @package         Sliders
 * @version         4.0.5
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class plgSystemSlidersHelperHead
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = plgSystemSlidersHelpers::getInstance();
		$this->params = $this->helpers->getParams();
	}

	public function addHeadStuff()
	{
		// do not load scripts/styles on feeds or print pages
		if (JFactory::getDocument()->getType() == 'feed' || JFactory::getApplication()->input->getInt('print', 0))
		{
			return;
		}

		if ($this->params->load_bootstrap_framework)
		{
			JHtml::_('bootstrap.framework');
		}


		$script = '
			var nn_sliders_urlscroll = 0;
			var nn_sliders_use_hash = ' . (int) $this->params->use_hash . ';
			';
		JFactory::getDocument()->addScriptDeclaration('/* START: Sliders scripts */ ' . preg_replace('#\n\s*#s', ' ', trim($script)) . ' /* END: Sliders scripts */');

		JHtml::script('sliders/script.min.js', false, true);

		switch ($this->params->load_stylesheet)
		{
			case 2:
				JHtml::stylesheet('sliders/old.min.css', false, true);
				break;

			case 1:
				JHtml::stylesheet('sliders/style.min.css', false, true);
				break;

			case 0:
			default:
				// Do not load styles
				break;
		}

	}

	public function removeHeadStuff(&$html)
	{
		// Don't remove if sliders id is found
		if (strpos($html, 'id="set-nn_sliders') !== false)
		{
			return;
		}

		// remove style and script if no items are found
		$html = preg_replace('#\s*<' . 'link [^>]*href="[^"]*/(sliders/css|css/sliders)/[^"]*\.css[^"]*"[^>]* />#s', '', $html);
		$html = preg_replace('#\s*<' . 'script [^>]*src="[^"]*/(sliders/js|js/sliders)/[^"]*\.js[^"]*"[^>]*></script>#s', '', $html);
		$html = preg_replace('#/\* START: Sliders .*?/\* END: Sliders [a-z]* \*/\s*#s', '', $html);
	}
}
