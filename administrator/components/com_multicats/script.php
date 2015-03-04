<?php 
/**
 * @module		com_multicats
 * @author-name Pavel Stary, Cesky WEB s.r.o.
 * @copyright	Copyright (C) 2012 Cesky WEB s.r.o.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
  define('DS',DIRECTORY_SEPARATOR);
} 
/**
 * Script file of Multicatss component
 */
class com_multicatsInstallerScript
{

	function preflight( $type, $parent ) {
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
    $this->jversion = $parent->get( "manifest" )->jversion;    
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// Show the essential information at the install/update back-end
		echo "<table>";
    echo '<tr><td>CW Multicats version</td><td>' . $this->release.'</td></tr>';
		echo '<tr><td>Updated from version</td><td>' . $this->getParam('version').'</td></tr>';
		//echo '<tr><td>Minimum Joomla version for correct behaviour</td><td>' . $this->minimum_joomla_release.'</td></tr>';
    echo '<tr><td>Recommended Joomla version</td><td>' . $this->jversion.'</td></tr>';
		echo '<tr><td>Current Joomla version is</td><td>' . $jversion->getShortVersion().'</td></tr>';
    echo '<tr><td style="font-weight: bold; color: orange; ">Note: For compatibility with CW Tags plugin,<br/>please update to CWTags Plugin to at least version 1.1.8.</td></tr>';
    echo "</table>";
    
		// abort if the current Joomla release is older
    $jshortversion = explode('-',$jversion->getShortVersion());
    if( version_compare( $jshortversion[0], $this->jversion, 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install CW Multicats in a Joomla release prior to '.$this->jversion);
			return false;
		}

		// abort if the component being installed is older than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'lt' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }
 
		//echo '<p>' . JText::_('COM_MULTICATS_PREFLIGHT_' . $type . ' ' . $rel) . '</p>';
	}


	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
    		// $parent is the class calling this method
    		//$parent->getParent()->setRedirectURL('index.php?option=com_ola');
        
        // import joomla's filesystem classes
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        
        // create a folder inside your images folder
        if(JFolder::create(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal')) {
           //echo "Folder created successfully";
        } else {
           echo "Unable to create folder";
        }
        $db = JFactory::getDBO();
        
        # get back up of files
         
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc');
          }
          */
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc');
          }
          */
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.phpbc');
          }
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.phpbc');
          } 
          */
          //2.6.6
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.phpbc');
          }
                    
          // this is just for preview update
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc');
          }
                    
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.phpbc');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc');
          }  
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php', JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc');
          }
          */
          /*
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.phpbc');
          } 
          */ 
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.phpbc');
          } 
          */
          // 2.5.10
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc');
          }
          //2.5.11
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.phpbc');
          }          
          */
          /* // REMOVED SINCE J3.4
          //2.5.12
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.phpbc');
          } 
          */ 
          //2.5.13
          if (is_file(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php')) {
                JFile::move(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php', JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.phpbc');
          } 
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.phpbc');
          }
          //2.5.14
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc');
          }
          //2.6.5
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.phpbc');
          }       
          //2.6.7
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.phpbc');
          } 
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.phpbc');
          } 
          //2.6.10
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.phpbc');
          } 
          if (is_file(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php')) {
                JFile::move(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php', JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.phpbc');
          } 
          
          //3.3.3
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.phpbc');
          } 
          // 3.4.0
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.phpbc');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.phpbc');
          } 
          //3.4.0.1
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc');
          }
          //3.4.2          
          if (is_file(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php')) {
                JFile::move(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php', JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.phpbc');
          } 
          
        /* Copy new ones */   
          // Admin
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php');
          }
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php');
          }
          */
          
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js');
          }    
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
          } 
          */
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
          }  
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
          }  
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php');
          } 
          */ 
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php');
          }     
          // just for preview
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
          }
          //2.6.6
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php');
          }   
          //3.4.0.1
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
          }           
          // SITE
          /* OBSOLETE SINCE J3.4
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'mcats.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'mcats.php',JPATH_ROOT.DS.'mcats.php');
          } */
                   
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
          }  
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php');
          }       
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
          }  
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php');
          }  
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php');
          }*/
          
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php');
          } 
          */   
          // 2.5.10
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc');
          }      
          // 2.5.11
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php');
          } 
          */
          /* REMOVED SINCE J3.4
          // 2.5.11
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php');
          }       
          */
           //2.5.13
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php',JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'router.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'router.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php');
          }
          // 2.5.14
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
          }
          // 2.6.5
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php');
          } 
          // 2.6.7
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php');
          }       
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php');
          }
          // 2.6.10
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php',JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php');
          } 
          // 3.3.3
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php');
          }
          // 3.4.0
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php');
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.phpbc',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.phpbc');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php');
          }          
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php',JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php');
          } 
          
          // languages
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_multicats.ini')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'cs-CZ'.DS.'cs-CZ.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'cs-CZ')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'cs-CZ'.DS.'cs-CZ.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'cs-CZ'.DS.'cs-CZ.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'de-DE'.DS.'de-DE.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'de-DE')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'de-DE'.DS.'de-DE.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'de-DE'.DS.'de-DE.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'fr-FR'.DS.'fr-FR.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'fr-FR')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'fr-FR'.DS.'fr-FR.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'fr-FR'.DS.'fr-FR.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'nl-NL'.DS.'nl-NL.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'nl-NL')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'nl-NL'.DS.'nl-NL.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'nl-NL'.DS.'nl-NL.com_multicats.ini');
          }        
          $sql = "ALTER TABLE `#__content` CHANGE `catid` `catid` varchar( 255 ) ";
          $db->setQuery( $sql);
          $db->Query();    
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
		//echo '<p>' . JText::_('COM_CWMULTICATS_UNINSTALL_TEXT') . '</p>';
    
    jimport('joomla.filesystem.file');
    
    // delete files
    /* Copy new ones */   
      // Admin
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php');
      }
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php');
      }
      /*
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php');
      }
      */
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js');
      }    
      /*
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
      } 
      */
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
      }  
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php');
      }
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
      }  
      /*
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php');
      } 
      */ 
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php');
      }     
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
      }
      //2.6.6.
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php');
      } 
      //3.4.0.1
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
      }                   
      /*if (is_file(JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.ini')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.ini');
      }
      if (is_file(JPATH_ADMINISTRATOR.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.ini')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.ini');
      }  
      if (is_file(JPATH_ADMINISTRATOR.DS.'ajax.php')) {
            JFile::delete(JPATH_ADMINISTRATOR.DS.'ajax.php');
      }
      
      // SITE
      if (is_file(JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.ini')) {
            JFile::delete(JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.ini');
      }
      if (is_file(JPATH_ROOT.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.ini')) {
            JFile::delete(JPATH_ROOT.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.ini');
      } */ 
      
      /* OBSOLETE SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'mcats.php')) {
            JFile::delete(JPATH_ROOT.DS.'mcats.php');
      }    
      */
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
      }  
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php');
      }       
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php');
      }
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
      }  
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php');
      }  
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php');
      } 
      /* REMOVED SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php');
      }
      */
      /*
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php');
      }
      */
      /* REMOVED SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php');
      }    
      */
      /* REMOVED SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php')) {
            JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php');
      } 
      */   
      //2.5.13
      if (is_file(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php')) {
            JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php');
      }   
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php');
          }
          //2.5.14  
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
          }
          //2.6.5
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php');
          } 
          //2.6.7 
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php');
          }
          //2.6.10
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php');
          } 
          if (is_file(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php')) {
                JFile::delete(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php');
          }
          //3.2.1
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
          }                           
          //3.3.3
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php');
          }
          //3.4.0
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php');
          } 
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php');
          }
          //3.4.2           
          if (is_file(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php')) {
                JFile::delete(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php');
          } 
          
    // rename back old ones
      /*
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php');
      }
      */
      /*
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
      }
      */
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
      }
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
      }
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php');
      }
      /*
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php');
      }
      */
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
      }
      //2.6.6
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php');
      }
      //3.4.0.1
      if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
      }      
                /*  
      if (is_file(JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.inibc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.inibc', JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.ini');
      }
      if (is_file(JPATH_ADMINISTRATOR.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.inibc')) {
            JFile::move(JPATH_ADMINISTRATOR.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.inibc', JPATH_ADMINISTRATOR.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.ini');
      }
      
      if (is_file(JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.inibc')) {
            JFile::move(JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.inibc', JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_content.ini');
      }        
      if (is_file(JPATH_ROOT.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.inibc')) {
            JFile::move(JPATH_ROOT.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.inibc', JPATH_ROOT.DS.'language'.DS.'de-DE'.DS.'de-DE.com_content.ini');
      } 
      */
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
      }
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php');
      }
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
      }  
      /* REMOVED SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc', JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php');
      }
      */
      /*
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php');
      } 
      */ 
      /* REMOVED SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php');
      } 
      */
      /* REMOVED SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php');
      }*/ 
      /* REMOVED SINCE J3.4
      if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php');
      }
      */
      //2.5.13
      if (is_file(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.phpbc')) {
            JFile::move(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.phpbc', JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php');
      }
          //2.5.14
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php');
          }     
          //2.6.5
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php');
          } 
          //2.6.7
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php');
          } 
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php');
          }                           
          //2.6.10
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php');
          }
          if (is_file(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.phpbc', JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php');
          }
          //3.2.1
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
          }
           //3.3.3
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php');
          } 
          //3.4.0
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.phpbc', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php');
          } 
          //3.4.2
          if (is_file(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.phpbc', JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php');
          }                            
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
          // import joomla's filesystem classes
          jimport('joomla.filesystem.file');
          jimport('joomla.filesystem.folder');

          // create a folder inside your images folder
          if(JFolder::create(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal')) {
             //echo "Folder created successfully";
          } else {
             echo "Unable to create folder";
          }
          $db = JFactory::getDBO();
          
          //clean old files
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php')) {
                JFile::delete(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php');
          }          
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php')) {
                JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'modal.php');
          }
          # get back up of files
         
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc');
          }
          */
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc');
          }
          */
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.phpbc');
          }
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.phpbc');
          }
          */
          // this is just for preview update
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc');
          }
          //2.6.6
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.phpbc');
          }
          
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.phpbc');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.phpbc');
          }
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xmlbc');
          }  
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php', JPATH_ROOT.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.phpbc');
          }
          */
          /*
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.phpbc');
          } 
          */ 
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.phpbc');
          } 
          */
          // 2.5.10
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc');
          }
          /* REMOVED SINCE J3.4
          //2.5.12
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.phpbc');
          }  
          */
          //2.5.13
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.phpbc');
          } 
          */
          if (is_file(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php') AND !is_file(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php', JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.phpbc');
          } 
        
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.phpbc');
          }   
          //2.5.14
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.phpbc');
          } 
          //2.6.5
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.phpbc');
          }
          //2.6.7
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.phpbc');
          } 
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.phpbc');
          }
          //2.6.10
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.phpbc');
          } 
          if (is_file(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php'  AND !is_file(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.phpbc'))) {
                JFile::move(JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php', JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.phpbc');
          }
          //3.3.3
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.phpbc');
          }
          //3.4.0
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.phpbc');
          } 
          if (is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php') AND !is_file(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.phpbc');
          }          
          //3.4.0.1
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php') AND !is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc')) {
                JFile::move(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.phpbc');
          }
          //3.4.2
          if (is_file(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php') AND !is_file(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.phpbc')) {
                JFile::move(JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php', JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.phpbc');
          }          
                            
        /* Copy new ones */   
          // Admin
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php');
          }
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'views'.DS.'categories'.DS.'view.html.php');
          }
          */

          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'jquery1.7.2.js');
          }
          /*    
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
          } 
          */
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
          }  
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
          }  
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'edit.php');
          } 
          */ 
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'articles'.DS.'tmpl'.DS.'default.php');
          }     
          // just for preview
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
          }
          //2.6.6
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default.php');
          } 
          //3.4.0.1
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'administrator'.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
          }           
          // SITE
   
          /* OBSOLETE SINCE J3.4
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'mcats.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'mcats.php',JPATH_ROOT.DS.'mcats.php');
          }*/
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
          }  
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'categories.php');
          }       
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'fields'.DS.'modal'.DS.'cats.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms'.DS.'article.xml');
          }  
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'cwmodal.php');
          }  
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'view.html.php');
          }
          */
          /*
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_item.php');
          } 
          */   
          // 2.5.10
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
                //JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.phpbc');
          }        
          // 2.5.12
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php');
          }  
          */          
          //2.5.13
          /* REMOVED SINCE J3.4
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'featured'.DS.'tmpl'.DS.'default_item.php');
          }
          */
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php',JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagenavigation'.DS.'pagenavigation.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'router.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'router.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'router.php');
          }
          // 2.5.14
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'view.html.php');
          }
          // 2.6.5
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'featured.php');
          } 
          // 2.6.7
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'blog_children.php');
          }       
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'category'.DS.'tmpl'.DS.'default_children.php');
          } 
          // 2.6.10
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'archive'.DS.'tmpl'.DS.'default_items.php');
          }   
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php',JPATH_ROOT.DS.'modules'.DS.'mod_articles_category'.DS.'helper.php');
          } 
          // 3.3.3
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'categories'.DS.'tmpl'.DS.'default_items.php');
          }
          //3.4.0
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php',JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models'.DS.'category.php');
          }
          //3.4.2
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php',JPATH_ROOT.DS.'layouts'.DS.'joomla'.DS.'content'.DS.'info_block'.DS.'category.php');
          }
                                      
          // languages
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_multicats.ini')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'cs-CZ'.DS.'cs-CZ.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'cs-CZ')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'cs-CZ'.DS.'cs-CZ.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'cs-CZ'.DS.'cs-CZ.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'de-DE'.DS.'de-DE.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'de-DE')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'de-DE'.DS.'de-DE.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'de-DE'.DS.'de-DE.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'fr-FR'.DS.'fr-FR.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'fr-FR')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'fr-FR'.DS.'fr-FR.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'fr-FR'.DS.'fr-FR.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'nl-NL'.DS.'nl-NL.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'nl-NL')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'nl-NL'.DS.'nl-NL.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'nl-NL'.DS.'nl-NL.com_multicats.ini');
          }
          if (is_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'tr-TR'.DS.'tr-TR.com_multicats.ini') AND JFolder::exists(JPATH_ROOT.DS.'language'.DS.'tr-TR')) {
                JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multicats'.DS.'copy'.DS.'site'.DS.'language'.DS.'tr-TR'.DS.'tr-TR.com_multicats.ini',JPATH_ROOT.DS.'language'.DS.'tr-TR'.DS.'tr-TR.com_multicats.ini');
          }
           

	}
 
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>' . JText::_('COM_MULTICATS_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_multicats"');
		
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
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_multicats"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_multicats"' );
				$db->query();
		}
	}
}
