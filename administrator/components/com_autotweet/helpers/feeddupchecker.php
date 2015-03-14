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
 * FeedDupCheckerHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class FeedDupCheckerHelper
{
	const BASIC 	= 1;
	const THOROUGH 	= 0;

	/**
	 * feedContentValidate.
	 *
	 * @param   object  &$article          Param
	 * @param   int     $compare_existing  Param
	 *
	 * @return	bool
	 */
	public static function feedContentIsDuplicated(&$article, $compare_existing = 0)
	{
		return self::_IsDuplicated($article, $compare_existing, '#__content', 'xreference', 'title', 'alias');
	}

	/**
	 * feedK2IsDuplicated.
	 *
	 * @param   object  &$article          Param
	 * @param   int     $compare_existing  Param
	 *
	 * @return	bool
	 */
	public static function feedK2IsDuplicated(&$article, $compare_existing)
	{
		return self::_IsDuplicated($article, $compare_existing, '#__k2_items', null, 'title', 'alias');
	}

	/**
	 * feedZooIsDuplicated.
	 *
	 * @param   object  &$article          Param
	 * @param   int     $compare_existing  Param
	 *
	 * @return	bool
	 */
	public static function feedZooIsDuplicated(&$article, $compare_existing)
	{
		return self::_IsDuplicated($article, $compare_existing, '#__zoo_item', null, 'name', 'alias');
	}

	/**
	 * feedZooIsDuplicated.
	 *
	 * @param   object  &$article          Param
	 * @param   int     $compare_existing  Param
	 * @param   string  $table             Param
	 * @param   string  $fld_xreference    Param
	 * @param   string  $fld_title         Param
	 * @param   string  $fld_alias         Param
	 *
	 * @return	bool
	 */
	private static function _IsDuplicated(&$article, $compare_existing, $table, $fld_xreference, $fld_title, $fld_alias)
	{
		$db = JFactory::getDBO();

		$id = null;

		if (($fld_xreference) && (isset($article->hash)))
		{
			$query = F0FQueryAbstract::getNew($db)->select('id')->from($db->quoteName($table));
			$query->where($db->qn($fld_xreference) . ' = ' . $db->q($article->hash));

			$db->setQuery($query);
			$id = $db->loadResult();

			if (!empty($id))
			{
				return true;
			}
		}

		$query = F0FQueryAbstract::getNew($db)->select('id')->from($db->quoteName($table));
		$query->where($db->qn($fld_alias) . ' = ' . $db->q($article->alias));
		$db->setQuery($query);
		$id = $db->loadResult();

		if (!empty($id))
		{
			return true;
		}

		// Return
		if ($compare_existing == self::BASIC)
		{
			return false;
		}

		$query = F0FQueryAbstract::getNew($db)->select('id')->from($db->quoteName($table));
		$query->where($db->qn($fld_title) . ' = ' . $db->q($article->title));
		$db->setQuery($query);
		$id = $db->loadResult();

		if (!empty($id))
		{
			return true;
		}

		return false;
	}
}
