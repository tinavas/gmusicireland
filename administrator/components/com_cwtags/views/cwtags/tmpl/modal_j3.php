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

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$extension	= $this->escape($this->state->get('filter.extension'));
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$saveOrder 	= ($listOrder == 'a.lft' && $listDirn == 'asc');
$canOrder	= $user->authorise('core.edit.state', 'com_cwtags.category');

$language =& JFactory::getLanguage();
$extension = 'com_cwtags';
$language_tag = $language->getTag(); // loads the current language-tag
$language->load('com_cwtags', JPATH_SITE, $language_tag, true);
 
$function = JRequest::getCmd('function', 'jSelectTag');


//$session =& JFactory::getSession();
//$data = $session->get("catz");
//echo $data;
//$data = $_SESSION['mcatz'];


$mainframe = &JFactory::getApplication();
$data = $mainframe->getUserState( "com_cwtags.settags", '' );

$data = json_decode($data);

$document =& JFactory::getDocument();
$document->addStyleDeclaration('
    .tagz { display: inline-block; line-height: 16px; margin: 3px; -webkit-border-radius: 5px; border-radius: 5px; color: #555; border: none; background: #5ba0ed;
color: white !important; padding: 5px 10px 5px 5px;}
    .tagz.red { border: none; background: #FF8080; color: white !important;}
    .tagz img { padding: 0px; margin: 0px; width: 16px; height: 16px; margin-right: 5px; cursor: pointer;}
    #tagmask {width: 100%; height: 100%; position: absolute; display: none; background: rgba(255,255,255,0.7) url('.JURI::root().'plugins/content/cwtags/cwtags/assets/loading.gif) center 0px no-repeat; }
    ');
?>

<script type="text/javascript">
function checkCategory(id, item, title, chck) {
    var $tjq = jQuery.noConflict();
    $tjq(document).ready(function($) {
      $tjq().ready(function() {  
        $tjq("#tagmask").css("display" , "block");
        
        catztitles = document.getElementById("catztitles").value;
        catzz = document.getElementById("catz").value;
        //alert(catztitles);
        var rand = 'rand='+Math.random();
        $tjq.ajax({
          type: "GET",                                                                             
          //url:"<?php echo JUri::root();?>settags.php",
          url:"<?php echo JUri::root();?>administrator/components/com_cwtags/helpers/settags.php",
          //data: <?php echo '"item="+item+"&title="+title+"&chck="+chck';?>,
          data: rand+"&item="+item+"&title="+title+"&chck="+chck+"&client=administrator&rand2="+Math.random(),
          //data: rand+"&item="+item+"r="+Math.random(),
          success:function(results){
              //alert(results);
              var obj=$tjq.parseJSON(results); // now obj is a json object
              var cattitles = '';
              var catids = '';
              var i = 1;
              var j = 1;
              $tjq.each( obj, function(key){  
  
                $tjq.each( obj[key], function(k,v){
                 //alert( "Key: " + k + ", Value: " + v );
                 if(k == 'id'){
                  //alert( "ID: " + v );
                  //if(i == obj.length) {
                  if(i == 1) { catids = v; }
                  else { catids = catids + ',' + v; }
                  i = i + 1;
                 }
                 if(k == 'title'){
                  //alert( "Title: " + v );
                  if(j == 1) { cattitles = v; }
                  else { cattitles = cattitles + ';' + v; }
                  j = j + 1;
                 }
                 //alert(obj[key].title); // will alert "1"
                });
              });
              //if(catids.substring(0,1) == ','){catids = catids.substring(1);}  
              //if(cattitles.substring(0,4) == 'null'){cattitles = cattitles.substring(5);}
              //alert(catids); 
              //alert(cattitles);
              document.getElementById("catz").value=catids;
              document.getElementById("catztitles").value=cattitles;
              //alert(obj[0].id); // will alert "1"
              //alert(obj[0].title); // will alert "This is some content"
              
              $tjq("#tagmask").css("display" , "none");
          }
        });
    });
  });
}
</script>
  
<?php
$document =& JFactory::getDocument();
//$document->addScript('http://code.jquery.com/jquery-latest.js');
$document->addScript(JUri::base().'components/com_cwtags/helpers/jquery1.7.2.js');
$document->addStyleDeclaration('
.conf { display: inline-block; padding: 5px; margin: 3px; -webkit-border-radius: 5px; border-radius: 5px; color: #555; border: 2px solid #AADE66; }
.conf:hover { border: 2px solid;}
#filter-bar { height: auto; }
#filter-bar select { width: auto; }
');
?>

<form class="tagform" action="<?php echo JRoute::_('index.php?option=com_cwtags&view=cwtags&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm">

<?php 
//echo "<pre>"; print_r($_SESSION['__default']['catz']); echo "</pre>";
//print_r($_SESSION['mcatz']);

$i = 1;
/*
$db = JFactory::getDbo();
$query = "SELECT data FROM #__session WHERE session_id ='".$session->getId()."'";
$db->setQuery($query);
$result = $db->loadObject();
*///echo $result->data;

$catz = '';
$catztitles = ''; 
//$data = object_to_array($data);
//echo "<pre>"; print_r($data); echo "</pre>";
$count = count( (array) $data);
if($count > 0)
{
  foreach($data as $key => $item){
    $catz .= $item->id;
    $catztitles .= $item->title;
    if($i < $count) {
      $catz .= ',';
      $catztitles .= ';';  
    }
    $i++;
  }
}
/*
$i = 1;

$catz = '';
$catztitles = ''; 

if(isset($_GET['catz']) AND $_GET['catz'] != ''){
  $catz = $_GET['catz'];
  $catzarray = explode(',',$_GET['catz']);
  $db = &JFactory::getDbo();
  foreach($catzarray as $key => $cat){
    $query = "SELECT title FROM #__categories WHERE id = ".(int)$cat."";
    $db->setQuery($query);
    $result = $db->loadObject();
    $catztitles .= $result->title;
    if(count($catzarray) > $key+1){ $catztitles .= ','; }
  }
}
*/  
?>

	<span><?php echo JText::_('COM_CWTAGS_SELECTED_CATEGORIES'); ?></span>
  <input type="inputbox" id="catz" name="catz" value="<?php echo $catz; ?>" readonly="readonly"/>
  <span><?php echo JText::_('COM_CWTAGS_SELECTED_CATEGORIES_TITLES'); ?></span>
  <input type="inputbox" id="catztitles" name="catztitles" value="<?php echo $catztitles; ?>" size="100" readonly="readonly"/>

  <button class="conf" type="button" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>(document.id('catz').value, document.id('catztitles').value);"><?php echo JText::_('COM_CWTAGS_CAT_SUBMIT'); ?></button>
 
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CWTAGS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">

			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_cwtags'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>


		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
          <button type="button" style="font-size: 90%" name="checkall-togglef" title="<?php echo JText::_('COM_CWTAGS_CAT_CANCEL'); ?>" onclick="uncheck();" /><?php echo JText::_('COM_CWTAGS_CAT_CANCEL'); ?></button>
          
				</th>
				<th width="5%">
					<?php echo JText::_('COM_CWTAGS_HEADING_IMAGE'); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_CWTAGS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder): ?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'cwtags.saveorder'); ?>
					<?php endif;?>
				</th>

				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			
      if(isset($catz)) {$catzarray = explode(',',$catz);}
      else {$catzarray = array();}
      
      $originalOrders = array();
			foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'ordering');
			$item->cat_link = JRoute::_('index.php?option=com_cwtags&task=edit&type=other&cid[]='. $item->catid);
			$canCreate	= $user->authorise('core.create',		'com_cwtags.category.'.$item->catid);
			$canEdit	= $user->authorise('core.edit',			'com_cwtags.category.'.$item->catid);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_cwtags.category.'.$item->catid) && $canCheckin;
			
      
      //For Modal view cant edit this way - should create a modal edit view
      $canEdit = false;
      // params for images
      //$itemparams = new JParameter( $item->params ); // J 2.5
      
      $itemparams = new JRegistry;
      $itemparams->loadString($item->params);
      //$item->params = $itemparams;  
      ?>
			<tr class="row<?php echo $i % 2; ?>">
        <td class="center">
						<?php //echo JHtml::_('grid.id', $i, $item->id);
            if(in_array($item->id,$catzarray)){
              $chck = ' checked="checked"';
            } else { $chck = ''; }
            ?>
            <input class="cats" <?php echo $chck;?> type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $item->id; ?>" onclick="checkCategory(id, <?php echo $item->id;?>, '<?php echo $this->escape($item->name); ?>', this.checked);" title="" />
				</td>

        <td>
        <?php if($itemparams->get('imageurl')) {
        ?><div style="padding: 10px;">
          <img src="<?php echo JUri::root().$itemparams->get('imageurl');?>" alt="image" style="max-width: 300px"/>
        </div><?php
        }?>
        </td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'cwtags.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_cwtags&task=cwtag.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->name); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'cwtags.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) : ?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->catid == $item->catid), 'cwtags.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->catid == $item->catid), 'cwtags.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->catid == $item->catid), 'cwtags.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->catid == $item->catid), 'cwtags.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled; ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>

				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php //Load the batch processing form. ?>
	<?php //echo $this->loadTemplate('batch'); ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<script>
function uncheck(){
  $("form input:checkbox.cats").attr('checked', false);
  checkCategory(0,0,0,false);
}
$('<div>', {id:"tagmask" } ).prependTo("form.tagform");  //recreate tagmask
</script>