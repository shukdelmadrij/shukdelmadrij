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
 * AutoTweetDispatcher
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetDispatcher extends F0FDispatcher
{
	/**
	 * onBeforeDispatch.
	 *
	 * @return	bool
	 */
	public function onBeforeDispatch()
	{
		$result = parent::onBeforeDispatch();

		if (($result) && (!F0FPlatform::getInstance()->isCli()))
		{
			$view = $this->input->getCmd('view');
			Extly::loadStyle(true, ($view != 'composer'));

			$document = JFactory::getDocument();
			$document->addStyleSheet(JUri::root() . 'media/com_autotweet/css/style.css?version=' . CAUTOTWEETNG_VERSION);

			Extly::getScriptManager(
				// LoadExtlyAdminMode
				true,
				// Own Jquery Disabled
				false,
				// LoadBootstrap
				true
			);
		}

		return $result;
	}
}
