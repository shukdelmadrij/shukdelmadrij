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
 * AutotweetModelAutomators
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelAutomators extends F0FModel
{
	/**
	 * lastRunCheck
	 *
	 * @param   string  $plugin    Param
	 * @param   int     $interval  Param
	 * @param   JDate   $next      Param
	 *
	 * @return  bool
	 */
	public function lastRunCheck($plugin, $interval = 0, $next = null)
	{
		$last = $this->_lastRun($plugin);

		$now = JFactory::getDate();
		$diff = $now->toUnix() - $last->toUnix();

		$result = ($diff > $interval);

		if ($result)
		{
			if ($next)
			{
				$this->_updateLastRun($plugin, $next);
			}
			else
			{
				$this->_updateLastRun($plugin, $now);
			}
		}

		return $result;
	}

	/**
	 * lastRun
	 *
	 * @param   string  $plugin  Param
	 *
	 * @return  JDate
	 */
	private function _lastRun($plugin)
	{
		$automat = $this->getTable();
		$automat->load(
			array(
				'plugin' => $plugin
			)
		);

		if (($automat->id) && ($automat->plugin == $plugin))
		{
			$this->getItem($automat->id);
			$last = JFactory::getDate($automat->lastexec);
		}
		else
		{
			$this->reset();
			$last = JFactory::getDate('1999-11-30 00:00:00');
		}

		return $last;
	}

	/**
	 * updateLastRun
	 *
	 * @param   string  $plugin  Param
	 * @param   JDate   $now     Param
	 *
	 * @return  void
	 */
	private function _updateLastRun($plugin, $now)
	{
		if (!$now)
		{
			$now = JFactory::getDate();
		}

		$this->save(
			array(
				'id' => $this->id,
				'plugin' => $plugin,
				'lastexec' => $now->toSql()
			)
		);
	}
}
