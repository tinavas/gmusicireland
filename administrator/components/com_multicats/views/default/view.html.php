<?php
/*
 * author Cesky WEB s.r.o.
 * @component Multicats
 * @copyright Copyright (C) Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
//jimport( 'joomla.application.component.view');
class MulticatsViewDefault extends JViewLegacy {
    function display($tpl = null) {

        $this->tmpl['version'] = MulticatsHelper::getVersion(); 
        
        $this->addToolbar();
        parent::display($tpl);
    }
    

  	protected function addToolbar()
  	{
  		$canDo	= MulticatsHelper::getActions();
  
      $doc = JFactory::getDocument();
      $doc->addStyleSheet( JURi::base().'components/com_multicats/assets/style.css' );

      //JHtml::stylesheet('com_multicats/assets/style.css', array(), true, false, false);
      JToolBarHelper::title( JText::_( 'CW MCats - multiple categories for articles' ), 'info' );
      
  		if ($canDo->get('core.admin')) {
  			JToolBarHelper::preferences('com_multicats');
  			JToolBarHelper::divider();
  		}
    }
   
}
?>