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
 * AutotweetViewChannel
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewChannel extends AutotweetDefaultView
{
	/**
	 * onAdd.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 */
	public function onAdd($tpl = null)
	{
		$result = parent::onAdd($tpl);

		Extly::loadAwesome();

		$file = EHtml::getRelativeFile('js', 'com_autotweet/channel.min.js');

		if ($file)
		{
			$dependencies = array();
			$dependencies['channel'] = array('extlycore');
			Extly::initApp(CAUTOTWEETNG_VERSION, $file, $dependencies);
		}

		$postsModel = F0FModel::getTmpInstance('Posts', 'AutoTweetModel');
		$postsModel->set('pubstate',
				array(
						AutotweetPostHelper::POST_SUCCESS,
						AutotweetPostHelper::POST_ERROR
			)
		);

		$postsModel->set('channel', $this->item->id);
		$postsModel->set('filter_order', 'id');
		$postsModel->set('filter_order_Dir', 'DESC');
		$postsModel->set('limit', 1);
		$posts = $postsModel->getItemList();

		$alert_message = '';
		$alert_style = 'alert-info';

		if (count($posts) > 0)
		{
			$lastpost = $posts[0];

			if ($lastpost->pubstate == AutotweetPostHelper::POST_ERROR)
			{
				$alert_style = 'alert-error';
			}

			$alert_message = $lastpost->postdate . ' - ' . JText::_($lastpost->resultmsg);
		}

		$this->assign('alert_message', $alert_message);
		$this->assign('alert_style', $alert_style);

		return $result;
	}
}
