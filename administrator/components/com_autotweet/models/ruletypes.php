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
 * AutotweetModelChanneltypes
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelRuletypes extends F0FModel
{
	const CATEGORY_IN = 1;
	const CATEGORY_NOTIN = 2;
	const TERM_OR = 3;
	const TERM_AND = 4;
	const CATCH_ALL_NOTFITS = 5;
	const WORDTERM_OR = 6;
	const WORDTERM_AND = 7;
	const REG_EXPR = 8;
	const TERM_NOTIN = 9;
	const WORDTERM_NOTIN = 10;
	const AUTHOR_IN = 11;
	const AUTHOR_NOTIN = 12;
	const CATCH_ALL = 13;
	const LANGUAGE_IN = 14;
	const LANGUAGE_NOTIN = 15;
	const ACCESS_IN = 16;
	const ACCESS_NOTIN = 17;

	/**
	 * buildQuery
	 *
	 * @param   bool  $overrideLimits  Param
	 *
	 * @return	F0FQuery
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDBO();
		$query = parent::buildQuery($overrideLimits);
		$query->order($db->qn('name'));

		return $query;
	}
}
