<?php
/*
 * author Cesky WEB s.r.o.
 * @component Multicats
 * @copyright Copyright (C) Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div id="news" style="
float: right;
width: 40%;
margin-left: 40px;
">
  <iframe style="border: 0px;" width="100%" src="http://extensions.cesky-web.eu/update/cwmulticats.php"></iframe>
</div>

<p>This component allows you to assign article into multiple categories.</p>
    <p style='font-weight: bold;'>
    Note: Do not forget to check out different Interfaces - which you can switch in Component Options
    </p>
<p>Following files are patched and original files were backed up. You can restore your original files by uninstalling this component.</p>
<ul>
  <li>administrator/components/com_content/models/article.php</li>
  <li>administrator/components/com_content/models/articles.php</li>
  <li>administrator/components/com_content/models/forms/article.xml</li>
  <li>administrator/components/com_content/views/articles/tmpl/default.php</li>
  <li>administrator/components/com_content/views/article/view.html.php</li>
  <li>administrator/components/com_content/views/featured/tmpl/default.php</li>
  <li>components/com_content/models/articles.php</li>
  <li>components/com_content/models/category.php</li>  
  <li>components/com_content/models/categories.php</li>
  <li>components/com_content/models/featured.php</li>
  <li>components/com_content/models/forms/article.xml</li>
  <li>components/com_content/views/category/tmpl/blog_item.php</li>
  <li>components/com_content/views/category/tmpl/blog_children.php</li>
  <li>components/com_content/views/category/tmpl/default_children.php</li>
  <li>components/com_content/views/categories/view.html.php</li>
  <li>components/com_content/helpers/query.php</li>
  <li>components/com_content/helpers/route.php</li>
  <li>components/com_content/views/article/view.html.php</li>  
  <li>components/com_content/router.php</li>
  <li>modules/mod_articles_category/helper.php</li>
  <li>plugins/content/pagenavigation/pagenavigation.php</li>
</ul>
    <p>Now you can publish articles into multiple categories via AJAX modal window</p>
    <p>Recommended way for Update Joomla while using Multicats is:
    - uninstall CW Multicats
    - update Joomla
    - install CW Multicats
    </p>
    <p>
    Note: If you will not re-save articles (f.e. edit and save) meanwhile, while having CW Multicats uninstalled, informations about multicategories assigned to article will not be lost. So dont worry about uninstalling for update process.
    </p>
    <p>If you do just update of Multicats, you can simply update it over existing installation.</p>    
    <p style='font-weight: bold;'>
    Note: For compatibility with CW Tags plugin, please update to CWTags Plugin to at least version 1.1.8.
    </p>
    
<p>
<?php echo "Version ".$this->tmpl['version']; ?>
</p>