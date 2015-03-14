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

/**
 * AutotweetControllerChannels
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerSef extends F0FController
{
	/**
	 * getSefRouter.
	 *
	 * @return	void
	 */
	public function route()
	{
		header('Content-type: text/plain');

		$url = base64_decode($this->input->get('url', 'index.php', 'BASE64'));

		@ob_end_clean();
		$routed_url = JRoute::_($url, false);
		echo base64_encode($routed_url);
		flush();

		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'AutotweetControllerSef route: ' . $url . ' = ' . $routed_url);

		JFactory::getApplication()->close();
	}
}
