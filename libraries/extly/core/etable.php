<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Form Class for the Extly Library.
 *
 * @package     Extly.Library
 * @subpackage  HTML
 * @since       11.1
 */
class ETable
{
	/**
	 * copy
	 *
	 * @param   F0FTable  &$table  Param
	 * @param   array  	  &$cid    Param
	 *
	 * @return	bool
	 */
	public function copy(&$table, &$cid = null)
	{
		JArrayHelper::toInteger($cid);
		$k = $table->getKeyName();

		if (count($cid) < 1)
		{
			if ($table->$k)
			{
				$cid = array(
								$table->$k
				);
			}
			else
			{
				$table->setError("No items selected.");

				return false;
			}
		}

		$created_by = $table->getColumnAlias('created_by');
		$created_on = $table->getColumnAlias('created_on');
		$modified_by = $table->getColumnAlias('modified_by');
		$modified_on = $table->getColumnAlias('modified_on');

		$locked_byName = $table->getColumnAlias('locked_by');
		$checkin = in_array($locked_byName, array_keys($table->getProperties()));

		foreach ($cid as $item)
		{
			// Prevent load with id = 0
			if (!$item)
			{
				continue;
			}

			$table->load($item);

			if ($checkin)
			{
				// We're using the checkin and the record is used by someone else
				if ($table->isCheckedOut($item))
				{
					continue;
				}
			}

			if (!$table->onBeforeCopy($item))
			{
				continue;
			}

			$table->$k = null;
			$table->$created_by = null;
			$table->$created_on = null;
			$table->$modified_on = null;
			$table->$modified_by = null;

			// Let's fire the event only if everything is ok
			if ($table->store())
			{
				$table->onAfterCopy($item);
			}

			$table->reset();
		}

		return true;
	}
}
