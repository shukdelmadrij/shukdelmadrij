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
 * AutotweetControllerGplusChannels
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerGplusChannels extends F0FController
{
	/**
	 * getCallbackUrl.
	 *
	 * @param   int     $channelId  Param
	 * @param   string  $callback   Param
	 *
	 * @return	string
	 *
	 * @since	1.5
	 */
	public static function getCallbackUrl($channelId, $callback = 'callback')
	{
		//  . '&channelId=' . $channelId
		// $url = 'index.php?option=com_autotweet&view=gpluschannels&task=' . $callback;
		$url = 'index.php?option=com_autotweet';

		if (F0FPlatform::getInstance()->isCli())
		{
			$routeHelp = RouteHelp::getInstance();

			return $routeHelp->getRoot() . '/' . $url;
		}
		else
		{
			return JUri::base() . $url;
		}
	}

	/**
	 * callback.
	 *
	 * @return	void
	 *
	 * @since	1.5
	 */
	public function callback()
	{
		// CSRF prevention disabled, we are trusting in code authentication

		/*
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}
		*/

		try
		{
			// $channelId = $this->input->getUint('channelId');

			$session = JFactory::getSession();
			$channelId = $session->get('channelId');

			// Invalidating
			$session->set('channelId', false);

			$gpluscode = $this->input->getString('code');

			// Error throw
			if (!empty($gpluscode))
			{
				$channel = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
				$result = $channel->load($channelId);

				if (!$result)
				{
					throw new Exception('Channel failed to load!');
				}

				$gplusChannelHelper = new GplusChannelHelper($channel);
				$gplusChannelHelper->authenticate($gpluscode);

				// Redirect
				$url = 'index.php?option=com_autotweet&view=channels&task=edit&id=' . $channelId;
				$this->setRedirect($url);
				$this->redirect();
			}
		}
		catch (Exception $e)
		{
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::ERROR, $e->getMessage());

			throw $e;
		}
	}
}
