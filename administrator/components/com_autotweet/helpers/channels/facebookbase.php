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

/**
 * FacebookBaseChannelHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class FacebookBaseChannelHelper extends ChannelHelper
{
	protected $facebook = null;

	const MAX_CHARS_NAME = 255;

	/**
	 * getApiInstance.
	 *
	 * @return	object
	 */
	protected function getApiInstance()
	{
		if (!$this->facebook)
		{
			$params = array(
							'appId' => $this->get('app_id', 'My-App-ID'),
							'secret' => $this->get('secret', 'My-App-Secret'),
							'cookie' => true
			);

			JLoader::import('facebook-php-sdk.facebook', dirname(__FILE__));
			$this->facebook = new facebookphpsdk\Facebook($params);
		}

		return $this->facebook;
	}

	/**
	 * addTargetArguments.
	 *
	 * @param   array  &$arguments  Params
	 * @param   int    $target_id   Params
	 *
	 * @return	void
	 */
	protected function addTargetArguments(&$arguments, $target_id)
	{
		$target = F0FModel::getTmpInstance('Targets', 'AutoTweetModel')->getItem($target_id);

		$criterias = $target->xtform->toArray();

		if (is_array($criterias))
		{
			$targeting = array();

			foreach ($criterias as $key => $criteria)
			{
				$fbkey = str_replace('fb', '', $key);
				$criteria = urldecode($criteria);
				$criteria = json_decode($criteria);

				if (!array_key_exists($fbkey, $targeting))
				{
					$targeting[$fbkey] = array();
				}

				$value = $criteria->criteriaValue;

				if (empty($value))
				{
					$value = $criteria->criteriaValueText;
				}

				$targeting[$fbkey][] = $value;
			}

			if ($targeting)
			{
				$arguments['targeting'] = json_encode($targeting);
			}
		}

		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'Targeting', $arguments);
	}

	/**
	 * sendFacebookMessage.
	 *
	 * @param   string  $message     Params
	 * @param   string  $title       Params
	 * @param   string  $text        Params
	 * @param   string  $url         Params
	 * @param   string  $org_url     Params
	 * @param   string  $image_url   Params
	 * @param   string  $media_mode  Params
	 * @param   object  &$post       Params
	 *
	 * @return	boolean
	 */
	protected function sendFacebookOG($message, $title, $text, $url, $org_url, $image_url, $media_mode, &$post)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'sendFacebookOG', $message);

		$fb_id = $this->get('fbchannel_id');
		$fb_token = $this->get('fbchannel_access_token');

		$arguments = array(
			'access_token' => $fb_token,
			'article' => $org_url,
			'message' => $text,
			'fb:explicitly_shared' => ($this->channel->params->get('og_explicitly_shared', true) ? true : false)
		);

		if (!empty($image_url))
		{
			$arguments['image[0][url]'] = $image_url;
			$arguments['image[0][user_generated]'] = ($this->channel->params->get('og_user_generated', true) ? true : false);
		}

		$result = null;

		try
		{
			// Simulated
			if ($this->channel->params->get('use_own_api') == 0)
			{
				$this->getApiInstance()->api("/me/permissions");
				$result = array(
								true,
								JText::_('COM_AUTOTWEET_VIEW_SIMULATED_OK')
				);
			}
			else
			{
				$result = $this->getApiInstance()->api("/{$fb_id}/news.publishes", 'post', $arguments);
				$msg = 'Facebook id: ' . $result['id'];
				$result = array(
								true,
								$msg
				);
			}
		}
		catch (Exception $e)
		{
			$result = array(
							false,
							$e->getCode() . ' - ' . $e->getMessage()
			);
		}

		return $result;
	}

	/**
	 * isUserProfile.
	 *
	 * @return	boolean
	 */
	protected function isUserProfile()
	{
		$fb_id = $this->get('fbchannel_id');

		$user = $this->getApiInstance()->api('/' . $fb_id);

		return ($user['id'] == $fb_id) && (array_key_exists('username', $user));
	}
}
