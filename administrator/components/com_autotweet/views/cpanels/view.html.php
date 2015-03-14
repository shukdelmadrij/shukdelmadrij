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
 * AutotweetViewCpanels
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewCpanels extends AutotweetDefaultView
{
	/**
	 * onBrowse
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	bool
	 */
	protected function onBrowse($tpl = null)
	{
		Extly::loadAwesome();

		GridHelper::loadStats($this);

		// Get component parameter - Offline mode
		$version_check = EParameter::getComponentParam(CAUTOTWEETNG, 'version_check', 1);
		$this->assign('version_check', $version_check);

		if ($version_check)
		{
			$file = EHtml::getRelativeFile('js', 'com_autotweet/liveupdate.min.js');

			if ($file)
			{
				$dependencies = array();
				$dependencies['liveupdate'] = array('extlycore');
				Extly::initApp(CAUTOTWEETNG_VERSION, $file, $dependencies);
			}
		}

		parent::onBrowse($tpl);
	}
}
