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
 * ChannelFactory class.
 *
 * Factory to create channel classes.
 * This is the central point to get and handle channel classes. Also all needed files are included here.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class ChannelFactory
{
	/**
	 * ChannelFactory
	 *
	 */
	protected function __construct()
	{
	}

	/**
	 * getInstance
	 *
	 * @return	object.
	 */
	public static function getInstance()
	{
		return new ChannelFactory;
	}

	/**
	 * createChannel
	 *
	 * @param   F0FTable  &$channel  Param
	 *
	 * @return	object
	 */
	protected function createChannel(&$channel)
	{
		$channeltype = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel');
		$classname = $channeltype->getChannelClass($channel->channeltype_id);
		JLoader::load($classname);

		return new $classname($channel);
	}

	/**
	 * getChannels
	 *
	 * @param   string  $author  Param
	 *
	 * @return	array
	 */
	public function getChannels($author)
	{
		$channels = F0FModel::getTmpInstance('Channels', 'AutoTweetModel');
		$channels->set('published', true);
		$channels->set('scope', 'S');
		$channels->set('filter_order', 'ordering');
		$channels->set('filter_order_Dir', 'ASC');

		$list = $channels->getItemList(true);

		if (!empty($author))
		{
			$user_id = JUserHelper::getUserId($author);

			if ($user_id)
			{
				$userChannels = F0FModel::getTmpInstance('Channels', 'AutoTweetModel');
				$userChannels->set('published', true);
				$userChannels->set('scope', 'U');
				$userChannels->set('created_by', $user_id);
				$userChannels->set('filter_order', 'ordering');
				$userChannels->set('filter_order_Dir', 'ASC');

				$userList = $userChannels->getItemList(true);
				$list = array_merge($list, $userList);
			}
		}

		$channels = array();

		foreach ($list as $channel)
		{
			$channels[$channel->id] = self::createChannel($channel);
		}

		return $channels;
	}

	/**
	 * getChannel
	 *
	 * @param   int  $id  Param
	 *
	 * @return	object.
	 */
	public function getChannel($id)
	{
		$channels = F0FModel::getTmpInstance('Channels', 'AutoTweetModel');
		$channel = $channels->getItem($id);

		return self::createChannel($channel);
	}
}
