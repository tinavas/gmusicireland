<?php
/**
 * Plugin Helper File
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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/tags.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

nnFrameworkFunctions::loadLanguage('plg_system_dummycontent');

/**
 * Plugin that places dummy texts
 */
class plgSystemDummyContentHelper
{
	var $helpers = array();

	public function __construct(&$params)
	{

		$this->option = JFactory::getApplication()->input->get('option');

		$this->params = $params;

		$bts = '((?:<[a-zA-Z][^>]*>\s*){0,3})'; // break tags start
		$bte = '((?:\s*<(?:/[a-zA-Z]|br|BR)[^>]*>){0,3})'; // break tags end

		$this->params->tags = explode(',', preg_replace('#[^a-z0-9-_,]#si', '', $this->params->tag));

		$this->params->regex = '#'
			. $bts
			. '\{(?:' . implode('|', $this->params->tags) . ')'
			. '((?:(?:\s|&nbsp;|&\#160;)'
			. '(?:[^\}]*?\{[^\}]*?\})*[^\}]*?)?)\}'
			. $bte
			. '#s';

		$this->params->disabled_components = array('com_acymailing');

		require_once __DIR__ . '/helpers/helpers.php';
		$this->helpers = plgSystemDummyContentHelpers::getInstance($params);
	}

	public function onContentPrepare(&$article, &$context)
	{
		$area = isset($article->created_by) ? 'articles' : 'other';

		nnFrameworkHelper::processArticle($article, $context, $this, 'replaceTags', array(&$article, $area));
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

		if (!preg_match('#{(?:' . implode('|', $this->params->tags) . ')#', $html))
		{
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

		if (!preg_match('#{(?:' . implode('|', $this->params->tags) . ')#', $string))
		{
			return;
		}

		$this->protect($string);

		$this->replace($string);

		nnProtect::unprotect($string);
	}

	private function replace(&$string)
	{

		if (preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER) < 1)
		{
			return;
		}

		foreach ($matches as $match)
		{
			$options = $this->getOptions($match['2']);
			$text = $this->generate($options);

			$pre = $match['1'];
			$post = $match['3'];
			if (substr($text, 0, 3) == '<p>')
			{
				list($pre, $post) = nnTags::setSurroundingTags($pre, $post);
			}

			$string = nnText::strReplaceOnce($match['0'], $pre . $text . $post, $string);
		}
	}

	private function getOptions($string = '')
	{
		$options = new stdClass;

		$string = trim(str_replace(array('&nbsp;', '&#160;'), '', $string));
		if ($string == '')
		{
			return $options;
		}

		$string = explode('|', $string);
		foreach ($string as $sub_string)
		{
			$key = $sub_string;
			$val = 1;
			if (strpos($sub_string, '=') !== false)
			{
				list($key, $val) = explode('=', $sub_string, 2);
			}

			switch ($key)
			{
				case 'i' :
				case 'img' :
				case 'images' :
					$key = 'image';
					break;

				case 'p' :
				case 'paragraph' :
					$key = 'paragraphs';
					break;

				case 's' :
				case 'sentence' :
					$key = 'sentences';
					break;

				case 'word' :
				case 'w' :
					$key = 'words';
					break;

				case 'l' :
				case 'lists' :
					$key = 'list';
					break;

				case 't' :
				case 'titles' :
					$key = 'title';
					break;

				case 'e' :
				case 'emails' :
					$key = 'email';
					break;

				case 'k' :
				case 'ks' :
				case 'kitchen' :
				case 'sink' :
					$key = 'kitchensink';
					break;
			}

			$options->{$key} = $val;
		}

		return $options;
	}

	private function generate($options = '')
	{
		if (isset($options->image))
		{
			return $this->generateImage($options);
		}

		return $this->generateText($options);
	}

	private function generateImage($options = '')
	{
		return $this->helpers->get('image')->render($options);
	}

	private function generateText($options = '')
	{

		switch (true)
		{
			case (isset($options->kitchensink)) :
				$text = $this->helpers->get('text')->kitchenSink();
				break;
			case (isset($options->paragraphs)) :
				$text = $this->helpers->get('text')->paragraphs((int) $options->paragraphs);
				break;
			case (isset($options->sentences)) :
				$text = $this->helpers->get('text')->sentences((int) $options->sentences);
				break;
			case (isset($options->words)) :
				$text = $this->helpers->get('text')->words((int) $options->words);
				break;
			case (isset($options->list)) :
				$type = isset($options->list_type) ? $options->list_type : '';
				$text = $this->helpers->get('text')->alist((int) $options->list, $type);
				break;
			case (isset($options->title)) :
				$text = $this->helpers->get('text')->title((int) $options->title);
				break;
			case (isset($options->email)) :
				$text = $this->helpers->get('text')->email();
				break;
			case ($this->params->type == 'list') :
				$text = $this->helpers->get('text')->alist((int) $this->params->list_count, $this->params->list_type);
				break;
			default :
				$type = method_exists('plgSystemDummyContentHelperText', $this->params->type) ? $this->params->type : 'paragraphs';

				$count = isset($this->params->{$type . '_count'}) ? $this->params->{$type . '_count'} : 0;

				$text = $this->helpers->get('text')->$type((int) $count);
				break;
		}

		return $text;
	}

	private function protect(&$string)
	{
		nnProtect::protectFields($string);
		nnProtect::protectSourcerer($string);
	}

	private function protectTags(&$string)
	{
		nnProtect::protectTags($string, $this->params->tags, false);
	}

	private function unprotectTags(&$string)
	{
		nnProtect::unprotectTags($string, $this->params->tags, false);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	private function cleanLeftoverJunk(&$string)
	{
		$this->unprotectTags($string);

		nnProtect::removeFromHtmlTagContent($string, $this->params->tags, false);
	}
}
