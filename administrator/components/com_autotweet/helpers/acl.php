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
 * AclPermsHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       7.0
 */
class AclPermsHelper
{
	/**
	 * whereOwnership
	 *
	 * @param   object  &$query  Param
	 * @param   string  $asset   Param
	 *
	 * @return  void
	 */
	public static function whereOwnership(&$query, $asset = 'com_autotweet')
	{
		$platform = F0FPlatform::getInstance();

		// Not able to edit anything
		if (!$platform->authorise('core.edit', $asset))
		{
			// Ok, just owned assets
				// Ups nothing can be edited or browsed
			if (($platform->authorise('core.edit.own', $asset))
				|| ((!defined('AUTOTWEET_CRONJOB_RUNNING')) && (!defined('AUTOTWEET_AUTOMATOR_RUNNING'))))
			{
				$db = JFactory::getDbo();
				$owner_id = $platform->getUser()->id;

				$query->where($db->qn('created_by') . ' = ' . $owner_id);
			}
		}
	}
}
