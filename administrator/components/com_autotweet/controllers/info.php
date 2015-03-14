<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

include_once 'default.php';

/**
 * AutoTweetControllerInfo - The Control Panel controller class
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutoTweetControllerInfo extends AutotweetControllerDefault
{
	/**
	 * onBeforeBrowse
	 *
	 * @return  void
	 */
	public function onBeforeBrowse()
	{
		$result = parent::onBeforeBrowse();

		if ($result)
		{
			// Run the automatic update site refresh
			$updateModel = F0FModel::getTmpInstance('LiveUpdates', 'AutoTweetModel');
			$updateModel->refreshUpdateSite();
		}

		return $result;
	}
}
