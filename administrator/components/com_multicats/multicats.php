<?php
/**
 * @package     Cweshops
 * @subpackage  com_cweshops
 * @copyright   Copyright (C) 2012 Ing.Pavel Stary, Cesky WEB s.r.o., Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
  define('DS',DIRECTORY_SEPARATOR);
}
/*
 * Define constants for all pages
 */

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'helpers'.DS.'multicats.php'); 

// Require the base controller


// Initialize the controller
$controller = new MulticatsController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>