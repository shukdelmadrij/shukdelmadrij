<?php

/**
 * @package     Extly.Modules
 * @subpackage  mod_light_rss - Light RSS
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

// Include the helper functions only once
require_once dirname(__FILE__) . '/helper.php';

$enable_tooltip = ($params->get('enable_tooltip', 'yes') == 'yes');

// Get data from helper class
$light_rss = modLightRSSHelper::getFeed($params);

// Run default template script for output
require JModuleHelper::getLayoutPath('mod_light_rss');
