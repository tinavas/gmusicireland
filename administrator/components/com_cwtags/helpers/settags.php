<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


//echo $_GET['item'];return;
//defined('_JEXEC') or die;
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__) . '/../../../../' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
                                          
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$mainframe = JFactory::getApplication($_GET['client']);
$mainframe->initialise();

//$session =& JFactory::getSession();
//$data = $session->get("catz");

$data = $mainframe->getUserState( "com_cwtags.settags", '' );

$data = json_decode($data);
if(!is_array($data)) { $data = array(); }
/* FUNKCE NA OBSLUHU $data: object <-> array */
// Function Object a Array
  function object_to_array($object)
  {
    if(is_array($object) || is_object($object))
    {
      $array = array();
      foreach($object as $key => $value)
      {
        $array[$key] = object_to_array($value);
      }
      return $array;
    }
    return $object;
  }
   
  // Function Array a Object
  function array_to_object($array = array())
  {
  	return (object) $array;
  }
  // Funkce na projití objectu - obdoba in_array pro pole
  function property_value_in_array($array, $property, $value) {
      $flag = false;
  
      foreach($array as $object) {
          if(!is_object($object) || !property_exists($object, $property)) {
              return false;        
          }
  
          if($object->$property == $value) {
              $flag = true;
          }
      }
      
      return $flag;
  }

 
  function addTag($name,$itemid=0,$oid=0)
	{
		$user = JFactory::getUser();
		if ($user->authorise('core.create','com_cwtags') AND $name != '')
		{      
      $db = JFactory::getDBO();

			$query = $db->getQuery(true);
			$query->select('`id`');
			$query->from('`#__cwtags`');
			$query->where('`name`="'.$name.'"');
			$db->setQuery($query);
			$id = $db->loadResult();
			if (!$id)
			{
        $object = new stdclass();
				$object->name = $name;
				$object->alias = JFilterOutput::stringURLSafe($name);
				$object->catid = getCategory();
        $object->state = 1;
        $object->access = 1;
        $object->language = '*';
				$db->insertObject('#__cwtags', $object); 
			  // id of inserted tag 
        $tid = $db->insertid();
        
        return $tid;
      }
      else {
        $tid = getTag($name);
        
        return $tid;
      }
		}
	}                                 

	function getCategory()
	{
   
      $db = JFactory::getDBO();
	
			$query = $db->getQuery(true);
			$query->select('`id`');
			$query->from('`#__categories`');
			$query->where('`extension`="com_cwtags"');
      $query->order('`id`');

			$db->setQuery($query,0, 1);
			$cid = $db->loadResult();

      return $cid;
	} 

	function getTag($name)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('`t`.id');
		$query->from('`#__cwtags` AS `t`');
		$query->where('`t`.`name`="'.$name.'"'); 
		$db->setQuery($query, 0, 1);

		$tag = $db->loadResult();
    //echo $query;
    return $tag;
	} 
/* END */


/* HERE STARTS CHECK HANDLING */

if(isset($_GET['add']) AND $_GET['add'] == true){
  if($_GET['title'] == '') { return; }
  $tid = addTag($_GET['title']);

  $_GET['item'] = $tid;
  $_GET['chck'] = true;

}

// uncheck
if($_GET['chck'] == 'false'){
  
  //$data = object_to_array($data); 
  foreach($data as $key => $cat){
    
    if($cat->id == $_GET['item']){
    //if($cat['id'] == $_GET['item']){
      unset($data[$key]);
    }
  }
  $data = object_to_array($data);
  $data = array_values($data);
  foreach($data as $key => $cat){
    $data[$key] = (object) $data[$key];
  }
}
// check
if(!property_value_in_array($data, 'id', $_GET['item'], $data) AND $_GET['chck'] == 'true'){
  $data = object_to_array($data);
  $data[] = array("id" => $_GET['item'], "title" => $_GET['title']);
  //echo $_GET['item']."+";
}

// uncheck all
if($_GET['item'] == 0){
  $data = object_to_array($data);
  foreach($data as $key => $cat){
    unset($data[$key]);
  }
  
}

$catz = json_encode($data);
//$session->set("catz",$catz);
$mainframe->setUserState( "com_cwtags.settags", $catz );

// Send the result
echo $catz; 
?>