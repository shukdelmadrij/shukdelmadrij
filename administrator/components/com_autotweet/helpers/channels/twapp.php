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
 * TwAppHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class TwAppHelper
{
	private $_twitter = null;

	protected $consumer_key = null;

	protected $consumer_secret = null;

	protected $access_token = null;

	protected $access_token_secret = null;

	/**
	 * TwAppHelper.
	 *
	 * @param   string  $consumer_key         Params.
	 * @param   string  $consumer_secret      Params.
	 * @param   string  $access_token         Params.
	 * @param   string  $access_token_secret  Params.
	 */
	public function __construct($consumer_key, $consumer_secret, $access_token = null, $access_token_secret = null)
	{
		require_once dirname(__FILE__) . '/tmhOAuth/tmhOAuth.php';

		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
		$this->access_token = $access_token;
		$this->access_token_secret = $access_token_secret;
	}

	/**
	 * login.
	 *
	 * @return	object
	 */
	public function login()
	{
		if (!$this->_twitter)
		{
			$config = array(
							'consumer_key' => $this->consumer_key,
							'consumer_secret' => $this->consumer_secret
			);

			if ($this->access_token)
			{
				$config['user_token'] = $this->access_token;
			}

			if ($this->access_token_secret)
			{
				$config['user_secret'] = $this->access_token_secret;
			}

			$this->_twitter = new tmhOAuth\tmhOAuth($config);
		}

		return $this->_twitter;
	}

	/**
	 * getApi.
	 *
	 * @return	object
	 */
	public function getApi()
	{
		return $this->_twitter;
	}

	/**
	 * verify.
	 *
	 * @return	boolean
	 */
	public function verify()
	{
		JLoader::register('TwitterChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/twitter.php');

		$api = $this->login();
		$code = $api->request('GET', $api->url('1.1/account/verify_credentials'));
		$response = TwitterChannelHelper::_processResponse($code, $api);

		if ($response[0])
		{
			// Process response-headers
			$result = TwitterChannelHelper::_processHeaders($code, $api);

			$user = $result[2];
			$url = 'https://twitter.com/' . $user->screen_name;

			$message = array(
				'status' => true,
				'error_message' => $result[1],
				'user' => $user,
				'url' => $url
			);

			return $message;
		}

		$message = array(
			'status' => false,
			'error_message' => $response[1]
		);

		return $message;
	}

	/**
	 * checkTimestamp.
	 *
	 * @return	boolean
	 */
	public static function checkTimestamp()
	{
		// Get component parameter - Offline mode
		$version_check = EParameter::getComponentParam(CAUTOTWEETNG, 'version_check', 1);

		if (!$version_check)
		{
			return '998 Offline';
		}

		$appHelper = new TwAppHelper('TOCHECK', 'TOCHECK', 'TOCHECK', 'TOCHECK');
		$result = $appHelper->verify();
		$api = $appHelper->getApi();

		$dateCompare = 999;

		if (array_key_exists('headers', $api->response))
		{
			$headers = $api->response['headers'];
			$twitterDate = $headers['date'];
			$twistamp = strtotime($twitterDate);
			$srvstamp = time();
			$dateCompare = abs($srvstamp - $twistamp);
		}

		return $dateCompare;
	}

	/**
	 * getUserTimeline.
	 *
	 * @param   string  $twUsername   Param
	 * @param   int     $twMaxTweets  Param
	 *
	 * @return	array
	 */
	public function getUserTimeline($twUsername, $twMaxTweets)
	{
		JLoader::register('TwitterChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/twitter.php');

		$api = $this->login();
		$code = $api->request('GET', $api->url('1.1/statuses/user_timeline'), array(
						'screen_name' => $twUsername,
						'count' => $twMaxTweets
			)
		);
		$response = TwitterChannelHelper::processJsonResponse($code, $api);

		if ($response[0])
		{
			// Process response-headers
			return $response[1];
		}

		return null;
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

		// Send request for a request token
		$this->_twitter->request(
				'POST',
				$this->_twitter->url('oauth/request_token', ''),
				array(
				// Pass a variable to set the callback
				// 'oauth_callback' => $oauth_callback
				)
		);

		if ($this->_twitter->response['code'] == 200)
		{
			// Get and store the request token
			$response = $this->_twitter->extract_params($this->_twitter->response['response']);

			$session = JFactory::getSession();
			$session->set('authtoken', $response['oauth_token']);
			$session->set('authsecret', $response['oauth_token_secret']);

			// State is now 1
			$session->set('twitter-authstate', 1);

			// Redirect the user to Twitter to authorize
			$url = $this->_twitter->url('oauth/authorize', '') . '?oauth_token=' . $response['oauth_token'];

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
		$user_token = $session->get('authtoken');
		$user_secret = $session->get('authsecret');

		$this->access_token = $user_token;
		$this->token_secret = $user_secret;
		$this->login();

		$input = new F0FInput;
		$oauth_verifier = $input->get('oauth_verifier');

		// Send request for an access token
		$this->_twitter->request('POST', $this->_twitter->url('oauth/access_token', ''), array(

						// Pass the oauth_verifier received from Twitter
						'oauth_verifier' => $oauth_verifier
			)
		);

		if ($this->_twitter->response['code'] == 200)
		{
			// Get the access token and store it in a cookie
			$response = $this->_twitter->extract_params($this->_twitter->response['response']);

			$access_token = $response['oauth_token'];
			$access_token_secret = $response['oauth_token_secret'];

			return array(
							'access_token' => $access_token,
							'access_token_secret' => $access_token_secret
			);
		}

		return false;
	}
}
