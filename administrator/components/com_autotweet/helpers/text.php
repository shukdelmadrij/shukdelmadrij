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
 * Helper for posts form AutoTweet to channels (twitter, Facebook, ...)
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class TextUtil
{
	/**
	 * truncString.
	 *
	 * @param   string   $text       Param
	 * @param   integer  $max_chars  Param
	 * @param   bool     $withDots   Param
	 *
	 * @return	string
	 */
	public static function truncString($text, $max_chars, $withDots = true)
	{
		$length = JString::strlen($text);

		if ($length > $max_chars)
		{
			if ($withDots)
			{
				// -Dots
				// -2 Utf8 case
				$max_chars = $max_chars - 3 - 2;
			}

			$text = JString::substr($text, 0, $max_chars);

			// Yes, it can return more characters, but utf8_strlen($text) is Ok, so ...
			$l = $max_chars;

			// Strlen shorter than JString::strlen for UTF-8  - 2 char languages E.g. Hebrew
			while (strlen($text) > $max_chars)
			{
				$l--;
				$text = JString::substr($text, 0, $l);
			}

			if ($withDots)
			{
				$text = $text . '...';
			}
		}

		return $text;
	}

	/**
	 * cleanText.
	 *
	 * @param   string  $text  Param
	 *
	 * @return	string
	 */
	public static function cleanText($text)
	{
		// Replace &nbsp;, to avoid #160
		$text = str_replace('&nbsp;', ' ', $text);

		// Strip HTML Tags
		$text = strip_tags($text);

		// Clean up things like &amp;
		$text = html_entity_decode($text);

		// Strip out any url-encoded stuff
		$text = urldecode($text);

		// Replace non-AlNum characters with space - TOO Strict
		// $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);

		// Trim the string of leading/trailing space
		$text = trim($text);

		// Line breaks and Tabs
		$text = str_replace(
				array(
						"\r\n",
						"\r",
						"\n",
						"\t"
			),
			' ',
			$text
		);

		// Removing [img]...[/img]
		$pattern = '/\[[^[]+\][^\[]+\[\/[^[]+\]/is';
		$text = preg_replace($pattern, '', $text);

		// Removing unmatched [img], [/img]
		$pattern = '/\[[^[]+\]/is';
		$text = preg_replace($pattern, '', $text);

		$pattern = '/\[\/[^[]+\]/is';
		$text = preg_replace($pattern, '', $text);

		// Removing {img}...{/img}
		$pattern = '/\{[^{]+\}[^\{]+\{\/[^{]+\}/is';
		$text = preg_replace($pattern, '', $text);

		// Removing unmatched {img}, {/img}
		$pattern = '/\{[^{]+\}/is';
		$text = preg_replace($pattern, '', $text);

		$pattern = '/\\{\/[^{]+\}/is';
		$text = preg_replace($pattern, '', $text);

		// Replace Multiple spaces with single space
		$text = preg_replace('/ +/', ' ', $text);

		return $text;
	}

	/**
	 * getMessageWithUrl
	 *
	 * @param   object  &$channel         Param
	 * @param   object  &$post            Param
	 * @param   string  $short_url        Param
	 * @param   bool    $shorturl_always  Param
	 *
	 * @return	array
	 */
	public static function getMessageWithUrl(&$channel, &$post, $short_url, $shorturl_always)
	{
		$includeHashTags = $channel->includeHashTags();
		$hashtags_chars = 0;
		$hashtags = null;

		if ($includeHashTags)
		{
			$hashtags = $post->xtform->get('hashtags');

			if ($hashtags)
			{
				$hashtags = trim($hashtags);
				$hashtags = self::cleanText($hashtags);
				$hashtags_chars = JString::strlen($hashtags) + 1;
			}
		}

		$message = self::cleanText($post->message);
		$message_len = JString::strlen($message);
		$long_url = $post->org_url;
		$is_showing_url = (($post->show_url != AutotweetPostHelper::SHOWURL_OFF) && !empty($long_url));
		$has_media = (($channel->getMediaMode() != 'message') && (isset($post->image_url)));
		$has_weight = $channel->hasWeight();
		$max_chars = $channel->getMaxChars();

		$url = null;
		$url_len = 0;
		$totalmsg_len = $message_len;

		// Url Required and there's a Long Url
		if ($is_showing_url)
		{
			// Let's try with the long url
			$url = $long_url;
			$url_len = self::_getUrlLength($long_url, $has_weight, $is_showing_url);
			$totalmsg_len = $message_len + $url_len;

			// If always use ShortUrl or message len > channel max
			if (($shorturl_always) || ($totalmsg_len > $max_chars))
			{
				$url = $short_url;
				$url_len = self::_getUrlLength($short_url, $has_weight, $is_showing_url);
				$totalmsg_len = $message_len + $url_len;
			}
		}

		$totalmsg_len = $totalmsg_len + $hashtags_chars;

		$max_chars = self::_getMaxCharsAvailable($max_chars, $has_weight, $is_showing_url, $has_media);

		// Trunc text if needed, when Message Len > Max Channel Chars
		if ($totalmsg_len > $max_chars)
		{
			// Available chars for Message text
			$available_chars = $max_chars - $url_len;

			// Needs 3 chars for replacement with 3 dots
			$available_chars = $available_chars - 3;

			// And, the final cut
			$message = JString::substr($message, 0, $available_chars) . '...';
		}

		if (($includeHashTags) && ($hashtags))
		{
			$message = $message . ' ' . $hashtags;
		}

		// Construct status message
		switch ($post->show_url)
		{
			case AutotweetPostHelper::SHOWURL_OFF:
				// Dont show url, do nothing
				break;
			case AutotweetPostHelper::SHOWURL_BEGINNING:
				// Show url at beginning of message
				$message = $url . ' ' . $message;
				break;
			case AutotweetPostHelper::SHOWURL_END:
				// Show url at end of message
				$message = $message . ' ' . $url;
				break;
		}

		return array(
						'url' => $url,
						'message' => $message
		);
	}

	/**
	 * _getUrlLength
	 *
	 * @param   string  $url             Param
	 * @param   bool    $has_weight      Param
	 * @param   bool    $is_showing_url  Param
	 *
	 * @return	int
	 */
	private static function _getUrlLength($url, $has_weight, $is_showing_url)
	{
		// If channel has Weight or Show_Url is off
		if ($has_weight)
		{
			// Url len does not count, we must substract the Weight to $max_chars
			// Url has a fixed "Weight", not the usual len
			$url_len = 0;
		}
		else
		{
			if ($is_showing_url)
			{
				// Simplest case: Len Url plus a space
				$url_len = JString::strlen($url) + 1;
			}
			else
			{
				// Url not required
				$url_len = 0;
			}
		}

		return $url_len;
	}

	/**
	 * _getMaxCharsAvailable
	 *
	 * @param   int   $max_chars       Param
	 * @param   bool  $has_weight      Param
	 * @param   bool  $is_showing_url  Param
	 * @param   bool  $has_media       Param
	 *
	 * @return	int
	 */
	private static function _getMaxCharsAvailable($max_chars, $has_weight, $is_showing_url, $has_media)
	{
		$url_weight = EParameter::getComponentParam(CAUTOTWEETNG, 'url_weight', 23);
		$media_weight = EParameter::getComponentParam(CAUTOTWEETNG, 'media_weight', 23);

		if ($has_weight)
		{
			if ($is_showing_url)
			{
				$max_chars = $max_chars - $url_weight;
			}
			// Also, if we are in media mode, and there's an image
			if ($has_media)
			{
				$max_chars = $max_chars - $media_weight;
			}
		}

		return $max_chars;
	}

	/**
	 * adminNotification
	 *
	 * @param   string  $channel  Param
	 * @param   string  $msg      Param
	 * @param   object  &$post    Param
	 *
	 * @return	boolean
	 */
	public static function adminNotification($channel, $msg, &$post)
	{
		if (!EParameter::getComponentParam(CAUTOTWEETNG, 'admin_notification', 0))
		{
			return;
		}

		JLoader::import('extly.mail.notification');

		$postmsg = print_r($post, true);

		$emailSubject = 'AutoTweetNG Notification - Error on Channel "' . $channel . '"';
		$emailBody = "<h2>Hi,</h2>
		<p>This is an <b>AutoTweetNG</b> Notification, about an error on channel \"{$channel}\".</p>
		<h3>Error Message</h3>
		<p>{$msg}</p>
		<h3>Post details</h3>
		<p>{$postmsg}</p>
		<p>If you are working in the configuration, it must be related with your work. However, if the site is stable, you should check if there's any problem (E.g. an expired token).</p>
		<p><br></p>
		<p>If you have any question, the support team is ready to answer!</p>
		<p>Best regards,</p>
		<p>Support Team<br> <b>Extly.com</b> - Extensions<br> Support: <a target=\"_blank\" href=\"http://support.extly.com\">http://support.extly.com</a><br> Twitter @extly | Facebook <a target=\"_blank\" href=\"http://www.facebook.com/extly\">www.facebook.com/extly</a></p>";
		Notification::mailToAdmin($emailSubject, $emailBody);
	}

	/**
	 * adminNotification
	 *
	 * @param   string  $list  Param
	 *
	 * @return	array
	 */
	public static function listToArray($list)
	{
		$list = str_replace(' ', '', $list);
		$arr = explode(',', $list);

		return array_filter($arr);
	}

	/**
	 * cleanListOfNumerics
	 *
	 * @param   string  $list  Param
	 *
	 * @return	array
	 */
	public static function cleanListOfNumerics($list)
	{
		return preg_replace('/[^,0-9]/', '', $list);
	}

	/**
	 * getImageFromTextWithBrackets
	 *
	 * @param   string  $text  Param.
	 *
	 * @return	string
	 */
	public static function getImageFromTextWithBrackets($text)
	{
		$pattern = '/\[img\]([^\[]+)\[\/img\]/is';

		if (preg_match($pattern, $text, $match))
		{
			return $match[1];
		}

		return null;
	}

	/**
	 * getImageFromYoutubeWithBrackets
	 *
	 * @param   string  $text  Param.
	 *
	 * @return	string
	 */
	public static function getImageFromYoutubeWithBrackets($text)
	{
		$image = null;
		$pattern = '/\{youtube\}([^\{]+)\{\/youtube\}/';

		if (preg_match($pattern, $text, $match))
		{
			$youtube = $match[1];

			// {youtube}V3_WLFvoIxc|600|450|1{/youtube}
			$youtube = explode('|', $youtube);

			if (!empty($youtube))
			{
				$youtube = $youtube[0];
				$image = 'http://img.youtube.com/vi/' . $youtube . '/0.jpg';
			}
		}

		return $image;
	}

	/**
	 * getImageFromGalleryTag
	 *
	 * @param   string  $text  Param.
	 *
	 * @return	string
	 */
	public static function getImageFromGalleryTag($text)
	{
		if (!preg_match('/{gallery}([^\:]+)\:\:\:[0-9]+\:[0-9]+{\/gallery}/', $text, $matches))
		{
			return null;
		}

		$folder = $matches[1];

		$media = 'images/' . $folder;
		$galpath = JPATH_ROOT . '/' . $media;

		try
		{
			foreach (new DirectoryIterator($galpath) as $file)
			{
				$img = $galpath . '/' . $file->getFilename();

				if (($file->isFile()) && (ImageHelper::isImage($img)))
				{
					return $media . '/' . $file->getFilename();
				}
			}
		}
		catch (Exception $e)
		{

		}
	}

	/**
	 * renderUrl
	 *
	 * @param   string  $url  Param.
	 *
	 * @return	string
	 */
	public static function renderUrl($url)
	{
		if (EXTLY_J3)
		{
			return htmlspecialchars(JStringPunycode::urlToUTF8($url), ENT_COMPAT, 'UTF-8');
		}
		else
		{
			return htmlspecialchars($url, ENT_COMPAT, 'UTF-8');
		}
	}

	/**
	 * convertUrlSafe
	 *
	 * @param   string  $string  Param
	 *
	 * @return	string
	 */
	public static function convertUrlSafe($string)
	{
		return JApplication::stringURLSafe($string);
	}
}
