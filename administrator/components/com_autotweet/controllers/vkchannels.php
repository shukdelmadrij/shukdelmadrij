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
 * AutotweetControllerVkChannels
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerVkChannels extends F0FController
{
	// Facebook Params
	private $_app_id = null;

	private $_secret = null;

	private $_access_token = null;

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
		$url = 'index.php?option=com_autotweet&view=vkchannels&task=' . $callback . '&channelId=' . $channelId;

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
	 * getCallbackUrlStandalone.
	 *
	 * @param   int  $channelId  Param
	 *
	 * @return	string
	 *
	 * @since	1.5
	 */
	public static function getCallbackUrlStandalone($channelId)
	{
		return 'http://api.vkontakte.ru/blank.html';
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
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$channelId = $this->input->getUint('channelId');
		$vkcode = $this->input->getCmd('code');

		// Error throw
		if (!empty($vkcode))
		{
			$channel = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
			$result = $channel->load($channelId);

			if (!$result)
			{
				throw new Exception('Channel failed to load!');
			}

			$vkChannelHelper = new VkChannelHelper($channel);

			$redirect_uri = self::getCallbackUrl($channelId);
			$jsonAccessToken = $vkChannelHelper->getJsonAccessToken($vkcode, $redirect_uri);

			$registry = new JRegistry;
			$registry->loadString($channel->params);
			$registry->set('access_token', $jsonAccessToken);
			$channel->bind(array('params' => (string) $registry));
			$channel->store();

			// Redirect
			$url = 'index.php?option=com_autotweet&view=channels&task=edit&id=' . $channelId;
			$this->setRedirect($url);
			$this->redirect();
		}
	}

	/**
	 * callback.
	 *
	 * @return	void
	 *
	 * @since	1.5
	 */
	public function callbackStandalone()
	{
			// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$channelId = $this->input->getUint('channelId');
		$access_token = $this->input->getCmd('access_token');
		$expires_in = $this->input->getCmd('expires_in');
		$user_id = $this->input->getCmd('user_id');

		// Error throw
		if (!empty($access_token))
		{
			$channel = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
			$result = $channel->load($channelId);

			if (!$result)
			{
				throw new Exception('Channel failed to load!');
			}

			$vkChannelHelper = new VkChannelHelper($channel);

			$oAccessToken = new StdClass;
			$oAccessToken->access_token = $access_token;
			$oAccessToken->expires_in = $expires_in;
			$oAccessToken->user_id = $user_id;

			$jsonAccessToken = json_encode($oAccessToken);
			$vkChannelHelper->setJsonAccessToken($jsonAccessToken);

			$userSettings = $vkChannelHelper->getUserSettings();

			if ((is_array($userSettings)) && ($userSettings['response'] = 65536))
			{
				$registry = new JRegistry;
				$registry->loadString($channel->params);
				$registry->set('access_token', $jsonAccessToken);
				$channel->bind(array('params' => (string) $registry));
				$channel->store();

				// Redirect
				$url = 'index.php?option=com_autotweet&view=channels&task=edit&id=' . $channelId;
				$this->setRedirect($url);
				$this->redirect();
			}
		}
	}
}
