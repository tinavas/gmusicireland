<?php
/*
 * author Cesky WEB s.r.o.
 * @component Multicats
 * @copyright Copyright (C) Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
 
defined('_JEXEC') or die;

class MulticatsHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_MULTICATS_SUBMENU_CPANEL'),
			'index.php?option=com_multicats&view=cpanel',
			$vName == 'cpanel'
		);		
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_cweshops';

		$actions = array(
			'core.admin', 'core.manage'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}


	/**
	 * Method to get Multicats Version
	 * @return string Version of Multicats
	 */
	static function getVersion()
	{

		$folder = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DS.'com_multicats';
    
    // import joomla's filesystem classes
    jimport('joomla.filesystem.folder');
    
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.'com_multicats';
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}

/**
 *  Functions for handling multicats
 */
  /* FUNKCE NA OBSLUHU $data: object <-> array */
  // Function Object a Array
  static function object_to_array($object)
  {
    if(is_array($object) || is_object($object))
    {
      $array = array();
      foreach($object as $key => $value)
      {
        $array[$key] = MulticatsHelper::object_to_array($value);
      }
      return $array;
    }
    return $object;
  }
   
  // Function Array a Object
  static function array_to_object($array = array())
  {
    return (object) $array;
  }
  // Funkce na projití objectu - obdoba in_array pro pole
  static function property_value_in_array($array, $property, $value) {
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
  static function getTitle($id){
    $db = JFactory::getDbo();
    $query = "SELECT title FROM #__categories WHERE id = ".$id;
    $db->setQuery($query);
    return $db->loadResult();
  }
  /* END */
     
  static public function multicats()
  {
    
    //echo $_GET['client'];//JRequest::getVar('client');
    //require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
     
    //require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
     
    $mainframe = JFactory::getApplication($_GET['client']);
    //$mainframe->initialise();
     
    //$session =& JFactory::getSession();
    //$data = $session->get("catz");
     
    $data = $mainframe->getUserState( "com_content.mcats", '' );
     
    $data = json_decode($data);
    if(!is_array($data)) { $data = array(); }

    //Get category title
    $title = MulticatsHelper::getTitle($_GET['item']);
     
    // uncheck
    if($_GET['chck'] == 'false'){
      //$data = object_to_array($data); 
      foreach($data as $key => $cat){
        if($cat->id == $_GET['item']){
        //if($cat['id'] == $_GET['item']){
          unset($data[$key]);
        }
      }
      $data = MulticatsHelper::object_to_array($data);
      $data = array_values($data);
      foreach($data as $key => $cat){
        $data[$key] = (object) $data[$key];
      }
    }
    // check
    if(!MulticatsHelper::property_value_in_array($data, 'id', $_GET['item'], $data) AND $_GET['chck'] == 'true'){
      $data = MulticatsHelper::object_to_array($data);
      $data[] = array("id" => $_GET['item'], "title" => $title);
      //echo $_GET['item']."+";
    }
     
    // uncheck all
    if($_GET['item'] == 0){
      $data = MulticatsHelper::object_to_array($data);
      foreach($data as $key => $cat){
        unset($data[$key]);
      }
      
    }
     
    $catz = json_encode($data);
    //$session->set("catz",$catz);
    $mainframe->setUserState( "com_content.mcats", $catz );
    
    //clean unused ordering - best place but would need to get an article ID by ajax
    //MulticatsHelper::synchronizeOrdering($_GET['item']);    
  
    // Send the result
    echo $catz; 
  } 
   
  static function synchronizeOrdering($id){
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    
    // get article list for category ID
    $query->select('a.id,a.catid');
    $query->from('#__content AS a');
    $query->where('a.id = '.$id);
    $db->setQuery($query);
    $article = $db->loadObject();
    
    //delete all unused
      $query = "DELETE FROM #__content_multicats WHERE content_id = ".$article->id." AND catid NOT IN(".$article->catid.")";
      $db->setQuery($query);
      $db->query();     
      //echo $query;
    return;
  }
  
  static public function getList($catid){
    $dir = JRequest::getVar('dir');
    $limitstart = JRequest::getVar('limitstart');
    $limit = JRequest::getVar('limit');

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);     
 
    // get article list for category ID
    $query->select('a.id,a.catid AS cids,m.*');
    $query->from('#__content AS a');
    $query->where('FIND_IN_SET('.$catid.',a.catid)');
    $query->join('LEFT','#__content_multicats AS m ON ( m.content_id = a.id AND m.catid = '.$catid.' )');
    $query->order('m.ordering '.$dir);
    $db->setQuery($query,$limitstart,$limit);
    $list = $db->loadObjectList();

    return $list;
  }
    
  static public function saveorder()
  {
    $morder = JRequest::getVar('morder');
    $mid = JRequest::getVar('mid');
    $catid = JRequest::getVar('catid');
    $dir = JRequest::getVar('dir');
    $limitstart = JRequest::getVar('limitstart');
    $limit = JRequest::getVar('limit');

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);     
 
    // get article list for category ID
    $list = MulticatsHelper::getList($catid);
    
    //get max order of cat
    $query = $db->getQuery(true);
    $query->select('MAX(ordering)');
    $query->from('#__content_multicats');
    $query->where('catid = '.$catid);
    $db->setQuery($query);
    $maxorder = $db->loadResult();
    $maxorder = $maxorder + 0;
    print_r($morder);
   // print_r($mid);
    
    foreach($mid as $id){
      //get item
      $query = $db->getQuery(true);
      $query->select('ordering');
      $query->from('#__content_multicats');
      $query->where('content_id = '.$id. ' AND catid = '.$catid);
      $db->setQuery($query);
      $item = $db->loadObject();
      //set automatic max ordering if isnt set yet
      if(!is_numeric($item->ordering)){
        $maxorder = $maxorder + 1;
        $query = "INSERT INTO #__content_multicats VALUES (".$id.",".$catid.",".$maxorder.")";
        echo "<pre>".$query;  
        $db->setQuery($query);
        $db->query();             
      }      
    }
    foreach($mid as $key => $id){
      if($morder[$key] >= "0"){
        $ord = (int) $morder[$key];
        $ord < 0 ? $ord = 0 : $ord = $ord;
        $query = "UPDATE #__content_multicats SET ordering = ".$ord." WHERE content_id = ".$id." AND catid = ".$catid;
        echo "<pre>".$query;  
        $db->setQuery($query);
        $db->query(); 
             
        //clean unused ordering - better would be in category selection process
        MulticatsHelper::synchronizeOrdering($id);
      }
    }        
    /*
    // go through the list and check the ordering for category
    foreach($list as $item){
      if(!is_numeric($item->ordering)){
        $maxorder = $maxorder + 1;
        $query = "INSERT INTO #__content_multicats VALUES (".$item->id.",".$catid.",".$maxorder.")";
        $db->setQuery($query);
        $db->query();             
      }
    }

    // get list again after re-check
    $list = MulticatsHelper::getList($catid);    
    foreach($list as $i => $item){
      $ord = (int) $morder[$i];
      $ord < 0 ? $ord = 0 : $ord = $ord;
      $query = "UPDATE #__content_multicats SET ordering = ".$ord." WHERE content_id = ".$item->id." AND catid = ".$catid;
            echo "<pre>".$query;  
      $db->setQuery($query);
      $db->query(); 
           
      //clean unused ordering - better would be in category selection process
      MulticatsHelper::synchronizeOrdering($item->id);     
    }
    */
         
    exit; // rather exit;
    
    return;  
  }
  
  static public function setorder()
  {
    $id = JRequest::getVar('id');
    $catid = JRequest::getVar('catid');
    $action = JRequest::getVar('action');
    
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    
    // get article list for category ID
    $list = MulticatsHelper::getList($catid);

    //get max order of cat
    $query = $db->getQuery(true);
    $query->select('MAX(ordering)');
    $query->from('#__content_multicats');
    $query->where('catid = '.$catid);
    $query->order('ordering');
    $db->setQuery($query);
    $maxorder = $db->loadResult();

    // go through the list and check the ordering for category
    foreach($list as $item){
      if(is_numeric($item->ordering)){
        // store original article's ordering for move up/down setting
        if($item->id == $id){
          $ordering = $item->ordering;
        }             
      }
      else {
        $maxorder = $maxorder + 1;
        $query = "INSERT INTO #__content_multicats VALUES (".$item->id.",".$catid.",".$maxorder.")";
        $db->setQuery($query);
        $db->query(); 
        // store original article's ordering for move up/down setting
        if($item->id == $id){
          $ordering = $maxorder;
        } 
      }      
    }

    // Update this article's ordering
    if($action == "orderup") {
      $dir = "-";
      $counter_dir = "+";
      $ordering = $ordering - 1;
      $sql_order = "IF(ordering = 0, ordering, ordering".$dir."1)";
    } else {
      $dir = "+";
      $counter_dir = "-";
      $ordering = $ordering + 1;
      $sql_order = "ordering".$dir."1";
    }
    $query = "UPDATE #__content_multicats SET ordering = ".$sql_order." WHERE content_id = ".$id." AND catid = ".$catid;        

    $db->setQuery($query);
    $db->query();

    //now check if other category needs to be reordered on this category's former place    
    $query = "UPDATE #__content_multicats SET ordering = ordering".$counter_dir."1 WHERE content_id != ".$id." AND catid = ".$catid." AND ordering = ".$ordering;        
    $db->setQuery($query);
    $db->query();       
           
    //clean unused ordering - better would be in category selection process
    MulticatsHelper::synchronizeOrdering($id);
      
    return;
  }
}