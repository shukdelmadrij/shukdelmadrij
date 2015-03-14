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
 * AutotweetViewFeeds
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewFeeds extends AutoTweetDefaultView
{
	/**
	 * onBrowse.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 *
	 * @since	1.5
	 */
	protected function onBrowse($tpl = null)
	{
		FeedImporterHelper::loadAjaxImporter($this);
		Extly::loadAwesome();

		return parent::onBrowse($tpl);
	}
}
