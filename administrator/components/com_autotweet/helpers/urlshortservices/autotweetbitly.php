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
 * AutotweetBitlyService
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetBitlyService extends AutotweetShortservice
{
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
		$enc_url = urlencode($long_url);
		$service_call = 'http://api.bit.ly/v3/shorten?login=' . $this->data['bit_username']
		. '&apiKey=' . $this->data['bit_key'] . '&longUrl=' . $enc_url . '&format=json';

		$result = $this->callComplexService($service_call);
		$result_code = $result[0];
		$js = $result[1];

		if ((200 != $result_code) || (200 != (int) $js->status_code) || !isset($js->data->url))
		{
			$short_url = '';
			$this->error_msg = $js->status_txt;
		}
		else
		{
			$short_url = $js->data->url;
		}

		return $short_url;
	}
}
