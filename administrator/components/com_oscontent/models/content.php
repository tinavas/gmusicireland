<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2014 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.3
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_oscontent/models/model.php';

/**
 * Model Content
 *
 * @since  1.0.0
 */
class OSContentModelContent extends OSModel
{
    /**
     * @var    string  The prefix to use with controller messages.
     * @since  1.6
     */
    protected $text_prefix = 'COM_OSCONTENT_CONTENT';

    /**
     * Model context string.
     *
     * @var  string
     */
    protected $_context = 'com_oscontent.content';

    /**
     * Get the form
     *
     * @param   array $data     Data
     * @param   bool  $loadData Load data
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  Form
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_oscontent.content',
            'content',
            array('control' => 'jform', 'load_data' => $loadData)
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Link the content to the menu
     *
     * @param   int    $id          The id of the content to insert
     * @param   string $title       The  title of the menu element
     * @param   string $menuselect  The menu where to create the link
     * @param   string $contentType to know the kind of content (static content or not)
     * @param   int    $parent      Parent
     * @param   string $alias       Alias
     *
     * @return void
     */
    public function menuLink($id, $title, $menuselect, $contentType, $parent, $alias = "")
    {
        $mainframe = JFactory::getApplication();
        $database  = JFactory::getDBO();

        $menu = strval($menuselect);
        $link = strval($title);

        $link = stripslashes(JFilterOutput::ampReplace($link));

        // Find what kind of link needs to be created in $row->link
        switch ($contentType) {
            case "content_section":
                $taskLink = "section";
                break;

            case "content_blog_section":
                $taskLink = "section&layout=blog";
                break;

            case "content_category":
                $taskLink = "category";
                break;

            case "content_blog_category":
                $taskLink = "category&layout=blog";
                break;

            default:
                $taskLink = "article";
        }

        $row           = JTable::getInstance('menu');
        $row->menutype = $menu;
        $row->title    = $link;
        $row->alias    = $alias ? JFilterOutput::stringURLSafe($alias) : JFilterOutput::stringURLSafe($link);

        if (trim(str_replace('-', '', $row->alias)) == '') {
            $row->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        $row->parent_id = ($parent == -1) ? 1 : $parent;
        $row->type      = 'component';
        $row->link      = 'index.php?option=com_content&view=' . $taskLink . '&id=' . $id;
        $row->published = 1;
        $row->language  = "*";

        // $row->componentid = $id;
        $row->component_id = 22;
        // $row->ordering = 9999;
        $params                          = array();
        $params['display_num']           = 10;
        $params['show_headings']         = 1;
        $params['show_date']             = 0;
        $params['date_format']           = "";
        $params['filter']                = 1;
        $params['filter_type']           = "title";
        $params['orderby_sec']           = "";
        $params['show_pagination']       = 1;
        $params['show_pagination_limit'] = 1;
        $params['show_noauth']           = "";
        $params['show_title']            = "";
        $params['link_titles']           = "";
        $params['show_intro']            = "";
        $params['show_section']          = "";
        $params['link_section']          = "";
        $params['show_category']         = "";
        $params['link_category']         = "";
        $params['show_author']           = "";
        $params['show_create_date']      = "";
        $params['show_modify_date']      = "";
        $params['show_item_navigation']  = "";
        $params['show_readmore']         = "";
        $params['show_vote']             = "";
        $params['show_icons']            = "";
        $params['show_pdf_icon']         = "";
        $params['show_print_icon']       = "";
        $params['show_email_icon']       = "";
        $params['show_hits']             = "";
        $params['feed_summary']          = "";
        $params['page_title']            = "";
        $params['show_page_title']       = 1;
        $params['pageclass_sfx']         = "";
        $params['menu_image']            = "";
        $params['secure']                = 0;

        $registry = new JRegistry;
        $registry->loadArray($params);
        $row->params = (string)$registry;

        $row->setLocation($row->parent_id, 'last-child');

        if (!$row->check()) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";

            exit();
        }

        if (!$row->store()) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";

            exit();
        }

        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $row->reorder("menutype = " . $database->Quote($row->menutype) . " AND parent = " . (int)$row->parent);
        }

        // Clean any existing cache files
        // mosCache::cleanCache('com_content');
    }

    /**
     * Get a list of the menu records associated with the type
     *
     * @param   string $menutype The menu type
     *
     * @return  array An array of records as objects
     */
    public function getMenuItems($menutype)
    {
        // TODO: Check if this is been used
        $table = $this->getTable();

        if ($table->menutype == '') {
            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '<')) {
                $table->menutype = JRequest::getString('menutype');
            } else {
                $table->menutype = JFactory::getApplication()->input->getString('menutype');
            }
        }

        $db = $this->getDBO();

        $query = 'SELECT a.id, a.name' .
            ' FROM #__menu AS a' .
            ' WHERE a.menutype = "' . $menutype . '"' .
            ' ORDER BY a.name';
        $db->setQuery($query);

        // return $db->loadResultArray();
        return $db->loadObjectList();
    }

    /**
     * Get parent category
     *
     * @access    protected
     * @since     1.0.0
     *
     * @return  array
     */
    protected function getCategoryParent()
    {
        // Initialise variables.
        $options = array();

        try {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('a.id AS value, a.title AS text, a.level');
            $query->from('#__categories AS a');
            $query->join('LEFT', '`#__categories` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

            $extension = "com_content";
            $query->where('(a.extension = "com_content" OR a.parent_id = 0) AND a.id <> 1');

            /*
            // Prevent parenting to children of this item.
            if ($id = $this->form->getValue('id')) {
                $query->join('LEFT', '`#__categories` AS p ON p.id = '.(int) $id);
                $query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

                $rowQuery	= $db->getQuery(true);
                $rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
                $rowQuery->from('#__categories AS a');
                $rowQuery->where('a.id = ' . (int) $id);
                $db->setQuery($rowQuery);
                $row = $db->loadObject();
            }
            */

            $query->where('a.published IN (0,1)');
            $query->group('a.id');
            $query->order('a.lft ASC');

            // Get the options.
            $db->setQuery($query);

            $options = $db->loadObjectList();

            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '<')) {
                // Check for a database error.
                if ($db->getErrorNum()) {
                    JError::raiseWarning(500, $db->getErrorMsg());
                }
            }
        } catch (Exception $e) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }

        // Pad the option text with spaces using depth level as a multiplier.
        for ($i = 0, $n = count($options); $i < $n; $i++) {
            // Translate ROOT
            if ($options[$i]->level == 0) {
                $options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
            }

            $options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
        }

        // Initialise variables.
        $user = JFactory::getUser();

        if (empty($id)) {
            // New item, only have to check core.create.
            foreach ($options as $i => $option) {
                // Unset the option if the user isn't authorised for it.
                if (!$user->authorise('core.create', $extension . '.category.' . $option->value)) {
                    unset($options[$i]);
                }
            }
        } else {
            // Existing item is a bit more complex. Need to account for core.edit and core.edit.own.
            foreach ($options as $i => $option) {
                // Unset the option if the user isn't authorised for it.
                if (!$user->authorise('core.edit', $extension . '.category.' . $option->value)) {
                    // As a backup, check core.edit.own
                    if (!$user->authorise('core.edit.own', $extension . '.category.' . $option->value)) {
                        // No core.edit nor core.edit.own - bounce this one
                        unset($options[$i]);
                    } else {
                        // TODO: I've got a funny feeling we need to check core.create here.
                        // Maybe you can only get the list of categories you are allowed to create in?
                        // Need to think about that. If so, this is the place to do the check.
                    }
                }
            }
        }

        if (isset($row) && !isset($options[0])) {
            if ($row->parent_id == '1') {
                $parent       = new stdClass;
                $parent->text = JText::_('JGLOBAL_ROOT_PARENT');
                array_unshift($options, $parent);
            }
        }

        return $options;
    }

    /**
     * Get Data
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  array
     */
    public function &getData()
    {
        $database = JFactory::getDBO();

        global $my;

        $mainframe         = JFactory::getApplication();
        $contentSection    = 0;
        $lists             = null;
        $sectioncategories = 0;

        if (!isset($id)) {
            $id = 0;
        }

        if (!isset($store)) {
            $store = '';
        }

        $row = $this->getTable();
        $row->load($id);

        $javascript  = "onchange=\"changeDynaList('catid', sectioncategories, document.adminForm.sectionid.options[document.adminForm.sectionid.selectedIndex].value, 0, 0);\"";
        $javascript2 = "onchange=\"changeDynaList('menuselect3', menulist, document.adminForm.menuselect.options[document.adminForm.menuselect.selectedIndex].value, 0, 0);\"";


        $categories     = $this->getCategoryParent();
        $lists['catid'] = JHTML::_(
            'select.genericlist',
            $categories,
            'catid',
            'class="inputbox" size="1"',
            'value',
            'text',
            intval($row->catid)
        );

        // Build the html select list for ordering
        $query = "SELECT ordering AS value, title AS text"
            . "\n FROM #__content"
            . "\n WHERE catid = " . (int)$row->catid
            . "\n AND state >= 0"
            . "\n ORDER BY ordering";

        $uid           = "";
        $row->ordering = null;

        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $lists['ordering'] = JHTML::_('list.specificordering', $row, $uid, $query, 1);
        } else {
            $lists['ordering'] = JHTML::_('list.ordering', 'ordering', $query, '', $row->ordering, 1);
        }

        // build the html select list for menu selection
        $menu3     = array();
        $menulist  = array();
        $menuTypes = $this->getMenuTypes();

        foreach ($menuTypes as $menuType) {
            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '<')) {
                $menu[] = JHTML::_('select.option', $menuType, $menuType);
            } else {
                $menu[] = JHTML::_('select.option', $menuType->menutype, $menuType->title);
            }
        }

        $stop_notice = array();

        $lists['menuselect3'] = JHTML::_(
            'select.genericlist',
            $stop_notice,
            'menuselect3',
            'class="inputbox" size="10"',
            'value',
            'text',
            null
        );


        $lists['menuselect'] = JHTML::_(
            'select.genericlist',
            $menu,
            'menuselect',
            'class="inputbox" size="10" ' . $javascript2,
            'value',
            'text',
            null
        );


        // Build the html select list for the group access
        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $lists['access'] = JHTML::_('list.accesslevel', $row);
        } else {
            $lists['access'] = JHtml::_('access.assetgrouplist', 'access', $row->access);
        }

        // Build list of users
        $lists['created_by'] = JHTML::_('list.users', 'created_by', 1);

        // Load params
        jimport('joomla.application.component.helper');
        $lists['sectioncategories'] = $sectioncategories;
        $lists['menulist']          = $this->createSubMenu();

        return $lists;
    }

    /**
     * Get a list of the menutypes
     *
     * @access    public
     *
     * @return array An array of menu type names
     */
    public function getMenuTypes()
    {
        $db = JFactory::getDBO();
        /*$query = 'SELECT menutype' .
                ' FROM #__menu_types';*/
        $query = 'SELECT a.menutype, a.title' .
            ' FROM #__menu_types AS a';
        $db->setQuery($query);

        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $result = $db->loadResultArray();
        } else {
            $result = $db->loadObjectList();
        }

        return $result;
    }

    /**
     * Create Submenu
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  array
     */
    public function createSubMenu()
    {
        // Build the html select list for menu selection

        $menulist  = array();
        $database  = JFactory::getDBO();
        $menuTypes = $this->getMenuTypes();

        foreach ($menuTypes as $menuType) {
            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '>=')) {
                $menuType = $menuType->menutype;
            }

            //$menu = JHTML::_('select.option',  $menuType, $menuType);
            ///////////////////////////////////////////////////
            //Create tje tree of menus
            //http://dev.joomla.org/component/option,com_jd-wiki/Itemid,/id,references:joomla.framework:html:jhtmlmenu-treerecurse/
            $query = 'SELECT id, parent_id, title, menutype, title AS name' .
                ' FROM #__menu' .
                ' WHERE menutype = "' . $menuType . '"  AND published = 1' .
                ' ORDER BY menutype, parent_id, ';

            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '<')) {
                $query .= 'ordering';
            } else {
                $query .= 'lft';
            }

            $database->setQuery($query);
            $menuItems4 = $database->loadObjectList();

            $children = array();

            if ($menuItems4) {
                // First pass - collect children
                // Iterate through the menu items
                foreach ($menuItems4 as $v) {
                    $id = $v->id;

                    // We use parent as our array index
                    $pt = $v->parent_id;

                    // If an array entry for the parent doesn't exist, we create a new array
                    $list = @$children[$pt] ? $children[$pt] : array();

                    // We push our item onto the array (either the existing one for the specified parent or the new one
                    array_push($list, $v);

                    // We put out updated list into the array
                    $children[$pt] = $list;
                }
            }

            // Second pass - get an indent list of the items
            // $list = JHtmlMenu::TreeRecurse(intval($menuItems4[0]->parent_id), '', array(), $children, 9999, 0, 0);
            $list       = JHTML::_(
                'menu.treerecurse',
                intval(@$menuItems4[0]->parent_id),
                '-',
                array(),
                $children,
                9999,
                0,
                0
            );
            $menulist[] = $list;
            ///////////////////////////////////////////////////
        }

        return $menulist;
    }

    /**
     * Get Post Data
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  array
     */
    public function getPostData()
    {
        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $post = JRequest::get('post');
        } else {
            $app   = JFactory::getApplication();
            $input = $app->input;
            $post  = $input->getArray(
                array(
                    'title'            => 'ARRAY',
                    'alias'            => 'ARRAY',
                    'published'        => 'STRING',
                    'access'           => 'INT',
                    'created_by'       => 'INT',
                    'created_by_alias' => 'STRING',
                    'created'          => 'DATE',
                    'catid'            => 'INT',
                    'publish_up'       => 'DATE',
                    'publish_down'     => 'DATE',
                    'metadesc'         => 'ARRAY',
                    'metakey'          => 'ARRAY',
                    'addMenu'          => 'INT',
                    'menuselect'       => 'STRING',
                    'menuselect3'      => 'STRING',
                    'state2'           => 'STRING'
                )
            );

            for ($i = 0; $i < count($post["title"]); $i++) {
                $index                       = $i + 1;
                $post['introtext_' . $index] = $input->get('introtext_' . $index, '', 'raw');
                $post['fulltext_' . $index]  = $input->get('fulltext_' . $index, '', 'raw');
            }
        }

        return $post;
    }

    /**
     * Save the content
     *
     * @param   array $option Options
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  array
     */
    public function saveOSContent($option = null)
    {

        $post = $this->getPostData();

        for ($i = 0; $i < count($post["title"]); $i++) {
            $index = $i + 1;

            if ($post["title"][$i] == "") {
                continue;
            }
            $row        = $this->getTable();
            $row->title = $post["title"][$i];

            if ($post["alias"][$i]) {
                $row->alias = JFilterOutput::stringURLSafe($post["alias"][$i]);
            } else {
                $row->alias = JFilterOutput::stringURLSafe($row->title);
            }

            if (trim(str_replace('-', '', $row->alias)) == '') {
                $row->alias = JFactory::getDate()->format('Y-m-d-H-i-s') . "-" . $i;
            }

            $intro_text      = $post['introtext_' . $index];
            $full_text       = $post['fulltext_' . $index];
            $row->introtext  = ($intro_text != "" ? $intro_text : $full_text);
            $row->fulltext   = ($intro_text != "" ? $full_text : "");
            $row->metakey    = $post["metakey"][$i];
            $row->metadesc   = $post["metadesc"][$i];
            $row->robots     = isset($post["robots"]) ? $post["robots"] : "";
            $row->author     = $post["created_by"];
            $row->catid      = $post["catid"];
            $row->access     = $post["access"];
            $row->language   = "*";
            $row->created_by = $post["created_by"];

            // TODO: implement the metadata/robots
            $row->metadata = "";
            if ($row->robots != "") {
                $row->metadata = "robots=" . $row->robots . "\n";
            }
            // TODO: implement the author_alias
            if ($row->author != "") {
                $row->metadata .= "author=" . $row->author;
            }
            if ($row->metadata == "") {
                $row->metadata = "robots=
								  author=";
            }

            if ($post["created"]) {
                $row->created = JFactory::getDate($post["created"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->created = $row->created->toMySQL();
                } else {
                    $row->created = $row->created->toSQL();
                }
            }

            if ($post["publish_up"]) {
                $row->publish_up = JFactory::getDate($post["publish_up"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->publish_up = $row->publish_up->toMySQL();
                } else {
                    $row->publish_up = $row->publish_up->toSQL();
                }
            }

            if ($post["publish_down"] && trim($post["publish_down"]) != JText::_('COM_OSCONTENT_NEVER')) {
                $row->publish_down = JFactory::getDate($post["publish_down"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->publish_down = $row->publish_down->toMySQL();
                } else {
                    $row->publish_down = $row->publish_down->toSQL();
                }
            } elseif (trim($post["publish_down"]) == JText::_('COM_OSCONTENT_NEVER')) {
                $post["publish_down"] = JFactory::getDBO()->getNullDate();

                $row->publish_down = JFactory::getDate($post["publish_down"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->publish_down = $row->publish_down->toMySQL();
                } else {
                    $row->publish_down = $row->publish_down->toSQL();
                }
            }

            // Handle archived
            if (isset($post["state2"]) && $post["state2"]) {
                $row->state = -1;
            } else {
                $row->state = 1;
            }

            $table = JTable::getInstance('Content', 'Table');

            $params = array(
                'alias' => $row->alias,
                'catid' => $row->catid
            );

            if ($table->load($params) && ($table->id != $row->id || $row->id == 0)) {
                JError::raiseWarning(
                    "Save content",
                    JText::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS') . ": " . $row->alias
                );

                return false;
            }
        }

        for ($i = 0; $i < count($post["title"]); $i++) {
            $index = $i + 1;

            if ($post["title"][$i] == "") {
                continue;
            }

            $row = $this->getTable();

            $row->id    = 0;
            $row->title = $post["title"][$i];
            if ($post["alias"][$i]) {
                $row->alias = JFilterOutput::stringURLSafe($post["alias"][$i]);
            } else {
                $row->alias = JFilterOutput::stringURLSafe($row->title);
            }

            if (trim(str_replace('-', '', $row->alias)) == '') {
                $row->alias = JFactory::getDate()->format('Y-m-d-H-i-s') . "-" . $i;
            }

            $intro_text      = $post['introtext_' . $index];
            $full_text       = $post['fulltext_' . $index];
            $row->introtext  = ($intro_text != "" ? $intro_text : $full_text);
            $row->fulltext   = ($intro_text != "" ? $full_text : "");
            $row->metakey    = $post["metakey"][$i];
            $row->metadesc   = $post["metadesc"][$i];
            $row->robots     = @$post["robots"];
            $row->author     = @$post["author"];
            $row->catid      = $post["catid"];
            $row->access     = $post["access"];
            $row->language   = "*";
            $row->created_by = $post["created_by"];

            $row->metadata = "";
            if ($row->robots != "") {
                $row->metadata = "robots=" . $row->robots . "\n";
            }
            if ($row->author != "") {
                $row->metadata .= "author=" . $row->author;
            }
            if ($row->metadata == "") {
                $row->metadata = "robots=
								  author=";
            }

            if ($post["created"]) {
                $row->created = JFactory::getDate($post["created"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->created = $row->created->toMySQL();
                } else {
                    $row->created = $row->created->toSQL();
                }
            }

            if ($post["publish_up"]) {
                $row->publish_up = JFactory::getDate($post["publish_up"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->publish_up = $row->publish_up->toMySQL();
                } else {
                    $row->publish_up = $row->publish_up->toSQL();
                }
            }

            if ($post["publish_down"] && trim($post["publish_down"]) != JText::_('COM_OSCONTENT_NEVER')) {
                $row->publish_down = JFactory::getDate($post["publish_down"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->publish_down = $row->publish_down->toMySQL();
                } else {
                    $row->publish_down = $row->publish_down->toSQL();
                }
            } elseif (trim($post["publish_down"]) == JText::_('COM_OSCONTENT_NEVER')) {
                $post["publish_down"] = JFactory::getDBO()->getNullDate();

                $row->publish_down = JFactory::getDate($post["publish_down"]);

                // Joomla 3.x Backward Compatibility
                if (version_compare(JVERSION, '3.0', '<')) {
                    $row->publish_down = $row->publish_down->toMySQL();
                } else {
                    $row->publish_down = $row->publish_down->toSQL();
                }
            }


            //handle archived
            if (@$post["state2"]) {
                $row->state = -1;
            } else {
                $row->state = 1;
            }

            if (!$row->store()) {
                return false;
            }

            $row->reorder('catid = ' . (int)$row->catid . ' AND state >= 0');

            if (@$post["addMenu"] === 0 || @$post['addMenu'] === 'on') {
                $type = "content_item_link";
                $this->menuLink($row->id, $row->title, @$post["menuselect"], $type, @$post["menuselect3"], $row->alias);
            }

            if ($frontpage) {
                $db = JFactory::getDBO();
                $fp = new TableFrontPage($db);

                // Is the item already viewable on the frontpage?
                // Insert the new entry
                $query = 'INSERT INTO #__content_frontpage' .
                    ' VALUES (' . (int)$row->id . ', 1)';
                $db->setQuery($query);

                if (!$db->query()) {
                    JError::raiseError(500, $db->stderr());

                    return false;
                }

                $fp->ordering = 1;
                $fp->reorder();
            }
        }

        return true;
    }
}
