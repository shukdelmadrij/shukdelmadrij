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
 * AutotweetViewRequest
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewRequest extends AutotweetDefaultView
{
	/**
	 * onAdd.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 */
	public function onAdd($tpl = null)
	{
		Extly::loadAwesome();

		$file = EHtml::getRelativeFile('js', 'com_autotweet/post.min.js');

		if ($file)
		{
			$dependencies = array();
			$dependencies['post'] = array('extlycore');
			Extly::initApp(CAUTOTWEETNG_VERSION, $file, $dependencies);
		}

		return parent::onAdd($tpl);
	}
}
