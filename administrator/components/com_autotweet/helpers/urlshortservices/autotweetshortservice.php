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

// Base class for AutoTweet url short services.

/**
 * AutotweetShortservice
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class AutotweetShortservice
{
	// Seconds
	const CXN_TIMEOUT		= 5;

	// Seconds
	const EXEC_TIMEOUT		= 10;

	protected $data;

	protected $error_msg;

	/**
	 * getShortURL
	 *
	 * @param   string  $long_url  Param.
	 *
	 * @return	string
	 *
	 * @since	1.0
	 */
	abstract public function getShortUrl($long_url);

	/**
	 * AutotweetShortservice
	 *
	 * @param   array  $data  Param
	 */
	protected function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * callSimpleService
	 *
	 * @param   string  $service_url  Param.
	 * @param   string  $long_url     Param.
	 *
	 * @return	string
	 *
	 * @since	1.0
	 */
	protected function callSimpleService($service_url, $long_url)
	{
		$enc_url = urlencode($long_url);
		$service_call = $service_url . $enc_url;

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $service_call);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, self::CXN_TIMEOUT);
		curl_setopt($c, CURLOPT_TIMEOUT, self::EXEC_TIMEOUT);

		$result = curl_exec($c);
		$result_code = curl_getinfo($c);
		curl_close($c);

		if (200 != (int) $result_code['http_code'])
		{
			$short_url = '';
			$this->error_msg = 'Code:' . $result_code['http_code'];
		}
		else
		{
			$short_url = $result;
		}

		return $short_url;
	}

	/**
	 * callComplexService
	 *
	 * @param   string  $service_call  Param.
	 *
	 * @return	string
	 *
	 * @since	1.0
	 */
	protected function callComplexService($service_call)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $service_call);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, self::CXN_TIMEOUT);
		curl_setopt($c, CURLOPT_TIMEOUT, self::EXEC_TIMEOUT);

		$result = curl_exec($c);
		$result_code = curl_getinfo($c);
		curl_close($c);

		return array ((int) $result_code['http_code'], json_decode($result));
	}

	/**
	 * callJsonService
	 *
	 * @param   string  $service_call  Param.
	 * @param   string  $requestData   Param.
	 *
	 * @return	string
	 *
	 * @since	1.0
	 */
	protected function callJsonService($service_call, $requestData)
	{
		// Initialize the cURL connection
		$ch = curl_init($service_call);

		// Tell cURL to return the data rather than outputting it
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Change the request type to POST
		curl_setopt($ch, CURLOPT_POST, true);

		// Set the form content type for JSON data
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

		// Set the post body to encoded JSON data
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CXN_TIMEOUT);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::EXEC_TIMEOUT);

		// Perform the request
		$result = curl_exec($ch);
		$result_code = curl_getinfo($ch);
		curl_close($ch);

		return array ((int) $result_code['http_code'], json_decode($result));
	}

	/**
	 * callPostService
	 *
	 * @param   string  $service_call  Param.
	 * @param   mixed   &$requestData  Param.
	 *
	 * @return	string
	 *
	 * @since	1.0
	 */
	protected function callPostService($service_call, &$requestData)
	{
		// Initialize the cURL connection
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $service_call);

		// No header in the result
		curl_setopt($ch, CURLOPT_HEADER, 0);

		// Return, do not echo result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// This is a POST request
		curl_setopt($ch, CURLOPT_POST, true);

		// Data to POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CXN_TIMEOUT);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::EXEC_TIMEOUT);

		// Fetch and return content
		$result = curl_exec($ch);
		$result_code = curl_getinfo($ch);
		curl_close($ch);

		return array ((int) $result_code['http_code'], json_decode($result));
	}

	/**
	 * getErrorMessage
	 *
	 * @return	string
	 *
	 * @since	1.0
	 */
	public function getErrorMessage()
	{
		return $this->error_msg;
	}
}
