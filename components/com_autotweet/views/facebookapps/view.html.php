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
 * AutotweetViewFacebookapp
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewFacebookapps extends F0FViewHtml
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
		JLoader::register('FacebookApp', JPATH_AUTOTWEET_HELPERS . '/facebookapp.php');
		require JPATH_AUTOTWEET_HELPERS . '/channels/facebook-php-sdk/facebook.php';

		Extly::initApp(CAUTOTWEETNG_VERSION);

		Extly::loadAwesome();

		JLoader::import('extly.html.egrid');

		$this->getModel()->savestate(0);

		return $this->onDisplay($tpl);
	}

	/**
	 * onDisplay.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 *
	 * @since	1.5
	 */
	protected function onDisplay($tpl = null)
	{
		$params = JFactory::getApplication()->getParams();
		$this->assignRef('params', $params);

		return true;
	}
}
