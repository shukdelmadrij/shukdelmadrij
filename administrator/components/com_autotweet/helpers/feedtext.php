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
abstract class FeedTextHelper
{
	/**
	 * truncString.
	 *
	 * @param   string   $text       Param
	 * @param   integer  $length     Param
	 * @param   integer  $max_chars  Param
	 * @param   bool     $withDots   Param
	 *
	 * @return	string
	 *
	 * @since	1.5
	 */
	private static function truncStringDeprecated($text, $length, $max_chars, $withDots = false)
	{
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
				$hashtags = TextUtil::cleanText($hashtags);
				$hashtags_chars = JString::strlen($hashtags) + 1;
			}
		}

		$message = TextUtil::cleanText($post->message);
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
	 *
	 * @since	1.5
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
	 * transliterate
	 *
	 * Adapted from http://docs.joomla.org/Making_a_Language_Pack_for_Joomla_1.6#Example_of_the_function_to_add_when_custom_transliteration_is_desired
	 * Returns a lowercase transliterated string - for use in aliases (SEF)
	 *
	 * @param   string  $string  Param
	 * @param   string  $custom  Param
	 *
	 * @return	string
	 */
	public static function transliterate($string, $custom)
	{
		static $basic_glyph_array = array(
						'a' => 'à,á,â,ã,ä,å,ā,ă,ą,ḁ,α,ά',
						'ae' => 'æ',
						'b' => 'β,б',
						'c' => 'ç,ć,ĉ,ċ,č,ч,ћ,ц',
						'ch' => 'ч',
						'd' => 'ď,đ,Ð,д,ђ,δ,ð',
						'dz' => 'џ',
						'e' => 'è,é,ê,ë,ē,ĕ,ė,ę,ě,э,ε,έ',
						'f' => 'ƒ,ф',
						'g' => 'ğ,ĝ,ğ,ġ,ģ,г,γ',
						'h' => 'ĥ,ħ,Ħ,х',
						'i' => 'ì,í,î,ï,ı,ĩ,ī,ĭ,į,и,й,ъ,ы,ь,η,ή',
						'ij' => 'ĳ',
						'j' => 'ĵ',
						'ja' => 'я',
						'ju' => 'яю',
						'k' => 'ķ,ĸ,κ',
						'l' => 'ĺ,ļ,ľ,ŀ,ł,л,λ',
						'lj' => 'љ',
						'm' => 'μ',
						'n' => 'ñ,ņ,ň,ŉ,ŋ,н,ν',
						'nj' => 'њ',
						'o' => 'ò,ó,ô,õ,ø,ō,ŏ,ő,ο,ό,ω,ώ',
						'oe' => 'œ,ö',
						'p' => 'п,π',
						'ph' => 'φ',
						'ps' => 'ψ',
						'r' => 'ŕ,ŗ,ř,р,ρ,σ,ς',
						's' => 'ş,ś,ŝ,ş,š,с',
						'ss' => 'ß,ſ',
						'sh' => 'ш',
						'shch' => 'щ',
						't' => 'ţ,ť,ŧ,τ,т',
						'th' => 'θ',
						'u' => 'ù,ú,û,ü,ũ,ū,ŭ,ů,ű,ų,у',
						'v' => 'в',
						'w' => 'ŵ',
						'x' => 'χ,ξ',
						'y' => 'ý,þ,ÿ,ŷ',
						'z' => 'ź,ż,ž,з,ж,ζ'
		);

		$glyph_array = array();

		if ($custom)
		{
			$array = explode("\n", $custom);

			foreach ($array as $v)
			{
				$v = explode('=', $v);
				$glyph_array[$v[0]] = $v[1];
			}
		}
		else
		{
			$glyph_array = $basic_glyph_array;
		}

		foreach ($glyph_array as $letter => $glyphs)
		{
			$glyphs = TextUtil::listToArray($glyphs);
			$string = str_replace($glyphs, $letter, $string);
		}

		return $string;
	}

	/**
	 * encodeUrl
	 *
	 * Based on a function by Nitin at http://publicmind.in/blog/url-encoding/ - proper urlencode
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 */
	public static function encodeUrl($url)
	{
		$reserved = array(
						":" => '!%3A!ui',
						"/" => '!%2F!ui',
						"?" => '!%3F!ui',
						"#" => '!%23!ui',
						"[" => '!%5B!ui',
						"]" => '!%5D!ui',
						"@" => '!%40!ui',
						"!" => '!%21!ui',
						"$" => '!%24!ui',
						"&" => '!%26!ui',
						"'" => '!%27!ui',
						"(" => '!%28!ui',
						")" => '!%29!ui',
						"*" => '!%2A!ui',
						"+" => '!%2B!ui',
						"," => '!%2C!ui',
						";" => '!%3B!ui',
						"=" => '!%3D!ui',
						"%" => '!%25!ui'
		);

		// Removes nasty whitespace
		$url = str_replace(
				array(
						'%09',
						'%0A',
						'%0B',
						'%0D'
				),
				'',
				$url
			);
		$url = rawurlencode($url);
		$url = preg_replace(array_values($reserved), array_keys($reserved), $url);

		return $url;
	}

	/**
	 * cleanFeedText
	 *
	 * Cleanup and htmLawed text cleaning
	 *
	 * @param   string  $text          Param
	 * @param   string  $clean_config  Param
	 * @param   string  $spec          Param
	 *
	 * @return	string
	 */
	public static function cleanFeedText($text, $clean_config, $spec)
	{
		include_once JPATH_ADMINISTRATOR . '/components/com_autotweet/libs/htmLawed.php';

		// Hacky fix for crap sites that really can't be cleaned up well - this confuses the html parsers!
		$text = str_replace('<sup> </sup>', ' ', $text);
		$text = str_replace('<sub> </sub>', ' ', $text);

		$text = htmLawed\htmLawed($text, $clean_config, $spec);

		return $text;
	}

	/**
	 * cleanMeta
	 *
	 * @param   string  &$content  Param
	 *
	 * @return	string
	 */
	public static function cleanMeta(&$content)
	{
		// Only process if not empty
		if (!empty($content['metakey']))
		{
			// Array of characters to remove
			$bad_characters = array(
							"\n",
							"\r",
							"\"",
							"<",
							">"
			);

			// Remove bad characters
			$after_clean = JString::str_ireplace($bad_characters, "", $content['metakey']);

			// Create array using commas as delimiter
			$keys = TextUtil::listToArray($after_clean);
			$clean_keys = array();

			foreach ($keys as $key)
			{
				// Ignore blank keywords
				if (trim($key))
				{
					$clean_keys[] = trim($key);
				}
			}

			// Put array back together delimited by ", "
			$content['metakey'] = implode(", ", $clean_keys);
		}

		// Clean up description -- eliminate quotes and <> brackets
		if (!empty($content['metadesc']))
		{
			// Only process if not empty
			$bad_characters = array(
							"\"",
							"<",
							">"
			);
			$content['metadesc'] = JString::str_ireplace($bad_characters, "", $content['metadesc']);
		}
	}

	/**
	 * trimText
	 *
	 * @param   string  $text       Param
	 * @param   string  $trimTo     Param
	 * @param   string  $type       Param
	 * @param   string  $keep_tags  Param
	 *
	 * @return	string
	 */
	public static function trimText($text, $trimTo, $type = 'char', $keep_tags = true)
	{
		if (!$keep_tags)
		{
			$text = strip_tags($text);
		}

		if (!$trimTo)
		{
			return $text;
		}

		$text = preg_replace('/\s\s+/', ' ', $text);

		// Html safe split text function
		$regex = '#<[^<^>]*>|[^<]*|<[^<^>]*#u';

		preg_match_all($regex, $text, $matches);

		switch ($type)
		{
			case 'char':
				$text = self::_trimTextChar($matches[0], $trimTo);
				break;

			case 'word':
				$text = self::_trimTextWord($matches[0], $trimTo);
				break;

			case 'sent':
				$text = self::_trimTextSent($matches[0], $trimTo);
				break;
		}

		return $text;
	}

	/**
	 * _trimTextChar
	 *
	 * @param   string  $parts   Param
	 * @param   string  $trimTo  Param
	 *
	 * @return	string
	 */
	private static function _trimTextChar($parts, $trimTo)
	{
		$result = array();
		$len = 0;
		$end = false;
		$firstWord = true;

		foreach ($parts as $part)
		{
			$m = JString::strlen($part);

			// It's a tag
			if ((JString::strpos($part, '<') === 0) || (JString::strpos($part, '>') === ($m - 1)))
			{
				$result[] = $part;
				$firstWord = true;
			}
			else
			{
				$words = explode(' ', $part);

				foreach ($words as $word)
				{
					$l = JString::strlen($word);

					if (!$firstWord)
					{
						$l++;
					}

					if ($len + $l < $trimTo)
					{
						$len += $l;

						if ($firstWord)
						{
							$result[] = $word;
							$firstWord = false;
						}
						else
						{
							$result[] = ' ' . $word;
							$len++;
						}
					}
					else
					{
						$end = true;
						break;
					}
				}
			}

			if ($end)
			{
				break;
			}
		}

		return trim(implode('', $result));
	}

	/**
	 * _trimTextWord
	 *
	 * @param   string  $parts   Param
	 * @param   string  $trimTo  Param
	 *
	 * @return	string
	 */
	private static function _trimTextWord($parts, $trimTo)
	{
		$result = array();
		$len = 0;
		$end = false;
		$firstWord = true;

		foreach ($parts as $part)
		{
			$m = JString::strlen($part);

			// It's a tag
			if ((JString::strpos($part, '<') === 0) || (JString::strpos($part, '>') === ($m - 1)))
			{
				$result[] = $part;
				$firstWord = true;
			}
			else
			{
				$words = explode(' ', $part);

				foreach ($words as $word)
				{
					if ($len < $trimTo)
					{
						$len++;

						if ($firstWord)
						{
							$result[] = $word;
							$firstWord = false;
						}
						else
						{
							$result[] = ' ' . $word;
						}
					}
					else
					{
						$end = true;
						break;
					}
				}
			}

			if ($end)
			{
				break;
			}
		}

		return trim(implode('', $result));
	}

	/**
	 * _trimTextSent
	 *
	 * @param   string  $parts   Param
	 * @param   string  $trimTo  Param
	 *
	 * @return	string
	 */
	private static function _trimTextSent($parts, $trimTo)
	{
		$result = array();
		$len = 0;
		$end = false;
		$firstSent = true;

		$pattern = '/(?<=[.?!;:])\s+/';

		foreach ($parts as $part)
		{
			$m = JString::strlen($part);

			// It's a tag
			if ((JString::strpos($part, '<') === 0) || (JString::strpos($part, '>') === ($m - 1)))
			{
				$result[] = $part;
			}
			else
			{
				$sentences = preg_split($pattern, $part, -1, PREG_SPLIT_NO_EMPTY);

				foreach ($sentences as $sent)
				{
					if ($len < $trimTo)
					{
						$len++;
						$result[] = $sent;
					}
					else
					{
						$end = true;
						break;
					}
				}
			}

			if ($end)
			{
				break;
			}
		}

		return trim(implode('', $result));
	}

	/**
	 * in_array_recursive
	 *
	 * @param   string  $needle    Param
	 * @param   string  $haystack  Param
	 *
	 * @return	string
	 */
	public static function inArrayRecursive($needle, $haystack)
	{
		foreach ($haystack as &$value)
		{
			if (is_array($value))
			{
				if (self::inArrayRecursive($needle, $value))
				{
					return true;
				}
			}
			else
			{
				if ($value == $needle)
				{
					return true;
				}
			}
		}
	}

	/**
	 * str_replace_first
	 *
	 * @param   string  $search   Param
	 * @param   string  $replace  Param
	 * @param   string  $subject  Param
	 *
	 * @return	string
	 */
	public static function str_replace_first($search, $replace, $subject)
	{
		$pos = strpos($subject, $search);

		if ($pos !== false)
		{
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}

		return $subject;
	}

	/**
	 * str_replace_last
	 *
	 * @param   string  $search   Param
	 * @param   string  $replace  Param
	 * @param   string  $str      Param
	 *
	 * @return	string
	 */
	public static function str_replace_last($search, $replace, $str)
	{
		if (($pos = strrpos($str, $search)) !== false)
		{
			$search_length = strlen($search);
			$str = substr_replace($str, $replace, $pos, $search_length);
		}

		return $str;
	}

	/**
	 * getUrl
	 *
	 * If file_path given, will automatically save resource to file
	 *
	 * @param   string  $url              Param
	 * @param   string  $expected_result  Param
	 * @param   string  $file_path        Param
	 * @param   string  $parts            Param
	 *
	 * @return	string
	 */
	public static function getUrl($url, $expected_result = 'html', $file_path = null, $parts = null)
	{
		$page = false;

		try
		{
			if (JString::strpos($url, '//'))
			{
				$url = implode('/', array_slice(explode('/', $url), 2));
			}

			$url = html_entity_decode(JString::trim($url), ENT_QUOTES);

			// Are these url cleaning methods really necessary??
			$url = utf8_encode(strip_tags($url));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);

			if (!ini_get('open_basedir'))
			{
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			}

			switch ($expected_result)
			{
				case 'html':
					curl_setopt($ch, CURLOPT_HEADER, 1);
					break;

				case 'noheader':
					// This is same as html above but with no header
					curl_setopt($ch, CURLOPT_HEADER, 0);
					break;

				case 'header':
					// Returns headers only
					curl_setopt($ch, CURLOPT_HEADER, 1);
					curl_setopt($ch, CURLOPT_NOBODY, 1);
					break;

				case 'images':
				default:
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
					break;
			}

			if ($file_path)
			{
				// Saving File (fopen)
				$fp = fopen($file_path, 'w');
				curl_setopt($ch, CURLOPT_FILE, $fp);
			}

			// Accessing URL (cURL)
			// UTF-8 and cleaning done later
			$page = curl_exec($ch);

			// Close cURL resource, and free up system resources
			curl_close($ch);

			if (isset($fp))
			{
				fclose($fp);
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
			$logger = AutotweetLogger::getInstance()->log(JLog::ERROR, 'AutoTweetNG - ' . $error_message);
		}

		return $page;
	}

	/**
	 * extractHttp
	 *
	 * @param   string  $string  Param
	 *
	 * @return	string
	 */
	public static function extractHttp($string)
	{
		$parts = array();

		$headers = explode("\r\n\r\n", $string, 1);
		$headers = array_pop($headers);

		// Why reinvent the wheel?
		$parser = new SimplePie_HTTP_Parser($headers);

		if ($parser->parse())
		{
			$parts['header'] = $parser->headers;
			$parts['body'] = $parser->body;
		}

		return $parts;
	}

	/**
	 * convert_to_utf8
	 *
	 * Convert $html to UTF8
	 * (uses HTTP headers and HTML to find encoding)
	 * adapted from http://stackoverflow.com/questions/910793/php-detect-encoding-and-make-everything-utf-8
	 * borrowed from Five Filters https://code.launchpad.net/~keyvan/fivefilters/content-only
	 *
	 * @param   string  $html    Param
	 * @param   string  $header  Param
	 *
	 * @return	string
	 */
	public static function convertToUtf8($html, $header = null)
	{
		$accept = array(
						'type' => array(
										'application/rss+xml',
										'application/xml',
										'application/rdf+xml',
										'text/xml'
						)
		);

		$encoding = null;

		if (($html) || ($header))
		{
			if (is_array($header))
			{
				$header = implode("\n", $header);
			}

			$pattern = '/^Content-Type:\s+([^;]+)(?:;\s*charset=["]?([^"^\s]*))?/im';

			if ((!$header) || (!preg_match_all($pattern, $header, $match, PREG_SET_ORDER)))
			{
				// Error parsing the response
			}
			else
			{
				// Get last matched element (in case of redirects)
				$match = end($match);

				if (!in_array(strtolower($match[1]), $accept['type']))
				{
					// Type not accepted
					// TO-DO: avoid conversion
				}

				if (isset($match[2]))
				{
					$encoding = JString::trim($match[2], '"\'');
				}
			}

			if (!$encoding)
			{
				if (preg_match('/^<\?xml\s+version=(?:"[^"]*"|\'[^\']*\')\s+encoding=("[^"]*"|\'[^\']*\')/s', $html, $match))
				{
					$encoding = JString::trim($match[1], '"\'');
				}
				elseif (preg_match('/<meta\s+http-equiv="Content-Type" content="([^;]+)(?:;\s*charset=["]?([^"^\s]*))?"/i', $html, $match))
				{
					if (isset($match[2]))
					{
						$encoding = JString::trim($match[2], '"\'');
					}
				}
			}

			if (!$encoding)
			{
				$encoding = 'utf-8';
			}
			else
			{
				$encoding = JString::trim(strtolower($encoding));

				// Encoding not accepted
				// TO-DO: avoid conversion

				if ($encoding != 'utf-8')
				{
					if (strtolower($encoding) == 'iso-8859-1')
					{
						// Replace MS Word smart qutoes
						$trans = array();

						// Single Low-9 Quotation Mark
						$trans[chr(130)] = '&sbquo;';

						// Latin Small Letter F With Hook
						$trans[chr(131)] = '&fnof;';

						// Double Low-9 Quotation Mark
						$trans[chr(132)] = '&bdquo;';

						// Horizontal Ellipsis
						$trans[chr(133)] = '&hellip;';

						// Dagger
						$trans[chr(134)] = '&dagger;';

						// Double Dagger
						$trans[chr(135)] = '&Dagger;';

						// Modifier Letter Circumflex Accent
						$trans[chr(136)] = '&circ;';

						// Per Mille Sign
						$trans[chr(137)] = '&permil;';

						// Latin Capital Letter S With Caron
						$trans[chr(138)] = '&Scaron;';

						// Single Left-Pointing Angle Quotation Mark
						$trans[chr(139)] = '&lsaquo;';

						// Latin Capital Ligature OE
						$trans[chr(140)] = '&OElig;';

						// Left Single Quotation Mark
						$trans[chr(145)] = '&lsquo;';

						// Right Single Quotation Mark
						$trans[chr(146)] = '&rsquo;';

						// Left Double Quotation Mark
						$trans[chr(147)] = '&ldquo;';

						// Right Double Quotation Mark
						$trans[chr(148)] = '&rdquo;';

						// Bullet
						$trans[chr(149)] = '&bull;';

						// En Dash
						$trans[chr(150)] = '&ndash;';

						// Em Dash
						$trans[chr(151)] = '&mdash;';

						// Small Tilde
						$trans[chr(152)] = '&tilde;';

						// Trade Mark Sign
						$trans[chr(153)] = '&trade;';

						// Latin Small Letter S With Caron
						$trans[chr(154)] = '&scaron;';

						// Single Right-Pointing Angle Quotation Mark
						$trans[chr(155)] = '&rsaquo;';

						// Latin Small Ligature OE
						$trans[chr(156)] = '&oelig;';

						// Latin Capital Letter Y With Diaeresis
						$trans[chr(159)] = '&Yuml;';

						$html = strtr($html, $trans);
					}

					if (function_exists('iconv'))
					{
						// Iconv appears to handle certain character encodings better than mb_convert_encoding
						$html = @iconv($encoding, 'utf-8', $html);
					}
					elseif (function_exists('mb_convert_encoding'))
					{
						$html = @mb_convert_encoding($html, 'utf-8', $encoding);
					}
					elseif (function_exists('recode_string'))
					{
						$html = @recode_string($encoding . '..utf-8', $html);
					}
				}
			}

			return $html;
		}

		return false;
	}

	/**
	 * getImageName
	 *
	 * @param   string  $title          Params
	 * @param   string  $alt            Params
	 * @param   string  $src            Params
	 * @param   string  $name_type      Params
	 * @param   string  $image_details  Params
	 * @param   bool    $add_ext        Params
	 *
	 * @return	string
	 */
	public static function getImageName($title, $alt, $src, $name_type, $image_details, $add_ext = 1)
	{
		preg_match('#[/?&]([^/?&]*)(\.jpg|\.jpeg|\.gif|\.png)#i', $src, $matches);
		$ext = isset($matches[2]) ? trim(strtolower($matches[2])) : '';

		if (!$ext and !empty($image_details))
		{
			switch ($image_details['mime'])
			{
				case 'image/pjpeg':
				case 'image/jpeg':
				case 'image/jpg':
					$ext = '.jpg';
					break;
				case 'image/x-png':
				case 'image/png':
					$ext = '.png';
					break;
				case 'image/gif':
					$ext = '.gif';
					break;
				case 'image/bmp':
					$ext = '.bmp';
					break;
			}
		}

		switch ($name_type)
		{
			case 0:
				list($name) = $title ? self::splitText($title, 50, 'char', false) : self::splitText($alt, 50, 'char', false);
				break;

			case 1:
				if (isset($matches[1]))
				{
					$name = $matches[1];
				}
				break;

			case 2:
				$name = md5($src);
				break;

			case 3:
				jexit('Image name error');
				break;
		}

		$name_type++;

		if (empty($name))
		{
			$name = self::getImageName($title, $alt, $src, $name_type, $image_details, 0);
		}

		$name = JFile::makeSafe(TextUtil::convertUrlSafe($name));

		return $add_ext ? $name . $ext : $name;
	}

	/**
	 * generateTags
	 *
	 * Use a simple frequency algorithm to compute meta tags
	 *
	 * @param   string  $text      Params
	 * @param   string  $max_tags  Params
	 *
	 * @return	string
	 */
	public static function generateTags($text, $max_tags = 3)
	{
		$text = strtolower(html_entity_decode(strip_tags($text), ENT_QUOTES));

		if (!JString::trim($text))
		{
			return '';
		}

		$words = explode(' ', $text);

		array_walk(
			$words,
			array(
					'FeedTextHelper',
					'trimTags'
			)
		);

		$words = array_filter(
				$words,
				array(
						'FeedTextHelper',
						'filterTerms'
			)
		);
		$words = self::removeIgnoreWords($words);
		$words = array_count_values($words);
		arsort($words);
		$words = is_array($words) ? array_slice($words, 0, $max_tags) : array();
		$words = implode(',', array_keys($words));

		return $words;
	}

	/**
	 * trimTags
	 *
	 * @param   string  &$term  Params
	 * @param   string  $key    Params
	 *
	 * @return	string
	 */
	public static function trimTags(&$term, $key)
	{
		$term = JString::trim($term);

		$term = str_replace(
				array(
						"\n",
						"\r"
			),
			' ',
			$term
		);
		$term = preg_replace('/[,.?:;!()=\\*\']/', '', $term);
	}

	/**
	 * filterTerms
	 *
	 * @param   string  $var            Params
	 * @param   int     $min_tag_chars  Params
	 *
	 * @return	bool
	 */
	public static function filterTerms($var, $min_tag_chars = 3)
	{
		$keep = !empty($var) && $var != '' && $var != null && !preg_match('/^\s*$/', $var);

		if ((!empty($min_tag_chars)) && ($min_tag_chars > 0))
		{
			$keep = (($keep) && (strlen($var) >= $min_tag_chars));
		}

		return ($keep);
	}

	/**
	 * splitArticleText
	 *
	 * @param   string  $articletext  Params
	 *
	 * @return	array
	 */
	public static function splitArticleText($articletext)
	{
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos = preg_match($pattern, $articletext);

		if ($tagPos == 0)
		{
			return array($articletext, null);
		}
		else
		{
			return preg_split($pattern, $articletext, 2);
		}
	}

	/**
	 * joinArticleText
	 *
	 * @param   string  $introtext  Params
	 * @param   string  $fulltext   Params
	 *
	 * @return	bool
	 */
	public static function joinArticleText($introtext, $fulltext)
	{
		if (empty($fulltext))
		{
			return $introtext;
		}
		else
		{
			return $introtext . '<hr id="system-readmore" />' . $fulltext;
		}
	}

	/**
	 * generateAuthor
	 *
	 * @param   string  $created_by        Params
	 * @param   string  $created_by_alias  Params
	 *
	 * @return	bool
	 */
	public static function generateAuthor($created_by, $created_by_alias)
	{
		return  JFactory::getUser($created_by)->username
				. (empty($created_by_alias) ? '' : ' (' . $created_by_alias . ')' );
	}

	// HookTagCleaning - Start

	protected static $open_tag = null;

	protected static $hasHookTags = null;

	protected static $whitemode = null;

	protected static $tags_attrs = null;

	/**
	 * Hook_tag function for htmLawed initialization
	 *
	 * @return	string
	 */
	public static function hookTagCleaningInit()
	{
		self::getHookTagList();
	}

	/**
	 * Hook_tag function for htmLawed initialization
	 *
	 * @return	string
	 */
	public static function hookTagCleaningItemInit()
	{
		self::$open_tag = array();
	}

	/**
	 * getHookTagList
	 *
	 * @return	void
	 */
	public static function getHookTagList()
	{
		static $regex = '/([\S]+)\s*?([^=]*)?=?([\S]*)?/';

		$hook_tag = FeedProcessorHelper::getHookTag();
		$whitemode = (strpos($hook_tag, '+') === 0) ? 1 : 0;

		if ($whitemode)
		{
			$hook_tag = self::str_replace_first('+', '', $hook_tag);
		}

		$parts = TextUtil::listToArray($hook_tag);
		$tags_attrs = array();

		foreach ($parts as $part)
		{
			preg_match($regex, $part, $matches);

			if (count($matches) == 4)
			{
				$element = trim($matches[1]);
				$attr = trim($matches[2]);
				$value = trim($matches[3]);

				$tags_attrs[$element . '-' . $attr] = $value;
			}
		}

		self::$hasHookTags = !empty($tags_attrs);
		self::$whitemode = $whitemode;
		self::$tags_attrs = $tags_attrs;
	}

	/**
	 * Hook_tag function for htmLawed
	 *
	 * Cleanup and htmLawed text cleaning
	 *
	 * @param   object  $element          Param
	 * @param   array   $attribute_array  Param
	 *
	 * @return	string
	 */
	public static function hookTagCleaning($element, $attribute_array = array())
	{
		static $empty_elements = array(
						'area' => 1,
						'br' => 1,
						'col' => 1,
						'embed' => 1,
						'hr' => 1,
						'img' => 1,
						'input' => 1,
						'isindex' => 1,
						'param' => 1
		);

		// It's a closing tag
		if (!array_key_exists($element, $empty_elements))
		{
			if ((array_key_exists($element, self::$open_tag))
				&& (self::$open_tag[$element]))
			{
				self::$open_tag[$element] = false;

				return "</$element>";
			}

			self::$open_tag[$element] = true;
		}

		if (self::$hasHookTags)
		{
			foreach ($attribute_array as $k => $v)
			{
				$key = $element . '-' . $k;

				if ((array_key_exists($key, self::$tags_attrs)) && (strpos($v, self::$tags_attrs[$key]) === 0))
				{
					if (self::$whitemode)
					{
						// It's ok, proceed
						break;
					}
					else
					{
						// Blacklisted!
						return '';
					}
				}
			}
		}

		$params = FeedProcessorHelper::getParams();
		$link_target = $params->get('link_target', 0);

		if (($element == 'a') && ($link_target))
		{
			$attribute_array['target'] = $link_target;
		}

		$string = '';

		foreach ($attribute_array as $k => $v)
		{
			$string .= " {$k}=\"{$v}\"";
		}

		return "<{$element}{$string}>";
	}

	// HookTagCleaning - End
}
