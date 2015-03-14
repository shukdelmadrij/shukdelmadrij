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
 * GridHelper
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class GridHelper
{
	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value     Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i         The index
	 * @param   bool     $isModule  Param
	 *
	 * @return  string
	 */
	public static function pubstates($value, $i, $isModule = false)
	{
		if (is_object($value))
		{
			$value = $value->pubstate;
		}

		return SelectControlHelper::getTextForEnum($value, true, $isModule);
	}

	/**
	 * loadStats
	 *
	 * @param   object  $view  Param
	 *
	 * @return  void
	 */
	public static function loadStats($view)
	{
		// Load the model
		$info = F0FModel::getTmpInstance('Update', 'AutoTweetModel');

		$view->assign('comp', $info->getComponentInfo());
		$view->assign('plugins', $info->getPluginInfo());
		$view->assign('thirdparty', $info->getThirdpartyInfo());
		$view->assign('sysinfo', $info->getSystemInfo());

		// 7 days = 7 * 24 * 60 * 60
		$time_intval = 604800;

		// Calculate date for interval
		$now = JFactory::getDate();
		$check_date = $now->toUnix();
		$check_date = $check_date - $time_intval;
		$check_date = JFactory::getDate($check_date);

		$postsModel = F0FModel::getTmpInstance('Posts', 'AutoTweetModel');
		$postsModel->set('after_date', $check_date->toSql());

		$postsModel->set('pubstate', 'success');
		$success = $postsModel->getTotal();

		$postsModel->reset();
		$postsModel->set('pubstate', 'error');
		$error = $postsModel->getTotal();

		$postsModel->reset();
		$postsModel->set('pubstate', 'approve');
		$approve = $postsModel->getTotal();

		$postsModel->reset();
		$postsModel->set('pubstate', 'cronjob');
		$cronjob = $postsModel->getTotal();

		$postsTotal = $success + $error + $approve + $cronjob;

		$view->assign('success', $success);
		$view->assign('error', $error);
		$view->assign('approve', $approve);
		$view->assign('cronjob', $cronjob);
		$view->assign('total', $postsTotal);

		if ($postsTotal)
		{
			$view->assign('p_success', round($success / $postsTotal * 100));
			$view->assign('p_error', round($error / $postsTotal * 100));
			$view->assign('p_approve', round($approve / $postsTotal * 100));
			$view->assign('p_cronjob', round($cronjob / $postsTotal * 100));
			$view->assign('p_total', $postsTotal);
		}
		else
		{
			$view->assign('p_success', 0);
			$view->assign('p_error', 0);
			$view->assign('p_approve', 0);
			$view->assign('p_cronjob', 0);
			$view->assign('p_total', 0);
		}

		$requestModel = F0FModel::getTmpInstance('Requests', 'AutoTweetModel');
		$requestModel->savestate(false);
		$requestModel->set('after_date', $check_date->toSql());
		$requestsTotal = $requestModel->getTotal();

		$total = $postsTotal + $requestsTotal;

		$view->assign('requests', $requestsTotal);
		$view->assign('posts', $postsTotal);

		if ($total)
		{
			$view->assign('p_requests', round($requestsTotal / $total * 100));
			$view->assign('p_posts', round($postsTotal / $total * 100));
		}
		else
		{
			$view->assign('p_requests', 0);
			$view->assign('p_posts', 0);
		}
	}
}
