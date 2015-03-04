<?php
/**
 * author Cesky WEB s.r.o.
 * @component CWtags
 * @copyright Copyright (C) Pavel Stary, Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * CWtags list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cwtags
 * @since		1.6
 */
class CWtagsControllerOrders extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_CWTAGS_CWTAGS';

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('sticky_unpublish',	'sticky_publish');
    $this->registerTask('status_unpublish',	'status_publish');
	}

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Order', $prefix = 'CWtagsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

  public function status_publish(){
    echo "ff";exit;
  }

	/**
	 * @since	1.6
	 */
	public function sticky_publish()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('sticky_publish' => 1, 'sticky_unpublish' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('COM_CWTAGS_NO_CWTAGS_SELECTED'));
		} else {
			// Get the model.
			$model	= $this->getModel();

			// Change the state of the records.
			if (!$model->stick($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			} else {
				if ($value == 1) {
					$ntext = 'COM_CWTAGS_N_CWTAGS_STUCK';
				} else {
					$ntext = 'COM_CWTAGS_N_CWTAGS_UNSTUCK';
				}
				$this->setMessage(JText::plural($ntext, count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_cwtags&view=cwtags');
	}
}
