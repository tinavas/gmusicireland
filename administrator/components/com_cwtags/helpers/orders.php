<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
//init Joomla Framework
//defined('_JEXEC') or die;
	define( '_JEXEC', 1 );
	define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../..' )); // print this out or observe errors to see which directory you should be in
	define( 'DS', DIRECTORY_SEPARATOR );

	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	require_once( JPATH_CONFIGURATION   .DS.'configuration.php' );
	require_once ( JPATH_LIBRARIES .DS.'joomla'.DS.'database'.DS.'database.php' );
	require_once ( JPATH_LIBRARIES .DS.'import.php' );
  
$app = JFactory::getApplication('site');  

JHTML::stylesheet( JUri::root().'components/com_cwtags/assets/css.css' );

$document =& JFactory::getDocument();
$document->addScript(JUri::root().'components/com_cwtags/assets/jquery-1.3.2.js');
$document->addScript(JUri::root().'components/com_cwtags/assets/jquery.livequery.js');

$data = $_POST;

// if we got order id, we can change status
if($data['id'] > 0){
  update($data);
}

function update($data){
  
  $db = JFactory::getDbo();
  $app = JFactory::getApplication('site');  
  
  $query = "SELECT * FROM #__cwtags_orders WHERE id = '".$data['id']."'";
  $db->setQuery($query);
  $item = $db->loadObject();

    switch($item->status){
    case "0":
      echo "update_status active";
      $data['status'] = 1;
      break;
    case "1":
      echo "update_status inactive";
      $data['status'] = 0;
      break;
    }
      
  $query = "UPDATE #__cwtags_orders SET status = ".$data['status']." WHERE id = '".$data['id']."'";
  $db->setQuery($query);
  $db->query();



  return true;
}
?>