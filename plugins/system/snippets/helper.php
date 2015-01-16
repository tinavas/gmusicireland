<?php
/**
 * Plugin Helper File
 *
 * @package         Snippets
 * @version         3.5.2
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

NNFrameworkFunctions::loadLanguage('plg_system_snippets');

/**
 * System Plugin that places a Snippets code block into the text
 */
class plgSystemSnippetsHelper
{
	public function __construct(&$params)
	{
		$this->option = JFactory::getApplication()->input->get('option');

		$this->params = $params;
		$this->params->comment_start = '<!-- START: Snippets -->';
		$this->params->comment_end = '<!-- END: Snippets -->';
		$this->params->message_start = '<!--  Snippets Message: ';
		$this->params->message_end = ' -->';

		$bts = '((?:<p(?: [^>]*)?>\s*)?)';
		$bte = '((?:\s*</p>)?)';
		$this->params->tag_regex = preg_quote($this->params->tag, '#') . (($this->params->tag == 'snippet') ? 's?' : '');
		$this->params->regex = '#' . $bts . '\{' . $this->params->tag_regex . ' ([^\}\|]+)((?:\|.*?[^\\\\])?)\}' . $bte . '#s';

		$this->params->protected_tags = array(
			$this->params->tag
		);
		if ($this->params->tag == 'snippet')
		{
			$this->params->protected_tags[] = $this->params->tag . 's';
		}

		$this->params->disabled_components = array('com_acymailing');

		require_once JPATH_ADMINISTRATOR . '/components/com_snippets/models/list.php';
		$list = new SnippetsModelList;
		$this->items = $list->getItems(1);
	}

	public function onContentPrepare(&$article, &$context)
	{
		NNFrameworkHelper::processArticle($article, $context, $this, 'replaceTags');
	}

	public function onAfterDispatch()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed')
		{
			return;
		}

		$html = JFactory::getDocument()->getBuffer('component');

		if (empty($html) || is_array($html))
		{
			return;
		}

		if (strpos($html, '{' . $this->params->tag) === false)
		{
			return;
		}

		$this->replaceTags($html, 'component');

		JFactory::getDocument()->setBuffer($html, 'component');
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

		$this->replaceTags($html, 'body');

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	function replaceTags(&$string, $area = 'article')
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

		while (preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$snippet_html = $this->processSnippet(trim($match['2']), trim($match['3']));
				if ($this->params->place_comments)
				{
					$snippet_html = $this->params->comment_start . $snippet_html . $this->params->comment_end;
				}
				if (!$match['1'] || !$match['4'])
				{
					$snippet_html = trim($match['1']) . $snippet_html . trim($match['4']);
				}
				$string = str_replace($match['0'], $snippet_html, $string);
			}
		}

		NNProtect::unprotect($string);
	}

	function processSnippet($id, $vars)
	{
		$item = isset($this->items[$id]) ? $this->items[$id] : isset($this->items[html_entity_decode($id, ENT_COMPAT, 'UTF-8')]) ? $this->items[html_entity_decode($id, ENT_COMPAT, 'UTF-8')] : '';

		if (!$item)
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('SNP_OUTPUT_REMOVED_NOT_FOUND') . $this->params->message_end;
			}
			else
			{
				return '';
			}
		}

		if (!$item->published)
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('SNP_OUTPUT_REMOVED_NOT_ENABLED') . $this->params->message_end;
			}
			else
			{
				return '';
			}
		}

		$html = $item->content;

		if ($vars)
		{
			$unprotected = array('\\|', '\\{', '\\}');
			$protected = nnProtect::protectArray($unprotected);
			nnProtect::protectInString($vars, $unprotected, $protected);

			$vars = explode('|', $vars);

			foreach ($vars as $i => $var)
			{
				if ($i)
				{
					nnProtect::unprotectInString($var, array('|', '{', '}'), $protected);
					$html = preg_replace('#\\\\' . $i . '(?![0-9])#', $var, $html);
				}
			}
		}

		if (strpos($html, '[[escape]]') !== false)
		{
			if (preg_match_all('#\[\[escape\]\](.*?)\[\[/escape\]\]#s', $html, $matches, PREG_SET_ORDER) > 0)
			{
				foreach ($matches as $match)
				{
					$replace = addslashes($match['1']);
					$html = str_replace($match['0'], $replace, $html);
				}
			}
		}

		return $html;
	}

	function protect(&$string)
	{
		NNProtect::protectFields($string);
		NNProtect::protectSourcerer($string);
	}

	function protectTags(&$string)
	{
		NNProtect::protectTags($string, $this->params->protected_tags);
	}

	function unprotectTags(&$string)
	{
		NNProtect::unprotectTags($string, $this->params->protected_tags);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	function cleanLeftoverJunk(&$string)
	{
		$this->unprotectTags($string);

		$string = preg_replace('#<\!-- (START|END): SN_[^>]* -->#', '', $string);
		if (!$this->params->place_comments)
		{
			$string = str_replace(
				array(
					$this->params->comment_start, $this->params->comment_end,
					htmlentities($this->params->comment_start), htmlentities($this->params->comment_end),
					urlencode($this->params->comment_start), urlencode($this->params->comment_end)
				), '', $string
			);
			$string = preg_replace('#' . preg_quote($this->params->message_start, '#') . '.*?' . preg_quote($this->params->message_end, '#') . '#', '', $string);
		}
	}
}
