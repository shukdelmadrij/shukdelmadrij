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

JLoader::import('channel', dirname(__FILE__));
JLoader::register('LinkedIn', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Simple-LinkedIn/linkedin_3.2.0.class.php');

/**
 * LinkedinBaseChannelHelper - AutoTweet LinkedIn channel base class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class LinkedinBaseChannelHelper extends ChannelHelper
{
	// 200 * 90% - To be sure
	const MAX_CHARS_TITLE = 180;

	// Max - 10% safety
	const MAX_CHARS_TEXT = 360;

	const MAX_CHARS_DESC = 256;

	protected $linkedin = null;

	/**
	 * getApiInstance
	 *
	 * @return	object
	 *
	 * @since	1.5
	 */
	protected function getApiInstance()
	{
		if (!$this->linkedin)
		{
			$API_CONFIG = array(
							'appKey' => $this->get('api_key'),
							'appSecret' => $this->get('secret_key'),
							'callbackUrl' => null
			);

			JLoader::load('LinkedIn');
			$this->linkedin = new SimpleLinkedIn\LinkedIn($API_CONFIG);

			$ACCESS_TOKEN = array(
							'oauth_token' => $this->get('oauth_user_token'),
							'oauth_token_secret' => $this->get('oauth_user_secret')
			);
			$this->linkedin->setTokenAccess($ACCESS_TOKEN);
		}

		return $this->linkedin;
	}
}
