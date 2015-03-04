<?php
/**
 * @package         Advanced Template Manager
 * @version         1.3.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 */
class JHtmlTemplates
{
	/**
	 * Display the thumb for the template.
	 *
	 * @param   string	The name of the active view.
	 */
	public static function thumb($template, $clientId = 0)
	{
		$client = JApplicationHelper::getClientInfo($clientId);
		$basePath = $client->path . '/templates/' . $template;
		$baseUrl = ($clientId == 0) ? JUri::root(true) : JUri::root(true) . '/administrator';
		$thumb = $basePath . '/template_thumbnail.png';
		$preview = $basePath . '/template_preview.png';
		$html = '';

		if (file_exists($thumb))
		{
			JHtml::_('bootstrap.tooltip');
			JHtml::_('behavior.modal');

			$clientPath = ($clientId == 0) ? '' : 'administrator/';
			$thumb = $clientPath . 'templates/' . $template . '/template_thumbnail.png';
			$html = JHtml::_('image', $thumb, JText::_('COM_TEMPLATES_PREVIEW'));
			if (file_exists($preview))
			{
				$preview = $baseUrl . '/templates/' . $template . '/template_preview.png';
				$html = '<a href="' . $preview . '" class="thumbnail pull-left modal hasTooltip" title="' . JHtml::tooltipText('COM_TEMPLATES_CLICK_TO_ENLARGE') . '">' . $html . '</a>';
			}
		}

		return $html;
	}
}
