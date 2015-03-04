<?php
 /**
 * @package Spider Random Article
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 
defined('_JEXEC') or die('Restricted access');

class JFormFieldtypeselect extends JFormField
{
	var	$_name = 'typeselect';
function getInput()
{
	
        ob_start();
        static $embedded;
                if(!$embedded)
        {

            $embedded=true;

        }



            ?>
<span id="cuc"></span>
<fieldset name="gag" class="radio">
<input type="radio" name="<?php echo $this->name;?>"  value="0" <?php if($this->value==0) echo 'checked="checked"'?> onChange="show_(0)" id="show0"><label for="show_(0)"> Playlist</label>
<input type="radio" name="<?php echo $this->name;?>"  value="1" <?php if($this->value==1) echo 'checked="checked"'?> onChange="show_(1)" id="show1"> <label for="show_(1)">Single Video</label>
</fieldset>
<script type="text/javascript">
<?php 
		$db	= JFactory::getDBO();
$query="SELECT MAX(version_id) FROM #__schemas";
$db->setQuery($query);
$version=$db->loadResult();


if((float)$version>3.1)
{
?>
var i=5;
var j=4;
var k=3;
<?php
}
else
{
?>
var i=11;
var j=9;
var k=7;

<?php }?>

function show_(x)
{
	
	if(x==0)
	{
	if(document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[i])
	{
	document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[i].setAttribute('style','display:none');	
	document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[j].removeAttribute('style');	
	}
	else
	{
	document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[j].setAttribute('style','display:none');	
	document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[k].removeAttribute('style');	
	
	}

	}
	else
	{
		if(document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[i])
	{
	document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[j].setAttribute('style','display:none');	
	document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[i].removeAttribute('style');
/*document.getElementById('cuc2').parentNode.parentNode.parentNode.parentNode.parentNode.style.height="171px";*/	
	}
	else
	{
		document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[k].setAttribute('style','display:none');	
		document.getElementById('cuc').parentNode.parentNode.parentNode.childNodes[j].removeAttribute('style');

	}

	}
}

</script>

        <?php

        $content=ob_get_contents();

        ob_end_clean();
        return $content;

    }
	}
	
	?>