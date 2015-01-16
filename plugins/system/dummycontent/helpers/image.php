<?php
/**
 * Plugin Helper File: Image
 *
 * @package         Dummy Content
 * @version         1.2.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class plgSystemDummyContentHelperImage
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = plgSystemDummyContentHelpers::getInstance();
		$this->params = $this->helpers->getParams();
	}

	public function render(&$options)
	{
		$options->width = isset($options->width) ? (int) $options->width : (int) $this->params->image_width;
		$options->height = isset($options->height) ? (int) $options->height : (int) $this->params->image_height;

		$alt = ' alt="' . (isset($options->alt) ? $options->alt : '') . '"';
		$title = isset($options->title) ? ' title="' . $options->title . '"' : '';
		$class = isset($options->class) ? ' class="' . $options->class . '"' : '';
		$float = isset($options->float) ? ' style="float:' . $options->float . ';"' : '';

		$url = $this->lorempixel($options);

		// make the url unique
		$this->addToUrl($url, uniqid());

		return '<img src="' . $url . '" width="' . $options->width . '" height="' . $options->height . '"' . $alt . $title . $class . $float . ' />';
	}

	private function addToUrl(&$url, $attribute)
	{
		if (empty($attribute))
		{
			return;
		}

		$url .= strpos($url, '?') === false ? '?' : '&';
		$url .= $attribute;
	}

	private function getText(&$options)
	{
		if (isset($options->text))
		{
			$options->text = trim($options->text);
			switch ($options->text)
			{
				case '':
				case 'none':
					return '+';

				case 'dimensions':
				case 'dimentions':
					return '';

				default:
					return $options->text;
			}
		}

		switch ($this->params->image_show_text)
		{
			case 'none':
				return '+';

			case 'dimensions':
				return '';

			default:
				return $this->params->image_text ?: '+';
		}
	}


	private function lorempixel(&$options)
	{
		$options->category = isset($options->category) ? $options->category : $this->params->image_category_lorempixel;
		$options->color = isset($options->color) ? $options->color : $this->params->image_color_scheme;

		$text = str_replace(array('.', '/', '\\', ';', '|'), '', $this->getText($options));

		if ($text == '')
		{
			$text = $options->width . ' x ' . $options->height;
		}
		if ($text == '+')
		{
			$text = '';
		}

		$url = 'http://lorempixel.com'
			. ($options->color ? '' : '/g')
			. '/' . $options->width . '/' . $options->height
			. (($options->category == 'none') ? '' : '/' . $options->category)
			. ($text ? '/' . $text : '');

		return $url;
	}

}
