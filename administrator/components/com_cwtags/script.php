<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
  define('DS',DIRECTORY_SEPARATOR);
} 
 
//the name of the class must be the name of your component + InstallerScript
class com_cwtagsInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// Show the essential information at the install/update back-end
		echo "<table>";
    echo '<tr><td>CW Tags version</td><td>' . $this->release.'</td></tr>';
		echo '<tr><td>Updated from version</td><td>' . $this->getParam('version').'</td></tr>';
		echo '<tr><td>Minimum Joomla version for correct behaviour</td><td>' . $this->minimum_joomla_release.'</td></tr>';
		echo '<tr><td>Current Joomla version is</td><td>' . $jversion->getShortVersion().'</td></tr>';
    echo "</table>";
    
		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install CW Tags in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
 
		// abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }
 
		//echo '<p>' . JText::_('COM_CWTAGS_PREFLIGHT_' . $type . ' ' . $rel) . '</p>';
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
		
    // import joomla's filesystem classes
    jimport('joomla.filesystem.file');
    jimport('joomla.filesystem.folder');
    
    // create a folder inside your images folder
    if(!is_dir(JPATH_ROOT.DS.'images'.DS.'cwtag')){
      if(JFolder::create(JPATH_ROOT.DS.'images'.DS.'cwtag')) {
         echo "Image Folder created successfully";
      } else {
         echo "Unable to create Image folder";
      }
    }
    
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag1.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag1.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag1.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag1.png');
    }
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag2.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag2.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag2.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag2.png');
    }
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag3.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag3.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag3.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag3.png');
    }
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag4.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag4.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag4.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag4.png');
    }  

    
      // Create categories for our component
    $basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
    require_once $basePath . '/models/category.php';
    $config = array( 'table_path' => $basePath . '/tables');
    $catmodel = new CategoriesModelCategory( $config);
    $catData = array( 'id' => 0, 'parent_id' => 0, 'level' => 1, 'path' => 'tags', 'extension' => 'com_cwtags'
    , 'title' => 'Tags', 'alias' => 'tags', 'description' => '', 'published' => 1, 'language' => '*');
    $status = $catmodel->save( $catData);
  
    if(!$status) 
    {
      JError::raiseWarning(500, JText::_('Unable to create default content category!'));
    }
    
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		//echo '<p>' . JText::_('COM_CWTAGS_UPDATE ' . $this->release) . '</p>';
		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');

          // import joomla's filesystem classes
          jimport('joomla.filesystem.file');
          jimport('joomla.filesystem.folder');

    // create a folder inside your images folder
    if(!is_dir(JPATH_ROOT.DS.'images'.DS.'cwtag')){
      if(JFolder::create(JPATH_ROOT.DS.'images'.DS.'cwtag')) {
         echo "Image Folder created successfully";
      } else {
         echo "Unable to create Image folder";
      }
    }
    
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag1.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag1.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag1.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag1.png');
    }
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag2.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag2.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag2.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag2.png');
    }
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag3.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag3.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag3.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag3.png');
    }
    if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag4.png') AND !is_file(JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag4.png') ) {
          JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cwtags'.DS.'assets'.DS.'images'.DS.'tag4.png',JPATH_ROOT.DS.'images'.DS.'cwtag'.DS.'tag4.png');
    }  

     /*
      // Create categories for our component
    $basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
    require_once $basePath . '/models/category.php';
    $config = array( 'table_path' => $basePath . '/tables');
    $catmodel = new CategoriesModelCategory( $config);
    $catData = array( 'id' => 0, 'parent_id' => 0, 'level' => 1, 'path' => 'tags', 'extension' => 'com_cwtags'
    , 'title' => 'Tags', 'alias' => 'tags', 'description' => '', 'published' => 1, 'language' => '*');
    $status = $catmodel->save( $catData);
  
    if(!$status) 
    {
      JError::raiseWarning(500, JText::_('Unable to create default content category!'));
    }
    */
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		// always create or modify these parameters
    
		// define the following parameters only if it is an original install
		if ( $type == 'install' ) {
			$params['tag_position'] = '0';
			$params['enable_images'] = '1';
      $params['show_content'] = '0';
      $params['show_related'] = '1';
      $params['related_count'] = '5';
		  
      $this->setParams( $params );
    }
    
		
 
		//echo '<p>' . JText::_('COM_CWTAGS_POSTFLIGHT ' . $type . ' to ' . $this->release) . '</p>';
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
	  
  	echo '<p>' . JText::_('COM_CWTAGS_UNINSTALL') . '</p>';
  
    // import joomla's filesystem classes
    jimport('joomla.filesystem.file');
    jimport('joomla.filesystem.folder');

    if (is_file(JPATH_ROOT.DS.'images'.DS.'cwtag')) {
        JFile::delete(JPATH_ROOT.DS.'images'.DS.'cwtag');
    }   
  
  }
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_cwtags"');
		
    $manifest = json_decode( $db->loadResult(), true );

    return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_cwtags"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_cwtags"' );
				$db->query();
		}
	}
}
