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
 * LinkedinCompanyChannelHelper - AutoTweet LinkedIn channel for posts to companies.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class LinkedinCompanyChannelHelper extends LinkedinBaseChannelHelper
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
	 *
	 * @since   1.5
	 */
	protected function sendLinkedinMessage($message, $title, $text, $url, $image_url, $media_mode)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'sendLinkedinMessage', $message);

		$result = null;
		$content = array();

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
				break;
			case 'message':
			default:
				$post_msg = true;
				$post_attach = false;
		}

		// Media: do also not post when text and image are empty
		if ($post_attach && !empty($title) && !empty($url))
		{
			// Prepare content
			$content['title'] = TextUtil::truncString($title, self::MAX_CHARS_TITLE);
			$content['submitted-url'] = $url;
			$content['submitted-image-url'] = $image_url;

			// Strlen shorter than JString::strlen for UTF-8  - 2 char languages E.g. Hebrew
			$text = TextUtil::truncString($text, self::MAX_CHARS_DESC);
			$content['description'] = $text;
		}

		// Message
		if ($post_msg)
		{
			$content['comment'] = $title;
		}

		// Default for visibility
		$private = false;

		try
		{
			$api = $this->getApiInstance();
			$response = $api->companyShare($this->get('company_id'), 'new', $content, $private);

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
