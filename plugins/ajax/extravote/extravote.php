<?php
/*------------------------------------------------------------------------
# plg_ajax_extravote - ExtraVote Ajax Plugin
# ------------------------------------------------------------------------
# author    Jesús Vargas Garita
# Copyright (C) 2010 www.joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class plgAjaxExtravote extends JPlugin
{

	function onAjaxExtravote()
	{
		$input = JFactory::getApplication()->input;

		$user = JFactory::getUser();
		$plugin	= JPluginHelper::getPlugin('content', 'extravote');

		$params = new JRegistry;
		$params->loadString($plugin->params);

		if ( $params->get('access') == 2 && !$user->get('id') )
		{
			return 'login';
		} else
		{
			$user_rating = $input->getVar('user_rating');
			$user_rating = JRequest::getVar('user_rating');
			$xid         = JRequest::getInt('xid');
			$table       = (($params->get('table',1)!=1 && !(int)$xid)?'#__content_rating':'#__content_extravote');
			$cid = 0;
			if ( $params->get('article_id') || $xid == 0 ) {
				$cid = JRequest::getInt('cid');
			}

			$db  = JFactory::getDbo();
			$query	= $db->getQuery(true);

			if ($user_rating >= 0.5 && $user_rating <= 5)
			{
				$currip = $_SERVER['REMOTE_ADDR'];

				$query->select('*')
				->from($db->qn($table))
				->where('content_id = '.$db->quote($cid).($table == '#__content_extravote' ? ' AND extra_id = '.$db->quote($xid) : ''));

				$db->setQuery($query);

				try
				{
					$votesdb = $db->loadObject();
				}
				catch (RuntimeException $e)
				{
					return  'error';
				}

				$query->clear;

				if ( !$votesdb )
				{
					$columns = array('content_id', 'rating_sum', 'rating_count', 'lastip');
					$values = array($cid, $user_rating, 1, $db->quote($currip));
					if($table=='#__content_extravote') :
						$columns[] = 'extra_id';
						$values[] = $xid;
					endif;
					$query
					->insert($db->quoteName($table))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));

					$db->setQuery($query);

					try
					{
						$result = $db->execute();
					}

					catch (RuntimeException $e)
					{
						return 'error';

					}
				} else
				{
					if ($currip != ($votesdb->lastip))
					{
						$query
						->update($db->quoteName($table))
						->set( 'rating_sum = rating_sum + ' . $user_rating)
						->set( 'rating_count = rating_count +'. 1)
						->set('lastip = '. $db->quote( $currip))
						->where('content_id = '.$cid.($table == '#__content_extravote' ? ' AND extra_id = '.$xid : ''));

						$db->setQuery($query);

						try
						{
							$result = $db->execute();
						}

						catch (RuntimeException $e)
						{
							return 'error';

						}
					} else {
						return 'voted';
					}
				}
				return 'thanks';
			}
		}
	}
}
