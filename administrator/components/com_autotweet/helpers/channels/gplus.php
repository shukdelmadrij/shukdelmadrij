<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Gplus, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * AutoTweet Gplus channel.
**/

JLoader::import('channel', dirname(__FILE__));

JLoader::register('Google_Client', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'gplus/Google_Client.php');
JLoader::register('Google_PlusService', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'gplus/contrib/Google_PlusService.php');

/**
 * GplusChannelHelper
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class GplusChannelHelper extends ChannelHelper
{
	const API_SETTINGS = 'wall,photos,audio,video,pages,offline';

	protected $gplusClient = null;

	protected $gplus = null;

	protected $client_id = null;

	protected $client_secret = null;

	protected $developer_key = null;

	protected $access_token = null;

	protected $is_auth = null;

	protected $me = null;

	/**
	 * ChannelHelper.
	 *
	 * @param   object  $channel  Params.
	 */
	public function __construct($channel)
	{
		parent::__construct($channel);

		if ($channel->id)
		{
			$this->client_id = $this->channel->params->get('client_id');
			$this->client_secret = $this->channel->params->get('client_secret');
			$this->developer_key = $this->channel->params->get('developer_key');

			$access_token = $this->channel->params->get('access_token');
			$this->setAccessToken($access_token);
		}
	}

	/**
	 * setAccessToken
	 *
	 * @param   string  $access_token  Param
	 *
	 * @return	void
	 */
	public function setAccessToken($access_token)
	{
		$this->access_token = $access_token;
	}

	/**
	 * getAccessToken
	 *
	 * @return	string
	 */
	public function getAccessToken()
	{
		return $this->access_token;
	}

	/**
	 * Internal service functions
	 *
	 * @return	object
	 */
	protected function getApiInstance()
	{
		if (!$this->gplusClient)
		{
			JLoader::load('Google_Client');
			JLoader::load('Google_PlusService');

			$this->gplusClient = new google_api\Google_Client;

			$sitename = JFactory::getConfig()->get('sitename');
			$this->gplusClient->setApplicationName($sitename);

			$this->gplusClient->setClientId($this->client_id);
			$this->gplusClient->setClientSecret($this->client_secret);
			$this->gplusClient->setDeveloperKey($this->developer_key);

			$requestVisibleActions = array(
							'http://schemas.google.com/AddActivity'
			);

			$this->gplusClient->setRequestVisibleActions($requestVisibleActions);

			if ((isset($this->channel->id)) && ($this->channel->id))
			{
				require_once dirname(__FILE__) . '/../../controllers/gpluschannels.php';

				$url = AutotweetControllerGplusChannels::getCallbackUrl($this->channel->id);
				$this->gplusClient->setRedirectUri($url);
			}

			$access_token = $this->getAccessToken();

			if (!empty($access_token))
			{
				$this->gplusClient->setAccessToken($access_token);
			}

			$this->gplus = new google_api\Google_PlusService($this->gplusClient);
		}

		return $this->gplusClient;
	}

	/**
	 * isAuth()
	 *
	 * @return	bool
	 */
	public function isAuth()
	{
		$ch = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
		$access_token = $this->getAccessToken();

		if (empty($access_token))
		{
			$this->access_token = null;

			return false;
		}

		try
		{
			$this->getApiInstance();

			// First try
			$isExpired = $this->gplusClient->isAccessTokenExpired();

			if (!$isExpired)
			{
				$user = $this->getUser();

				return true;
			}

			$this->_refreshToken();

			// Second try, and the last
			$isExpired = $this->gplusClient->isAccessTokenExpired();

			if ($isExpired)
			{
				// Invalidating access_token
				$ch->setToken($this->channel->id, 'access_token', '');
			}
			else
			{
				$user = $this->getUser();

				// We Ok, and it's new one!
				$ch->setToken($this->channel->id, 'access_token', $this->getAccessToken());

				return true;
			}
		}
		catch (Exception $e)
		{
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::ERROR, $e->getMessage());

			// Invalidating access_token
			$ch->setToken($this->channel->id, 'access_token', '');
		}

		return false;
	}

	/**
	 * _refreshToken()
	 *
	 * @return	void
	 */
	private function _refreshToken()
	{
		$this->access_token = $this->gplusClient->getAccessToken();
		$access_token = json_decode($this->access_token);

		$this->gplusClient->refreshToken($access_token->refresh_token);

		$this->access_token = $this->gplusClient->getAccessToken();
	}

	/**
	 * authenticate
	 *
	 * @param   string  $code  Param
	 *
	 * @return	bool
	 */
	public function authenticate($code)
	{
		$this->getApiInstance();

		$this->access_token = $this->gplusClient->authenticate($code);

		$ch = F0FTable::getAnInstance('Channel', 'AutoTweetTable');

		if ($this->access_token)
		{
			$ch->setToken($this->channel->id, 'access_token', $this->access_token);

			return true;
		}
		else
		{
			// Invalidating access_token
			$ch->setToken($this->channel->id, 'access_token', '');
		}

		return false;
	}

	/**
	 * getAuthorizationUrl
	 *
	 * @return	string
	 */
	public function getAuthorizationUrl()
	{
		$this->getApiInstance();

		return $this->gplusClient->createAuthUrl();
	}

	/**
	 * getUser
	 *
	 * @return	object
	 */
	public function getUser()
	{
		if (!$this->me)
		{
			$this->me = $this->gplus->people->get('me');
		}

		return $this->me;
	}

	/**
	 * getExpiresIn
	 *
	 * @return	string
	 */
	public function getExpiresIn()
	{
		if (($this->access_token) && (!empty($this->access_token)))
		{
			$access_token = json_decode($this->access_token);

			$created = $access_token->created;
			$expires_in = $access_token->expires_in;

			$expires_in += $created;

			return JHtml::_('date', $expires_in, JText::_('COM_AUTOTWEET_DATE_FORMAT'));
		}

		return null;
	}

	/**
	 * sendMessage
	 *
	 * @param   string  $message  Param
	 * @param   array   $data     Param
	 *
	 * @return	array
	 */
	public function sendMessage($message, $data)
	{
		$isAuth = $this->isAuth();

		if (!$isAuth)
		{
			return array(
				false,
				JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_NOT_AUTH_ERR')
			);
		}

		$result = array(false, 'Gplus Unknown', null);

		try
		{
			$image_url = $data->image_url;
			$media_mode = $this->getMediaMode();

			$moment_body = new google_api\Google_Moment;
			$moment_body->setType("http://schemas.google.com/AddActivity");

			$item_scope = new google_api\Google_ItemScope;

			if ($this->channel->params->get('schemaorg_url'))
			{
				$item_scope->setUrl($data->org_url);
			}
			else
			{
				$item_scope->setId(md5($message));
				$item_scope->setType("http://schemas.google.com/AddActivity");

				$item_scope->setName($message);
				$item_scope->setDescription($data->title);
				$item_scope->setText($data->fulltext);

				if (($media_mode != 'message') && !empty($image_url))
				{
					$item_scope->setImage($image_url);
				}
			}

			$moment_body->setTarget($item_scope);
			$momentResult = $this->gplus->moments->insert('me', 'vault', $moment_body);

			$result = array(true, $momentResult['kind'] . ' - ' . $momentResult['id'], null);
		}
		catch (Exception $e)
		{
			return array(
							false,
							$e->getMessage()
			);
		}

		return $result;
	}

	/**
	 * hasWeight
	 *
	 * @return	bool
	 */
	public function hasWeight()
	{
		return true;
	}

	/**
	 * includeHashTags
	 *
	 * @return  bool
	 */
	public function includeHashTags()
	{
		return $this->channel->params->get('hashtags', true);
	}
}
