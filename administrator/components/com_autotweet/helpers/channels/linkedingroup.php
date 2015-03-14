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

JLoader::import('linkedinbase', dirname(__FILE__));

/**
 * LinkedinGroupChannelHelper - AutoTweet LinkedIn channel for posts to groups.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class LinkedinGroupChannelHelper extends LinkedinBaseChannelHelper
{
	/**
	 * sendMessage.
	 *
	 * @param   string  $message  Params
	 * @param   object  $data     Params
	 *
	 * @return  boolean
	 */
	public function sendMessage($message, $data)
	{
		return $this->sendLinkedinMessage($message, $data->title, $data->fulltext, $data->url, $data->image_url, $this->getMediaMode());
	}

	/**
	 * sendLinkedinMessage.
	 *
	 * @param   string  $message     Params
	 * @param   string  $title       Params
	 * @param   string  $text        Params
	 * @param   string  $url         Params
	 * @param   string  $image_url   Params
	 * @param   string  $media_mode  Params
	 *
	 * @return  boolean
	 */
	protected function sendLinkedinMessage($message, $title, $text, $url, $image_url, $media_mode)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'sendLinkedinMessage', $message);

		$result = null;

		// Post message and/or media
		switch ($media_mode)
		{
			case 'attachment':
				$post_attach = true;
				$post_msg = false;
				break;
			case 'both':
				$post_msg = true;
				$post_attach = true;
			case 'message':
			default:
				$post_msg = true;
				$post_attach = false;
		}

		// Strlen shorter than JString::strlen for UTF-8  - 2 char languages E.g. Hebrew
		$text = TextUtil::truncString($text, self::MAX_CHARS_TEXT);

		try
		{
			$api = $this->getApiInstance();

			if ($post_attach)
			{
				$response = $api->createPost($this->get('group_id'), $title, $text, $url);
			}
			else
			{
				$response = $api->createPost($this->get('group_id'), $title, $text, $url, $image_url);
			}

			if ($response['success'] === true)
			{
				$result = array(
								true,
								'OK'
				);
			}
			else
			{
				$http_code = $response['info']['http_code'];

				if ($http_code == 202)
				{
					$result = array(
									true,
									'202 - Accepted / waiting for approval'
					);
				}
				else
				{
					$msg = $http_code . ' - ' . JText::_('COM_AUTOTWEET_HTTP_ERR_' . $response['info']['http_code']);
					$result = array(
									false,
									$msg
					);
				}
			}
		}
		catch (Exception $e)
		{
			$result = array(
							false,
							$e->getMessage()
			);
		}

		return $result;
	}
}
