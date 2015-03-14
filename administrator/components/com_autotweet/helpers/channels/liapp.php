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
 * LiAppHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class LiAppHelper
{
	private $_linkedin;

	protected $api_key = null;

	protected $secret_key = null;

	protected $oauth_user_token = null;

	protected $oauth_user_secret = null;

	/**
	 * LiAppHelper.
	 *
	 * @param   string  $api_key            Params.
	 * @param   string  $secret_key         Params.
	 * @param   string  $oauth_user_token   Params.
	 * @param   string  $oauth_user_secret  Params.
	 */
	public function __construct($api_key, $secret_key, $oauth_user_token = null, $oauth_user_secret = null)
	{
		require_once dirname(__FILE__) . '/Simple-LinkedIn/linkedin_3.2.0.class.php';

		$this->api_key = $api_key;
		$this->secret_key = $secret_key;
		$this->oauth_user_token = $oauth_user_token;
		$this->oauth_user_secret = $oauth_user_secret;
	}

	/**
	 * login.
	 *
	 * @return	object
	 */
	public function login()
	{
		if (!$this->_linkedin)
		{
			$API_CONFIG = array(
					'appKey' => $this->api_key,
					'appSecret' => $this->secret_key,
					'callbackUrl' => null
			);

			$this->_linkedin = new SimpleLinkedIn\LinkedIn($API_CONFIG);

			if ($this->oauth_user_token)
			{
				$ACCESS_TOKEN = array(
						'oauth_token' => $this->oauth_user_token,
						'oauth_token_secret' => $this->oauth_user_secret
				);
				$this->_linkedin->setTokenAccess($ACCESS_TOKEN);
			}
		}

		return $this->_linkedin;
	}

	/**
	 * getUser.
	 *
	 * @return	object
	 */
	public function getUser()
	{
		if (empty($this->api_key)
			|| empty($this->secret_key)
			|| empty($this->oauth_user_token)
			|| empty($this->oauth_user_secret))
		{
			return array(false, 'Access Token and/or Token secret not entered (getUser).');
		}

		$result = null;

		try
		{
			$api = $this->login();
			$response = $api->profile('~:(id,first-name,last-name,headline,public-profile-url)');

			if ($response['success'] === true)
			{
				$xml = $response['linkedin'];
				$user = simplexml_load_string($xml);
				$user = json_decode(json_encode($user));
				$url = $user->{'public-profile-url'};

				$result = array(
					'status' => true,
					'error_message' => 'Ok!',
					'user' => $user,
					'url' => $url
				);
			}
			else
			{
				$msg = $response['info']['http_code'] . ' ' . JText::_('COM_AUTOTWEET_HTTP_ERR_' . $response['info']['http_code']);
				$result = array(
					'status' => false,
					'error_message' => $msg
				);
			}
		}
		catch (LinkedInException $e)
		{
			$result = array(
				'status' => false,
				'error_message' => $e->getMessage()
			);
		}

		return $result;
	}

	/**
	 * getMyGroup.
	 *
	 * @return	object
	 */
	public function getMyGroups()
	{
		if (empty($this->api_key)
			|| empty($this->secret_key)
			|| empty($this->oauth_user_token)
			|| empty($this->oauth_user_secret))
		{
			return array(false, 'Access Token and/or Token secret not entered (getMyGroup).');
		}

		$result = null;

		try
		{
			$api = $this->login();
			$response = $api->groupXTDOwnerships();

			if ($response['success'] === true)
			{
				$xml = $response['linkedin'];
				$groups = simplexml_load_string($xml);
				$groups = json_decode(json_encode($groups));

				$result = array();

				if (isset($groups->{'group-membership'}))
				{
					$results = $groups->{'group-membership'};

					if (is_array($results))
					{
						foreach ($results as $group)
						{
							$g = $group->group;
							$g->url = 'https://www.linkedin.com/groups?home=&gid=' . $g->id;

							$result[] = $g;
						}
					}
					elseif (is_object($results))
					{
						$g = $results->group;
						$g->url = 'https://www.linkedin.com/groups?home=&gid=' . $g->id;

						$result[] = $g;
					}
				}
			}
			else
			{
				$msg = $response['info']['http_code'] . ' ' . JText::_('COM_AUTOTWEET_HTTP_ERR_' . $response['info']['http_code']);
				$result = array(false, $msg);

				return $result;
			}

			$response = $api->groupXTDMemberships();

			if ($response['success'] === true)
			{
				$xml = $response['linkedin'];
				$groups = simplexml_load_string($xml);
				$groups = json_decode(json_encode($groups));

				if (isset($groups->{'group-membership'}))
				{
					$results = $groups->{'group-membership'};

					if (is_array($results))
					{
						foreach ($results as $group)
						{
							$g = $group->group;
							$g->url = 'https://www.linkedin.com/groups?home=&gid=' . $g->id;

							$result[] = $g;
						}
					}
					elseif (is_object($results))
					{
						$g = $results->group;
						$g->url = 'https://www.linkedin.com/groups?home=&gid=' . $g->id;

						$result[] = $g;
					}
				}
			}
		}
		catch (LinkedInException $e)
		{
			$result = array('id' => false, 'name' => $e->getMessage());
		}

		return $result;
	}

	/**
	 * getMyCompanies.
	 *
	 * @return	object
	 */
	public function getMyCompanies()
	{
		if (empty($this->api_key)
			|| empty($this->secret_key)
			|| empty($this->oauth_user_token)
			|| empty($this->oauth_user_secret))
		{
			return array(false, 'Access Token and/or Token secret not entered (getMyCompanies).');
		}

		$result = null;

		try
		{
			$api = $this->login();
			$response = $api->company('?is-company-admin=true');

			if ($response['success'] === true)
			{
				$xml = $response['linkedin'];
				$companies = simplexml_load_string($xml);
				$companies = json_decode(json_encode($companies));

				$result = array();

				// One or more companies
				if (isset($companies->company))
				{
					$result = $companies->company;

					// We have an array
					if (is_array($result))
					{
						// Building Urls
						$companies = array();

						foreach ($result as $c)
						{
							$url = 'https://www.linkedin.com/company/' . $c->id;
							$c->url = $url;
							$companies[] = $c;
						}

						return $result;
					}
					else
					{
						// One Company
						// It's an object wrapped in an array

						$url = 'https://www.linkedin.com/company/' . $result->id;
						$result->url = $url;

						return array($result);
					}
				}
			}
			else
			{
				$msg = $response['info']['http_code'] . ' ' . JText::_('COM_AUTOTWEET_HTTP_ERR_' . $response['info']['http_code']);
				$result = array(false, $msg);
			}
		}
		catch (LinkedInException $e)
		{
			$result = array('id' => false, 'name' => $e->getMessage());
		}

		return $result;
	}

	/**
	 * Obtain a request token from Twitter
	 *
	 * @return string
	 */
	public function getRequestTokenUrl()
	{
		$input = new F0FInput;
		$itemid = $input->getInt('Itemid');

		$oauth_callback = 'index.php?option=com_autotweet&view=userchannels';

		if ($itemid)
		{
			$oauth_callback .= '&Itemid=' . $itemid;
		}

		$oauth_callback = RouteHelp::getInstance()->getAbsoluteUrl($oauth_callback);
		$this->_linkedin->setCallbackUrl($oauth_callback);
		$response = $this->_linkedin->retrieveTokenRequest();

		if ($response['info']['http_code'] == 200)
		{
			// Get and store the request token
			$session = JFactory::getSession();
			$session->set('oauth_token', $response['linkedin']['oauth_token']);
			$session->set('oauth_token_secret', $response['linkedin']['oauth_token_secret']);

			// State is now 1
			$session->set('linkedin-authstate', 1);

			// Redirect the user to Twitter to authorize
			$url = $response['linkedin']['xoauth_request_auth_url'] . '?oauth_token=' . $response['linkedin']['oauth_token'];

			return $url;
		}

		return false;
	}

	/**
	 * Obtain a request token from Twitter
	 *
	 * @return string
	 */
	public function getAccessToken()
	{
		$session = JFactory::getSession();

		// Set the request token and secret we have stored
		$oauth_token = $session->get('oauth_token');
		$oauth_token_secret = $session->get('oauth_token_secret');

		$this->oauth_user_token = $oauth_token;
		$this->oauth_user_secret = $oauth_token_secret;
		$this->login();

		$input = new F0FInput;
		$oauth_verifier = $input->get('oauth_verifier');

		// Send request for an access token
		$response = $this->_linkedin->retrieveTokenAccess($oauth_token, $oauth_token_secret, $oauth_verifier);

		if ($response['info']['http_code'] == 200)
		{
			$oauth_user_token = $response['linkedin']['oauth_token'];
			$oauth_user_secret = $response['linkedin']['oauth_token_secret'];

			return array(
							'oauth_user_token' => $oauth_user_token,
							'oauth_user_secret' => $oauth_user_secret
			);
		}

		return false;
	}
}
