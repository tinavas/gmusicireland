<?php
/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
# ------------------------------------------------------------------------
# author    Jesús Vargas Garita
# Copyright (C) 2010 www.joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class plgContentExtraVoteInstallerScript
{
	function install($parent) {
		echo JText::_('PLG_CONTENT_EXTRAVOTE_ENABLED_0');
	}
	/*
	function uninstall($parent) {
	}
	*/
	function update($parent) {
		echo JText::_('PLG_CONTENT_EXTRAVOTE_ENABLED_'.plgContentExtraVoteInstallerScript::isEnabled());
	}
	/*
	function preflight($type, $parent) {
		echo '<p>'. JText::sprintf('1.6 Preflight for %s', $type) .'</p>';
	}
	function postflight($type, $parent) {		
		echo '<p>'. JText::sprintf('1.6 Postflight for %s', $type) .'</p>';
	}
	*/
	function isEnabled() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('enabled'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote('extravote'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('content'));
		$db->setQuery($query);
		
		return $db->loadResult();
	}
}