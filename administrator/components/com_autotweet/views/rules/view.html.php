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
 * AutotweetViewRules
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewRules extends AutotweetDefaultView
{
	/**
	 * onBrowse.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 */
	protected function onBrowse($tpl = null)
	{
		Extly::initApp(CAUTOTWEETNG_VERSION);
		Extly::loadAwesome();

		return parent::onBrowse($tpl);
	}

	/**
	 * hasAjaxOrderingSupport
	 *
	 * @return bool
	 */
	public function hasAjaxOrderingSupport()
	{
		return (EXTLY_J3);
	}
}
