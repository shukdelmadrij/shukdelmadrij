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
// defined('_JEXEC') or die('Restricted access');

define('EXTLY_CRONJOB_RUNNING', true);
define('AUTOTWEET_CRONJOB_RUNNING', true);

/**
 * starts the AutoTweet cronjob
 * Call this file form crontab.
 **/

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER))
{
	die();
}

// Not included in this membership
