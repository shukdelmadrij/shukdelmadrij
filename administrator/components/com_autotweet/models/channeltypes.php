<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * AutotweetModelChanneltypes
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelChanneltypes extends F0FModel
{
	const TYPE_FBCHANNEL = 2;
	const TYPE_FBEVENTCHANNEL = 4;
	const TYPE_FBLINKCHANNEL = 7;
	const TYPE_FBPHOTOCHANNEL = 8;
	const TYPE_FBVIDEOCHANNEL = 9;
	const TYPE_LINKCHANNEL = 5;
	const TYPE_LIGROUPCHANNEL = 6;
	const TYPE_LICOMPANYCHANNEL = 10;
	const TYPE_MAILCHANNEL = 3;
	const TYPE_TWCHANNEL = 1;
	const TYPE_VKCHANNEL = 11;
	const TYPE_VKGROUPCHANNEL = 12;
	const TYPE_GPLUSCHANNEL = 13;

	/**
	 * getParamsForm
	 *
	 * @param   int  $channeltype_id  Param
	 *
	 * @return	string
	 */
	public static function getParamsForm($channeltype_id)
	{
		if (($channeltype_id == self::TYPE_FBCHANNEL) || ($channeltype_id == self::TYPE_FBEVENTCHANNEL) || ($channeltype_id == self::TYPE_FBLINKCHANNEL) || ($channeltype_id == self::TYPE_FBPHOTOCHANNEL) || ($channeltype_id == self::TYPE_FBVIDEOCHANNEL))
		{
			return 'fbchannel';
		}

		if ($channeltype_id == self::TYPE_GPLUSCHANNEL)
		{
			return 'gpluschannel';
		}

		if ($channeltype_id == self::TYPE_LINKCHANNEL)
		{
			return 'lichannel';
		}

		if ($channeltype_id == self::TYPE_LIGROUPCHANNEL)
		{
			return 'ligroupchannel';
		}

		if ($channeltype_id == self::TYPE_LICOMPANYCHANNEL)
		{
			return 'licompanychannel';
		}

		if ($channeltype_id == self::TYPE_MAILCHANNEL)
		{
			return 'mailchannel';
		}

		if ($channeltype_id == self::TYPE_TWCHANNEL)
		{
			return 'twchannel';
		}

		if (($channeltype_id == self::TYPE_VKCHANNEL) || ($channeltype_id == self::TYPE_VKGROUPCHANNEL))
		{
			return 'vkchannel';
		}

		return null;
	}

	/**
	 * getIcon
	 *
	 * @param   int  $channeltype_id  Param
	 *
	 * @return	string
	 */
	public static function getIcon($channeltype_id)
	{
		if (($channeltype_id == self::TYPE_FBCHANNEL) || ($channeltype_id == self::TYPE_FBEVENTCHANNEL) || ($channeltype_id == self::TYPE_FBLINKCHANNEL) || ($channeltype_id == self::TYPE_FBPHOTOCHANNEL) || ($channeltype_id == self::TYPE_FBVIDEOCHANNEL))
		{
			return '<i class=\'xticon xticon-facebook\'></i>';
		}

		if ($channeltype_id == self::TYPE_GPLUSCHANNEL)
		{
			return '<i class=\'xticon xticon-google-plus\'></i>';
		}

		if (($channeltype_id == self::TYPE_LINKCHANNEL) || ($channeltype_id == self::TYPE_LIGROUPCHANNEL) || ($channeltype_id == self::TYPE_LICOMPANYCHANNEL))
		{
			return '<i class=\'xticon xticon-linkedin\'></i>';
		}

		if ($channeltype_id == self::TYPE_MAILCHANNEL)
		{
			return '<i class=\'xticon xticon-envelope\'></i>';
		}

		if ($channeltype_id == self::TYPE_TWCHANNEL)
		{
			return '<i class=\'xticon xticon-twitter\'></i>';
		}

		if ($channeltype_id == self::TYPE_VKCHANNEL)
		{
			return '<i class=\'xticon xticon-vk\'></i>';
		}

		if ($channeltype_id == self::TYPE_VKGROUPCHANNEL)
		{
			return '<i class=\'xticon xticon-vk\'></i>';
		}

		return null;
	}

	/**
	 * getChannelClass
	 *
	 * @param   int  $channeltype_id  Param
	 *
	 * @return	string
	 */
	public static function getChannelClass($channeltype_id)
	{
		switch ($channeltype_id)
		{
			case self::TYPE_FBCHANNEL:
				return 'FacebookChannelHelper';

			case self::TYPE_FBEVENTCHANNEL:
				return 'FacebookEventChannelHelper';

			case self::TYPE_FBLINKCHANNEL:
				return 'FacebookLinkChannelHelper';

			case self::TYPE_FBPHOTOCHANNEL:
				return 'FacebookPhotoChannelHelper';

			case self::TYPE_FBVIDEOCHANNEL:
				return 'FacebookVideoChannelHelper';

			case self::TYPE_LINKCHANNEL:
				return 'LinkedinChannelHelper';

			case self::TYPE_LIGROUPCHANNEL:
				return 'LinkedinGroupChannelHelper';

			case self::TYPE_LICOMPANYCHANNEL:
				return 'LinkedinCompanyChannelHelper';

			case self::TYPE_MAILCHANNEL:
				return 'MailChannelHelper';

			case self::TYPE_TWCHANNEL:
				return 'TwitterChannelHelper';

			case self::TYPE_VKCHANNEL:
				return 'VkChannelHelper';

			case self::TYPE_VKGROUPCHANNEL:
				return 'VkGroupChannelHelper';

			case self::TYPE_GPLUSCHANNEL:
				return 'GplusChannelHelper';
		}

		return null;
	}

	/**
	 * isFrontendEnabled
	 *
	 * @param   int  $channeltype_id  Param
	 *
	 * @return	string
	 */
	public static function isFrontendEnabled($channeltype_id)
	{
		switch ($channeltype_id)
		{
			case self::TYPE_FBCHANNEL:
				return true;

			case self::TYPE_FBEVENTCHANNEL:
				return false;

			case self::TYPE_FBLINKCHANNEL:
				return true;

			case self::TYPE_FBPHOTOCHANNEL:
				return false;

			case self::TYPE_FBVIDEOCHANNEL:
				return false;

			case self::TYPE_LINKCHANNEL:
				return true;

			case self::TYPE_LIGROUPCHANNEL:
				return true;

			case self::TYPE_LICOMPANYCHANNEL:
				return false;

			case self::TYPE_MAILCHANNEL:
				return true;

			case self::TYPE_TWCHANNEL:
				return true;

			case self::TYPE_VKCHANNEL:
				return false;

			case self::TYPE_VKGROUPCHANNEL:
				return false;

			case self::TYPE_GPLUSCHANNEL:
				return false;
		}

		return false;
	}

	/**
	 * buildQuery
	 *
	 * @param   bool  $overrideLimits  Param
	 *
	 * @return	F0FQuery
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDBO();
		$query = parent::buildQuery($overrideLimits);
		$query->order($db->qn('name'));

		return $query;
	}

	/**
	 * formatUrl
	 *
	 * @param   int     $channeltype_id  Param
	 * @param   string  $socialUrl       Param
	 *
	 * @return	string
	 */
	public static function formatUrl($channeltype_id, $socialUrl)
	{
		$socialIcon = self::getIcon($channeltype_id);

		return '<p><a href="' . $socialUrl . '" target="_blank">' . $socialIcon . ' ' . $socialUrl . '</a></p>';
	}
}
