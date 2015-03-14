<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Vk, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * AutoTweet Vk channel.
**/

JLoader::import('channel', dirname(__FILE__));

JLoader::register('VK', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'VK/VK.php');
JLoader::register('VKException', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'VK/VKException.php');

/**
 * VkChannelHelper
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class VkChannelHelper extends ChannelHelper
{
	const API_SETTINGS = 'wall,photos,audio,video,pages,offline';

	protected $vk = null;

	protected $_access_token_json = null;

	protected $_access_token = null;

	protected $is_auth = null;

	/**
	 * ChannelHelper.
	 *
	 * @param   object  $channel  Params.
	 */
	public function __construct($channel)
	{
		parent::__construct($channel);

		$access_token_json = $this->get('access_token');
		$this->setJsonAccessToken($access_token_json);
	}

	/**
	 * getAuthorizeUrl
	 *
	 * @param   int     $channelId  Param
	 * @param   string  $type       Param
	 *
	 * @return	string
	 */
	public function getAuthorizeUrl($channelId, $type = 'code')
	{
		if ($type == 'code')
		{
			$url = AutotweetControllerVkChannels::getCallbackUrl($channelId);
		}
		else
		{
			$url = AutotweetControllerVkChannels::getCallbackUrlStandalone($channelId);
		}

		return $this->_getAuthorizeUrl($url, $type);
	}

	/**
	 * _getAuthorizeUrl
	 *
	 * @param   string  $url   Param
	 * @param   string  $type  Param
	 *
	 * @return	string
	 */
	private function _getAuthorizeUrl($url, $type = 'code')
	{
		$vk = $this->getApiInstance();
		$authorize_url = $vk->getAuthorizeURL(
				self::API_SETTINGS,
				$url
		);

		// .... scope=offline&redirect_uri=local-server.extly.net&response_type=code

		$api_settings = rawurlencode(self::API_SETTINGS);
		$p = strpos($authorize_url, $api_settings);
		$authorize_url = substr($authorize_url, 0, $p + strlen($api_settings));
		$authorize_url .= '&response_type=' . $type . '&redirect_uri=' . rawurlencode($url);

		return $authorize_url;
	}

	/**
	 * getAuthorizeUrl
	 *
	 * @param   int     $channelId  Param
	 * @param   string  $type       Param
	 *
	 * @return	string
	 */
	public function getAuthorizeUrlStandalone($channelId, $type = 'token')
	{
		return $this->getAuthorizeUrl($channelId, $type);
	}

	/**
	 * getAuthorizeUrlJsAppStandalone
	 *
	 * @param   int     $clientId  Param
	 * @param   string  $editUrl   Param
	 * @param   string  $type      Param
	 *
	 * @return	string
	 */
	public function getAuthorizeUrlJsAppStandalone($clientId, $editUrl, $type = 'token')
	{
		if (F0FPlatform::getInstance()->isCli())
		{
			$routeHelp = RouteHelp::getInstance();
			$editUrl = $routeHelp->getRoot() . '/' . $editUrl;
		}
		else
		{
			$editUrl = JUri::base() . $editUrl;
		}

		$editUrl = rawurlencode($editUrl);

		$scope = rawurlencode(self::API_SETTINGS);

		$authorizeUrlFormat = 'http://api.vkontakte.ru/oauth/authorize?client_id=%s&scope=%s&response_type=%s&redirect_uri=%s';
		$editUrl = sprintf(
				$authorizeUrlFormat,
				$clientId,
				$scope,
				$type,
				$editUrl
		);

		return $editUrl;
	}

	/**
	 * getAuthorizeUrlJsAppStandalone
	 *
	 * @param   int     $clientId  Param
	 * @param   string  $editUrl   Param
	 * @param   string  $type      Param
	 *
	 * @return	string
	 */
	public function getAuthorizeUrlDesktopStandalone($clientId, $editUrl, $type = 'token')
	{
		$editUrl = rawurlencode($editUrl);

		$scope = rawurlencode(self::API_SETTINGS);

		$authorizeUrlFormat = 'http://api.vkontakte.ru/oauth/authorize?client_id=%s&scope=%s&response_type=%s&redirect_uri=%s';
		$editUrl = sprintf(
				$authorizeUrlFormat,
				$clientId,
				$scope,
				$type,
				$editUrl
		);

		return $editUrl;
	}

	/**
	 * getJsonAccessToken
	 *
	 * @param   string  $code  Param
	 * @param   string  $url   Param
	 *
	 * @return	string
	 */
	public function getJsonAccessToken($code = null, $url = null)
	{
		if (empty($code))
		{
			return $this->_access_token_json;
		}

		$vk = $this->getApiInstance();
		$access_token = $vk->getAccessToken($code, $url);

		return json_encode($access_token);
	}

	/**
	 * setAccessTokenJson
	 *
	 * @param   string  $access_token_json  Param
	 *
	 * @return	string
	 */
	public function setJsonAccessToken($access_token_json)
	{
		$this->_access_token_json = null;
		$this->_access_token = json_decode($access_token_json);

		if ($this->_access_token)
		{
			$this->_access_token_json = $access_token_json;
		}
	}

	/**
	 * getAccessToken
	 *
	 * @return	string
	 */
	public function getAccessToken()
	{
		if (($this->_access_token) && (isset($this->_access_token->access_token)))
		{
			return $this->_access_token->access_token;
		}

		return null;
	}

	/**
	 * getUserId
	 *
	 * @return	int
	 */
	public function getUserId()
	{
		if (($this->_access_token) && (isset($this->_access_token->user_id)))
		{
			return $this->_access_token->user_id;
		}

		return null;
	}

	/**
	 * getExpiresIn
	 *
	 * @return	bool
	 */
	public function getExpiresIn()
	{
		if (($this->_access_token) && (isset($this->_access_token->expires_in)))
		{
			return $this->_access_token->expires_in;
		}

		return null;
	}

	/**
	 * isAuth()
	 *
	 * @return	bool
	 */
	public function isAuth()
	{
		$access_token = $this->getAccessToken();

		if (empty($access_token))
		{
			$this->_access_token_json = null;
			$this->_access_token = null;

			return false;
		}

		try
		{
			$vk = $this->getApiInstance();

			return $vk->isAuth();
		}
		catch (Exception $e)
		{
			$this->_access_token_json = null;
			$this->_access_token = null;

			// Invalidating access_token
			if ((isset($this->channel->id)) && ($this->channel->id))
			{
				$ch = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
				$result = $ch->load($this->channel->id);

				if (!$result)
				{
					throw new Exception('Channel failed to load!');
				}

				$params = $ch->params;
				$registry = new JRegistry;
				$registry->loadString($params);
				$registry->set('access_token', null);
				$ch->bind(array('params' => (string) $registry));
				$ch->store();
			}

			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::ERROR, $e->getMessage());

			return false;
		}
	}

	/**
	 * getUserSettings()
	 *
	 * @return	object
	 */
	public function getUserSettings()
	{
		$vk = $this->getApiInstance();
		$user_id = $this->getUserId();
		$url = null;

		if ($user_id)
		{
			$parameters = array(
				'user_id' => $user_id
			);
		}

		$response = $vk->api(
				'getUserSettings',
				$parameters
		);

		$result = $this->_processResponse($response);

		if ($result[0])
		{
			$parameters = array(
				'uids' => $user_id,
				'fields' => 'uid,first_name,last_name,nickname,domain'
			);

			$response2 = $vk->api(
					'getProfiles',
					$parameters
			);

			$result2 = $this->_processResponse($response2);

			if ($result2[0])
			{
				$url = 'https://vk.com/' . $result2[2][0]['domain'];
			}
		}

		$message = array(
			'status' => $result2[0],
			'error_message' => $result[1],
			'user' => $user_id,
			'url' => $url
		);

		return $message;
	}

	/**
	 * _processResponse
	 *
	 * @param   array  $response  Param
	 *
	 * @return	object
	 */
	protected function _processResponse($response)
	{
		$result = array(false, 'Vk Unknown', null);

		if (array_key_exists('response', $response))
		{
			$result[0] = true;

			$r = $response['response'];

			if (is_array($r))
			{
				$result[1] = null;

				if (array_key_exists('post_id', $r))
				{
					$result[1] = 'Ok (' . $r['post_id'] . ')';
				}
				else
				{
					$result[2] = $r;
				}
			}
			else
			{
				$result[1] = 'Ok (' . $r . ')';
			}
		}
		elseif (array_key_exists('error', $response))
		{
			$result[1] = 'Error (' . $response['error']['error_code'] . ' ' . $response['error']['error_msg'] . ')';
		}

		return $result;
	}

	/**
	 * Internal service functions
	 *
	 * @return	object
	 */
	protected function getApiInstance()
	{
		if (!$this->vk)
		{
			JLoader::load('VK');
			JLoader::load('VKException');

			$this->vk = new vladkensVK\VK(
					$this->get('application_id'),
					$this->get('secure_key'),
					$this->getAccessToken()
			);
		}

		return $this->vk;
	}

	/**
	 * sendMessage
	 *
	 * @param   string  $message  Param
	 * @param   array   $data     Param
	 *
	 * @return	bool
	 */
	public function sendMessage($message, $data)
	{
		try
		{
			$image_url = $data->image_url;
			$media_mode = $this->getMediaMode();

			if (($media_mode != 'message') && !empty($image_url))
			{
				$result = $this->_sendVkMessageWithImage($message, $image_url, $data->url);

				return $result;
			}
			else
			{
				return $this->_sendVkMessage($message, $image_url);
			}
		}
		catch (Exception $e)
		{
			return array(
							false,
							$e->getMessage()
			);
		}
	}

	/**
	 * _sendVkMessage
	 *
	 * @param   string  $status_msg  Param
	 * @param   string  $image_url   Param
	 *
	 * @return	array
	 */
	protected function _sendVkMessage($status_msg, $image_url)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, '_sendVkMessage', $status_msg);

		$vk = $this->getApiInstance();

		try
		{
			$gid = $this->channel->params->get('vkgroup_id');

			$parameters = array(
							'owner_id' => ($gid ? -1 * $gid : $this->getUserId()),
							'message' => $status_msg,
							'from_group' => ($gid ? 1 : 0)
				);
			$result = $vk->api('wall.post', $parameters);

			return $this->_processResponse($result);
		}
		catch (Exception $e)
		{
			return array(
							false,
							$e->getMessage()
			);
		}

		return array(
			false,
			'Failed _sendVkMessage'
		);
	}

	/**
	 * uploadImage
	 *
	 * @param   string  $image_url  Param
	 *
	 * @return	object
	 */
	public function uploadImage($image_url)
	{
		if (empty($image_url))
		{
			return false;
		}

		$imagefile = $this->loadImage($image_url);

		if (!$imagefile)
		{
			return false;
		}

		$status = false;

		try
		{
			$uid = $this->getUserId();

			$gid = $this->channel->params->get('vkgroup_id');

			$vk = $this->getApiInstance();

			if ($gid)
			{
				$parameters = array(
								'gid' => $gid
				);
			}
			else
			{
				$parameters = array(
								'uid' => $uid
				);
			}

			$result = $vk->api('photos.getWallUploadServer', $parameters);

			if (array_key_exists('response', $result))
			{
				$response = $result['response'];

				if (array_key_exists('upload_url', $response))
				{
					$upload_url = $response['upload_url'];

					$ch = curl_init();
					$data = array(
									'photo' => '@' . $imagefile
					);
					curl_setopt($ch, CURLOPT_URL, $upload_url);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$status = curl_exec($ch);

					if ((!$status) && (curl_errno($ch)))
					{
						$logger = AutotweetLogger::getInstance();
						$logger->log(JLog::ERROR, curl_error($ch));
					}
					else
					{
						$status = json_decode($status);
						$photo = json_decode($status->photo);

						$parameters = array(
										'server' => $status->server,
										'photo' => $status->photo,
										'hash' => $status->hash,
						);

						if ($gid)
						{
							$parameters['gid'] = $gid;
						}
						else
						{
							$parameters['uid'] = $uid;
						}

						$status = $vk->api('photos.saveWallPhoto', $parameters);

						if ( (array_key_exists('response', $status)) && (is_array($status['response'])) && (count($status['response']) == 1))
						{
							$status = $status['response'][0];
						}
					}

					curl_close($ch);
				}
			}

			if (array_key_exists('error', $result))
			{
				$message = 'Error (' . $result['error']['error_code'] . ' ' . $result['error']['error_msg'] . ')';
				$logger = AutotweetLogger::getInstance();
				$logger->log(JLog::ERROR, $message);
			}
		}
		catch (Exception $e)
		{
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::ERROR, $e->getMessage());
		}

		$this->cleanTmpImage($imagefile);

		return $status;
	}

	/**
	 * _sendVkMessageWithImage
	 *
	 * @param   string  $status_msg  Param
	 * @param   string  $image_url   Param
	 * @param   string  $url         Param
	 *
	 * @return	array
	 */
	protected function _sendVkMessageWithImage($status_msg, $image_url, $url)
	{
		$vk = $this->getApiInstance();

		try
		{
			$owner_id = $this->getUserId();
			$result = $this->uploadImage($image_url);

			if (!$result)
			{
				return array(
								false,
								'Failed to upload!'
				);
			}

			$photo_param = $result['id'] . ',' . $url;

			$gid = $this->channel->params->get('vkgroup_id');

			$parameters = array(
							'owner_id' => ($gid ? -1 * $gid : $this->getUserId()),
							'message' => $status_msg,
							'from_group' => ($gid ? 1 : 0),
							'attachments' => $photo_param
				);
			$result = $vk->api('wall.post', $parameters);

			return $this->_processResponse($result);
		}
		catch (Exception $e)
		{
			return array(
							false,
							$e->getMessage()
			);
		}

		return array(
			false,
			'Failed _sendVkMessageWithImage'
		);
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

	/**
	 * getGroups()
	 *
	 * @return	array
	 */
	public function getGroups()
	{
		$vk = $this->getApiInstance();

		$response = $vk->api('getGroupsFull');

		$response = $this->_processResponse($response);

		if ($response[0])
		{
			$groups = $response[2];
			$final = array();

			foreach ($groups as $group)
			{
				if ($group['type'] == 'group')
				{
					$url = 'https://vk.com/' . $group['screen_name'];
					$group['url'] = $url;

					$final[] = $group;
				}
			}

			$response[2] = $final;
		}

		$message = array(
			'status' => $response[0],
			'error_message' => $response[1],
			'items' => $response[2]
		);

		return $message;
	}
}
