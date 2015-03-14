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

include_once 'default.php';

/**
 * AutotweetControllerChannels
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerChannels extends AutotweetControllerDefault
{
	// Facebook Params
	private $_app_id = null;

	private $_secret = null;

	private $_access_token = null;

	private $_ownapp = null;

	private $_channel_id = null;

	private $_channel_access_token = null;

	// LinkedIn Params
	private $_api_key = null;

	private $_secret_key = null;

	private $_oauth_user_token = null;

	private $_oauth_user_secret = null;

	/**
	 * getParamsForm.
	 *
	 * @return	void
	 */
	public function getParamsForm()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$channeltype_id = $data['channelTypeId'];
		$channeltype_id = $safeHtmlFilter->clean($channeltype_id, 'ALNUM');
		$channel_id = $data['channelId'];
		$channel_id = $safeHtmlFilter->clean($channel_id, 'ALNUM');

		// Load the model
		$channeltype = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel');

		$view = 'nochannel';

		if ($channeltype_id)
		{
			$view = $channeltype->getParamsForm($channeltype_id);
		}

		$config = array(
				'input' => array(
						'option' => 'com_autotweet',
						'view' => $view,
						'task' => (empty($channel_id) ? 'add' : 'edit'),
						'id' => $channel_id,
						'channeltype_id' => $channeltype_id
				),
				'modelName' => 'AutotweetModelChannels'
		);

		@ob_start();
		F0FDispatcher::getTmpInstance('com_autotweet', $view, $config)->dispatch();
		$result = ob_get_contents();
		@ob_end_clean();

		$message = json_encode(
				array(
						'status' => true,
						'message' => $result
				)
		);
		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getFbValidation.
	 *
	 * @return	void
	 */
	public function getFbValidation()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		$this->_loadFbParams();

		JLoader::register('FbAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/fbapp.php');

		$status = false;
		$error_message = 'Unknown';
		$user = null;
		$tokenInfo = null;

		try
		{
			$fbAppHelper = new FbAppHelper($this->_app_id, $this->_secret, $this->_access_token);

			if ($fbAppHelper->login())
			{
				$user = $fbAppHelper->getUser();
				$result = $fbAppHelper->verify();
				$tokenInfo = $fbAppHelper->getDebugToken();

				$status = $result[0];
				$error_message = $result[1];
			}
			else
			{
				$error_message = 'Facebook Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		$message = json_encode(
				array(
						'status' => $status,
						'error_message' => $error_message,
						'user' => $user,
						'tokenInfo' => $tokenInfo)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getFbChValidation.
	 *
	 * @return	void
	 */
	public function getFbChValidation()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		$this->_loadFbParams();

		JLoader::register('FbAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/fbapp.php');

		$status = false;
		$error_message = 'Unknown';
		$tokenInfo = null;

		try
		{
			$fbAppHelper = new FbAppHelper($this->_app_id, $this->_secret, $this->_access_token);

			if ($fbAppHelper->login())
			{
				$tokenInfo = $fbAppHelper->getDebugToken($this->_fbchannel_access_token);

				$status = true;
				$error_message = 'Ok';
			}
			else
			{
				$error_message = 'Facebook Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		$message = json_encode(
			array(
				'status' => $status,
				'error_message' => $error_message,
				'tokenInfo' => $tokenInfo
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getFbExtend.
	 *
	 * @return	void
	 */
	public function getFbExtend()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		$this->_loadFbParams();

		JLoader::register('FbAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/fbapp.php');

		$status = false;
		$error_message = 'Unknown';
		$extended_token = null;
		$user = null;
		$result = null;
		$tokenInfo = null;

		try
		{
			$fbAppHelper = new FbAppHelper($this->_app_id, $this->_secret, $this->_access_token);

			if ($fbAppHelper->login())
			{
				$extended_token = $fbAppHelper->getExtendedAccessToken();

				if ($extended_token)
				{
					$this->_access_token = $extended_token;

					$fbAppHelper = new FbAppHelper($this->_app_id, $this->_secret, $this->_access_token);

					if ($fbAppHelper->login())
					{
						$tokenInfo = $fbAppHelper->getDebugToken();
						$user = $fbAppHelper->getUser();

						$status = true;
						$error_message = 'Ok';
					}
					else
					{
						$error_message = 'Facebook Login (extended) Failed!';
					}
				}
				else
				{
					$error_message = 'Unable to extend the token';
				}
			}
			else
			{
				$error_message = 'Facebook Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		$message = json_encode(
				array(
					'status' => $status,
					'error_message' => $error_message,
					'extended_token' => $extended_token,
					'user' => $user,
					'tokenInfo' => $tokenInfo
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getLiValidation.
	 *
	 * @return	void
	 */
	public function getLiValidation()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$api_key = $data['api_key'];
		$api_key = $safeHtmlFilter->clean($api_key, 'ALNUM');

		$secret_key = $data['secret_key'];
		$secret_key = $safeHtmlFilter->clean($secret_key, 'ALNUM');

		$oauth_user_token = $data['oauth_user_token'];
		$oauth_user_token = $safeHtmlFilter->clean($oauth_user_token, 'CMD');

		$oauth_user_secret = $data['oauth_user_secret'];
		$oauth_user_secret = $safeHtmlFilter->clean($oauth_user_secret, 'CMD');

		JLoader::register('LiAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/liapp.php');

		$status = false;
		$error_message = 'Unknown';
		$user = null;
		$url = null;
		$icon = null;

		try
		{
			$appHelper = new LiAppHelper($api_key, $secret_key, $oauth_user_token, $oauth_user_secret);

			if ($appHelper->login())
			{
				$result = $appHelper->getUser();
				$status = $result['status'];
				$error_message = $result['error_message'];

				if ($status)
				{
					$user = $result['user'];
					$url = $result['url'];

					$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
						->getIcon(AutotweetModelChanneltypes::TYPE_LINKCHANNEL);
				}
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		$message = json_encode(
			array(
				'status' => $status,
				'error_message' => $error_message,
				'user' => $user,
				'url' => $url,
				'icon' => $icon
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getTwValidation.
	 *
	 * @return	void
	 */
	public function getTwValidation()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$consumer_key = $data['consumer_key'];
		$consumer_key = $safeHtmlFilter->clean($consumer_key, 'ALNUM');

		$consumer_secret = $data['consumer_secret'];
		$consumer_secret = $safeHtmlFilter->clean($consumer_secret, 'ALNUM');

		$access_token = $data['access_token'];
		$access_token = $safeHtmlFilter->clean($access_token, 'CMD');

		$access_token_secret = $data['access_token_secret'];
		$access_token_secret = $safeHtmlFilter->clean($access_token_secret, 'CMD');

		JLoader::register('TwAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/twapp.php');

		$status = false;
		$error_message = 'Unknown';
		$user = null;
		$url = null;
		$icon = null;

		try
		{
			$appHelper = new TwAppHelper($consumer_key, $consumer_secret, $access_token, $access_token_secret);

			if ($result = $appHelper->verify())
			{
				$status = $result['status'];
				$error_message = $result['error_message'];

				if ($status)
				{
					$error_message = $result['error_message'];
					$user = $result['user'];
					$url = $result['url'];

					$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
						->getIcon(AutotweetModelChanneltypes::TYPE_TWCHANNEL);
				}
			}
			else
			{
				$error_message = 'Twitter Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		$message = json_encode(
			array(
				'status' => $status,
				'error_message' => $error_message,
				'user' => $user,
				'icon' => $icon,
				'url' => $url
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getFbChannels.
	 *
	 * @return	void
	 */
	public function getFbChannels()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		$this->_loadFbParams();

		JLoader::register('FbAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/fbapp.php');

		$status = false;
		$error_message = 'Unknown';
		$icon = null;

		try
		{
			$fbAppHelper = new FbAppHelper($this->_app_id, $this->_secret, $this->_access_token);

			if ($fbAppHelper->login())
			{
				$channels = $fbAppHelper->getChannels();
				$status = true;
				$error_message = 'Ok';

				$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
					->getIcon(AutotweetModelChanneltypes::TYPE_FBCHANNEL);
			}
			else
			{
				$error_message = 'Facebook Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		$message = json_encode(
			array(
				'status' => $status,
				'error_message' => $error_message,
				'channels' => $channels,
				'icon' => $icon
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getFbAlbums.
	 *
	 * @return	void
	 */
	public function getFbAlbums()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		$this->_loadFbParams();

		JLoader::register('FbAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/fbapp.php');

		$status = false;
		$error_message = 'Unknown';

		try
		{
			$fbAppHelper = new FbAppHelper($this->_app_id, $this->_secret, $this->_access_token);

			if ($fbAppHelper->login())
			{
				// $this->_channel_access_token ?
				$channels = $fbAppHelper->getAlbums($this->_channel_id);
				$status = true;
				$error_message = 'Ok';
			}
			else
			{
				$error_message = 'Facebook Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		$message = json_encode(
				array(
						'status' => $status,
						'error_message' => $error_message,
						'albums' => $channels)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getLiGroups
	 *
	 * @return	void
	 */
	public function getLiGroups()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		$this->_loadLiParams();

		JLoader::register('LiAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/liapp.php');

		$status = false;
		$error_message = 'Unknown';
		$icon = null;

		try
		{
			$liAppHelper = new LiAppHelper(
					$this->_api_key,
					$this->_secret_key,
					$this->_oauth_user_token,
					$this->_oauth_user_secret
				);

			if ($liAppHelper->login())
			{
				$channels = $liAppHelper->getMyGroups();

				$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
					->getIcon(AutotweetModelChanneltypes::TYPE_LINKCHANNEL);

				$status = true;
				$error_message = 'Ok';
			}
			else
			{
				$error_message = 'LiGroups Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		if ((count($channels) == 2) && ($channels[0] == false))
		{
			$message = json_encode(
				array(
					'status' => false,
					'error_message' => $channels[1]
				)
			);
		}
		else
		{
			$message = json_encode(
				array(
					'status' => $status,
					'error_message' => $error_message,
					'channels' => $channels,
					'icon' => $icon
				)
			);
		}

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getLiCompanies
	 *
	 * @return	void
	 */
	public function getLiCompanies()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		$this->_loadLiParams();

		JLoader::register('LiAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/liapp.php');

		$status = false;
		$error_message = 'Unknown';
		$icon = null;

		try
		{
			$liAppHelper = new LiAppHelper(
					$this->_api_key,
					$this->_secret_key,
					$this->_oauth_user_token,
					$this->_oauth_user_secret
			);

			if ($liAppHelper->login())
			{
				$channels = $liAppHelper->getMyCompanies();

				$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
					->getIcon(AutotweetModelChanneltypes::TYPE_LINKCHANNEL);

				$status = true;
				$error_message = 'Ok';
			}
			else
			{
				$error_message = 'LiCompanies Login Failed!';
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
		}

		if ((count($channels) == 2) && ($channels[0] == false))
		{
			$message = json_encode(
				array(
					'status' => false,
					'error_message' => $channels[1]
				)
			);
		}
		else
		{
			$message = json_encode(
				array(
					'status' => $status,
					'error_message' => $error_message,
					'channels' => $channels,
					'icon' => $icon
				)
			);
		}

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getVkValidation.
	 *
	 * @return	void
	 */
	public function getVkValidation()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$channel_id = $data['channel_id'];
		$channel_id = $safeHtmlFilter->clean($channel_id, 'ALNUM');

		$access_token = $data['access_token'];
		$access_token = $safeHtmlFilter->clean($access_token, 'STRING');

		$status = false;
		$error_message = 'Unknown';
		$user = null;
		$url = null;
		$icon = null;

		$channel = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
		$result = $channel->load($channel_id);

		if (!$result)
		{
			$error_message = 'Channel failed to load!';
		}
		else
		{
			try
			{
				$params = $channel->params;
				$registry = new JRegistry;
				$registry->loadString($params);
				$registry->set('access_token', $access_token);
				$channel->bind(array('params' => (string) $registry));

				$vkChannelHelper = new VkChannelHelper($channel);
				$result = $vkChannelHelper->getUserSettings();

				$status = $result['status'];
				$message = $result['error_message'];
				$user = $result['user'];
				$url = $result['url'];

				$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
					->getIcon(AutotweetModelChanneltypes::TYPE_VKCHANNEL);
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
			}
		}

		$message = json_encode(
			array(
				'status' => $status,
				'error_message' => $error_message,
				'user' => $user,
				'social_icon' => $icon,
				'social_url' => $url
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getVkGroups.
	 *
	 * @return	void
	 */
	public function getVkGroups()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$channel_id = $data['channel_id'];
		$channel_id = $safeHtmlFilter->clean($channel_id, 'ALNUM');

		$access_token = $data['access_token'];
		$access_token = $safeHtmlFilter->clean($access_token, 'STRING');

		$status = false;
		$error_message = 'Unknown';
		$items = null;
		$icon = null;

		$channel = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
		$result = $channel->load($channel_id);

		if (!$result)
		{
			$error_message = 'Channel failed to load!';
		}
		else
		{
			try
			{
				$params = $channel->params;
				$registry = new JRegistry;
				$registry->loadString($params);
				$registry->set('access_token', $access_token);
				$channel->bind(array('params' => (string) $registry));

				$vkChannelHelper = new VkChannelHelper($channel);
				$result = $vkChannelHelper->getGroups();

				$status = $result['status'];
				$message = $result['error_message'];
				$items = $result['items'];

				$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
					->getIcon(AutotweetModelChanneltypes::TYPE_VKCHANNEL);
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
			}
		}

		$message = json_encode(
			array(
				'status' => $status,
				'error_message' => $error_message,
				'groups' => $items,
				'social_icon' => $icon
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getGplusValidation.
	 *
	 * @return	void
	 */
	public function getGplusValidation()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$channel_id = $data['channel_id'];
		$channel_id = $safeHtmlFilter->clean($channel_id, 'ALNUM');

		$status = false;
		$error_message = 'Unknown';

		$channel = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
		$result = $channel->load($channel_id);

		$user = null;
		$url = null;
		$icon = null;

		if (!$result)
		{
			$error_message = 'Channel failed to load!';
		}
		else
		{
			try
			{
				$gplusChannelHelper = new GplusChannelHelper($channel);
				$isAuth = $gplusChannelHelper->isAuth();

				if ($isAuth)
				{
					$status = true;
					$error_message = 'Ok';
					$user = $gplusChannelHelper->getUser();
					$url = $user['url'];

					$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
						->getIcon(AutotweetModelChanneltypes::TYPE_GPLUSCHANNEL);
				}
				else
				{
					$error_message = JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_NOT_AUTH_ERR');
				}
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
			}
		}

		$message = json_encode(
			array(
				'status' => $status,
				'error_message' => $error_message,
				'user' => $user,
				'social_icon' => $icon,
				'social_url' => $url
			)
		);

		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * _loadFbParams.
	 *
	 * @return	void
	 */
	private function _loadFbParams()
	{
		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		/*
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, "_loadFbParams", $data);
		*/

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$this->_app_id = $data['app_id'];
		$this->_app_id = $safeHtmlFilter->clean($this->_app_id, 'ALNUM');

		$this->_secret = $data['secret'];
		$this->_secret = $safeHtmlFilter->clean($this->_secret, 'ALNUM');

		$this->_access_token = $data['access_token'];
		$this->_access_token = $safeHtmlFilter->clean($this->_access_token, 'ALNUM');

		$this->_ownapp = $data['own_app'];
		$this->_ownapp = $safeHtmlFilter->clean($this->_ownapp, 'ALNUM');

		$this->_channel_id = null;
		$this->_channel_access_token = null;

		if (array_key_exists('channel_id', $data))
		{
			$this->_channel_id = $data['channel_id'];
			$this->_channel_id = $safeHtmlFilter->clean($this->_channel_id, 'ALNUM');

			$this->_channel_access_token = $data['channel_access_token'];
			$this->_channel_access_token = $safeHtmlFilter->clean($this->_channel_access_token, 'ALNUM');
		}

		if (array_key_exists('fbchannel_access_token', $data))
		{
			$this->_fbchannel_access_token = $data['fbchannel_access_token'];
			$this->_fbchannel_access_token = $safeHtmlFilter->clean($this->_fbchannel_access_token, 'ALNUM');
		}

		if (!$this->_ownapp)
		{
			$channeltype = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')->getTable();
			$channeltype->reset();
			$channeltype->load(AutotweetModelChanneltypes::TYPE_FBCHANNEL);

			$this->_app_id = $channeltype->auth_key;
			$this->_secret = $channeltype->auth_secret;
		}
	}

	/**
	 * _loadLiParams.
	 *
	 * @return	void
	 */
	private function _loadLiParams()
	{
		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$this->_api_key = $data['api_key'];
		$this->_api_key = $safeHtmlFilter->clean($this->_api_key, 'ALNUM');

		$this->_secret_key = $data['secret_key'];
		$this->_secret_key = $safeHtmlFilter->clean($this->_secret_key, 'ALNUM');

		$this->_oauth_user_token = $data['oauth_user_token'];
		$this->_oauth_user_token = $safeHtmlFilter->clean($this->_oauth_user_token, 'CMD');

		$this->_oauth_user_secret = $data['oauth_user_secret'];
		$this->_oauth_user_secret = $safeHtmlFilter->clean($this->_oauth_user_secret, 'CMD');
	}
}
