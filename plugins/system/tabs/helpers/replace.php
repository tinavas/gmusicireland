<?php
/**
 * Plugin Helper File: Replace
 *
 * @package         Tabs
 * @version         4.0.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class plgSystemTabsHelperReplace
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = plgSystemTabsHelpers::getInstance();
		$this->params = $this->helpers->getParams();

		$this->params->tag_delimiter = ($this->params->tag_delimiter == 'space') ? '(?:\s|&nbsp;|&\#160;)' : '=';

		$bts = '((?:<[a-zA-Z][^>]*>\s*){0,3})'; // break tags start
		$bte = '((?:\s*<(?:/[a-zA-Z]|br|BR)[^>]*>){0,3})'; // break tags end

		$this->params->regex = '#'
			. $bts
			. '\{(' . $this->params->tag_open . 's?'
			. '((?:-[a-zA-Z0-9-_]+)?)'
			. $this->params->tag_delimiter
			. '((?:[^\}]*?\{[^\}]*?\})*[^\}]*?)|/' . $this->params->tag_close
			. '(?:-[a-z0-9-_]*)?)\}'
			. $bte
			. '#s';
		$this->params->regex_end = '#'
			. $bts
			. '\{/' . $this->params->tag_close
			. '(?:-[a-z0-9-_]+)?\}'
			. $bte
			. '#s';
		$this->params->regex_link = '#'
			. '\{' . $this->params->tag_link
			. '(?:-[a-z0-9-_]+)?' . $this->params->tag_delimiter
			. '([^\}]*)\}'
			. '(.*?)'
			. '\{/' . $this->params->tag_link . '\}'
			. '#s';

		$this->ids = array();
		$this->matches = array();
		$this->allitems = array();
		$this->setcount = 0;

		$this->setMainClass();
	}

	private function setMainClass()
	{
		$this->mainclass = array();
		if ($this->params->load_stylesheet == 2)
		{
			$this->mainclass[] = 'oldschool';
			$this->params->use_responsive_view = 0;

			return;
		}

		if ($this->params->color_inactive_handles)
		{
			$this->mainclass[] = 'color_inactive_handles';
		}
		if ($this->params->outline_handles)
		{
			$this->mainclass[] = 'outline_handles';
		}
		if ($this->params->outline_content)
		{
			$this->mainclass[] = 'outline_content';
		}
		if (!$this->params->alignment)
		{
			$this->params->alignment = JFactory::getLanguage()->isRTL() ? 'right' : 'left';
		}
		$positioning = 'top';
		if ($positioning == 'top' || $positioning == 'bottom')
		{
			$this->mainclass[] = 'align_' . $this->params->alignment;
		}
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
				$this->helpers->get('protect')->protectTags($string);

				return;
			}
		}

		if (
			strpos($string, '{' . $this->params->tag_open) === false
			&& strpos($string, '{' . $this->params->tag_link) === false
		)
		{
			// Links with #tab-name or &tab=tab-name
			$this->replaceLinks($string);

			return;
		}

		$this->helpers->get('protect')->protect($string);

		if (JFactory::getApplication()->input->getInt('print', 0))
		{
			// Replace syntax with general html on print pages
			$this->handlePrintPage($string);

			nnProtect::unprotect($string);

			return;
		}

		$sets = $this->getSets($string);
		$this->initSets($sets);

		// Tag syntax: {tab ...}
		$this->replaceSyntax($string, $sets);

		// Closing tag: {/tab}
		$this->replaceClosingTag($string);

		// Links with #tab-name or &tab=tab-name
		$this->replaceLinks($string);

		// Link tag {tablink ...}
		$this->replaceLinkTag($string);

		nnProtect::unprotect($string);
	}

	private function handlePrintPage(&$string)
	{
		if (preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$title = nnText::cleanTitle($match['4']);
				if (strpos($title, '|') !== false)
				{
					list($title, $extra) = explode('|', $title, 2);
				}
				$title = trim($title);
				$name = nnText::cleanTitle($title, 1);
				$title = preg_replace('#<\?h[0-9](\s[^>]* )?>#', '', $title);
				$replace = '<' . $this->params->title_tag . ' class="nn_tabs-title"><a name="' . $name . '" class="anchor"></a>' . $title . '</' . $this->params->title_tag . '>';
				$string = str_replace($match['0'], $replace, $string);
			}
		}
		if (preg_match_all($this->params->regex_end, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$string = str_replace($match['0'], '', $string);
			}
		}
		if (preg_match_all($this->params->regex_link, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$href = nnText::getURI($match['1']);
				$link = '<a href="' . $href . '">' . $match['2'] . '</a>';
				$string = str_replace($match['0'], $link, $string);
			}
		}
	}

	private function getSets(&$string)
	{

		if (!preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER))
		{
			return array();
		}

		$sets = array();
		$setids = array();

		foreach ($matches as $match)
		{
			if ($match['2']['0'] == '/')
			{
				array_pop($setids);
				continue;
			}

			end($setids);

			$item = new stdClass;
			$item->orig = $match['0'];
			$item->setid = trim(str_replace('-', '_', $match['3']));

			if (empty($setids) || current($setids) != $item->setid)
			{
				$this->setcount++;
				$setids[$this->setcount . '.'] = $item->setid;
			}

			$item->set = str_replace('__', '_', array_search($item->setid, array_reverse($setids)) . $item->setid);
			$item->title = nnText::cleanTitle($match['4']);
			list($item->pre, $item->post) = nnTags::setSurroundingTags($match['1'], $match['5']);

			if (!isset($sets[$item->set]))
			{
				$sets[$item->set] = array();
			}

			$sets[$item->set][] = $item;
		}

		return $sets;
	}


	private function initSets(&$sets)
	{
		foreach ($sets as $set_id => $items)
		{
			$active = 0;
			foreach ($items as $i => $item)
			{
				// Fix some different syntaxes
				$tag = str_replace(
					array(
						'|alias:',
						'title-close=',
						'title-closed=',
						'title-open=',
						'title-opened=',
					),
					array(
						'|alias=',
						'title-inactive=',
						'title-inactive=',
						'title-active=',
						'title-active=',
					),
					$item->title
				);
				$tag = preg_replace('#^title-(in)?active=#', '', $tag);

				// Get the values from the tag
				$tag = nnTags::getTagValues($tag);

				$item->title = $tag->title;

				$item->title_full = $item->title;

				if (isset($tag->{'title-active'}) || isset($tag->{'title-inactive'}))
				{
					$title_inactive = isset($tag->{'title-inactive'}) ? $tag->{'title-inactive'} : $item->title;
					$title_active = isset($tag->{'title-active'}) ? $tag->{'title-active'} : $item->title;

					// Set main title to the title-active, otherwise to title-inactive
					$item->title = $title_active ?: ($title_inactive ?: $item->title);

					// place the title-active and title-inactive in css controlled spans
					$item->title_full = '<span class="nn_tabs-title-inactive">' . $title_inactive . '</span>'
						. '<span class="nn_tabs-title-active">' . $title_active . '</span>';
				}

				$item->haslink = preg_match('#<a [^>]*>.*?</a>#usi', $item->title);

				$item->title = nnText::cleanTitle($item->title, 1);
				$item->title = $item->title ?: nnText::getAttribute('title', $item->title_full);
				$item->title = $item->title ?: nnText::getAttribute('alt', $item->title_full);
				$item->title = str_replace(array('&nbsp;', '&#160;'), ' ', $item->title);
				$item->title = preg_replace('#\s+#', ' ', $item->title);

				$item->alias = nnText::createAlias(isset($tag->alias) ? $tag->alias : $item->title);
				$item->alias = $item->alias ?: 'tab';

				$item->id = $this->createId($item->alias);
				$item->set = (int) $set_id;
				$item->count = $i + 1;
				$item->active = 0;

				foreach ($tag->params as $j => $val)
				{
					if (!$val)
					{
						continue;
					}

					if (in_array($val, array('active', 'opened', 'open')))
					{
						$item->active = 1;
						$active = $i;
						unset($tag->params[$j]);
						continue;
					}

					if (strpos($val, ' ') !== false)
					{
						$vals = explode(' ', $val);
						foreach ($vals as $v)
						{
							$tag->params[] = $v;
						}
						unset($tag->params[$j]);
					}
				}
				$item->classes = $this->getClassesFromTag($tag);
				$item->class = trim(implode(' ', $item->classes));

				$item->matches = nnText::createUrlMatches(array($item->id, $item->title));
				$item->matches[] = ($i + 1) . '';
				$item->matches[] = $item->set . '-' . ($i + 1);

				$item->matches = array_unique($item->matches);
				$item->matches = array_diff($item->matches, $this->matches);
				$this->matches = array_merge($this->matches, $item->matches);

				if (!$item->active && $active == $i && $item->haslink)
				{
					$active++;
				}

				$sets[$set_id][$i] = $item;
				$this->allitems[] = $item;
			}

			$active = (int) $active;

			if (!isset($sets[$set_id][$active]))
			{
				$active = 0;
			}

			$sets[$set_id][$active]->active = 1;
		}
	}

	private function replaceSyntax(&$string, $sets)
	{
		if (!preg_match($this->params->regex_end, $string))
		{
			return;
		}

		foreach ($sets as $items)
		{
			$this->replaceSyntaxItemList($string, $items);
		}
	}

	private function replaceSyntaxItemList(&$string, $items)
	{
		$first = key($items);
		end($items);

		foreach ($items as $i => &$item)
		{
			$this->replaceSyntaxItem($string, $item, $items, ($i == $first));
		}
	}

	private function replaceSyntaxItem(&$string, $item, $items, $first = 0)
	{
		$s = '#' . preg_quote($item->orig, '#') . '#';
		if (@preg_match($s . 'u', $string))
		{
			$s .= 'u';
		}

		if (!preg_match($s, $string, $match))
		{
			return;
		}

		$html = array();
		$html[] = $item->post;
		$html[] = $item->pre;

		$html[] = $this->getPreHtml($item, $items, $first);

		$class = array();
		$class[] = 'tab-pane nn_tabs-pane';
		if ($item->active)
		{
			$class[] = 'active';
		}
		$class[] = trim($item->class);

		$html[] = '<div class="' . trim(implode(' ', $class)) . '" id="' . $item->id . '">';

		if (!$item->haslink)
		{
			$class = 'anchor';
			$html[] = '<' . $this->params->title_tag . ' class="nn_tabs-title">'
				. '<a name="' . $item->id . '" class="' . $class . '"></a>'
				. $item->title . '</' . $this->params->title_tag . '>';
		}

		$html = implode("\n", $html);
		$string = nnText::strReplaceOnce($match['0'], $html, $string);
	}

	private function getPreHtml($item, $items, $first = 0)
	{
		if (!$first)
		{
			return '</div>';
		}

		$classes = $this->mainclass;

		// Set overruling main classes
		if (in_array('color_inactive_handles=0', $item->classes))
		{
			$this->addClass($item, $classes, '', array('color_inactive_handles', 'color_inactive_handles=0'));
		}
		if (in_array('nooutline', $item->classes))
		{
			$this->addClass($item, $classes, '', array('nooutline', 'outline_handles', 'outline_handles=0', 'outline_content', 'outline_content=0'));
		}
		if (in_array('outline_handles=0', $item->classes))
		{
			$this->addClass($item, $classes, '', array('outline_handles', 'outline_handles=0'));
		}
		if (in_array('outline_content=0', $item->classes))
		{
			$this->addClass($item, $classes, '', array('outline_content', 'outline_content=0'));
		}

		if (in_array('color_inactive_handles', $item->classes))
		{
			$this->addClass($item, $classes, 'color_inactive_handles');
		}
		if (in_array('outline_handles', $item->classes))
		{
			$this->addClass($item, $classes, 'outline_handles');
		}
		if (in_array('outline_content', $item->classes))
		{
			$this->addClass($item, $classes, 'outline_content');
		}

		$positions = array('top', 'bottom', 'left', 'right');
		$aligns = array('align_left', 'align_right', 'align_center', 'align_justify');
		$this->addClass($item, $classes, 'top', $positions);

		foreach ($aligns as $align)
		{
			if (!in_array($align, $item->classes))
			{
				continue;
			}

			$this->addClass($item, $classes, $align, $aligns);
			break;
		}

		$item->class = trim(implode(' ', $item->classes));

		$html[] = '<div class="' . trim('nn_tabs ' . implode(' ', $classes)) . '">';
		$html[] = $this->getNav($items);
		$html[] = '<div class="tab-content">';

		return implode("\n", $html);
	}

	private function addClass(&$item, &$classes, $class = '', $removes = array())
	{
		if (empty($removes))
		{
			$removes = array($class);
		}

		$item->classes = array_diff($item->classes, $removes);
		$classes = array_diff($classes, $removes);

		if ($class)
		{
			$classes[] = $class;
		}
	}

	private function replaceClosingTag(&$string)
	{
		if (!preg_match_all($this->params->regex_end, $string, $matches, PREG_SET_ORDER))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$html = '</div></div></div>';


			list($pre, $post) = nnTags::setSurroundingTags($match['1'], $match['2']);
			$html = $pre . $html . $post;

			$string = nnText::strReplaceOnce($match['0'], $html, $string);
		}
	}

	private function replaceLinks(&$string)
	{
		// Links with #tab-name
		$this->replaceAnchorLinks($string);
		// Links with &tab=tab-name
		$this->replaceUrlLinks($string);
	}

	private function replaceAnchorLinks(&$string)
	{
		if (!preg_match_all(
			'#(<a\s[^>]*href="([^"]*)?\#([^"]*)"[^>]*>)(.*?)</a>#si',
			$string,
			$matches,
			PREG_SET_ORDER
		)
		)
		{
			return;
		}

		$this->replaceLinksMatches($string, $matches);
	}

	private function replaceUrlLinks(&$string)
	{
		if (!preg_match_all(
			'#(<a\s[^>]*href="([^"]*)(?:\?|&(?:amp;)?)tab=([^"\#&]*)(?:\#[^"]*)?"[^>]*>)(.*?)</a>#si',
			$string,
			$matches,
			PREG_SET_ORDER
		)
		)
		{
			return;
		}

		$this->replaceLinksMatches($string, $matches);
	}

	private function replaceLinksMatches(&$string, $matches)
	{
		$uri = JURI::getInstance();
		$current_urls = array();
		$current_urls[] = $uri->toString(array('path'));
		$current_urls[] = $uri->toString(array('scheme', 'host', 'path'));
		$current_urls[] = $uri->toString(array('scheme', 'host', 'port', 'path'));

		foreach ($matches as $match)
		{
			$link = $match['1'];

			if (
				strpos($link, 'data-toggle=') !== false
				|| strpos($link, 'onclick=') !== false
				|| strpos($link, 'nn_tabs-toggle-sm') !== false
			)
			{
				continue;
			}

			$url = $match['2'];
			if (strpos($url, 'index.php/') === 0)
			{
				$url = '/' . $url;
			}

			if (strpos($url, 'index.php') === 0)
			{
				$url = JRoute::_($url);
			}

			if ($url != '' && !in_array($url, $current_urls))
			{
				continue;
			}

			$id = $match['3'];

			if (!$this->stringHasItem($string, $id))
			{
				// This is a link to a normal anchor or other element on the page
				// Remove the prepending obsolete url and leave the hash
				$string = str_replace('href="' . $match['2'] . '#' . $id . '"', 'href="#' . $id . '"', $string);

				continue;
			}

			$attribs = $this->getLinkAttributes($id);

			// Combine attributes with original
			$attribs = nnText::combineAttributes($link, $attribs);

			$html = '<a ' . $attribs . '"><span class="nn_tabs-link-inner">' . $match['4'] . '</span></a>';

			$string = str_replace($match['0'], $html, $string);
		}
	}

	private function replaceLinkTag(&$string)
	{
		if (!preg_match_all($this->params->regex_link, $string, $matches, PREG_SET_ORDER))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$this->replaceLinkTagMatch($string, $match);
		}
	}

	private function replaceLinkTagMatch(&$string, $match)
	{
		$id = nnText::createAlias($match['1']);

		if (!$this->stringHasItem($string, $id))
		{
			$html = '<a href="' . nnText::getURI($id) . '">' . $match['2'] . '</a>';

			$string = nnText::strReplaceOnce($match['0'], $html, $string);

			return;
		}

		$html = '<a ' . $this->getLinkAttributes($id) . '>'
			. '<span class="nn_tabs-link-inner">' . $match['2'] . '</span>'
			. '</a>';

		$string = nnText::strReplaceOnce($match['0'], $html, $string);
	}

	private function getLinkAttributes($id)
	{
		$onclick = 'nnTabs.show(this.rel);return false;';

		return 'href="' . nnText::getURI($id) . '"'
		. ' class="nn_tabs-link nn_tabs-link-' . $id . '"'
		. ' rel="' . $id . '"'
		. ' onclick="' . $onclick . '"';
	}

	private function stringHasItem(&$string, $id)
	{
		return (strpos($string, 'data-toggle="tab" data-id="' . $id . '"') !== false);
	}

	private function getClassesFromTag($tag)
	{
		$classes = $tag->params;
		$overrules = array('color_inactive_handles', 'outline_handles', 'outline_content', 'nooutline');
		foreach ($overrules as $overrule)
		{
			if (!isset($tag->{$overrule}))
			{
				continue;
			}

			if ($tag->{$overrule} === '0')
			{
				$classes[] = $overrule . '=0';
				continue;
			}

			$classes[] = $overrule;
		}

		return $classes;
	}

	private function getNav(&$items)
	{
		$html = array();

		// Nav for non-mobile view
		$html[] = '<a name="nn_tabs-scrollto_' . $items['0']->set . '" class="anchor nn_tabs-scroll"></a>';
		$html[] = '<ul class="nav nav-tabs" id="set-nn_tabs-' . $items['0']->set . '">';
		foreach ($items as $item)
		{
			$html[] = '<li class="' . trim('nn_tabs-tab ' . ($item->active ? 'active' : '') . ' ' . trim($item->class)) . '">';

			if ($item->haslink)
			{
				$html[] = $item->title_full;
				$html[] = '</li>';
				continue;
			}

			$class = 'nn_tabs-toggle';

			$html[] = '<a href="#' . $item->id . '" class="' . $class . '"'
				. ' data-toggle="tab" data-id="' . $item->id . '"'
				. '>'
				. '<span class="nn_tabs-toggle-inner">'
				. $item->title_full
				. '</span></a>';
			$html[] = '</li>';
		}
		$html[] = '</ul>';

		return implode("\n", $html);
	}


	private function createId($alias)
	{
		$id = $alias;

		$i = 1;
		while (in_array($id, $this->ids))
		{
			$id = $alias . '-' . ++$i;
		}

		$this->ids[] = $id;

		return $id;
	}
}
