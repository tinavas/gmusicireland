<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;

// ensure this file is being included by a parent file
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

// register delete user code function
global $_PLUGINS;
$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'userDeleted','getmypmsproTab' );

class getmypmsproTab extends cbPMSHandler
{
	private $uddeconfig;

	/**
	* Constructor
	*/
	public function __construct( )
	{
		parent::__construct();
	}

	protected function _checkPMSinstalled( $pmsType )
	{
		global $_CB_framework;
		
		$absolutePath = $_CB_framework->getCfg('absolute_path');
		
		if (!(	(($pmsType==1 || $pmsType==5) && file_exists( $absolutePath . '/components/com_pms/pms.php' ))
			||	($pmsType==2 && file_exists( $absolutePath . '/components/com_mypms/mypms.php' ))
			||	($pmsType==6 && file_exists( $absolutePath . '/components/com_jim/jim.php'	))
			||	(($pmsType==3 || $pmsType==4) && file_exists( $absolutePath . '/components/com_uddeim/uddeim.php' )))) {
			$this->_setErrorMSG( CBTxt::T( 'UE_PMS_NOTINSTALLED', 'The selected PMS System is not installed.' ) );
			return false;
		}
		return true;
	}

	protected function _sendPMSProMSG( $to, $from, $sub, $msg )
	{
		global $_CB_database;

		// escaping not necessary, already escaped before this internal function gets called
		$sql="INSERT INTO #__mypms (username,whofrom,time,readstate,subject,message,owner,sent_id) VALUES('"
			.$_CB_database->getEscaped($to)."','".$_CB_database->getEscaped($from)."',NOW(),0,'".$sub."','".$msg."','".$to."',0);";
		$_CB_database->SetQuery($sql);
		$_CB_database->query();
	}

	protected function _sendPMSOSMSG( $to, $from, $sub, $msg )
	{
		global $_CB_database;

		// escaping not necessary, already escaped before this internal function gets called
		$sql="INSERT INTO #__pms (username,whofrom,date,time,readstate,subject,message) VALUES('"		// MyPMS II
			.$_CB_database->getEscaped($to)."','".$_CB_database->getEscaped($from)."',CURDATE(),CURTIME(),0,'".$sub."','".$msg."');";
		$_CB_database->SetQuery($sql);
		if (!$_CB_database->query()) {
			$sql = "INSERT INTO #__pms (username,whofrom,date,readstate,subject,message) VALUES('"		// PMS OS
			.$to."','".$from."',NOW(),0,'".$sub."','".$msg."');";
			$_CB_database->SetQuery($sql);
			$_CB_database->query();
		}
	}

	protected function _sendPMSJimMSG( $to, $from, $sub, $msg )
	{
		global $_CB_database;
		
		// escaping not necessary, already escaped before this internal function gets called
		$sql="INSERT INTO #__jim (username,whofrom,date,readstate,subject,message) VALUES('"
			.$_CB_database->getEscaped($to)."','".$_CB_database->getEscaped($from)."',NOW(),0,'".$sub."','".$msg."');";
		$_CB_database->SetQuery($sql);
		$_CB_database->query();
	}

	protected function _sendPMSenhancedMSG( $to_id, $from_id, $sub, $msg )
	{
		global $_CB_framework, $_CB_database;

		// escaping not necessary, already escaped before this internal function gets called
		$sql="INSERT INTO #__pms (recip_id,sender_id,date,time,readstate,subject,message) VALUES(".$to_id.",".$from_id.",CURDATE(),CURTIME(),0,'".$sub."','".$msg."');";
		$_CB_database->SetQuery($sql);
		$_CB_database->query();

		// email notification

		/** @noinspection PhpIncludeInspection */
		require_once( "administrator/components/com_pms/config.pms.php" );
		
		// get the right language if it exists
		if (file_exists('administrator/components/com_pms/language/'.$_CB_framework->getCfg( 'lang' ).'.php')) {
			/** @noinspection PhpIncludeInspection */
			include_once( 'administrator/components/com_pms/language/'.$_CB_framework->getCfg( 'lang' ).'.php' );
		}
		else {
			/** @noinspection PhpIncludeInspection */
			include_once('administrator/components/com_pms/language/english.php');
		}

		// get default configuration from database
		$_CB_database->setQuery("SELECT email_new, email_html, email_offline FROM #__pms_conf WHERE user_id=0");
		$_CB_database->query();

		$rows = $_CB_database->loadObjectList();
		$row = $rows[0];
		$email_new = $row->email_new;
		$email_html = $row->email_html;
		$email_offline = $row->email_offline;
		
		// check settings of recip and override defaults if allowed
		/** @noinspection PhpUndefinedVariableInspection */
		if($allow_change_email_new==1)
		{
			// check if recip has personal settings, otherwise load defaults
			$_CB_database->setQuery("SELECT count(id) FROM #__pms_conf WHERE user_id=$to_id", 0, 1);
			$_CB_database->query();
			$result = $_CB_database->loadResult();
			if($result==1)
			{
				$_CB_database->setQuery("SELECT email_new, email_html, email_offline FROM #__pms_conf WHERE user_id=$to_id");
				$_CB_database->query();
				$rows = $_CB_database->loadObjectList();
				$row = $rows[0];
				if($allow_change_email_new==1) $email_new = $row->email_new;
				/** @noinspection PhpUndefinedVariableInspection */
				if($allow_change_email_html==1) $email_html = $row->email_html;
				/** @noinspection PhpUndefinedVariableInspection */
				if($allow_change_email_offline==1) $email_offline = $row->email_offline;
			}
		}
		
		// send email notification
		if($email_new==1)
		{
			// get name and email of recip
			$_CB_database->setQuery("SELECT username, email FROM #__users WHERE id=$to_id");
			$_CB_database->query();
			$rows = $_CB_database->loadObjectList();
			$row = $rows[0];
			$recip_name = $row->username;
			$recip_email = $row->email;
		
			// check if recip is offline
			$_CB_database->setQuery("SELECT count(session_id) FROM #__session WHERE userid=$to_id");
			$_CB_database->query();
			$result = $_CB_database->loadResult();
			if($result==0 OR $email_offline==0)
			{
				$email_site_name = $_CB_framework->getCfg( 'sitename' );
				$email_sender_name = $_CB_framework->myUsername();
				$_CB_database->setQuery("SELECT email FROM #__users WHERE id=$from_id", 0, 1);
				$_CB_database->query();
				$email_sender_email = $_CB_database->loadResult();
				$email_recip_name = $recip_name;
				$email_recip_email = $recip_email;
				$message = stripslashes($msg);
				$subject = stripslashes($sub);
				if($email_html==1) $message = nl2br($message);

				/** @noinspection PhpUndefinedConstantInspection */
				$email_subject = sprintf(_PMS_EMAIL_SUBJECT_NEW, $email_site_name, $email_sender_name);

				if ( $email_html == 0 ) {
					/** @noinspection PhpUndefinedConstantInspection */
					$email_message = sprintf(_PMS_EMAIL_MESSAGE_NEW_TEXT, $email_recip_name, $email_sender_name, $email_site_name, $subject, $message, $email_site_name);
				} else {
					/** @noinspection PhpUndefinedConstantInspection */
					$email_message = sprintf(_PMS_EMAIL_MESSAGE_NEW_HTML, $email_recip_name, $email_sender_name, $email_site_name, $subject, $message, $email_site_name);
				}
				comprofilerMail( $email_sender_email, $email_sender_name, $email_recip_email, $email_subject, $email_message, $email_html );
			} // end check if recip is offline
		} // end send email notification
	}

