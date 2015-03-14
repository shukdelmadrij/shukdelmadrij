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
class AutotweetPostHelper
{
	// States of post and for publish_all
	// New message is ready for sending
	const POST_SUCCESS = 'success';
	const POST_ERROR = 'error';
	const POST_APPROVE = 'approve';
	const POST_CRONJOB = 'cronjob';
	const POST_CANCELLED = 'cancelled';

	// Static text modes
	const STATICTEXT_OFF = 'off';
	const STATICTEXT_BEGINNING = 'beginning_of_message';
	const STATICTEXT_END = 'end_of_message';

	// Url mode
	const SHOWURL_OFF = 'off';
	const SHOWURL_BEGINNING = 'beginning_of_message';
	const SHOWURL_END = 'end_of_message';

	private static $_instance = null;

	/**
	 * getInstance
	 *
	 * @return	Instance
	 */
	public static function &getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new AutotweetPostHelper;
		}

		return self::$_instance;
	}

	/**
	 * postQueuedMessages
	 *
	 * @param   integer  $max  Param
	 *
	 * @return	boolean
	 */
	public function postQueuedMessages($max)
	{
		$now = JFactory::getDate();
		$logger = AutotweetLogger::getInstance();

		if ((AUTOTWEETNG_JOOCIAL) && (!VirtualManager::getInstance()->isWorking($now)))
		{
			$logger->log(JLog::INFO, 'AutotweetPostHelper - VM not working now ' . $now->toISO8601(true));

			return false;
		}

		// Get msgs from queue (sending is allowed only, when publish date is not in the future)
		// Sub 1 minute to avoid problems when automator plugin and extension plugin are executed at the same time...
		$check_date = $now->toUnix();

		// Sub 1 minute check
		$mincheck_time_intval = EParameter::getComponentParam(CAUTOTWEETNG, 'mincheck_time_intval', 60);

		$check_date = $check_date - $mincheck_time_intval;
		$check_date = JFactory::getDate($check_date);

		$requests = RequestHelp::getRequestList($check_date, $max);

		$sharingHelper = SharingHelper::getInstance();

		$logger->log(JLog::INFO, 'postQueuedMessages Requests: ' . count($requests));

		foreach ($requests as $request)
		{
			$result = false;

			$message = null;

			try
			{
				$result = $sharingHelper->publishRequest($request);
			}
			catch (Exception $e)
			{
				$message = $e->getMessage();
				$logger->log(JLog::ERROR, 'postQueuedMessages: Exception! ' . $message);
			}

			if ($result)
			{
				RequestHelp::processed($request->id);
			}
			else
			{
				RequestHelp::saveError($request->id, $message);
			}
		}

		if ((AUTOTWEETNG_JOOCIAL) && (empty($requests)))
		{
			VirtualManager::getInstance()->enqueueEvergreenMessage($check_date, $max);
		}
	}
}
