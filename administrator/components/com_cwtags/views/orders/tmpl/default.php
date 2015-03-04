<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
//$canOrder	= $user->authorise('core.edit.state', 'com_cwtags.category');
$saveOrder	= $listOrder=='ordering';
$params		= (isset($this->state->params)) ? $this->state->params : new JObject();

// get lang tag for ajax calling
$lang =& JFactory::getLanguage();
$language_tag = $lang->getTag();

JHTML::stylesheet( JUri::root().'components/com_cwtags/assets/cwtags.css' );
JHTML::stylesheet( JUri::root().'components/com_cwtags/assets/css.css' );

$document =& JFactory::getDocument();
$document->addScript(JUri::root().'components/com_cwtags/assets/jquery-1.3.2.js');
$document->addScript(JUri::root().'components/com_cwtags/assets/jquery.livequery.js');
?>


<script type="text/javascript">

$(document).ready(function() {

	
	var Arrays=new Array();
	
	$('.update_status').click(function(){
    
		var thisID 	  = $(this).attr('id').replace('status-','');
    //var status 	  = $(this).children('img').attr('class').replace('status-','');
		//alert(thisID);
    //$('#status-'+thisID).removeClass("active inactive").addClass("active");  
    
      $.ajax({
         type: 'POST',
         url: '<?php echo JURi::base();?>components/com_cwtags/helpers/orders.php',
         data: 'id=' + thisID + '&lang=<?php echo $language_tag;?>',
         cache: false,
         success: function(html){
          //$('#info').html(html);
          $('#status-'+thisID).attr('class', html);         
          //$('#shout').text('<?php echo JText::_('COM_CWTAGS_ORDERS_DOWNLOAD');?> ');
          //$('#shout').fadeIn('slow'); 
          //$('#shout').fadeOut('slow');
          
         }
      });	
	});	

	

	
	// this is for 2nd row's li offset from top. It means how much offset you want to give them with animation
	var single_li_offset 	= 200;

	$('.showitems').click(function() {

		var $this  = $(this).parent();

		//$('#orders .detail').hide('slow')		
		//var id = $('#orders li.order').index($this);
    $this.children('.detail').toggle("slow");

    
	});
	
});


</script>
<div id="info"></div>
<div id="shout"></div>

<form action="<?php echo JRoute::_('index.php?option=com_cwtags&view=orders'); ?>" method="post" name="adminForm" id="adminForm">
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

			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_CWTAGS_HEADING_NAME', 'a.author', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_CWTAGS_HEADING_ACCOUNT', 'a.account', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_CWTAGS_HEADING_STATUS', 'a.status', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
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
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'ordering');
			$canCreate	= $user->authorise('core.create',		'com_cwtags.orders.'.$item->catid);
			$canEdit	= $user->authorise('core.edit',			'com_cwtags.orders.'.$item->catid);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_cwtags.orders.'.$item->catid) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'orders.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_cwtags&task=order.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->author); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->author); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->username));?></p>
				</td>
				<td class="left">
					<?php echo $item->account;
          //get order items
          $model = $this->getModel('orders');
          $rows = $model->getOrderItems($item->id);
          //get total price
          $total = 0;
          foreach($rows AS $row){
            $total += $row->price*$row->ammount;
          }          
          ?>
          <span class="showitems"><?php echo JText::_('COM_CWTAGS_ORDERS_SHOW_ITEMS'); ?></span>
          <div class="detail">
            <table class="items"><?php
              echo "<tr>";
              echo "<td width='70%'>".JText::_('COM_CWTAGS_ORDERS_ITEMS_NAME')."</td>";
              echo "<td align='center'>".JText::_('COM_CWTAGS_ORDERS_ITEMS_PRICE')."</td>";
              echo "<td align='center'>".JText::_('COM_CWTAGS_ORDERS_ITEMS_AMMOUNT')."</td>";
              echo "</tr>";
            foreach($rows AS $row){
              $link = CwtagsHelper::getFileLink($row->item_id);
              echo "<tr>";
              echo "<td width='70%'>";
                echo $row->name;
                //if($link){ echo "<a id='file-".$row->item_id."' class='button download' href='#'>".JText::_('COM_CWTAGS_ORDERS_DOWNLOAD')."</a>"; 
                if($link){
                  //echo '<button id=\'file-".$row->item_id."\' type="button" class="download" onClick="window.location=\''.JUri::base().'components/com_cwtags/helpers/download.php?id='.$row->item_id.'\'" name="AdvancedButton1">'.JText::_('COM_CWTAGS_ORDERS_DOWNLOAD').'</button>';
                  echo " (".JText::_('COM_CWTAGS_ORDERS_DOWNLOAD').")";
                }
              echo "</td>";
              echo "<td align='center'>".$row->price."</td>";
              echo "<td align='center'>".$row->ammount."</td>";
              echo "</tr>";
            }
            ?>
            </table>
          </div> 				
        </td>
				<td class="center">
          <?php if($item->status == 0) {
            $img = JUri::base()."components/com_cwtags/assets/images/publish_x.png";
            $title = JText::_('COM_CWTAGS_ORDERS_STATUS_INACTIVE');
            $class = "inactive";
          } elseif($item->status == 1){
            $img = JUri::base()."components/com_cwtags/assets/images/tick.png";
            $title = JText::_('COM_CWTAGS_ORDERS_STATUS_ACTIVE');
            $class = "active";
          }?>
          <span id="status-<?php echo $item->id;?>" class="update_status <?php echo $class;?>" href="#"></span>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'orders.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php //Load the batch processing form. ?>
	<?php echo $this->loadTemplate('batch'); ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
