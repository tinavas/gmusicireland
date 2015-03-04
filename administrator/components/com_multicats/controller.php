<?php
/*
 * author Cesky WEB s.r.o.
 * @component Multicats
 * @copyright Copyright (C) Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );


/**
 * Multi category Controller
 *
 * @package Joomla
 * @subpackage Multi category
 */


class MulticatsController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{	// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'default'));

		// call parent behavior
		parent::display($cachable);
	}
  
  /**
   * Function for common multicats select process call
   * note: could be included in ajaxCall   
   */     
  public function multicats() 
  {

      require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'helpers'.DS.'multicats.php' );

      MulticatsHelper::multicats();

      return;
  }
  
  /**
   * Function for ajax calls 
   */       
  public function ajaxCall() 
  {

      require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'helpers'.DS.'multicats.php' );

      $function = JRequest::getVar('function');
      
      MulticatsHelper::$function();
      
      return;        
  } 

  // autocomplete
  public function autocomplete() 
  {
      
    $q = strtolower($_GET["term"]);
    if (!$q) return;  
    
    $db = JFactory::getDbo();
    $query =  "SELECT title AS value, id FROM #__categories WHERE title LIKE '%".mysql_escape_string($q)."%' AND extension = 'com_content' AND published=1 LIMIT 0,15"; 
    $db->setQuery($query);
    $result = $db->loadObjectList();
    
    $catz = json_encode($result);
    echo $catz;

  }           
}
?>