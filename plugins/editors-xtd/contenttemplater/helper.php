<?php
/**
 * Plugin Helper File
 *
 * @package         Content Templater
 * @version         4.10.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 ** Plugin that places the button
 */
class plgButtonContentTemplaterHelper
{
	public function __construct(&$params)
	{
		$this->params = $params;

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$this->parameters = NNParameters::getInstance();
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function render($editor)
	{
		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.popover');

		JFactory::getDocument()->addScriptVersion(JURI::root(true) . '/media/nnframework/js/script.min.js');
		JHtml::stylesheet('nnframework/style.min.css', false, true);

		JHtml::stylesheet('contenttemplater/button.min.css', false, true);
		JHtml::script('contenttemplater/script.min.js', false, true);


		require_once JPATH_ADMINISTRATOR . '/components/com_contenttemplater/models/list.php';
		$list = new ContentTemplaterModelList;
		$items = $list->getItems(1, $this->params->orderby);

		$options = $this->getOptions($items, $editor);
		$buttons = $this->getButtons($items, $editor);
		$id = preg_replace('#[^a-z0-9]#i', '_', $editor);

		$html = array();
		if (!empty($options))
		{
			$text_ini = strtoupper(str_replace(' ', '_', $this->params->button_text));
			$text = JText::_($text_ini);
			if ($text == $text_ini)
			{
				$text = JText::_($this->params->button_text);
			}

			if ($this->params->button_icon)
			{
				$text = '<span class="icon-nonumber icon-contenttemplater"></span> ' . $text;
			}

			if ($this->params->open_in_modal == 1 || (count($options) >= $this->params->switch_to_modal && $this->params->open_in_modal == 2))
			{
				JHtml::_('behavior.modal');
				$new = '';
				if (JFactory::getApplication()->isAdmin())
				{
					require_once JPATH_ADMINISTRATOR . '/components/com_contenttemplater/helpers/helper.php';
					$canDo = ContentTemplaterHelper::getActions();
					if ($canDo->get('core.create'))
					{
						$new = '<a target="_blank" href="index.php?option=com_contenttemplater&view=item&layout=edit" class="btn">'
							. '<span class="icon-save-new"></span> '
							. JText::_('CT_CREATE_NEW_TEMPLATE')
							. '</a>';
						$new .= '<a target="_blank" href="index.php?option=com_contenttemplater" class="btn">'
							. '<span class="icon-nonumber icon-contenttemplater"></span> '
							. JText::_('CT_MANAGE_TEMPLATES')
							. '</a>';
					}
				}

				$html[] = '<a class="btn" data-toggle="modal" data-target="#contenttemplater-modal-' . $id . '">'
					. $text . '</a>';
				$html[] = '<div id="contenttemplater-modal-' . $id . '" class="modal hide fade" tabindex="-1">';
				$html[] = '
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h3>' . JText::_('INSERT_TEMPLATE') . '</h3>
					</div>
					<div class="modal-body">
						<div class="row-fluid" style="width:101%;">
							<ul class="list list-striped"><li>' . implode('</li><li>', $options) . '</li></ul>
						</div>
					</div>
				';
				if ($new)
				{
					$html[] = '
					<div class="modal-footer">
						' . $new . '
					</div>
				';
				}
				$html[] = '</div>';
			}
			else
			{
				if ($this->params->show_list_below)
				{
					$icon = 'arrow-down-3';
					$class = 'dropdown-menu';
				}
				else
				{
					$icon = 'arrow-up-3';
					$class = 'dropdown-menu dropup-menu';
				}

				$html[] = '<div class="dropdown" style="margin-left: 5px;">';
				$html[] = '<a style="display:none;" class="btn"></a>';
				$html[] = '<a class="btn" data-toggle="dropdown" data-position="top" href="#">'
					. $text . ' <span class="icon-' . $icon . '"></span></a>';
				$html[] = '<ul class="' . $class . '" role="menu" aria-labelledby="contenttemplater-dropdown-' . $id . '">'
					. '<li>' . implode('</li><li>', $options) . '</li>'
					. '</ul>';
				$html[] = '</div>';
			}
		}

		// needed for the auto load templates to get triggered
		$html[] = '<!-- CT_editor = "' . $editor . '" -->';

		if (!empty($buttons))
		{
			$html[] = '<a style="display:none;" class="btn"></a>';
			$html[] = implode('', $buttons);
		}

		if (!empty($html))
		{
			$html = '" style="display:none;" class="btn"></a>'
				. implode('', $html)
				. '<a style="display:none;" class="btn';
		}
		else
		{
			$html = '" style="display:none;" class="btn';
		}

		$button = new JObject;
		$button->name = 'contenttemplater';
		$button->options = $html;

		return $button;
	}

	function getOptions(&$items, $name)
	{
		$options = array();

		if (empty($items))
		{
			return $options;
		}

		$onclick = 'ContentTemplater.getXML( this.rel, \'' . $name . '\' );';
		if ($this->params->show_confirm)
		{
			$onclick = 'if( confirm(\'' . sprintf(JText::_('CT_ARE_YOU_SURE', true), '\n') . '\') ) { ' . $onclick . ' };';
		}
		foreach ($items as $item)
		{
			// not enabled if: not published
			if (!$item->published || !$item->button_enabled || $item->button_separate)
			{
				continue;
			}

			if ($this->passChecks($item))
			{

				$options[] = '<a class="hasPopover" data-trigger="hover"'
					. ' title="' . $item->name . '" data-content="' . $item->description . '"'
					. ' href="javascript://" onclick="' . $onclick . '" rel="' . $item->id . '"'
					. '>'
					. $item->name
					. '</a>';
			}
		}

		return $options;
	}

	function getButtons(&$items, $name)
	{
		$buttons = array();

		if (empty($items))
		{
			return $buttons;
		}

		$onclick = 'ContentTemplater.getXML( this.rel, \'' . $name . '\' );';
		if ($this->params->show_confirm)
		{
			$onclick = 'if( confirm(\'' . sprintf(JText::_('CT_ARE_YOU_SURE', true), '\n') . '\') ) { ' . $onclick . ' };';
		}
		foreach ($items as $item)
		{
			// not enabled if: not published
			if (!$item->published || !$item->button_enabled || !$item->button_separate)
			{
				continue;
			}

			if ($this->passChecks($item))
			{
				// template should be displayed as a button
				$icon = str_replace('.png', '', $item->button_image);
				if ($icon == -1)
				{
					$icon = '';
				}
				if (!strlen($item->button_name))
				{
					$item->button_name = $item->name;
				}
				$buttons[] = '<a title="' . $item->button_name . '" class="btn" onclick="try{IeCursorFix();}catch(e){}' . $onclick . '" rel="' . $item->id . '">'
					. '<span class="icon-' . $icon . '"></span> '
					. $item->button_name
					. '</a>';
			}
		}
		return $buttons;
	}

	function passChecks(&$item)
	{
		if (!$item->button_enabled)
		{
			return 0;
		}

		// not enabled if: not active in this area (frontend/backend)
		if (
			(JFactory::getApplication()->isAdmin() && $item->button_enable_in_frontend == 2)
			|| (JFactory::getApplication()->isSite() && $item->button_enable_in_frontend == 0)
		)
		{
			return 0;
		}

		$pass = 1;
		return $pass;
	}
}
