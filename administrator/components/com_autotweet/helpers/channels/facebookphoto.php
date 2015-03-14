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

JLoader::import('facebookbase', dirname(__FILE__));

/**
 * FacebookPhotoChannelHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class FacebookPhotoChannelHelper extends FacebookBaseChannelHelper
{
	/**
	 * sendMessage.
	 *
	 * @param   string  $message  Params
	 * @param   object  $data     Params
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public function sendMessage($message, $data)
	{
		return $this->sendFacebookMessage($message, $data->title, $data->fulltext, $data->url, $data->org_url, $data->image_url, $this->getMediaMode(), $data);
	}

	// Internal service functions

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
	 *
	 * @since	1.5
	 */
	protected function sendFacebookMessage($message, $title, $text, $url, $org_url, $image_url, $media_mode, &$post)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'sendFacebookMessage', $message);

		if (empty($image_url))
		{
			return array(
				true,
				'No photo.'
			);
		}

		$fb_id = $this->get('fbchannel_id');
		$fbalbum_id = $this->get('fbalbum_id');
		$fb_token = $this->get('fbchannel_access_token');

		$result = null;
		$imagefile = $this->loadImage($image_url);

		if (!$imagefile)
		{
			return array(
				true,
				'No photo.'
			);
		}

		$status_type = array(
			'value'     => 'added_photos'
		);

		// Photo object: /user/photos
		$arguments = array(
			'message' => $message,
			'image' => '@' . $imagefile,
			'access_token' => $fb_token,
			'type' => 'photo',
			'created_time' => JFactory::getDate()->toISO8601(),
			'status_type' => json_encode($status_type)
		);

		$isUserChannel = ($this->get('channel_type') == 'User');

		if ($isUserChannel)
		{
			$privacy = $this->get('sharedwith', 'EVERYONE');
			$privacy = array('value' => $privacy);
			$arguments['privacy'] = json_encode($privacy);
		}

		$target_id = $post->xtform->get('target_id');

		if ($target_id)
		{
			$this->addTargetArguments($arguments, $target_id);
		}

		try
		{
			$api = $this->getApiInstance();
			$api->setFileUploadSupport(true);

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
				if ($fbalbum_id)
				{
					$result = $this->getApiInstance()->api("/{$fbalbum_id}/photos", 'post', $arguments);
				}
				else
				{
					$result = $this->getApiInstance()->api("/{$fb_id}/photos", 'post', $arguments);
				}

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

		$this->cleanTmpImage($imagefile);

		return $result;
	}
}
