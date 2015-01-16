<?php
/**
 * Plugin Helper File
 *
 * @package         Tooltips
 * @version         3.7.7
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Load common functions
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_tooltips');

/**
 * Plugin that replaces stuff
 */
class plgSystemTooltipsHelper
{
	public function __construct(&$params)
	{
		$this->params = $params;

		$this->params->comment_start = '<!-- START: Tooltips -->';
		$this->params->comment_end = '<!-- END: Tooltips -->';

		$this->params->tag = preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag);
		$this->params->regex = '#'
			. '\{' . preg_quote($this->params->tag, '#') . '((?:\s|&nbsp;|&\#160;|<)(?:[^\}]*\{[^\}]*\})*[^\}]*)\}'
			. '(.*?)'
			. '\{/' . preg_quote($this->params->tag, '#') . '\}'
			. '#s';

		$this->params->protected_tags = array(
			$this->params->tag
		);

		$this->params->disabled_components = array('com_acymailing');
	}

	public function onContentPrepare(&$article, &$context)
	{
		NNFrameworkHelper::processArticle($article, $context, $this, 'replaceTags');
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed')
		{
			return;
		}

		$buffer = JFactory::getDocument()->getBuffer('component');

		if (empty($buffer) || is_array($buffer))
		{
			return;
		}

		// do not load scripts/styles on print page
		if (JFactory::getDocument()->getType() !== 'feed' && !JFactory::getApplication()->input->getInt('print', 0) && !JFactory::getApplication()->input->getInt('noscript', 0))
		{
			if ($this->params->load_bootstrap_framework)
			{
				JHtml::_('bootstrap.framework');
			}

			JHtml::script('tooltips/script.min.js', false, true);
			if ($this->params->load_stylesheet)
			{
				JHtml::stylesheet('tooltips/style.min.css', false, true);
			}

			$styles = array();
			if ($this->params->color_link)
			{
				$styles['.nn_tooltips-link'][] = 'color: ' . $this->params->color_link;
			}
			if ($this->params->underline && $this->params->underline_color)
			{
				$styles['.nn_tooltips-link'][] = 'border-bottom: 1px ' . $this->params->underline . ' ' . $this->params->underline_color;
			}
			if ($this->params->max_width)
			{
				$styles['.nn_tooltips.popover'][] = 'max-width: ' . (int) $this->params->max_width . 'px';
			}
			if ($this->params->zindex)
			{
				$styles['.nn_tooltips.popover'][] = 'z-index: ' . (int) $this->params->zindex;
			}
			if ($this->params->border_color)
			{
				$styles['.nn_tooltips.popover'][] = 'border-color: ' . $this->params->border_color;
				$styles['.nn_tooltips.popover.top .arrow'][] = 'border-top-color: ' . $this->params->border_color;
			}
			if ($this->params->bg_color_text)
			{
				$styles['.nn_tooltips.popover'][] = 'background-color: ' . $this->params->bg_color_text;
				$styles['.nn_tooltips.popover.top .arrow:after'][] = 'border-top-color: ' . $this->params->bg_color_text;
			}
			if ($this->params->text_color)
			{
				$styles['.nn_tooltips.popover'][] = 'color: ' . $this->params->text_color;
			}
			if ($this->params->link_color)
			{
				$styles['.nn_tooltips.popover a'][] = 'color: ' . $this->params->link_color;
			}
			if ($this->params->bg_color_title)
			{
				$styles['.nn_tooltips.popover .popover-title'][] = 'background-color: ' . $this->params->bg_color_title;
			}
			if ($this->params->title_color)
			{
				$styles['.nn_tooltips.popover .popover-title'][] = 'color: ' . $this->params->title_color;
			}
			if (!empty($styles))
			{
				$style = array();
				foreach ($styles as $key => $vals)
				{
					$style[] = $key . ' {' . implode(';', $vals) . ';}';
				}
				JFactory::getDocument()->addStyleDeclaration('/* START: Tooltips styles */ ' . implode(' ', $style) . ' /* END: Tooltips styles */');
			}
		}

		if (strpos($buffer, '{' . $this->params->tag) === false)
		{
			return;
		}

		$this->replaceTags($buffer, 'component');

		JFactory::getDocument()->setBuffer($buffer, 'component');
	}

	public function onAfterRender()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed')
		{
			return;
		}

		$html = JResponse::getBody();
		if ($html == '')
		{
			return;
		}

		if (strpos($html, '{' . $this->params->tag) === false)
		{
			if (strpos($html, 'class="nn_tooltips-link') === false)
			{
				// remove style and script if no items are found
				$html = preg_replace('#\s*<' . 'link [^>]*href="[^"]*/(tooltips/css|css/tooltips)/[^"]*\.css[^"]*"[^>]* />#s', '', $html);
				$html = preg_replace('#\s*<' . 'script [^>]*src="[^"]*/(tooltips/js|js/tooltips)/[^"]*\.js[^"]*"[^>]*></script>#s', '', $html);
				$html = preg_replace('#/\* START: Tooltips .*?/\* END: Tooltips [a-z]* \*/\s*#s', '', $html);
			}

			$this->cleanLeftoverJunk($html);

			JResponse::setBody($html);

			return;
		}

		// only do stuff in body
		list($pre, $body, $post) = nnText::getBody($html);
		$this->replaceTags($body, 'body');
		$html = $pre . $body . $post;

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	public function replaceTags(&$string, $area = 'article')
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		if ($area == 'component')
		{
			// allow in component?
			if (in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components))
			{
				$this->protectTags($string);

				return;
			}
		}

		if (strpos($string, '{' . $this->params->tag) === false)
		{
			return;
		}

		$this->protect($string);

		if (preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$tip = $match['1'];
				$text = $match['2'];

				$classes = str_replace('\|', '[:TT_BAR:]', $tip);
				$classes = explode('|', $classes);
				foreach ($classes as $i => $class)
				{
					$classes[$i] = trim(str_replace('[:TT_BAR:]', '|', $class));
				}
				$tip = array_shift($classes);

				$classes_popover = $classes;

				$classes = array_diff($classes, array('hover', 'sticky', 'click'));
				$classes[] = 'hover';

				$position = 'top';

				if (preg_match_all('#href="([^"]*)"#si', $tip, $url_matches, PREG_SET_ORDER) > 0)
				{
					foreach ($url_matches as $url_match)
					{
						$url = 'href="' . JRoute::_($url_match['1']) . '"';
						$tip = str_replace($url_match['0'], $url, $tip);
					}
				}
				if (preg_match_all('#src="([^"]*)"#si', $tip, $url_matches, PREG_SET_ORDER) > 0)
				{
					foreach ($url_matches as $url_match)
					{
						$url = $url_match['1'];
						if (strpos($url, 'http') !== 0)
						{
							$url = JURI::root() . $url;
						}
						$url = 'src="' . $url . '"';
						$tip = str_replace($url_match['0'], $url, $tip);
					}
				}

				$tip = explode('::', $this->makeSave($tip), 2);
				if (!isset($tip['1']))
				{
					$classes_popover[] = 'notitle';
					$title = '';
					$content = $tip['0'];
					if (preg_match('#^\s*(&lt;|<)img [^>]*(&gt;|>)\s*$#', $content))
					{
						$classes_popover[] = 'isimg';
					}
				}
				else
				{
					if (!$tip['1'])
					{
						$classes_popover[] = 'nocontent';
					}
					$title = $tip['0'];
					$content = $tip['1'];
				}

				if (preg_match('#^\s*<img [^>]*>\s*$#', $text))
				{
					$classes[] = 'isimg';
				}

				$template = '<div class="popover nn_tooltips ' . implode(' ', $classes_popover) . '"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>';

				$r = '<span'
					. ' class="nn_tooltips-link ' . implode(' ', $classes) . '"'
					. ' data-toggle="popover"'
					. ' data-html="true"'
					. ' data-template="' . $this->makeSave($template) . '"'
					. ' data-placement="' . $position . '"'
					. ' data-content="' . $content . '"'
					. ' title="' . $title . '">' . $text . '</span>';

				if (in_array('isimg', $classes_popover))
				{
					// place the full image in a hidden span to make it pre-load it
					$r .= '<span style="display:none;">' . html_entity_decode($content) . '</span>';
				}
				$string = str_replace($match['0'], $r, $string);
			}
		}

		NNProtect::unprotect($string);
	}

	private function makeSave($string)
	{
		return str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $string);
	}

	private function protect(&$string)
	{
		NNProtect::protectFields($string);
		NNProtect::protectSourcerer($string);
	}

	private function protectTags(&$string)
	{
		NNProtect::protectTags($string, $this->params->protected_tags);
	}

	private function unprotectTags(&$string)
	{
		NNProtect::unprotectTags($string, $this->params->protected_tags);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	private function cleanLeftoverJunk(&$string)
	{
		$this->unprotectTags($string);

		NNProtect::removeInlineComments($string, 'Tooltips');
	}
}
