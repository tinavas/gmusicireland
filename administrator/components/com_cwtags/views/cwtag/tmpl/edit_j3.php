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
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cwtag.cancel' || document.formvalidator.isValid(document.id('cwtag-form'))) {
			Joomla.submitform(task, document.getElementById('cwtag-form'));
		}
	}
	window.addTag('domready', function() {
		document.id('jform_type0').addTag('click', function(e){
			document.id('image').setStyle('display', 'block');
			document.id('url').setStyle('display', 'block');
			document.id('custom').setStyle('display', 'none');
		});
		document.id('jform_type1').addTag('click', function(e){
			document.id('image').setStyle('display', 'none');
			document.id('url').setStyle('display', 'block');
			document.id('custom').setStyle('display', 'block');
		});
		if(document.id('jform_type0').checked==true) {
			document.id('jform_type0').fireTag('click');
		} else {
			document.id('jform_type1').fireTag('click');
		}
	});
</script>
<style>
ul.noicon { list-style: none;}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_cwtags&layout=edit&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="cwtag-form" class="form-validate">
<div class="span10 form-horizontal">

	<fieldset>
      <legend><?php echo empty($this->item->id) ? JText::_('COM_CWTAGS_NEW_CWTAG') : JText::sprintf('COM_CWTAGS_CWTAG_DETAILS', $this->item->id); ?></legend>

  		<ul class="nav nav-tabs">
  			<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_CWTAGS_CWTAG_DETAILS');?></a></li>
  			<li style="display: none"><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_CWTAGS_CWTAG_DETAILS');?></a></li>
  		</ul>
  			

       <?php if(isset($this->item->params['imageurl'])){
        ?><div style="  padding: 10px;"><img src="<?php echo JUri::root().$this->item->params['imageurl'];?>" alt="image" style="max-width: 300px"/></div><?php
        }?>


  		<div class="tab-content">
  			<div class="tab-pane active" id="details">
  				<div class="control-group">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('name'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('name'); ?>
  					</div>
  				</div>
  				<div class="control-group">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('alias'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('alias'); ?>
  					</div>
  				</div>
  				<div class="control-group">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('catid'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('catid'); ?>
  					</div>
  				</div>
          
          <?php foreach($this->form->getFieldset('image') as $field): ?>
  				<div class="control-group">
  					<div class="control-label">
  						<?php echo $field->label; ?>
  					</div>
  					<div class="controls">
  						<?php echo $field->input; ?>
  					</div>
  				</div>
          <?php endforeach; ?>
        <?php // Zatim vypnuto - neni využití, ale může v být užitečné pro budoucí vývoj aplikace
        if( isset($advanced) and $advanced == true ) { ?>
  				<div class="control-group">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('access'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('access'); ?>
  					</div>
  				</div>
  				<div class="control-group">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('language'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('language'); ?>
  					</div>
  				</div>
        <?php } ?>
   
   				<div class="control-group">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('state'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('state'); ?>
  					</div>
  				</div>
  				<div class="control-group">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('id'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('id'); ?>
  					</div>
  				</div>        
   
  				<div class="control-group" style="display: none">
  					<div class="control-label">
  						<?php echo $this->form->getLabel('file'); ?>
  					</div>
  					<div class="controls">
  						<?php echo $this->form->getInput('file'); ?>
              <?php if($this->item->file){
              //echo "<li><label></label><div style='float: left; height: 20px;'><a href='".$this->item->file."' >".$this->item->file."</a></div></li>";
              echo "<li style=\"display: none\"><label></label><div style='float: left; height: 20px; font-weight: bold;'>".$this->item->file." ( ".JText::_('COM_CWTAGS_FILE_NOTE')." )</div>
              <input type='hidden' name='file[name]' id='file_name' value='".$this->item->file."' />";
              }?>            
  					</div>
  				</div>
  
        </div>
  	 </div>
	</fieldset>

</div>


<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
<script>
/*itemid = document.getElementById("jform_id").value;
if(itemid > 0){
  document.getElementById("jform_file").value = '<?php echo $this->item->file;?>';
} */
</script>

