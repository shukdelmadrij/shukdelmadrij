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
 * AutotweetYourlsService
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetYourlsService extends AutotweetShortservice
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
		// Create the data to be encoded into JSON
		$requestData = array(
				'url'      => $long_url,
				'format'   => 'json',
				'action'   => 'shorturl',
				'signature' => $this->data['yourls_token']
		);

		$result = $this->callPostService($this->data['yourls_host'], $requestData);
		$result_code = $result[0];
		$output = $result[1];

		if ((200 != $result_code) || (!isset($output->shorturl)))
		{
			$short_url = '';
			$this->error_msg = '(' . $result_code . ')'
					. ' / ' . $output->status
					. ' - ' . $output->code;
		}
		else
		{
			$short_url = $output->shorturl;
		}

		return $short_url;
	}
}
