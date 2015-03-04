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
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_CWTAGS_NEW_CWTAG') : JText::sprintf('COM_CWTAGS_CWTAG_DETAILS', $this->item->id); ?></legend>

       <?php if(isset($this->item->params['imageurl'])){
        ?><div style="float: right; border: 1px solid #d5d5d5; padding: 10px;"><img src="<?php echo JUri::root().$this->item->params['imageurl'];?>" alt="image" style="max-width: 300px"/></div><?php
        }?>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>

				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>

  			<?php foreach($this->form->getFieldset('image') as $field): ?>
  				<li><?php echo $field->label; ?>
  					<?php echo $field->input; ?></li>
  			<?php endforeach; ?>                      
				
				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

        <li style="display: none"><?php echo $this->form->getLabel('file'); ?>
				<?php echo $this->form->getInput('file'); ?></li>         
        <?php if($this->item->file){
        //echo "<li><label></label><div style='float: left; height: 20px;'><a href='".$this->item->file."' >".$this->item->file."</a></div></li>";
        echo "<li style=\"display: none\"><label></label><div style='float: left; height: 20px; font-weight: bold;'>".$this->item->file." ( ".JText::_('COM_CWTAGS_FILE_NOTE')." )</div>
        <input type='hidden' name='file[name]' id='file_name' value='".$this->item->file."' />
        </li>";
        } ?> 
      	
               
      <?php
      // Zatim vypnuto - neni využití, ale může v být užitečné pro budoucí vývoj aplikace
      if( isset($advanced) and $advanced == true ) { ?>
        <li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>
        <li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>
      <?php } ?>
        
				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
			</ul>


		</fieldset>
	</div>

<div class="width-40 fltrt">
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</div>

<div class="clr"></div>
</form>
<script>
/*itemid = document.getElementById("jform_id").value;
if(itemid > 0){
  document.getElementById("jform_file").value = '<?php echo $this->item->file;?>';
} */
</script>

