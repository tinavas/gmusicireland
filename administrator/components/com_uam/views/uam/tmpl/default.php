<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<span style="font-weight:bold;">
	<?php echo JText::_('COM_UAM_WELCOME'); echo " com_uam_v" . $this->params->get('version'); ?>
</span>
<br /><br />
<?php echo JText::_('COM_UAM_MESSAGE'); ?>

<br />
<br /><?php echo JText::_('COM_UAM_TRANSLATIONS_BY'); ?>
<br />Fran&ccedil;ais - Marc Bénita, Michel Cercueil, G. Costes
<br />Deutsch - Jojo Murer
<br />Español  - Marcos J. Diep
<br />Italiano  - Luca Ratti (frontend)
<br />Русский язык - Vladimir, JanRUmoN Team
<br />Nederlands - Daniel van Keulen
<br />Dansk - Jakob Dalsgaard (frontend)
<br />Język Polski - Leszek Boroch
<br />Norsk (Bokmål) - Goran Aasen
<br />Português (Brasil) - Bernd R
<br />中文 (简体及正體) - Xiaogang Zhang
<br />فارسی - Abdulhalim Pourdaryaei
<br />العربية (Unitag) - Maad Saad (frontend)
<br />български език - Stefan Stefanov
<br />Svenska - Mats Arvendal, Stefan Lewitas (frontend)
<br />ελληνικά - Nicholas Antimisiaris
<br />Suomi - www.kalamies.com
<br />Română - Mystic Angel
<br />
<br />
<br />
<?php echo JText::_('COM_UAM_DONATE'); ?>
<br />
<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="XUMEANJBX9USA">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal � The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>