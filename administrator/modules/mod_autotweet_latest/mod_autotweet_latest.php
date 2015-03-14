<?php

/**
 * @package     Extly.Modules
 * @subpackage  mod_autotweet_latest - AutoTweet NG Latest-Module
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.prieco.com http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('AUTOTWEET_API'))
{
	include_once JPATH_ADMINISTRATOR . '/components/com_autotweet/api/autotweetapi.php';
}

// To have the version helper available always
JLoader::load('VersionHelper');

$count = $params->get('count', 5);
$mode = $params->get('mode', 1);

$input = array(
		'savestate'	=> 0,
		'limit'		=> $count,
		'limitstart' => 0,
		'no_clear'	=> true,
		'only_once'	=> true,
		'task'		=> 'browse',
		'filter_order' => 'postdate',
		'filter_order_Dir' => 'DESC',
		'enabled'	=> 1,
		'caching'	=> false
);

switch ($mode)
{
	// Show latest successful Posts
	case 1:
		$input['pubstate'] = 'success';
		break;

	// Show latest error or pending Posts
	case 2:
		$input['pubstate'] = array('error', 'approve');
		break;

	// Show waiting (cronjob) Posts
	case 3:
	default:
		$input['pubstate'] = 'cronjob';
		break;
}

$config = array(
		'option'	=> 'com_autotweet',
		'view'		=> 'posts',
		'layout'	=> 'module',
		'input'		=> $input
);

F0FDispatcher::getTmpInstance('com_autotweet', 'posts', $config)->dispatch();
