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

/*
 *
* NO ENCRYPTION, NO SECRET SAUCE, JUST A "define"
*
* I KNOW YOUR ARE HERE TO EXTEND THE NUMBER OF CHANNELS ....
*
* Before you change it, please consider, that:
*
* Your subscription supports our work, ensuring that we can continue
* developing products and offering support services.
*
* Right now, I'm writing this notice on a beatiful Sunday morning
* to release a new version of the Free Version.
*
* So, please, purchase a Pro/Joocial membership instead of change the "define('AUTOTWEETNG_FREE', true);"
* http://www.extly.com/autotweet-ng-pro.html
*
* PD: I trust your are going to do the right thing, in the same way
* I'm just coding a "define('AUTOTWEETNG_FREE', true);" and avoiding any encryption / DRM schema.
*
*/

define('AUTOTWEETNG_JOOCIAL', false);
define('AUTOTWEETNG_PRO', false);
define('AUTOTWEETNG_BASIC', false);
define('AUTOTWEETNG_FREE', true);

define('JED_ID', 9347);

/**
 * VersionHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class VersionHelper
{
	/**
	 * getLogo
	 *
	 * @return	string
	 */
	public static function getLogo()
	{
		$logo = JUri::root() . 'media/com_autotweet/images/' . (AUTOTWEETNG_JOOCIAL ? 'joocial-logo-36.png' : 'autotweet-logo-36.png');

		return $logo;
	}

	/**
	 * getTitle
	 *
	 * @param   string  $title  Title+
	 *
	 * @return	string
	 */
	public static function getTitle($title)
	{
		$name = self::getFlavourName();

		return $name . ' - ' . $title;
	}

	/**
	 * getFlavourName
	 *
	 * @return	void
	 */
	public static function getFlavourName()
	{
		return (AUTOTWEETNG_JOOCIAL ? JText::_('COM_AUTOTWEET_NAME') : JText::_('COM_AUTOTWEET_NAME') . ' ' . self::getFlavour());
	}

	/**
	 * getFlavour
	 *
	 * @return	void
	 */
	public static function getFlavour()
	{
		$version_title = null;

		if (AUTOTWEETNG_JOOCIAL)
		{
			$version_title = 'Joocial';
		}

		if (AUTOTWEETNG_PRO)
		{
			$version_title = 'Pro';
		}

		if (AUTOTWEETNG_BASIC)
		{
			$version_title = 'Basic';
		}

		if (AUTOTWEETNG_FREE)
		{
			$version_title = 'Free';
		}

		return $version_title;
	}

	/**
	 * isFreeFlavour
	 *
	 * @return	void
	 */
	public static function isFreeFlavour()
	{
		return (self::getFlavour() == 'Free');
	}

	/**
	 * getUpdatesSite
	 *
	 * @return	void
	 */
	public static function getUpdatesSite()
	{
		$version_site = null;

		if (AUTOTWEETNG_JOOCIAL)
		{
			$version_site = 'http://www.extly.com/update-autotweetng-joocial';
		}

		if (AUTOTWEETNG_PRO)
		{
			$version_site = 'http://www.extly.com/update-autotweetng-pro';
		}

		if (AUTOTWEETNG_BASIC)
		{
			$version_site = 'http://www.extly.com/update-autotweetng-basic';
		}

		if (AUTOTWEETNG_FREE)
		{
			$version_site = 'http://www.extly.com/update-autotweetng-free';
		}

		return $version_site;
	}
}
