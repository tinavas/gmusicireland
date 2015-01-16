<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemOSPositions extends JPlugin
{

	function plgSystemOSPositions(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterInitialise()
	{
		JRequest::setVar('tp', '1');
	}
}
