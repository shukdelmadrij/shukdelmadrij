<?php

/**
 * @package     Extly.Modules
 * @subpackage  mod_twfollow - This module shows a Twitter Stream.
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('AUTOTWEET_API'))
{
	include_once JPATH_ADMINISTRATOR . '/components/com_autotweet/api/autotweetapi.php';
}

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$twData = ModTwfollowHelper::getTwitterData($params);
require JModuleHelper::getLayoutPath('mod_twfollow');
