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

//  AutoTweet bit.ly url short service.

JLoader::import('autotweetshortservice', dirname(__FILE__));

/**
 * AutotweetGooglService
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetGooglService extends AutotweetShortservice
{
	const GOOGLE_ENDPOINT = 'https://www.googleapis.com/urlshortener/v1';

	/**
	 * Construct
	 *
	 * @param   array  $data  Param
	 */
	public function __construct($data)
	{
		parent::__construct($data);
	}

	/**
	 * getShortURL
	 *
	 * @param   string  $long_url  Param.
	 *
	 * @return	string
	 */
	public function getShortUrl($long_url)
	{
		$service_call = sprintf('%s/url?key=%s', self::GOOGLE_ENDPOINT, $this->data['google_api_key']);

		// Create the data to be encoded into JSON
		$requestData = array(
				'longUrl' => $long_url
		);

		$result = $this->callJsonService($service_call, $requestData);
		$result_code = $result[0];
		$js = $result[1];

		if ((200 != $result_code) || !isset($js->id))
		{
			$short_url = '';
			$this->error_msg = $js->error->message;
		}
		else
		{
			$short_url = $js->id;
		}

		return $short_url;
	}
}
