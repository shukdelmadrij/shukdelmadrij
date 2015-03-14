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
 * ShorturlHelper
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class ShorturlHelper
{
	// Seconds
	const RESEND_DELAY = 1;

	// General params for message and posting
	protected $resend_attempts = 2;

	protected $shorturl_service = 'Tinyurlcom';

	// Bit.ly and yourls account data
	protected $bit_username = '';

	protected $bit_key = '';

	protected $yourls_host = '';

	protected $yourls_token = '';

	private static $_instance = null;

	/**
	 * ShorturlHelper. No public access (singleton pattern).
	 *
	 */
	protected function __construct()
	{
		JLoader::register('AutotweetShortservice', dirname(__FILE__) . '/urlshortservices/autotweetshortservice.php');
		JLoader::register('AutotweetURLShortserviceFactory', dirname(__FILE__) . '/urlshortservices/autotweeturlshortservicefactory.php');

		// General params for message and posting
		$this->resend_attempts = EParameter::getComponentParam(CAUTOTWEETNG, 'resend_attempts', 2);
		$this->shorturl_service = EParameter::getComponentParam(CAUTOTWEETNG, 'shorturl_service', 'Tinyurlcom');

		// Bit.ly, Goog.gl and yourls account data
		$this->bit_username = EParameter::getComponentParam(CAUTOTWEETNG, 'bit_username', null);
		$this->bit_key = EParameter::getComponentParam(CAUTOTWEETNG, 'bit_key', null);
		$this->google_api_key = EParameter::getComponentParam(CAUTOTWEETNG, 'google_api_key', null);
		$this->yourls_host = EParameter::getComponentParam(CAUTOTWEETNG, 'yourls_host', null);
		$this->yourls_token = EParameter::getComponentParam(CAUTOTWEETNG, 'yourls_token', null);

		// Init AutoTweet logging
		$this->logger = AutotweetLogger::getInstance();
	}

	/**
	 * getInstance
	 *
	 * @return	Instance
	 *
	 * @since	1.5
	 */
	public static function &getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new ShorturlHelper;
		}

		return self::$_instance;
	}

	/**
	 * getShortUrl.
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 *
	 * @since	1.5
	 */
	public function getShortUrl($url)
	{
		$shorturl_service = $this->shorturl_service;

		if (('0' != $shorturl_service) && !empty($url))
		{
			// Get short url service
			$data = array(
							'type' => $shorturl_service,
							'bit_username' => $this->bit_username,
							'bit_key' => $this->bit_key,
							'google_api_key' => $this->google_api_key,
							'yourls_host' => $this->yourls_host,
							'yourls_token' => $this->yourls_token
			);
			$service = AutotweetURLShortserviceFactory::getInstance($data);

			// Get short url
			$attempt = 0;

			do
			{
				$resend = false;
				$attempt++;

				$short_url = $service->getShortUrl($url);

				if (($attempt < $this->resend_attempts) && empty($short_url))
				{
					$resend = true;
					$this->logger->log(JLog::WARNING, 'getShortUrl: Short url service ' . $shorturl_service . ' ' . $service->getErrorMessage() . ' - try again in ' . self::RESEND_DELAY . ' seconds');

					sleep(self::RESEND_DELAY);
				}
			}

			while ($resend);

			if (!empty($short_url))
			{
				$url = $short_url;
				$this->logger->log(JLog::INFO, 'getShortUrl: url shortened, short url = ' . $short_url);
			}
			else
			{
				$this->logger->log(JLog::WARNING, 'getShortUrl: Short url service ' . $shorturl_service . ' failed. Normal url used.');
			}
		}

		return $url;
	}
}
