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
 * AutotweetViewRule
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewRule extends F0FViewHtml
{
	/**
	 * onEdit.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 */
	public function onAdd($tpl = null)
	{
		Extly::loadAwesome();

		$file = EHtml::getRelativeFile('js', 'com_autotweet/rule.min.js');

		if ($file)
		{
			Extly::initApp(CAUTOTWEETNG_VERSION, $file);
		}

		return parent::onAdd($tpl);
	}
}