	protected function _sendPMSuddesysMSG( $udde_toid, $udde_fromid, /** @noinspection PhpUnusedParameterInspection */ $to, $from, $sub, $msg )
	{
		global $_CB_database, $_CB_framework; 

		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');
		/** @noinspection PhpUnusedLocalVariableInspection */
        $udde_sysm = "System";
        $config_realnames = "0";
        $config_cryptmode = 0;
        $config_cryptkey = 'uddeIMcryptkey';
        
		if ( ( $pmsType==4 ) && file_exists( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/crypt.class.php" ) ) { // uddeIM 1.0+
			/** @noinspection PhpIncludeInspection */
			require_once( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/crypt.class.php");

			if ( file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php");
			}
			/** @noinspection PhpUndefinedClassInspection */
			$this->uddeconfig = $config = new uddeimconfigclass();
			if ( isset($config->sysm_username)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$udde_sysm = $config->sysm_username;
			}
			if (isset($config->realnames)) {
				$config_realnames = $config->realnames;
			}
			if (isset($config->cryptmode)) {
				$config_cryptmode = $config->cryptmode;
			}
			if (file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" )) {
				/** @noinspection PhpIncludeInspection */
				require_once ( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" );
			}
			if (isset($config->cryptkey)) {
				$config_cryptkey = $config->cryptkey;
			}

		} else {
			if(file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php");
			}
			if(isset($config_sysm_username)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$udde_sysm = $config_sysm_username;
			}
		}		

		// format the message
		if($sub) {
			$udde_msg = "[b]".$sub."[/b]\n\n".$msg;
		} else {
			$udde_msg = $msg;
		}
		
		// now change the <strong> or <b> tags to BB Code
		$udde_msg = str_replace("<strong>","[b]",$udde_msg);
		$udde_msg = str_replace("<b>","[b]",$udde_msg);
		$udde_msg = str_replace("</strong>","[/b]",$udde_msg);
		$udde_msg = str_replace("</b>","[/b]",$udde_msg);
		
		// now change the links to BB code links
		$udde_msg = str_replace("<a href=\"", "[url=", $udde_msg);
		$udde_msg = str_replace("<a href=\\\"", "[url=", $udde_msg);		
		$udde_msg = str_replace("\">", "]", $udde_msg);
		$udde_msg = str_replace("\\\">", "]", $udde_msg);		
		$udde_msg = str_replace("</a>", "[/url]", $udde_msg);
		$udde_msg = str_replace("<br/>", "\n", $udde_msg);
		$udde_msg = str_replace("<br />", "\n", $udde_msg);
		$udde_msg = str_replace("<br>", "\n", $udde_msg);
		$udde_msg = str_replace("&amp;", "&", $udde_msg);
		
		// workaround
		// commands above made the closing bracket of the div to a ]
		// we change it back to a > here so that the next command can strip the div entirely
		$udde_msg = str_replace("cbNotice\\\"]", "cbNotice\\\">", $udde_msg);
		$udde_msg = str_replace("cbNotice]", "cbNotice\">", $udde_msg);
		$udde_msg = str_replace("cbNotice\\]", "cbNotice\">", $udde_msg);
		
		// now strip the remaining html tags
		$udde_msg = strip_tags($udde_msg);
		
		// get current time but recognize time offset
		$currentTime=time();
		$udde_time=$this->_pmsUddeGetTime($currentTime);
		
		// set the udde systemmessage username to the virtual sender
		$udde_sysm=$from;
		
		// try to find the realnames settings of udde
		// if(file_exists( $_CB_framework->getCfg('absolute_path') . '/administrator/components/com_uddeim/uddeim_config.php')) {
			// include_once( $_CB_framework->getCfg('absolute_path') . '/administrator/components/com_uddeim/uddeim_config.php');
		if($config_realnames) {
			$sql="SELECT name FROM #__users WHERE id=".(int) $udde_fromid;
			$_CB_database->setQuery($sql);
			$quereply=$_CB_database->loadResult();
			if($quereply) {
				$udde_sysm=$quereply;
			}
		}

		if ($config_cryptmode==1) {
            if (function_exists('uddeIMencrypt')) { // this added for uddeIM 1.4+
				/** @noinspection PhpUndefinedConstantInspection */
			    $cm = uddeIMencrypt($udde_msg,$config_cryptkey,CRYPT_MODE_BASE64);
            } else {
				/** @noinspection PhpUndefinedFunctionInspection */
				/** @noinspection PhpUndefinedConstantInspection */
				$cm = Encrypt($udde_msg,$config_cryptkey,CRYPT_MODE_BASE64);
            }
			$sql="INSERT INTO #__uddeim (fromid, toid, message, datum, systemmessage, disablereply, cryptmode, crypthash) VALUES (".$udde_fromid.", ".$udde_toid.", '".$cm."', ".$udde_time.", '".$udde_sysm."', 0, 1,'".md5($config_cryptkey)."')";
		} else {
			$sql="INSERT INTO #__uddeim (fromid, toid, message, datum, systemmessage, disablereply) VALUES (".$udde_fromid.", ".$udde_toid.", '".$udde_msg."', ".$udde_time.", '".$udde_sysm."', 0)";
		}

		
		// escaping not necessary, already escaped before this internal function gets called
		// now insert the message as system message 
		// REPLY IS NOT DISABLED AS THE SYSTEMMESSAGE USERNAME WILL CONTAIN A VALID USERNAME
		if($udde_fromid && $udde_toid) {
			$_CB_database->SetQuery($sql);
			$_CB_database->query();
		}

		$udde_msgid = $_CB_database->insertid();
		
		// E-Mail notification code
		$this->_pmsUddeNotify($udde_msgid, $udde_fromid, $udde_toid, $udde_msg, $udde_sysm);
		
	}

	protected function _sendPMSuddeimMSG( $udde_toid, $udde_fromid, /** @noinspection PhpUnusedParameterInspection */ $to, $from, $sub, $msg )
	{
		global $_CB_database, $_CB_framework; 

		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');
		/** @noinspection PhpUnusedLocalVariableInspection */
		$udde_sysm = "System";
		/** @noinspection PhpUnusedLocalVariableInspection */
        $config_realnames = "0";
        $config_cryptmode = 0;
        $config_cryptkey = 'uddeIMcryptkey';
        
		if ($pmsType==4) { // uddeIM 1.0+
			/** @noinspection PhpIncludeInspection */
			require_once( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/crypt.class.php");
			
			if(file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php");
			}
			/** @noinspection PhpUndefinedClassInspection */
			$this->uddeconfig = $config = new uddeimconfigclass();
			if(isset($config->sysm_username)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$udde_sysm = $config->sysm_username;
			}
			if (isset($config->realnames)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$config_realnames = $config->realnames;
			}
			if (isset($config->cryptmode)) {
				$config_cryptmode = $config->cryptmode;
			}
            if (file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" )) {
				/** @noinspection PhpIncludeInspection */
				require_once ( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" );
			}
			if (isset($config->cryptkey)) {
				$config_cryptkey = $config->cryptkey;
			}
		} else {
			if(file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php");
			}
			if(isset($config_sysm_username)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$udde_sysm = $config_sysm_username;
			}
		}		
		// format the message
		if($sub) { // is actually impossible
			$udde_msg = "[b]".$sub."[/b]\n\n".$msg;
		} else {
			$udde_msg = $msg;
		}
		
		// strip any bb code that might be present, but only in 0.4
		if($pmsType==3) {
			/** @noinspection PhpIncludeInspection */
			require_once ( $_CB_framework->getCfg('absolute_path') . '/components/com_uddeim/bbparser.php' );
			if ( function_exists( 'bbcode_strip' ) ) {
				$udde_msg=bbcode_strip($udde_msg);
			} elseif ( function_exists( 'uddeIMbbcode_strip' ) ) {
				$udde_msg=uddeIMbbcode_strip($udde_msg);
			}
		}
		
		// now strip the remaining html tags
		$udde_msg = strip_tags($udde_msg);
				
		// escape dangerous stuff
		// not necessary, already escaped before this internal function gets called
		
		// get current time but recognize time offset
		$currentTime=time();
		$udde_time=$this->_pmsUddeGetTime($currentTime);
		
		// set the udde systemmessage username to the virtual sender

		/** @noinspection PhpUnusedLocalVariableInspection */
		$udde_sysm=$from;

		if ($config_cryptmode==1) {
            if (function_exists('uddeIMencrypt')) { // this added for uddeIM 1.4+
				/** @noinspection PhpUndefinedConstantInspection */
                $cm = uddeIMencrypt($udde_msg,$config_cryptkey,CRYPT_MODE_BASE64);
            } else {
				/** @noinspection PhpUndefinedFunctionInspection */
				/** @noinspection PhpUndefinedConstantInspection */
				$cm = Encrypt($udde_msg,$config_cryptkey,CRYPT_MODE_BASE64);
            }
   			$sql="INSERT INTO #__uddeim (fromid, toid, message, datum, cryptmode, crypthash) VALUES (".$udde_fromid.", ".$udde_toid.", '".$cm."', ".$udde_time.",1,'".md5($config_cryptkey)."')";
   		} else {
   			$sql="INSERT INTO #__uddeim (fromid, toid, message, datum) VALUES (".$udde_fromid.", ".$udde_toid.", '".$udde_msg."', ".$udde_time.")";
		}
			
		// now insert the message  
		if($udde_fromid && $udde_toid) {
			$_CB_database->SetQuery($sql);
			$_CB_database->query();
		}

		$udde_msgid = $_CB_database->insertid();

		// E-Mail notification code
		$udde_sysm="";
		$this->_pmsUddeNotify($udde_msgid, $udde_fromid, $udde_toid, $udde_msg, $udde_sysm);
		
	}

	/**
	 * Sends a PMS message
	 *
	 *  @param  int      $toUserId         UserId of receiver
	 *  @param  int      $fromUserId       UserId of sender
	 *  @param  string   $subject          Subject of PMS message
	 *  @param  string   $message          Body of PMS message
	 *  @param  boolean  $systemGenerated  False: real user-to-user message; True: system-Generated by an action from user $fromid (if non-null)
	 *  @return string|boolean             Either string HTML for tab content, or false if ErrorMSG generated
	 */
	public function sendUserPMS( $toUserId, $fromUserId, $subject, $message, $systemGenerated = false )
	{
		global $_CB_database;

		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');

		if (!$this->_checkPMSinstalled($pmsType)) {
			return false;
		}

		$toUserId	= (int) $toUserId;
		$fromUserId	= (int) $fromUserId;

		$subject = $_CB_database->getEscaped($subject);
		$message = $_CB_database->getEscaped($message);

		if ($systemGenerated && ($fromUserId == 0)) {
			if (in_array($pmsType,array(1,2,6))) {
				$this->_setErrorMSG(CBTxt::T( 'UE_PMS_TYPE_UNSUPPORTED', 'This private message type is not supported by the selected PMS system!' ));		// PMS OS, MyPMS Pro and JIM do not handle systemGenerated from nobody)
				return false;
			}
		}

		if ($fromUserId != 0) {
			$rowFrom = new UserTable();
			$rowFrom->load( (int) $fromUserId );
			$from = $rowFrom->username;
		} else {
			$from = null;
		}
		
		$rowTo = new UserTable();
		$rowTo->load( (int) $toUserId );
		$to=$rowTo->username;
		
		SWITCH($pmsType) {
			case 1:		//MyPMS OS
				$this->_sendPMSOSMSG($to,$from,$subject,$message);
				return true;
				break;
			case 2:		//PMS Pro
				$this->_sendPMSProMSG($to,$from,$subject,$message);
				return true;
				break;
			case 3:		//UddeIM 0.4
			case 4:		//UddeIM 1.0
				if($systemGenerated || $fromUserId==0) {
					$this->_sendPMSuddesysMSG($toUserId,$fromUserId,$to,$from,$subject,$message);
				} else {
					$this->_sendPMSuddeimMSG($toUserId,$fromUserId,$to,$from,$subject,$message);
				}
				return true;
				break;
			case 5:		//PMS enhanced 2.x by Stefan Klingner
				$this->_sendPMSenhancedMSG($toUserId,$fromUserId,$subject,$message);
				return true;
				break;
			case 6:		//JIM 1.0.1
				$this->_sendPMSJimMSG($to,$from,$subject,$message);
				return true;
				break;
			default:
				$this->_setErrorMSG("Incorrect PMS type");
				return false;
				break;
		}
	}

	/**
	 * returns all the parameters needed for a hyperlink or a menu entry to do a pms action
	 *
	 * @param  int     $toUserId     UserId of receiver
	 * @param  int     $fromUserId   UserId of sender
	 * @param  string  $subject      Subject of PMS message
	 * @param  string  $message      Body of PMS message
	 * @param  int     $kind         kind of link: 1: link to compose new PMS message for $toid user. 2: link to inbox of $fromid user; 3: outbox, 4: trashbox, 5: link to edit pms options
	 * @return array|boolean         Array of string {"caption" => menu-text ,"url" => NON-cbSef relative url-link, "tooltip" => description} or false and errorMSG
	 */
	public function getPMSlink( $toUserId, $fromUserId, $subject, $message, $kind )
	{
		global $_CB_framework, $_CB_database;

		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');

		if (!$this->_checkPMSinstalled($pmsType)) {
			return false;
		}

		$pmsurlOutbox	=	null;
		$pmsurlTrashbox	=	null;
		$pmsurlOptions	=	null;

		switch ( $pmsType ) {
			case 1:		//MyPMS OS
				$rowTo = new UserTable();
				$rowTo->load( (int) $toUserId );
				$pmsurlBase="index.php?option=com_pms";
				$pmsurlSend=$pmsurlBase."&amp;page=new&amp;id=".urlencode($rowTo->username);
				$pmsurlInbox=$pmsurlBase."&amp;page=index";
				break;
			case 2:		//PMS Pro
				$rowTo = new UserTable();
				$rowTo->load( (int) $toUserId );
				$pmsurlBase="index.php?option=com_mypms";
				$pmsurlSend=$pmsurlBase."&amp;task=new&amp;to=".urlencode($rowTo->username);
				$pmsurlInbox=$pmsurlBase."&amp;task=inbox";
				$pmsurlOutbox=$pmsurlBase."&amp;task=sent";
				$pmsurlTrashbox=$pmsurlBase."&amp;task=trash";
				$pmsurlOptions=$pmsurlBase."&amp;task=editprofile";
				break;
			case 3:		//UddeIM 0.4
				$pmsurlBase="index.php?option=com_uddeim";
				$pmsurlSend=$pmsurlBase."&amp;task=new&amp;recip=".$toUserId;
				$pmsurlInbox=$pmsurlBase."&amp;task=inbox";
				$pmsurlOutbox=$pmsurlBase."&amp;task=outbox";
				$pmsurlTrashbox=$pmsurlBase."&amp;task=trashcan";
				break;		
			case 4:		//UddeIM 1.0
				$pmsurlBase="index.php?option=com_uddeim";
				$pmsurlSend=$pmsurlBase."&amp;task=new&amp;recip=".$toUserId;
				$pmsurlInbox=$pmsurlBase."&amp;task=inbox";
				$pmsurlOutbox=$pmsurlBase."&amp;task=outbox";
				$pmsurlTrashbox=$pmsurlBase."&amp;task=trashcan";
				$pmsurlOptions=$pmsurlBase."&amp;task=settings";
				break;							
			case 5:		//PMS enhanced 2.x by Stefan Klingner
				$rowTo = new UserTable();
				$rowTo->load( (int) $toUserId );
				$pmsurlBase="index.php?option=com_pms";
				$pmsurlSend=$pmsurlBase."&amp;page=new&amp;id=".urlencode($rowTo->username);
				$pmsurlInbox=$pmsurlBase."&amp;page=index";
				$pmsurlOutbox=$pmsurlBase."&amp;page=sent_items";
				$pmsurlTrashbox=$pmsurlBase."&amp;page=trash";
				$pmsurlOptions=$pmsurlBase."&amp;page=settings";
				break;
			case 6:		//JIM 1.0.1
				$rowTo = new UserTable();
				$rowTo->load( (int) $toUserId );
				$pmsurlBase="index.php?option=com_jim";
				$pmsurlSend=$pmsurlBase."&amp;page=new&amp;id=".urlencode($rowTo->username);
				$pmsurlInbox=$pmsurlBase."&amp;page=index";
				break;
			default:
				$this->_setErrorMSG("Incorrect PMS type");
				return false;
				break;
		}
		$query				=	'SELECT ' . $_CB_database->NameQuote( 'id' )
							.	"\n FROM " . $_CB_database->NameQuote( '#__menu' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'link' ) . " LIKE " . $_CB_database->Quote( $pmsurlBase . '%', false )
							.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
							.	"\n AND " . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( Application::MyUser()->getAuthorisedViewLevels() )
							.	( checkJversion() >= 2 ? "\n AND " . $_CB_database->NameQuote( 'language' ) . " IN ( " . $_CB_database->Quote( $_CB_framework->getCfg( 'lang_tag' ) ) . ", '*', '' )" : null );
		$_CB_database->setQuery( $query );
		$pms_id				=	$_CB_database->loadResult();
		if ($pms_id) {
			$pmsitemid = "&amp;Itemid=".$pms_id;
		} else {
			$pmsitemid = null;
		}

		/**
		 * For languages string picker:
		CBTxt::T( '_UE_PM_USER', 'Send Private Message' );
		CBTxt::T( '_UE_MENU_PM_USER_DESC', 'Send a Private Message to this user' );
		CBTxt::T( '_UE_PM_INBOX', 'Show Private Inbox' );
		CBTxt::T( '_UE_MENU_PM_INBOX_DESC', 'Show Received Private Messages' );
		CBTxt::T( '_UE_PM_OUTBOX', 'Show Private Outbox' );
		CBTxt::T( '_UE_MENU_PM_OUTBOX_DESC', 'Show Sent/Pending Private Messages' );
		CBTxt::T( '_UE_PM_TRASHBOX', 'Show Private Trashbox' );
		CBTxt::T( '_UE_MENU_PM_TRASHBOX_DESC', 'Show Trashed Private Messages' );
		CBTxt::T( '_UE_PM_OPTIONS', 'Edit PMS Options' );
		CBTxt::T( '_UE_MENU_PM_OPTIONS_DESC', 'Edit Private Messaging System Options' );
		 */

		switch($kind) {
			case 1:
				return array("caption"	=> CBTxt::T($params->get('pmsMenuText', '_UE_PM_USER')),
							 "url"		=> $pmsurlSend.$pmsitemid,
							 "tooltip"	=> CBTxt::T($params->get('pmsMenuDesc', '_UE_MENU_PM_USER_DESC')));
				break;
			case 2:
				return array("caption"	=> CBTxt::T($params->get('pmsMenuInboxText', '_UE_PM_INBOX')),
							 "url"		=> $pmsurlInbox.$pmsitemid,
							 "tooltip"	=> CBTxt::T($params->get('pmsMenuInboxDesc', '_UE_MENU_PM_INBOX_DESC')));
				break;
			case 3:
				if ($pmsType != 1 && $pmsType !=6) return array("caption"	=> CBTxt::T($params->get('pmsMenuOutboxText', '_UE_PM_OUTBOX')),
												"url"		=> $pmsurlOutbox.$pmsitemid,
												"tooltip"	=> CBTxt::T($params->get('pmsMenuOutboxDesc', '_UE_MENU_PM_OUTBOX_DESC')));
				break;
			case 4:
				if ($pmsType != 1 && $pmsType !=6) return array("caption"	=> CBTxt::T($params->get('pmsMenuTrashboxText', '_UE_PM_TRASHBOX')),
												"url"		=> $pmsurlTrashbox.$pmsitemid,
												"tooltip"	=> CBTxt::T($params->get('pmsMenuTrashboxDesc', '_UE_MENU_PM_TRASHBOX_DESC')));
				break;
			case 5:
				if ($pmsType == 2 || $pmsType == 5) return array("caption"	=> CBTxt::T($params->get('pmsMenuOptionsText', '_UE_PM_OPTIONS')),
												"url"		=> $pmsurlOptions.$pmsitemid,
												"tooltip"	=> CBTxt::T($params->get('pmsMenuOptionsDesc', '_UE_MENU_PM_OPTIONS_DESC')));
				break;

			default:
			break;
		}
		$this->_setErrorMSG("Function not supported by this PMS type");
		return false;
	}

	/**
	 * gets PMS system capabilities
	 *
	 * @return mixed array of string {'subject' => boolean ,'body' => boolean} or false if ErrorMSG generated
	 */
	public function getPMScapabilites( )
	{
		$params = $this->params;
		$pmsType		= $params->get('pmsType', '1');

		if (!$this->_checkPMSinstalled($pmsType)) {
			return false;
		}
		
		SWITCH($pmsType) {
			case 1:
			case 2:
			case 6:
				$capacity = array( "subject" => true, "body" => true);
				break;
			case 3:
			case 4:
				$capacity = array( "subject" => false, "body" => true);
				break;
			case 5:
				$capacity = array( "subject" => true, "body" => true);
				break;
			default:
				$this->_setErrorMSG("Incorrect PMS type");
				$capacity = false;
				break;
		}
		return $capacity;
	}

	/**
	 * gets PMS unread messages count
	 *
	 * @param  int          $userId  User id
	 * @return int|boolean           Number of messages unread by user $userid or false if ErrorMSG generated
	 */
	public function getPMSunreadCount( $userId )
	{
		global $_CB_database;

		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');

		if (!$this->_checkPMSinstalled($pmsType)) {
			return false;
		}

		$user = new UserTable();
		$user->load( (int) $userId );
		
		SWITCH($pmsType) {
			case 1:
				$query_pms_count = "SELECT count(id) FROM #__pms WHERE username='" . $_CB_database->getEscaped($user->username) ."' AND readstate=0";
				$_CB_database->setQuery( $query_pms_count );
				$total_pms = $_CB_database->loadResult();
				break;
			case 2:
				$query_pms_count = "SELECT count(id) FROM #__mypms WHERE username='" . $_CB_database->getEscaped($user->username) ."' AND readstate=0";
				$_CB_database->setQuery( $query_pms_count );
				$total_pms = $_CB_database->loadResult();
				break;
			case 3:
			case 4:
				$sql="SELECT count(id) FROM #__uddeim WHERE toread<1 AND toid=".(int) $userId;
				$_CB_database->setQuery($sql);
				$total_pms = $_CB_database->loadResult();	
				break;			
			case 5:
				$query_pms_count = "SELECT count(id) FROM #__pms WHERE recip_id=" . (int) $userId ." AND readstate%2=0 AND inbox=1";
				$_CB_database->setQuery( $query_pms_count );
				$total_pms = $_CB_database->loadResult();
				break;
			case 6:
				$query_pms_count = "SELECT count(id) FROM #__jim WHERE username='" . $_CB_database->getEscaped($user->username) ."' AND readstate=0";
				$_CB_database->setQuery( $query_pms_count );
				$total_pms = $_CB_database->loadResult();
				break;
			default:
				$this->_setErrorMSG("Incorrect PMS type");
				$total_pms = false;
				break;
		}
		return $total_pms;
	}

	/**
	 * Generates the HTML to display the user profile tab
	 *
	 * @param  \CB\Database\Table\TabTable   $tab       the tab database entry
	 * @param  \CB\Database\Table\UserTable  $user      the user being displayed
	 * @param  int                           $ui        1 for front-end, 2 for back-end
	 * @return string|boolean                           Either string HTML for tab content, or false if ErrorMSG generated
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_POST;

		if ( ! $_CB_framework->myId() ) {
			return null;
		}

		$params = $this->params;
		$pmsType		= $params->get('pmsType', '1');
		$showTitle		= $params->get('showTitle', "1");
		$showSubject	= $params->get('showSubject', "1");
		$width			= $params->get('width', "30");
		$height			= $params->get('height', "5");

		$capabilities = $this->getPMScapabilites();

		if (!$this->_checkPMSinstalled($pmsType) || ($capabilities === false)) {
			return false;
		}
		if ($_CB_framework->myId() == $user->id) {
			return null;
		}

		$newsub							=	null;
		$newmsg							=	null;

		// send PMS from this tab form input:
		if ( cbGetParam( $_POST, $this->_getPagingParamName( 'newmsg' ) ) ) {
			$sender						=	$this->_getReqParam( 'sender', null );
			$recip						=	$this->_getReqParam( 'recip', null );

			if ( $sender && $recip && ( $sender == $_CB_framework->myId() ) && ( $recip == $user->id ) && ( CBuser::getMyInstance()->authoriseView( 'profile', $user->id ) ) ) {
				cbSpoofCheck( 'pms' );

				$newsub					=	htmlspecialchars( $this->_getReqParam( 'newsub', null ) );	//urldecode done in _getReqParam

				if ( ( $pmsType == 3 ) || ( $pmsType == 4 ) ) {
					$newmsg				=	$this->_getReqParam( 'newmsg', null );
				} else {
					$newmsg				=	htmlspecialchars( $this->_getReqParam( 'newmsg', null ) );	//don't allow html input on user profile!
				}

				if ( ( $newsub || $newmsg ) && isset( $_POST[$this->_getPagingParamName( 'protect' )] ) ) {
					$parts				=	explode( '_', $this->_getReqParam( 'protect', '' ) );

					if ( ( count( $parts ) == 3 ) && ( $parts[0] == 'cbpms1' ) && ( strlen( $parts[2] ) == 32 ) && ( $parts[1] == md5($parts[2].$user->id.$user->lastvisitDate) ) ) {
						if ( ( ! $newsub ) && $capabilities['subject'] ) {
							$newsub		=	CBTxt::T( 'UE_PM_PROFILEMSG', 'Message from your profile view' );
						}

						if ( $this->sendUserPMS( $recip, $sender, $newsub, $newmsg, false, true ) ) {
							$_CB_framework->enqueueMessage( CBTxt::Th( 'UE_PM_SENTSUCCESS', 'Your private message was sent successfully!' ) );

							$newsub		=	null;
							$newmsg		=	null;
						} else {
							$_CB_framework->enqueueMessage( $this->getErrorMSG(), 'error' );
						}
					} else {
						$_CB_framework->enqueueMessage( CBTxt::Th( 'UE_SESSIONTIMEOUT', 'Session timed out.' ) . ' ' . CBTxt::Th( 'UE_PM_NOTSENT', 'Your private message could not be sent!' ) . ' ' . CBTxt::Th( 'UE_TRYAGAIN', 'Please try again.' ), 'error' );
					}
				} else {
					$_CB_framework->enqueueMessage( CBTxt::Th( 'UE_PM_EMPTYMESSAGE', 'Empty message.' ) . ' ' . CBTxt::Th( 'UE_PM_NOTSENT', 'Your private message could not be sent!' ), 'error' );
				}
			}
		}

		$base_url		=	$this->_getAbsURLwithParam( array() );
		$description	=	$this->_writeTabDescription( $tab, $user );
		$salt			=	cbMakeRandomString( 32 );

		cbValidator::loadValidation();

		$return			=	'<form method="post" action="' . $base_url . '" class="cb_form cbValidation">'
						.		'<div class="panel panel-default">'
						.			( $showTitle ? '<div class="panel-heading">' . cbUnHtmlspecialchars( CBTxt::T( $tab->title ) ) . '</div>' : null )
						.			'<div class="panel-body">';

		if ( $description ) {
			$return		.=				'<div class="form-group cb_form_line clearfix">'
						.					$description
						.				'</div>';
		}

		if ( $showSubject && $capabilities['subject'] ) {
			$return		.=				'<div class="form-group cb_form_line clearfix">'
						.					'<label for="' . $this->_getPagingParamName( 'newsub' ) . '" class="control-label">' . CBTxt::Th( 'UE_EMAILFORMSUBJECT', 'Subject' ) . '</label>'
						.					'<div class="cb_field">'
						.						'<input type="email" class="form-control" name="' . $this->_getPagingParamName( 'newsub' ) . '" value="' . stripslashes( $newsub ) . '" size="' . ( $width - 8 ) . '" />'
						.					'</div>'
						.				'</div>';
		}

		$return			.=				'<div class="form-group cb_form_line clearfix">'
						.					( $showSubject && $capabilities['subject'] ? '<label for="' . $this->_getPagingParamName( 'newmsg' ) . '" class="control-label">' . CBTxt::Th( 'UE_EMAILFORMMESSAGE', 'Message' ) . '</label>' : null )
						.					'<div class="cb_field">'
						.						'<textarea class="form-control required" name="' . $this->_getPagingParamName( 'newmsg' ) . '" rows="' . $height . '" cols="' . $width . '">'
						.							stripslashes( $newmsg )
						.						'</textarea>'
						.					'</div>'
						.				'</div>'
						.			'</div>'
						.			'<div class="panel-footer">'
						.				'<input type="submit" class="btn btn-primary" name="' . $this->_getPagingParamName( 'sndnewmsg' ) . '" value="' . CBTxt::Th( 'UE_PM_SENDMESSAGE', 'Send Message' ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
						.			'</div>'
						.		'</div>'
						.		'<input type="hidden" name="' . $this->_getPagingParamName( 'sender' ) . '" value="' . $_CB_framework->myId() . '" />'
						.		'<input type="hidden" name="' . $this->_getPagingParamName( 'recip' ) . '" value="' . $user->id . '" />'
						.		'<input type="hidden" name="' . $this->_getPagingParamName( 'protect' ) . '" value="cbpms1_' . md5( $salt . $user->id . $user->lastvisitDate ) . '_' . $salt . '" />'
						.		cbGetSpoofInputTag( 'pms' )
						.	'</form>';

		return $return;
	}
	
	//****************************************************************************
	// UddeIM specific private methods:
	
	/**
	 * Udde PMS notification by email depending on user's settings
	 *
	 * @access private
	 * @param int $savemsgid
	 * @param int $savefromid
	 * @param int $savetoid
	 * @param string $savemessage
	 * @param boolean $udde_sysm
	 */
	protected function _pmsUddeNotify( $savemsgid, $savefromid, $savetoid, $savemessage, $udde_sysm )
	{
		global $_CB_database, $_CB_framework;

		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');
		/** @noinspection PhpUnusedLocalVariableInspection */
        $config_realnames = "0";
		/** @noinspection PhpUnusedLocalVariableInspection */
        $config_cryptmode = 0;
		/** @noinspection PhpUnusedLocalVariableInspection */
        $config_cryptkey = 'uddeIMcryptkey';
        
		if ($pmsType==4 && file_exists( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/crypt.class.php" ) ) { // uddeIM 1.0+
			/** @noinspection PhpIncludeInspection */
			require_once( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/crypt.class.php");
			
			if(file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php");
			}
			/** @noinspection PhpUndefinedClassInspection */
			/** @var StdClass $config */
			$this->uddeconfig = $config = new uddeimconfigclass();

			if ($config->notifydefault>0 || $config->popupdefault>0) {
				$sql="SELECT count(id) FROM #__uddeim_emn WHERE userid=".(int)$savetoid;
				$_CB_database->setQuery($sql);
				$entryexists=$_CB_database->loadResult();
				if (!$entryexists) {
					$sql="INSERT INTO #__uddeim_emn (status, popup, userid) VALUES (".(int)$config->notifydefault.", ".(int)$config->popupdefault.", ".(int)$savetoid.")";
					$_CB_database->setQuery($sql);
					$_CB_database->query();
				}
			}
			if (isset($config->realnames)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$config_realnames = $config->realnames;
			}
			if (isset($config->cryptmode)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$config_cryptmode = $config->cryptmode;
			}
            if (file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" )) {
				/** @noinspection PhpIncludeInspection */
				require_once ( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" );
			}
			if (isset($config->cryptkey)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$config_cryptkey = $config->cryptkey;
			}
			if (isset($config->emailtrafficenabled)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$config_emailtrafficenabled = $config->emailtrafficenabled;
			}
			if (isset($config->quotedivider)) {
				$config_quotedivider = $config->quotedivider;	
			}
			if (isset($config->allowemailnotify)) {
				$config_allowemailnotify = $config->allowemailnotify;	
			}
		} else {
			if (file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php");
			} else {
				return;
			}
		}		

// --		
		
		// is this a reply?
		$itisareply= (isset($config_quotedivider) ? stristr($savemessage, $config_quotedivider) : false);
		
		// is the receiver currently online?
		$sql="SELECT userid FROM #__session WHERE userid=".(int) $savetoid;
		$_CB_database->setQuery($sql);
		$currentlyonline=$_CB_database->loadResult();

		if (isset( $config_allowemailnotify )) {
			if ($config_allowemailnotify==1) {
				$sql="SELECT status FROM #__uddeim_emn WHERE userid=".(int) $savetoid;
				$_CB_database->setQuery($sql);
				$ison=$_CB_database->loadResult();
				if (($ison==1) || ($ison==2 && !$currentlyonline) || ($ison==10 && !$itisareply) || ($ison==20 && !$currentlyonline && !$itisareply))  {
					$this->_pmsUddeDispatchEMN($savemsgid, $savefromid, $savetoid, $savemessage, 0, $udde_sysm);
									// 0 stands for normal (not forgetmenot)
				} 
			} elseif ($config_allowemailnotify==2) {
				$sql="SELECT gid FROM #__users WHERE id=".(int) $savetoid;
				$_CB_database->setQuery($sql);
				$my_gid=$_CB_database->loadResult();
				// if ($my_gid>23) { // JACL support
				if ( in_array( $my_gid, $_CB_framework->acl->mapGroupNamesToValues( array( 'Administrator', 'Superadministrator' ) ) ) ) {
					$sql="SELECT status FROM #__uddeim_emn WHERE userid=".(int) $savetoid;
					$_CB_database->setQuery($sql);
					$ison=$_CB_database->loadResult();
					if (($ison==1) || ($ison==2 && !$currentlyonline) || ($ison==10 && !$itisareply) || ($ison==20 && !$currentlyonline && !$itisareply))  {
						$this->_pmsUddeDispatchEMN($savemsgid, $savefromid, $savetoid, $savemessage, 0, $udde_sysm);
									// 0 stands for normal (not forgetmenot)
					} 	
				}	
			}
		}
	}
	
	/**
	 * Udde PMS notification by email
	 *
	 * @access private
	 * @param int $var_msgid
	 * @param int $var_fromid
	 * @param int $var_toid
	 * @param string $var_message
	 * @param int $emn_option
	 * @param boolean $udde_sysm
	 */
	protected function _pmsUddeDispatchEMN( $var_msgid, $var_fromid, $var_toid, $var_message, $emn_option, /** @noinspection PhpUnusedParameterInspection */ $udde_sysm )
	{
		global $_CB_database, $_CB_framework;

// --
		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');
        $udde_sysm = "System";
        $config_realnames = "0";
		/** @noinspection PhpUnusedLocalVariableInspection */
        $config_cryptmode = 0;
		/** @noinspection PhpUnusedLocalVariableInspection */
        $config_cryptkey = 'uddeIMcryptkey';

		$config_emn_sendername	=	null;
		$config_emn_sendermail	=	null;

		if ($pmsType==4) { // uddeIM 1.0+
			/** @noinspection PhpIncludeInspection */
			require_once( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/crypt.class.php");
			
			if(file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php");
			}
			/** @noinspection PhpUndefinedClassInspection */
			$this->uddeconfig = $config = new uddeimconfigclass();
			if (isset($config->realnames)) {
				$config_realnames = $config->realnames;
			}
			if (isset($config->cryptmode)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$config_cryptmode = $config->cryptmode;
			}
            if (file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" )) {
				/** @noinspection PhpIncludeInspection */
				require_once ( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_crypt.php" );
			}
			if (isset($config->cryptkey)) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$config_cryptkey = $config->cryptkey;
			}
			if (isset($config->emailtrafficenabled)) {
				$config_emailtrafficenabled = $config->emailtrafficenabled;		
			}
			if (isset($config->emailwithmessage)) {
				$config_emailwithmessage = $config->emailwithmessage;
			} 
			if (isset($config->emn_sendername)) {
				$config_emn_sendername = $config->emn_sendername;
			}
			if (isset($config->emn_sendermail)) {
				$config_emn_sendermail = $config->emn_sendermail;
			}			
		} else {
			if (file_exists( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php")) {
				/** @noinspection PhpIncludeInspection */
				include_once( $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim/uddeim_config.php");
			} else {
				return;
			}
		}		

// --		

		// load the uddeim lang file		
		$adminpath = $_CB_framework->getCfg('absolute_path') . "/administrator/components/com_uddeim";
		if (file_exists($adminpath.'/language/'.$_CB_framework->getCfg( 'lang' ).'.php')) {
			/** @noinspection PhpIncludeInspection */
			include_once($adminpath.'/language/'.$_CB_framework->getCfg( 'lang' ).'.php');
		} elseif (file_exists($adminpath.'/language/english.php')) {
			/** @noinspection PhpIncludeInspection */
			include_once($adminpath.'/language/english.php');
		}
		
		// if e-mail traffic stopped, don't send.
		if (isset($config_emailtrafficenabled) && !($config_emailtrafficenabled > 0)) {
			return;
		}
		
		if (isset($config_realnames) && $config_realnames) {
			$sql = "SELECT name FROM #__users WHERE `id`=".(int) $var_fromid;
		} else {
			$sql = "SELECT username FROM #__users WHERE `id`=".(int) $var_fromid;	
		}
		$_CB_database->setQuery($sql);
		$var_fromname=$_CB_database->loadResult();
		if (!$var_fromname) {
			$var_fromname = $udde_sysm;
		}

		$var_tomail		=	null;
		$var_toname		=	null;

		if (isset($config_realnames) && $config_realnames) {
			$sql = "SELECT name AS displayname, email FROM #__users WHERE `id`=".(int) $var_toid;	
		} else {
			$sql = "SELECT username AS displayname, email FROM #__users WHERE `id`=".(int) $var_toid;
		}
		$_CB_database->setQuery($sql);
		$results=$_CB_database->loadObjectList();
		foreach ($results as $result) {
			$var_toname = $result->displayname;
			$var_tomail = $result->email;
		}
		
		if (!$var_tomail) {
			return;
		}

		$msglink		=	cbSef( "index.php?option=com_uddeim&task=show&messageid=" . (int) $var_msgid, false );

		if ($emn_option==1) {
			/** @noinspection PhpUndefinedConstantInspection */
			$var_body = _UDDEIM_EMN_FORGETMENOT;
			$var_body = str_replace("%livesite%", $_CB_framework->getCfg( 'live_site' ), $var_body);
			$var_body = str_replace("%you%", $var_toname, $var_body);
			$var_body = str_replace("%site%", $_CB_framework->getCfg( 'sitename' ), $var_body);
			$var_body = str_replace("%msglink%", $msglink, $var_body);
		} else {
			if (isset($config_emailwithmessage) && $config_emailwithmessage) {
				/** @noinspection PhpUndefinedConstantInspection */
				$var_body = _UDDEIM_EMN_BODY_WITHMESSAGE;
				$var_body = str_replace("%livesite%", $_CB_framework->getCfg( 'live_site' ), $var_body);
				$var_body = str_replace("%you%", $var_toname, $var_body);
				$var_body = str_replace("%site%", $_CB_framework->getCfg( 'sitename' ), $var_body);
				$var_body = str_replace("%msglink%", $msglink, $var_body);
				$var_body = str_replace("%user%", $var_fromname, $var_body);
				$var_body = str_replace("%pmessage%", $var_message, $var_body);
			} else {
				/** @noinspection PhpUndefinedConstantInspection */
				$var_body = _UDDEIM_EMN_BODY_NOMESSAGE;
				$var_body = str_replace("%livesite%", $_CB_framework->getCfg( 'live_site' ), $var_body);
				$var_body = str_replace("%you%", $var_toname, $var_body);
				$var_body = str_replace("%site%", $_CB_framework->getCfg( 'sitename' ), $var_body);
				$var_body = str_replace("%msglink%", $msglink, $var_body);
				$var_body = str_replace("%user%", $var_fromname, $var_body);
			}
		}

		/** @noinspection PhpUndefinedConstantInspection */
		$subject = _UDDEIM_EMN_SUBJECT;
		$subject = str_replace("%livesite%", $_CB_framework->getCfg( 'live_site' ), $subject);
		$subject = str_replace("%site%", $_CB_framework->getCfg( 'sitename' ), $subject);
		$subject = str_replace("%you%", $var_toname, $subject);
		$subject = str_replace("%user%", $var_fromname, $subject);

		if (comprofilerMail($config_emn_sendermail, $config_emn_sendername, $var_tomail,$subject,$this->_pmsMailcompatible($var_body))) {
			// set the remindersent status of this user to true
			$sql="SELECT count(id) FROM #__uddeim_emn WHERE userid=".(int) $var_toid;
			$_CB_database->setQuery($sql);
			$exists=$_CB_database->loadResult();
			if($exists) {
				$sql="UPDATE #__uddeim_emn SET remindersent=".(int) $this->_pmsUddeGetTime(time())." WHERE userid=".(int) $var_toid;
				$_CB_database->setQuery($sql);
				$_CB_database->query();
			} else {
				$sql="INSERT INTO #__uddeim_emn (userid, status, remindersent) VALUES (".(int) $var_toid.", 0, ".(int) $this->_pmsUddeGetTime(time()).")";
				$_CB_database->setQuery($sql);
				$_CB_database->query();
			}
		}
	}

	protected function _pmsMailcompatible( $string )
	{
	
		$string=str_replace('\\n', '#!CRLF!#', $string);

		$string=stripslashes($string);
	
		// bold
	    $string = preg_replace("/(\[b\])(.*?)(\[\/b\])/si","\\2",$string);
	
		// underline
	    $string = preg_replace("/(\[u\])(.*?)(\[\/u\])/si","\\2",$string);
	
		// italic
		$string = preg_replace("/(\[i\])(.*?)(\[\/i\])/si","\\2",$string);
	
		// size Max size is 7
		$string = preg_replace("/\[size=([1-7])\](.+?)\[\/size\]/si","\\2",$string);
	
		// color
		$string = preg_replace("%\[color=(.*?)\](.*?)\[/color\]%si","\\2",$string);
		
		// ul li replacements
		
		// lists
		$string = preg_replace("/(\[ul\])(.*?)(\[\/ul\])/si","\\2",$string);
		$string = preg_replace("/(\[ol\])(.*?)(\[\/ol\])/si","\\2",$string);
		$string = preg_replace("/(\[li\])(.*?)(\[\/li\])/si","\\2\\n",$string);
		
		// url replacement
		$string = preg_replace('/\[url\](.*?)javascript(.*?)\[\/url\]/si','',$string);
		$string = preg_replace('/\[url=(.*?)javascript(.*?)\](.*?)\[\/url\]/si','',$string);
		$string = preg_replace("/\[url\](.*?)\[\/url\]/si","\\1",$string);
		$string = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si","\\2 (\\1)",$string);	
		
		// only front tag present
		$string = preg_replace("/\[url=(.*?)\]/si","",$string);	
		
		// img replacement
		// img
		$string = preg_replace("/\[img size=([0-9][0-9][0-9])\](.*?)\[\/img\]/si","",$string);
		$string = preg_replace("/\[img size=([0-9][0-9])\](.*?)\[\/img\]/si","",$string);
		$string = preg_replace("/\[img\](.*?)\[\/img\]/si","",$string);
		$string = preg_replace("/<img(.*?)javascript(.*?)>/si",'',$string);	
	
		// only front tag present
		$string = preg_replace("/\[img size=([0-9][0-9][0-9])\]]/si","",$string);
		$string = preg_replace("/\[img size=([0-9][0-9])\]]/si","",$string);
		
		// cut remaining single tags
		$string=str_replace(array("[i]","[/i]","[b]","[/b]","[u]","[/u]","[ul]","[/ul]","[ol]","[/ol]","[li]","[/li]"), "", $string);
	
	    $string = preg_replace('/\[url=(.*?)javascript(.*?)\]/si','',$string);	
	    $string = preg_replace("/\[img size=([0-9][0-9][0-9])\]/si","",$string);
	    $string = preg_replace("/\[img size=([0-9][0-9])\]/si","",$string);
	    $string = preg_replace("/\[size=([1-7])\]/si","",$string);
	    $string = preg_replace("%\[color=(.*?)\]%si","",$string);
		$string=str_replace(array("[img]","[/img]","[url]","[/url]","[/color]","[/size]"), "", $string);
			
		$string=str_replace("#!CRLF!#", "\n", $string);	 
		return $string;
	
	}

	protected function _pmsUddeGetTime( $currentTime, $jVersion = 'auto' )
	{
		global $_CB_framework;

		static $libLoaded	=	0;

		if ( ! $libLoaded++ ) {
			$absolutePath	=	$_CB_framework->getCfg( 'absolute_path' );

			if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib.php' ) ) {
				require_once( $absolutePath . '/components/com_uddeim/uddeimlib.php' );
			
			} elseif ( ( checkJversion( '3.3+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.3' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib33.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib33.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '3.2' );
				}
			} elseif ( ( checkJversion( '3.2+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.2' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib32.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib32.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '3.1' );
				}
			} elseif ( ( checkJversion( '3.1+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.1' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib31.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib31.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '3.0' );
				}
			} elseif ( ( checkJversion( '3.0+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.0' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib30.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib30.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '2.5' );
				}
			} elseif ( ( checkJversion( '2.5+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '2.5' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib25.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib25.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '1.7' );
				}
			} elseif ( ( checkJversion( '1.7+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '1.7' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib17.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib17.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '1.6' );
				}
			} elseif ( ( checkJversion( '1.6+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '1.6' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib16.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib16.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '1.5' );
				}
			} elseif ( ( checkJversion( '1.5+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '1.5' ) ) {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib15.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once( $absolutePath . '/components/com_uddeim/uddeimlib15.php' );
				} else {
					$this->_pmsUddeGetTime( $currentTime, '1.0' );
				}
			} else {
				if ( file_exists( $absolutePath . '/components/com_uddeim/uddeimlib10.php' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once ($absolutePath.  '/components/com_uddeim/uddeimlib10.php' );
				}
			}
		}
		/** @noinspection PhpUndefinedCallbackInspection */
		if ( is_callable( 'uddetime' ) && isset( $this->uddeconfig ) ) {
			/** @noinspection PhpUndefinedFunctionInspection */
			return uddetime( $this->uddeconfig->timezone );
		} else {
			return $currentTime + ( intval( $_CB_framework->getCfg( 'offset' ) ) * 3600 );
		}
	}

	//  delete user code function
	public function userDeleted( $user, /** @noinspection PhpUnusedParameterInspection */ $success )
	{
		global $_CB_database,$_CB_framework;

		$params = $this->params;
		$pmsType = $params->get('pmsType', '1');

		if (!$this->_checkPMSinstalled($pmsType)) {
			return false;
		}
		$pmsUserDeleteOption = $params->get('pmsUserDeleteOption', '3');
		$pmsUserFunction = $params->get('pmsUserFunction','1');

		$query_pms_delete_extra1	=	null;
		$query_pms_delete_extra2	=	null;

		$cb_extra_rules = 0;

		switch ( $pmsType ) {
			case 1:		//MyPMS OS
				switch ($pmsUserDeleteOption) {
					case '1':	// Keep all messages
						$query_pms_delete = "";
						break;
					case '2':	// Remove all messages (received and sent)
					case '3':	// Remove received messages only
					case '4':	// Remove sent message only
						$query_pms_delete = "DELETE FROM #__pms WHERE username='" . $_CB_database->getEscaped($user->username) ."'";
						break;
					default:
						$query_pms_delete = "DELETE FROM #__pms WHERE username='" . $_CB_database->getEscaped($user->username) ."'";
						break;	
				}
				if(file_exists( $_CB_framework->getCfg('absolute_path') . "/components/com_pms/cb_extra.php")) {
					/** @noinspection PhpIncludeInspection */
					include_once( $_CB_framework->getCfg('absolute_path') . "/components/com_pms/cb_extra.php");
					if (function_exists('user_delete')) {
						$cb_extra_rules = 1;
					}
					if (function_exists('user_delete_ext')) {
						$cb_extra_rules = 2;
					}
				}		
				break;
			case 2:		//PMS Pro
				switch ($pmsUserDeleteOption) {
					case '1':	// Keep all messages
						$query_pms_delete = "";
						break;
					case '2':	// Remove all messages (received and sent)
					case '3':	// Remove received messages only
					case '4':	// Remove sent message only
						$query_pms_delete = "DELETE FROM #__mypms WHERE username='" . $_CB_database->getEscaped($user->username) ."'";
						break;
					default:
						$query_pms_delete = "DELETE FROM #__mypms WHERE username='" . $_CB_database->getEscaped($user->username) ."'";
						break;	
				}
				if(file_exists( $_CB_framework->getCfg('absolute_path') . "/components/com_mypms/cb_extra.php")) {
					/** @noinspection PhpIncludeInspection */
					include_once( $_CB_framework->getCfg('absolute_path') . "/components/com_mypms/cb_extra.php");
					if (function_exists('user_delete')) {
						$cb_extra_rules = 1;
					}
					if (function_exists('user_delete_ext')) {
						$cb_extra_rules = 2;
					}
				}		
				break;
			case 3:		//UddeIM 0.4
			case 4:		//UddeIM 1.0
				switch ($pmsUserDeleteOption) {
					case '1':	// Keep all messages
						$query_pms_delete = "";
						break;
					case '2':	// Remove all messages (received and sent)
						$query_pms_delete = "DELETE FROM #__uddeim WHERE fromid='" . (int) $user->id ."' OR toid='" . (int) $user->id . "'";
						break;
					case '3':	// Remove received messages only
						$query_pms_delete = "DELETE FROM #__uddeim WHERE toid='" . (int) $user->id . "'";
						break;
					case '4':	// Remove sent message only
						$query_pms_delete = "DELETE FROM #__uddeim WHERE fromid='" . (int) $user->id ."'";
						break;
					default:
						$query_pms_delete = "DELETE FROM #__uddeim WHERE fromid='" . (int) $user->id ."' OR toid='" . (int) $user->id . "'";
						break;	
				}
				$query_pms_delete_extra1 = "DELETE FROM #__uddeim_emn WHERE userid='" . (int) $user->id . "'";
				$query_pms_delete_extra2 = "DELETE FROM #__uddeim_blocks WHERE blocker='" . (int) $user->id . "' OR blocked='" . (int) $user->id . "'";
				if(file_exists( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/cb_extra.php")) {
					/** @noinspection PhpIncludeInspection */
					include_once( $_CB_framework->getCfg('absolute_path') . "/components/com_uddeim/cb_extra.php");
					if (function_exists('user_delete')) {
						$cb_extra_rules = 1;
					}
					if (function_exists('user_delete_ext')) {
						$cb_extra_rules = 2;
					}
				}		
				break;		
			case 5:		//PMS enhanced 2.x by Stefan Klingner
				switch ($pmsUserDeleteOption) {
					case '1':	// Keep all messages
						$query_pms_delete = "";
						break;
					case '2':	// Remove all messages (received and sent)
						$query_pms_delete = "DELETE FROM #__pms WHERE recip_id='" . (int) $user->id . "' OR sender_id='" . (int) $user->id . "'";
						break;
					case '3':	// Remove received messages only
						$query_pms_delete = "DELETE FROM #__pms WHERE recip_id='" . (int) $user->id . "'";
						break;
					case '4':	// Remove sent message only
						$query_pms_delete = "DELETE FROM #__pms WHERE sender_id='" . (int) $user->id . "'";
						break;
					default:
						$query_pms_delete = "DELETE FROM #__pms WHERE recip_id='" . (int) $user->id . "' OR sender_id='" . (int) $user->id . "'";
						break;	
				}
				if(file_exists( $_CB_framework->getCfg('absolute_path') . "/components/com_pms/cb_extra.php")) {
					/** @noinspection PhpIncludeInspection */
					include_once( $_CB_framework->getCfg('absolute_path') . "/components/com_pms/cb_extra.php");
					if (function_exists('user_delete')) {
						$cb_extra_rules = 1;
					}
					if (function_exists('user_delete_ext')) {
						$cb_extra_rules = 2;
					}
				}		
				break;
			case 6:		//JIM 1.0.1
				$query_pms_delete = "DELETE FROM #__jim WHERE username='" . $_CB_database->getEscaped($user->username) ."'";
				if(file_exists( $_CB_framework->getCfg('absolute_path') . "/components/com_jim/cb_extra.php")) {
					/** @noinspection PhpIncludeInspection */
					include_once( $_CB_framework->getCfg('absolute_path') . "/components/com_jim/cb_extra.php");
					if (function_exists('user_delete')) {
						$cb_extra_rules = 1;
					}
					if (function_exists('user_delete_ext')) {
						$cb_extra_rules = 2;
					}
				}		
				break;
			default:
				$this->_setErrorMSG("Incorrect PMS type");
				return false;
				break;
		}
		
		if (!$cb_extra_rules || $pmsUserFunction=='1') {
			// print "Deleting pms data for user ".$user->id;
			if ($pmsUserDeleteOption != 1) {
				$_CB_database->setQuery( $query_pms_delete );
				$_CB_database->query();
			}
			if ($pmsType == 4 || $pmsType == 3) {
				$_CB_database->setQuery( $query_pms_delete_extra1 );
				$_CB_database->query();

				$_CB_database->setQuery( $query_pms_delete_extra2 );
				$_CB_database->query();
			}

			$cb_extra_return = true;
		} else {
			switch ($cb_extra_rules) {
				case 1:
					/** @noinspection PhpUndefinedFunctionInspection */
					$cb_extra_return = user_delete($user->id);
					break;
				case 2:
					/** @noinspection PhpUndefinedFunctionInspection */
			    	$cb_extra_return = user_delete_ext($user->id,$pmsUserDeleteOption);
			    	break;
				default:
					return false;
			}	
		}
		return $cb_extra_return;
	}	
}
