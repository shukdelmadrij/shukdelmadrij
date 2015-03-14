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

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * AutotweetViewInfos
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewInfos extends AutotweetDefaultView
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

		// Load the model
		$info = F0FModel::getTmpInstance('Update', 'AutoTweetModel');

		$this->assign('comp', $info->getComponentInfo());
		$this->assign('plugins', $info->getPluginInfo());
		$this->assign('thirdparty', $info->getThirdpartyInfo());
		$this->assign('sysinfo', $info->getSystemInfo());

		Extly::initApp(CAUTOTWEETNG_VERSION);

		return;
	}
}
