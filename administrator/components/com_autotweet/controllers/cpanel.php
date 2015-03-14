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

include_once 'default.php';

/**
 * AutoTweetControllerCpanel - The Control Panel controller class
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutoTweetControllerCpanel extends AutotweetControllerDefault
{
	/**
	 * onBeforeBrowse
	 *
	 * @return  void
	 */
	public function onBeforeBrowse()
	{
		$result = parent::onBeforeBrowse();

		if ($result)
		{
			// Run the automatic update site refresh
			$updateModel = F0FModel::getTmpInstance('LiveUpdates', 'AutoTweetModel');
			$updateModel->refreshUpdateSite();
		}

		return $result;
	}

	/**
	 * getUpdateInfo
	 *
	 * @return  void
	 */
	public function getUpdateInfo()
	{
		@ob_end_clean();
		header('Content-type: text/plain');

		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$safeHtmlFilter = JFilterInput::getInstance();

		$token = $data['token'];
		$token = $safeHtmlFilter->clean($token, 'ALNUM');
		$this->input->set($token, 1);

		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$updateModel = F0FModel::getTmpInstance('LiveUpdates', 'AutoTweetModel');
		$updateInfo = (object) $updateModel->getUpdates();

		$updateInfo->result = false;

		if ($updateInfo->hasUpdate)
		{
			$strings = array(
							'header'		=> JText::sprintf('COM_AUTOTWEET_CPANEL_MSG_UPDATEFOUND', VersionHelper::getFlavourName(), $updateInfo->version),
							'button'		=> JText::sprintf('COM_AUTOTWEET_CPANEL_MSG_UPDATENOW', $updateInfo->version),
							'infourl'		=> $updateInfo->infoURL,
							'infolbl'		=> JText::_('COM_AUTOTWEET_CPANEL_MSG_MOREINFO'),
			);

			$updateInfo->result = <<<ENDRESULT
	<div class="alert alert-warning">
		<h3>
			<span class="xticon xticon-info-circle glyphicon glyphicon-exclamation-sign"></span>
			{$strings['header']}
		</h3>
		<p>
			<a href="index.php?option=com_installer&view=update" class="btn btn-primary">
				{$strings['button']}
			</a>
			<a href="{$strings['infourl']}" target="_blank" class="btn btn-small btn-info">
				{$strings['infolbl']}
			</a>
		</p>
	</div>
ENDRESULT;
		}

		$message = json_encode($updateInfo);
		echo EJSON_START . $message . EJSON_END;

		// Cut the execution short
		JFactory::getApplication()->close();
	}
}
