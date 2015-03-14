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
 * AutotweetTableAutomator
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetTableAutomator extends F0FTable
{
	/**
	 * Instantiate the table object
	 *
	 * @param   string     $table  Param
	 * @param   string     $key    Param
	 * @param   JDatabase  &$db    The Joomla! database object
	 */
	public function __construct($table, $key, &$db)
	{
		parent::__construct('#__autotweet_automator', 'id', $db);
	}
}
