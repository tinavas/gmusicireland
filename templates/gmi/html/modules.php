<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg. To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_no($module, &$params, &$attribs)
{
	if ($module->content)
	{
		echo $module->content;
	}
}

function modChrome_well($module, &$params, &$attribs)
{
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}
	
	if ($module->content)
	{
		echo "<div class=\"well " . $module_class . "\">";
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\">" . $module->title . "</" . $header_tag . ">";
		}
		echo $module->content;
		echo "</div>";
	}
}

function modChrome_well_custom($module, &$params, &$attribs)
{
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}
	
	if ($module->content)
	{
		echo "<div class=\"well-custom " . $module_class . "\">";
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\">" . $module->title . "</" . $header_tag . ">";
		}
		echo $module->content;
		echo "</div>";
	}
}

function modChrome_banner($module, &$params, &$attribs)
{
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}
	
	if ($module->content){
		echo "<div class=\"banner " . $module_class . "\">";
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><span></span><span>" . $module->title . "</span></" . $header_tag . ">";
		}
		echo $module->content;
		echo "</div>";			
	}
}
function modChrome_custom_news($module, &$params, &$attribs)
{
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}
	
	if ($module->content){
		echo "<div class=\"custom_news " . $module_class . "\">";
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><div>&nbsp;</div><div>" . $module->title . "</div></" . $header_tag . ">";
		}
		echo $module->content;
		echo "</div>";			
	}
}
function modChrome_custom_news_link($module, &$params, &$attribs)
{
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}	
	
	if ($module->content){
		echo "<div class=\"custom_news_link " . $module_class . "\">";
		if (!$module->showtitle or !isset($module->title)) {
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><div></div><div>" . $module->title . "</div></" . $header_tag . ">";
		}	
/*		if ($module->showtitle )
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\">" . $module->title . "</" . $header_tag . ">";
		}*/
		echo $module->content;
		echo "</div>";			
	}
}
function modChrome_custom_header_module($module, &$params, &$attribs)
{
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}	
	
	if ($module->content){		
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><span></span><span>" . $module->title . "</span></" . $header_tag . ">";
		}
		echo $module->content;		
	}
}
function modChrome_footer_menu($module, &$params, &$attribs)
{
	$app = JFactory::getApplication();
	$global_params = $app->getTemplate(true)->params;
	$sitename = $app->get('sitename');
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));	
	$gmi_params = json_decode($module->params);

	//$logo = '<img src="' . JUri::root() . "/images/gmi_logo_notext.png" . '" alt="' . $sitename . '" />';
	$logo = '<div class="footer-logo"><a href="' . JUri::root() . '"></a></div>';

	
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}

	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}	
	
	if ($module->content){
		if ($params->get('moduleclass_sfx')) {  
			echo "<div class=\"" . $module_class . "\">";
		}
		
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><span></span><span>" . $module->title . "</span></" . $header_tag . ">";
		}
		
		echo $logo . $module->content;
		
		if ($params->get('moduleclass_sfx')) {  
			echo "</div>";
		}
	}
}
function modChrome_footer_blocks($module, &$params, &$attribs)
{	
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}
	
	if ($module->content)
	{
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><div>&nbsp;</div><div>" . $module->title . "</div></" . $header_tag . ">";
		}
		echo $module->content;
	}
}
function modChrome_follow_us($module, &$params, &$attribs)
{
	$module_class = htmlspecialchars($params->get('moduleclass_sfx'));
	$gmi_params = json_decode($module->params);  
	if (isset($gmi_params->header_tag)) {  
		$header_tag = htmlspecialchars($gmi_params->header_tag);  
	} else {  
		$header_tag = "h3";  
	}
	if (isset($gmi_params->header_class)) {  
		$header_class = htmlspecialchars($gmi_params->header_class);  
	} else {  
		$header_class = "";  
	}
	
	if ($module->content){
		echo "<div class=\"custom_followus " . $module_class . "\">";
		if ($module->showtitle)
		{
			echo "<" . $header_tag . " class=\"page-header " . $header_class . "\"><div>&nbsp;</div><div>" . $module->title . "</div></" . $header_tag . ">";
		}
		echo $module->content;
		echo "</div>";			
	}
}
?>