<?php
/**
 * Plugin Helper File: Replace
 *
 * @package         ReReplacer
 * @version         5.12.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Plugin that replaces stuff
 */
class plgSystemReReplacerHelperReplace
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = plgSystemReReplacerHelpers::getInstance();
	}

	public function replaceInAreas(&$string)
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		$this->replaceInArea($string, 'component');
		$this->replaceInArea($string, 'head');
		$this->replaceInArea($string, 'body');

		$this->replaceEverywhere($string);
	}

	private function replaceInArea(&$string, $area_type = '')
	{
		if (!is_string($string) || $string == '' || !$area_type)
		{
			return;
		}

		$items = $this->helpers->get('items')->getItemList($area_type);

		if (empty($items))
		{
			return;
		}

		$areas = $this->helpers->get('tag')->getAreaByType($string, $area_type);

		foreach ($areas as $area_type)
		{
			$this->replaceItemList($area_type['1'], $items);
			$string = str_replace($area_type['0'], $area_type['1'], $string);
		}

		unset($areas);
	}

	private function replaceEverywhere(&$string)
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		$items = $this->helpers->get('items')->getItemList('everywhere');

		$this->replaceItemList($string, $items);
	}

	public function replace(&$string, $item)
	{
		if (empty($string))
		{
			return;
		}

		if (is_array($string))
		{
			$this->replaceArray($string, $item);

			return;
		}

		$this->helpers->get('variables')->protectVariables($string);

		switch ($item->regex)
		{
			case true:
				$this->replaceRegEx($string, $item);
				break;
			default:
				$this->replaceString($string, $item);
				break;
		}

		$this->helpers->get('variables')->replaceVariables($string);
	}

	private function replaceArray(&$array, $item)
	{
		if (!is_array($array))
		{
			return;
		}

		foreach ($array as &$string)
		{
			$this->replace($string, $item);
		}
	}

	private function replaceItemList(&$string, $items)
	{
		if (empty($items))
		{
			return;
		}

		if (!is_array($items))
		{
			$items = array($items);
		}

		foreach ($items as $item)
		{
			$this->replace($string, $item);
		}
	}

	private function replaceRegEx(&$string, $item)
	{
		$string = str_replace(chr(194) . chr(160), ' ', $string);
		$string_array = $this->helpers->get('protect')->stringToProtectedArray($string, $item);

		$search = $item->search;
		$this->helpers->get('clean')->cleanString($search);

		// escape hashes
		$search = str_replace('#', '\#', $search);
		// unescape double escaped hashes
		$search = str_replace('\\\\#', '\#', $search);

		$this->prepareRegex($search, $item->s_modifier, $item->casesensitive);

		$replace = $item->replace;
		$this->helpers->get('clean')->cleanStringReplace($replace, 1);
		$this->replaceInArray($string_array, $search, $replace, $item->thorough);

		$string = implode('', $string_array);
	}

	private function replaceString(&$string, $item)
	{
		$string_array = $this->helpers->get('protect')->stringToProtectedArray($string, $item);

		$search_array = $item->treat_as_list ? explode(',', $item->search) : array($item->search);
		$replace_array = $item->treat_as_list ? explode(',', $item->replace) : array($item->replace);
		$replace_count = count($replace_array);

		foreach ($search_array as $key => $search)
		{
			if ($search == '')
			{
				continue;
			}

			// Prepare search string
			$this->helpers->get('clean')->cleanString($search);
			$search = preg_quote($search, "#");
			if ($item->word_search)
			{
				$search = '(?<!\p{L})(' . $search . ')(?!\p{L})';
			}
			$this->prepareRegex($search, 1, $item->casesensitive);

			// Prepare replace string
			$replace = ($replace_count > $key) ? $replace_array[$key] : $replace_array['0'];
			$this->helpers->get('clean')->cleanStringReplace($replace);

			$this->replaceInArray($string_array, $search, $replace, $item->thorough);
		}

		$string = implode('', $string_array);
	}

	public function replaceInArray(&$array, $search, $replace, $thorough = 0)
	{
		foreach ($array as $key => &$string)
		{
			// only do something if string is not empty
			// or on uneven count = not yet protected
			if (trim($string) == '' || fmod($key, 2))
			{
				continue;
			}

			$this->replacer($string, $search, $replace, $thorough);
		}
	}

	private function replacer(&$string, $search, $replace, $thorough = 0)
	{
		if (@preg_match($search . 'u', $string))
		{
			$search .= 'u';
		}

		if (!preg_match($search, $string))
		{
			return;
		}

		// Do a simple replace if not thorough and counter is not found
		if (!$thorough && strpos($replace, '[[counter]]') === false && strpos($replace, '\#') === false)
		{
			$string = preg_replace($search, $replace, $string);

			return;
		}

		$counter_name = $this->getCounterName($search, $replace);

		$thorough_count = 1; // prevents the thorough search to repeat endlessly
		// preg_match_all: Needs the 3rd parameter in < php 5.4.0
		while ($count = preg_match_all($search, $string, $matches))
		{
			$this->replaceOccurrence($search, $replace, $string, $count, $counter_name);

			if (!$thorough || ++$thorough_count >= 100)
			{
				break;
			}
		}
	}

	private function getCounterName($search, $replace)
	{
		if (strpos($replace, '[[counter]]') === false && strpos($replace, '\#') === false)
		{
			return '';
		}

		// Counter is used to make it possible to use \# or [[counter]] in the replacement to refer to the incremental counter
		$counter_name = base64_encode($search . $replace);

		if (!isset($this->counter[$counter_name]))
		{
			$this->counter[$counter_name] = 0;
		}

		return $counter_name;
	}

	private function replaceOccurrence($search, $replace, &$string, $count = 0, $counter_name = '')
	{
		if (!$counter_name)
		{
			$string = preg_replace($search, $replace, $string);

			return;
		}

		for ($i = 1; $i <= $count; $i++)
		{
			// Replace \# with the incremental counter
			$replace_c = str_replace(array('\#', '[[counter]]'), ++$this->counter[$counter_name], $replace);

			$string = preg_replace($search, $replace_c, $string, 1);
		}
	}

	private function prepareRegex(&$string, $dotall = 1, $casesensitive = 1)
	{
		$string = '#' . $string . '#';

		$string .= $dotall ? 's' : ''; // . (dot) also matches newlines
		$string .= $casesensitive ? '' : 'i'; // case-insensitive pattern matching

		// replace new lines with regex match
		$string = str_replace(array("\r", "\n"), array('', '(?:\r\n|\r|\n)'), $string);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	public function cleanLeftoverJunk(&$string)
	{
		$string = preg_replace('#<\!-- (START|END): RR_[^>]* -->#', '', $string);

		// Remove any leftover protection strings (shouldn't be necessary, but just in case)
		$this->helpers->get('protect')->cleanProtect($string);

		// Remove any leftover protection tags
		if (strpos($string, '{noreplace}') !== false)
		{
			$item = null;
			$string_array = $this->helpers->get('protect')->stringToProtectedArray($string, $item, 1);
			$this->replaceInArray($string_array, '#\{noreplace\}#', '');
			$this->replaceInArray($string_array, '#\{/noreplace\}#', '');
			$string = implode('', $string_array);
		}
	}
}
