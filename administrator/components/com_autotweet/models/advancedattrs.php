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
 * AutotweetModelAdvancedattrs
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelAdvancedattrs extends F0FModel
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

		$query = F0FQueryAbstract::getNew($db)
			->select('a.*')
			->from($db->quoteName('#__autotweet_advanced_attrs') . '  a');

		$fltOption = $this->getState('option-filter', null, 'cmd');

		if ($fltOption)
		{
			if ($fltOption == 'com_flexicontent')
			{
				$fltOption = 'com_content';
			}

			$query->where($db->qn('a.option') . ' = ' . $db->q($fltOption));
		}

		$fltRefId = $this->getState('ref_id', null, 'int');

		if ($fltRefId)
		{
			$query->where($db->qn('a.ref_id') . ' = ' . $db->q($fltRefId));
		}

		$fltRequestId = $this->getState('request_id', null, 'int');

		if ($fltRequestId)
		{
			$query->where($db->qn('a.request_id') . ' = ' . $db->q($fltRequestId));
		}

		$fltEvergreen = $this->getState('evergreentype_id');

		if ($fltEvergreen)
		{
			$query->where($db->qn('a.evergreentype_id') . ' = ' . $db->q($fltEvergreen));

			if ($fltEvergreen == PlgAutotweetBase::POSTTHIS_YES)
			{
				$query->from($db->quoteName('#__autotweet_requests') . ' r');
				$query->where('a.request_id = r.id');
				$query->where('r.published = 1');
			}
		}

		$fltNextseq = $this->getState('nextseq');

		if ($fltNextseq)
		{
			$query->where($db->qn('a.id') . ' < ' . $db->q($fltNextseq));
		}

		$query->order($db->qn('a.id') . ' DESC');

		return $query;
	}
}
