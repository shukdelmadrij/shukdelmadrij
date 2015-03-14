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

// Factory to create url short services.

// Include new services here only!!!

/**
 * AutotweetURLShortserviceFactory
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetURLShortserviceFactory
{
	/**
	 * AutotweetURLShortserviceFactory
	 */
	private function __construct()
	{
		// Static class
	}

	/**
	 * getInstance
	 *
	 * @param   array  $data  Param
	 *
	 * @return	object
	 */
	public static function getInstance($data)
	{
		JLoader::register('AutotweetIsgdService', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autotweetisgd.php');
		JLoader::register('AutotweetBitlyService', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autotweetbitly.php');
		JLoader::register('AutotweetTinyurlcomService', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autotweettinyurlcom.php');
		JLoader::register('AutotweetYourlsService', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autotweetyourls.php');

		$classname = 'AutoTweet' . $data['type'] . 'Service';
		JLoader::load($classname);

		return new $classname($data);
	}
}
