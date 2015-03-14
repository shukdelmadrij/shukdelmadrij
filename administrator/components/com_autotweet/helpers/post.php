<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Helper for posts form AutoTweet to channels (twitter, Facebook, ...)
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class PostHelper
{
	/**
	 * savePost
	 *
	 * @param   string  $state       Param
	 * @param   string  $result_msg  Param
	 * @param   string  &$post       Param
	 * @param   object  $userid      Param
	 * @param   object  $url         Param
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public static function savePost($state, $result_msg, &$post, $userid, $url = null)
	{
		$row = F0FModel::getTmpInstance('Posts', 'AutoTweetModel')->getTable();
		$row->reset();

		if ($post->id)
		{
			$row->load($post->id);
		}

		// Avoid databse warnings when desc is longer then expected
		if (!empty($result_msg))
		{
			$result_msg = JString::substr($result_msg, 0, 254);
		}

		// Params
		if (!isset($post->xtform))
		{
			$post->xtform = new JRegistry;
		}

		$params = (string) $post->xtform;

		if ($url)
		{
			$post->url = $url;
		}

		$post->pubstate = $state;
		$post->resultmsg = $result_msg;
		$post->params = $params;
		$post->created_by = $userid;
		$post->modified_by = $userid;
		$post->modified = JFactory::getDate()->toSql();

		// It's already in the post queue
		unset($post->autopublish);
		unset($post->published);

		$stored = $row->save($post);

		$logger = AutotweetLogger::getInstance();

		if (!$stored)
		{
			$logger->log(JLog::ERROR, 'logMessage: error storing message to database message log, ref_id = ' . $post->ref_id . ', error message = ' . $row->getError());
		}
		else
		{
			$logger->log(JLog::INFO, 'logMessage: message stored to database message log, ref_id = ' . $post->ref_id);
		}

		return $stored;
	}

	/**
	 * publishPosts - This static function should used from backend only for manual (re)posting attempts
	 *
	 * @param   array  $posts   Param
	 * @param   int    $userid  Param
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public static function publishPosts($posts, $userid = null)
	{
		JLoader::register('SharingHelper', JPATH_AUTOTWEET_HELPERS . '/sharing.php');

		$cron_enabled = EParameter::getComponentParam(CAUTOTWEETNG, 'cron_enabled', 0);
		$success = false;

		$sharinghelper = SharingHelper::getInstance();
		$post = F0FModel::getTmpInstance('Posts', 'AutoTweetModel')->getTable();

		foreach ($posts as $pid)
		{
			$post->reset();
			$post->load($pid);
			$post->xtform = EForm::paramsToRegistry($post);

			if (($post->pubstate == AutotweetPostHelper::POST_APPROVE) && ($cron_enabled))
			{
				$success = self::savePost(AutotweetPostHelper::POST_CRONJOB, 'COM_AUTOTWEET_MSG_POSTRESULT_CRONJOB', $post, $userid);
			}
			else
			{
				$success = $sharinghelper->publishPost($post, $userid);
			}
		}

		return $success;
	}

	/**
	 * moveToState - This static function should used from backend only for manual (re)posting attempts
	 *
	 * @param   array   $posting_ids  Param
	 * @param   int     $userid       Param
	 * @param   string  $pubstate     Param
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public static function moveToState($posting_ids, $userid = null, $pubstate = null)
	{
		$success = true;

		if (!$pubstate)
		{
			$pubstate = AutotweetPostHelper::POST_CANCELLED;
		}

		if (count($posting_ids) > 0)
		{
			$idslist = implode(',', $posting_ids);
			$idslist = '(' . $idslist . ')';

			$now = JFactory::getDate();

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->update('#__autotweet_posts')->set($db->qn('pubstate') . ' = ' . $db->q($pubstate))->set($db->qn('resultmsg') . ' = ' . $db->q(JText::_('COM_AUTOTWEET_MSG_POSTRESULT_MOVED')))->set($db->qn('modified') . ' = ' . $db->q($now->toSql()))->set($db->qn('modified_by') . ' = ' . $db->q($userid))->where($db->qn('id') . ' IN ' . $idslist);
			$db->setQuery($query);
			$db->execute();
		}

		return $success;
	}

	/**
	 * publishCronjobPosts
	 *
	 * @param   int  $limit  Param
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public static function publishCronjobPosts($limit)
	{
		$postsModel = F0FModel::getTmpInstance('Posts', 'AutoTweetModel');
		$postsModel->set('pubstate', AutotweetPostHelper::POST_CRONJOB);
		$postsModel->set('filter_order', 'postdate');
		$postsModel->set('filter_order_Dir', 'ASC');
		$postsModel->set('limit', $limit);
		$posts = $postsModel->getItemList();

		$sharingHelper = SharingHelper::getInstance();

		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'publishCronjobPosts Posts: ' . count($posts));

		foreach ($posts as $post)
		{
			$logger->log(JLog::INFO, 'Sending Post ID: ' . $post->id . ' Channel: ' . $post->channel_id . ' Plugin: ' . $post->plugin);

			$post->xtform = EForm::paramsToRegistry($post);
			$sharingHelper->publishPost($post);
		}
	}

	/**
	 * isDuplicatedPost.
	 *
	 * @param   integer  $id           Param
	 * @param   integer  $ref_id       Param
	 * @param   string   $plugin       Param
	 * @param   integer  $channel_id   Param
	 * @param   string   $message      Param
	 * @param   integer  $time_intval  Param
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public static function isDuplicatedPost($id, $ref_id, $plugin, $channel_id, $message, $time_intval)
	{
		// Duplicate post detection: check message log for message in time interval
		$is_duplicate = false;

		// Calculate date for interval
		$now = JFactory::getDate();
		$check_date = $now->toUnix();
		$check_date = $check_date - $time_intval;
		$check_date = JFactory::getDate($check_date);

		// Get articles only when they are not in the queue and not in the message log for time horizon
		$postsModel = F0FModel::getTmpInstance('Posts', 'AutoTweetModel');
		$postsModel->set('not_id', $id);

		// $postsModel->set('ref_id', $ref_id);
		$postsModel->set('message', $message);

		$postsModel->set('plugin', $plugin);
		$postsModel->set('channel', $channel_id);
		$postsModel->set('pubstate', AutotweetPostHelper::POST_SUCCESS);
		$postsModel->set('after_date', $check_date->toSql());
		$posts = $postsModel->getItemList(true);

		if (count($posts) > 0)
		{
			$is_duplicate = true;
		}

		return $is_duplicate;
	}

	/**
	 * isBannedPost.
	 *
	 * @param   string  &$message       Param
	 * @param   string  &$banned_words  Param
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public static function isBannedPost(&$message, &$banned_words)
	{
		return (preg_match('~\b(' . $banned_words . ')\b~i', $message));
	}
}
