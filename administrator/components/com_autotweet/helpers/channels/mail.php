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
 * AutoTweet e-mail channel.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class MailChannelHelper extends ChannelHelper
{
	const MAX_CHARS_SUBJECT = 76;

	protected $mailer;

	/**
	 * AutotweetMailChannel
	 *
	 * @param   object  &$channel  Param
	 */
	public function __construct(&$channel)
	{
		parent::__construct($channel);

		$this->mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array(
						$config->get($this->get('mail_sender_email')),
						$config->get($this->get('mail_sender_name'))
		);
		$this->mailer->setSender($sender);
	}

	/**
	 * sendMessage
	 *
	 * @param   string  $message  Param
	 * @param   string  $data     Param
	 *
	 * @return	bool
	 */
	public function sendMessage($message, $data)
	{
		return $this->sendMailMessage($this->get('mail_sender_email'), $this->get('mail_sender_name'), $this->get('mail_recipient_email'), $message, $data->title, $data->fulltext, $data->url, $data->image_url, null, $this->getMediaMode());
	}

	/**
	 * sendMailMessage
	 *
	 * @param   string  $sender_mail     Param
	 * @param   string  $sender_name     Param
	 * @param   string  $recipient_mail  Param
	 * @param   string  $message         Param
	 * @param   string  $title           Param
	 * @param   string  $fulltext        Param
	 * @param   string  $url             Param
	 * @param   object  $image_url       Param
	 * @param   object  $mode            Param
	 * @param   object  $media_mode      Param
	 *
	 * @return	boolean
	 */
	protected function sendMailMessage($sender_mail, $sender_name, $recipient_mail, $message, $title, $fulltext, $url, $image_url, $mode, $media_mode)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'sendMailMessage', $message);

		$result = null;

		$this->mailer->isHtml(true);
		$this->mailer->SetFrom($sender_mail, $sender_name);
		$this->mailer->AddAddress($recipient_mail);
		$this->mailer->Subject = TextUtil::truncString($title, self::MAX_CHARS_SUBJECT);

		$body = $message;

		if (!empty($image_url))
		{
			$body .= '<br/><br/><a href="' . $url . '"><img src="' . $image_url . '"></a>';
		}

		if (!empty($url))
		{
			$body .= '<br/><br/><a href="' . $url . '">' . $url . '</a>';
		}

		$body .= '<br/><br/>';

		$this->mailer->Body = $body;

		if (!$this->mailer->Send())
		{
			$result = array(
							false,
							'error sending mail'
			);
		}
		else
		{
			$result = array(
							true,
							'successfully sent'
			);
		}

		return $result;
	}
}
