<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * AutotweetModelFeedContentCategories
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelFeedContentCategories extends F0FModel
{
	/**
	 * buildQuery
	 *
	 * @param   bool  $overrideLimits  Param
	 *
	 * @return	F0FQuery
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();

		$query = F0FQueryAbstract::getNew($db)->select('id, title')->from($db->quoteName('#__categories'));

		$fltName = $this->getState('title', null, 'string');

		if ($fltName)
		{
			$fltName = "%$fltName%";
			$query->where($db->qn('title') . ' LIKE ' . $db->q($fltName));
		}

		$fltPublished = $this->getState('published', 1, 'cmd');

		if (!empty($fltPublished))
		{
			$query->where($db->qn('published') . ' = ' . $db->q($fltPublished));
		}

		$query->where($db->qn('extension') . ' = ' . $db->q('com_content'));
		$query->where($db->qn('id') . ' > 1');

		$order = $this->getState('filter_order', 'title', 'cmd');
		$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
		$query->order($order . ' ' . $dir);

		return $query;
	}
}
