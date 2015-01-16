<?php
/**
 * Plugin Helper File: Text
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

class plgSystemDummyContentHelperWordlist
{
	var $list = array();
	var $type = 'lorem';
	var $issentence = false;

	public function setType($type)
	{
		$this->type = 'lorem';
		$this->issentence = false;
	}

	public function getList()
	{
		if (isset($this->list[$this->type]))
		{
			return $this->list[$this->type];
		}

		$path = dirname(dirname(__FILE__)) . '/wordlists/';
		$words = file_get_contents($path . $this->type . '.txt');
		$words = trim(preg_replace('#(^|\n)\/\/ [^\n]*#s', '', $words));

		$this->list[$this->type] = explode("\n", $words);

		return $this->list[$this->type];
	}

	public function isSentenceList()
	{
		return $this->issentence;
	}
}
